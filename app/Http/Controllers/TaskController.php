<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskProgress;
use App\Models\TaskExtensionRequest;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\TaskExtensionRequested;
use App\Notifications\TaskExtensionDecision;
use App\Notifications\TaskEvaluated;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\AnnualEvaluationReportRequest;
use Illuminate\Support\Facades\Cache;

/**
 * TaskController handles all task-related operations such as assigning, updating, evaluating,
 * and generating reports for tasks within the task management system.
 */
class TaskController extends Controller
{
    /**
     * Display the form for assigning tasks to lecturers.
     *
     * @return \Illuminate\View\View
     */
    public function assignTasks()
    {
        $departmentId = Auth::user()->department_id;
        // Fetch lecturers in the same department, excluding the current user
        $lecturers = User::where('department_id', $departmentId)
                         ->where('role', 'Lecturer')
                         ->where('user_id', '!=', Auth::id())
                         ->orderBy('name', 'asc')
                         ->get();

        return view('tasks.assign', compact('lecturers'));
    }

    /**
     * Store a newly created task and assign it to selected users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeTask(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_users' => 'required|array|min:1',
            'assigned_users.*' => 'required|exists:users,user_id',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        DB::beginTransaction();
        try {
            $departmentId = Auth::user()->department_id;
            // Create new task
            $task = Task::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'due_date' => $validatedData['due_date'],
                'status' => 'Not Started',
                'created_by' => Auth::id(),
                'department_id' => $departmentId,
            ]);

            // Assign task to selected users
            $task->assignedUsers()->attach($validatedData['assigned_users']);
            DB::commit();

            return redirect()->back()->with('success', 'Phân công công việc thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating task or assigning users: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi phân công công việc. Vui lòng thử lại.')->withInput();
        }
    }

    /**
     * Display details of a specific task.
     *
     * @param  int  $taskId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show($taskId)
    {
        $user = Auth::user();
        // Determine redirect route based on user role
        $routeName = match ($user->role) {
            'Department Head' => 'dashboard.department_head',
            'Lecturer' => 'dashboard.lecturer',
            'Dean' => 'dashboard.dean',
            default => 'home',
        };

        try {
            // Fetch task with related data
            $task = Task::with(['creatorUser', 'assignedUsers', 'department', 'progressUpdates.user', 'extensionRequests.user', 'extensionRequests.approver', 'evaluator'])
                        ->findOrFail($taskId);

            // Check user permissions
            $isAssignedLecturer = $user->role === 'Lecturer' && $task->assignedUsers->contains($user->user_id);
            $isDepartmentHeadOfTask = $user->role === 'Department Head' && $user->department_id === $task->department_id;

            if ($isAssignedLecturer || $isDepartmentHeadOfTask) {
                return view('tasks.show', compact('task'));
            }

            return redirect()->route($routeName)->with('error', 'Bạn không có quyền xem chi tiết công việc này.');
        } catch (\Exception $e) {
            Log::error('Error showing task details: ' . $e->getMessage());
            return redirect()->route($routeName)->with('error', 'Đã xảy ra lỗi khi xem chi tiết công việc.');
        }
    }

    /**
     * Display the form for updating task progress.
     *
     * @param  int  $taskId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showUpdateProgressForm($taskId)
    {
        $task = Task::findOrFail($taskId);
        // Verify if user is assigned to the task
        if (!$task->assignedUsers->contains(Auth::id())) {
            return redirect()->back()->with('error', 'Bạn không được phân công cho công việc này.');
        }

        return view('tasks.update_progress', compact('task'));
    }

    /**
     * Update the progress of a task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $taskId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProgress(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        // Verify if user is assigned to the task
        if (!$task->assignedUsers->contains(Auth::id())) {
            return redirect()->back()->with('error', 'Bạn không được phân công cho công việc này.');
        }

        // Validate request data
        $request->validate([
            'status' => 'required|string|in:Not Started,In Progress,Completed',
            'comment' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Create new progress update
            $progress = new TaskProgress();
            $progress->task_id = $taskId;
            $progress->user_id = Auth::id();
            $progress->status = $request->status;
            $progress->comment = $request->comment;

            // Handle file attachment if present
            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('attachments', 'public');
                $progress->attachment = $path;
            }

            $progress->save();
            // Update task status
            $task->status = $request->status;
            $task->save();

            DB::commit();
            return redirect()->route('dashboard.lecturer')->with('success', 'Tiến độ công việc đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating task progress: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật tiến độ. Vui lòng thử lại.')->withInput();
        }
    }

    /**
     * Display the form for requesting a task extension.
     *
     * @param  int  $taskId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showRequestExtensionForm($taskId)
    {
        $task = Task::findOrFail($taskId);
        // Verify if user is assigned to the task
        if (!$task->assignedUsers->contains(Auth::user())) {
            return redirect()->back()->with('error', 'Bạn không được phân công cho công việc này.');
        }

        return view('tasks.request_extension', compact('task'));
    }

    /**
     * Submit a task extension request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $taskId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function requestExtension(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        // Verify if user is assigned to the task
        if (!$task->assignedUsers->contains(Auth::user())) {
            return redirect()->back()->with('error', 'Bạn không được phân công cho công việc này.');
        }

        // Check for existing pending extension requests
        if ($task->extensionRequests()->where('status', 'Pending')->exists()) {
            return redirect()->back()->with('error', 'Công việc này đã có yêu cầu gia hạn đang chờ duyệt.');
        }

        // Validate request data
        $request->validate([
            'reason' => 'required|string|max:1000',
            'new_due_date' => 'required|date|after:today',
        ]);

        DB::beginTransaction();
        try {
            // Create new extension request
            $extensionRequest = new TaskExtensionRequest();
            $extensionRequest->task_id = $taskId;
            $extensionRequest->user_id = Auth::id();
            $extensionRequest->reason = $request->reason;
            $extensionRequest->new_due_date = $request->new_due_date;
            $extensionRequest->status = 'Pending';
            $extensionRequest->save();

            // Notify department head
            $departmentHead = User::where('role', 'Department Head')
                                 ->where('department_id', $task->department_id)
                                 ->first();
            if ($departmentHead) {
                $departmentHead->notify(new TaskExtensionRequested($extensionRequest));
            }

            DB::commit();
            return redirect()->route('dashboard.lecturer')->with('success', 'Yêu cầu gia hạn đã được gửi thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error requesting task extension: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi gửi yêu cầu gia hạn. Vui lòng thử lại.')->withInput();
        }
    }

    /**
     * Display the form for evaluating a task.
     *
     * @param  int  $taskId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showEvaluationForm($taskId)
    {
        $user = Auth::user();
        // Restrict access to Department Heads only
        if ($user->role !== 'Department Head') {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền đánh giá công việc.');
        }

        $task = Task::with(['creatorUser', 'assignedUsers', 'department', 'progressUpdates.user'])
                    ->findOrFail($taskId);

        // Verify department ownership and task eligibility
        if ($user->department_id !== $task->department_id) {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền đánh giá công việc này.');
        }

        if ($task->status !== 'Completed' || $task->evaluation_level) {
            return redirect()->route('dashboard.department_head')->with('error', 'Công việc này không thể được đánh giá.');
        }

        return view('tasks.evaluate', compact('task'));
    }

    /**
     * Evaluate a completed task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $taskId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function evaluateTask(Request $request, $taskId)
    {
        $user = Auth::user();
        // Restrict access to Department Heads only
        if ($user->role !== 'Department Head') {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền đánh giá công việc.');
        }

        $task = Task::with(['assignedUsers'])->findOrFail($taskId);

        // Verify department ownership and task eligibility
        if ($user->department_id !== $task->department_id) {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền đánh giá công việc này.');
        }

        if ($task->status !== 'Completed' || $task->evaluation_level) {
            Log::warning('Task ID: ' . $taskId . ' cannot be evaluated. Status: ' . $task->status . ', Evaluation Level: ' . $task->evaluation_level);
            return redirect()->route('dashboard.department_head')->with('error', 'Công việc này không thể được đánh giá.');
        }

        // Validate request data
        $request->validate([
            'evaluation_level' => 'required|in:Không hoàn thành,Hoàn thành yếu,Hoàn thành,Hoàn thành tích cực,Hoàn thành tốt,Hoàn thành xuất sắc',
            'evaluation_comment' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            Log::info('Evaluating task ID: ' . $taskId . ' by user: ', [
                'user_id' => $user->user_id,
                'username' => $user->name,
                'role' => $user->role,
            ]);

            // Update task evaluation
            $task->evaluation_level = $request->evaluation_level;
            $task->evaluation_comment = $request->evaluation_comment;
            $task->evaluated_by = $user->user_id;
            $task->status = 'Evaluated';
            $task->save();

            Log::info('Task ID: ' . $taskId . ' evaluated successfully with evaluated_by: ' . $task->evaluated_by);

            // Notify assigned users
            foreach ($task->assignedUsers as $assignee) {
                $assignee->notify(new TaskEvaluated($task));
            }

            DB::commit();
            return redirect()->route('dashboard.department_head')->with('success', 'Đánh giá đã được lưu thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error evaluating task: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi lưu đánh giá. Vui lòng thử lại.')->withInput();
        }
    }

    /**
     * List pending extension requests for Department Head.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function listExtensionRequests()
    {
        $user = Auth::user();
        // Restrict access to Department Heads only
        if ($user->role !== 'Department Head') {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xem danh sách yêu cầu gia hạn.');
        }

        // Fetch pending extension requests for the user's department
        $extensionRequests = TaskExtensionRequest::whereHas('task', function ($query) use ($user) {
            $query->where('department_id', $user->department_id);
        })
        ->where('status', 'Pending')
        ->with(['task', 'user'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('tasks.extension_requests', compact('extensionRequests'));
    }

    /**
     * Display details of a specific extension request.
     *
     * @param  int  $requestId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showExtensionRequest($requestId)
    {
        $user = Auth::user();
        // Restrict access to Department Heads only
        if ($user->role !== 'Department Head') {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xem chi tiết yêu cầu gia hạn.');
        }

        $extensionRequest = TaskExtensionRequest::with(['task', 'user'])
                                                ->findOrFail($requestId);

        // Verify department ownership and request status
        if ($extensionRequest->task->department_id !== $user->department_id) {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xem chi tiết yêu cầu này.');
        }

        if ($extensionRequest->status !== 'Pending') {
            return redirect()->route('tasks.extension_requests')->with('error', 'Yêu cầu này đã được xử lý.');
        }

        return view('tasks.approve_extension', compact('extensionRequest'));
    }

    /**
     * Approve or reject a task extension request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $requestId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveExtensionRequest(Request $request, $requestId)
    {
        $user = Auth::user();
        // Restrict access to Department Heads only
        if ($user->role !== 'Department Head') {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xử lý yêu cầu gia hạn.');
        }

        $extensionRequest = TaskExtensionRequest::with(['task', 'user'])
                                                ->findOrFail($requestId);

        // Verify department ownership and request status
        if ($extensionRequest->task->department_id !== $user->department_id) {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xử lý yêu cầu này.');
        }

        if ($extensionRequest->status !== 'Pending') {
            return redirect()->route('tasks.extension_requests')->with('error', 'Yêu cầu này đã được xử lý.');
        }

        // Validate request data
        $request->validate([
            'decision' => 'required|in:approved,rejected',
            'comment' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $decision = $request->decision;
            // Update extension request status
            $extensionRequest->status = $decision === 'approved' ? 'Approved' : 'Rejected';
            $extensionRequest->approved_by = $user->user_id;
            $extensionRequest->comment = $request->comment;
            $extensionRequest->save();

            // Update task due date if approved
            if ($decision === 'approved') {
                $task = $extensionRequest->task;
                $task->due_date = $extensionRequest->new_due_date;
                $task->save();
            }

            // Notify the requester
            $extensionRequest->user->notify(new TaskExtensionDecision($extensionRequest, $decision));

            DB::commit();
            $message = $decision === 'approved' ? 'Yêu cầu gia hạn đã được phê duyệt.' : 'Yêu cầu gia hạn đã bị từ chối.';
            return redirect()->route('tasks.extension_requests')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing extension request: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xử lý yêu cầu. Vui lòng thử lại.')->withInput();
        }
    }

    /**
     * Delete a task and its related data.
     *
     * @param  int  $taskId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($taskId)
    {
        $user = Auth::user();
        // Restrict access to Department Heads only
        if ($user->role !== 'Department Head') {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xóa công việc.');
        }

        $task = Task::findOrFail($taskId);

        // Verify department ownership
        if ($user->department_id !== $task->department_id) {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xóa công việc này.');
        }

        DB::beginTransaction();
        try {
            // Detach assigned users and delete related data
            $task->assignedUsers()->detach();
            TaskProgress::where('task_id', $task->id)->delete();
            TaskExtensionRequest::where('task_id', $task->id)->delete();
            $task->delete();

            DB::commit();
            return redirect()->route('dashboard.department_head')->with('success', 'Công việc đã được xóa thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting task: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xóa công việc. Vui lòng thử lại.');
        }
    }

    /**
     * Display the task search form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function searchForm()
    {
        $user = Auth::user();
        // Restrict access to Dean or Department Head
        if (!in_array($user->role, ['Dean', 'Department Head'])) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập chức năng tìm kiếm.');
        }

        // Fetch lecturers based on user role
        $lecturers = User::where('role', 'Lecturer')
                        ->when($user->role === 'Department Head', function ($query) use ($user) {
                            $query->where('department_id', $user->department_id);
                        })
                        ->orderBy('name', 'asc')
                        ->get();

        $tasks = collect([]);

        return view('tasks.search', compact('lecturers', 'tasks'));
    }

    /**
     * Perform a task search based on provided criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        // Restrict access to Dean or Department Head
        if (!in_array($user->role, ['Dean', 'Department Head'])) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập chức năng tìm kiếm.');
        }

        // Validate request data
        $request->validate([
            'due_date_from' => 'nullable|date',
            'due_date_to' => 'nullable|date|after_or_equal:due_date_from',
            'assignee' => 'nullable|exists:users,user_id',
            'keyword' => 'nullable|string|max:255',
        ]);

        try {
            // Build task query
            $query = Task::with(['assignedUsers', 'department'])
                        ->when($user->role === 'Department Head', function ($query) use ($user) {
                            $query->where('department_id', $user->department_id);
                        });

            // Apply filters
            if ($request->filled('due_date_from')) {
                $query->whereDate('due_date', '>=', $request->due_date_from);
            }
            if ($request->filled('due_date_to')) {
                $query->whereDate('due_date', '<=', $request->due_date_to);
            }
            if ($request->filled('assignee')) {
                $query->whereHas('assignedUsers', function ($q) use ($request) {
                    $q->where('task_assignments.user_id', $request->assignee);
                });
            }
            if ($request->filled('keyword')) {
                $query->where('title', 'like', '%' . $request->keyword . '%');
            }

            // Fetch tasks with pagination
            $tasks = $query->orderBy('due_date', 'asc')->paginate(10);

            // Fetch lecturers for form
            $lecturers = User::where('role', 'Lecturer')
                            ->when($user->role === 'Department Head', function ($query) use ($user) {
                                $query->where('department_id', $user->department_id);
                            })
                            ->orderBy('name', 'asc')
                            ->get();

            // Handle empty results
            if ($tasks->isEmpty() && $request->filled(['due_date_from', 'due_date_to', 'assignee', 'keyword'])) {
                return view('tasks.search', compact('tasks', 'lecturers'))
                    ->with('error', 'Không tìm thấy công việc phù hợp.');
            }

            return view('tasks.search', compact('tasks', 'lecturers'));
        } catch (\Exception $e) {
            Log::error('Error searching tasks: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại sau.');
        }
    }

    /**
     * Generate an overview report for task performance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function overviewReport(Request $request)
    {
        try {
            // Clear cache to ensure fresh data
            Cache::flush();

            // Validate request data
            $request->validate([
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'department_id' => 'nullable|exists:departments,department_id',
                'lecturer_id' => 'nullable|exists:users,user_id',
            ]);

            // Fetch departments and lecturers
            $departments = Department::orderBy('name', 'asc')->get();
            $lecturers = User::where('role', 'Lecturer')->orderBy('name', 'asc')->get();

            // Build task query
            $query = Task::with(['department', 'assignedUsers']);
            if ($request->filled('start_date')) {
                $query->whereDate('due_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('due_date', '<=', $request->end_date);
            }
            if ($request->filled('department_id')) {
                $query->where('department_id', $request->department_id);
            }
            if ($request->filled('lecturer_id')) {
                $query->whereHas('assignedUsers', function ($q) use ($request) {
                    $q->where('users.user_id', $request->lecturer_id);
                });
            }

            // Fetch tasks
            $tasks = $query->get();

            // Calculate metrics
            $totalTasks = $tasks->count();
            $completedOnTime = $tasks->filter(function ($task) {
                return in_array($task->status, ['Completed', 'Evaluated']) && ($task->updated_at <= $task->due_date);
            })->count();
            $overdueTasks = $tasks->filter(function ($task) {
                return !in_array($task->status, ['Completed', 'Evaluated']) && now()->gt($task->due_date);
            })->count();
            $completionRate = $totalTasks > 0 ? round(($completedOnTime / $totalTasks) * 100, 2) : 0;

            // Department statistics
            $departmentStats = $tasks->groupBy('department_id')->map(function ($group) {
                $total = $group->count();
                $completed = $group->whereIn('status', ['Completed', 'Evaluated'])->count();
                return [
                    'name' => $group->first()->department->name ?? 'Không xác định',
                    'total' => $total,
                    'completed' => $completed,
                    'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                ];
            })->values();

            // Lecturer statistics
            $lecturerStats = $tasks->flatMap(function ($task) {
                return $task->assignedUsers->map(function ($user) use ($task) {
                    return ['user_id' => $user->user_id, 'task' => $task];
                });
            })->groupBy('user_id')->map(function ($group) {
                $total = $group->count();
                $completed = $group->whereIn('task.status', ['Completed', 'Evaluated'])->count();
                $user = User::find($group->first()['user_id']);
                return [
                    'name' => $user->name ?? 'Không xác định',
                    'total' => $total,
                    'completed' => $completed,
                    'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
                ];
            })->values();

            // Prepare view data
            $viewData = compact(
                'totalTasks', 'completedOnTime', 'overdueTasks', 'completionRate',
                'departmentStats', 'lecturers', 'departments', 'lecturerStats'
            );

            // Handle empty results
            if ($totalTasks === 0 && $request->filled(['start_date', 'end_date', 'department_id', 'lecturer_id'])) {
                return view('dashboard.overview', $viewData)
                    ->with('error', 'Hiện không có dữ liệu tổng hợp. Vui lòng kiểm tra lại sau.');
            }

            return view('dashboard.overview', $viewData);
        } catch (\Exception $e) {
            Log::error('Error displaying overview report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi hiển thị dashboard. Vui lòng thử lại sau.');
        }
    }

    /**
     * Export the overview report as a PDF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportReport(Request $request)
    {
        // Validate request data
        $request->validate([
            'format' => 'required|in:pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'department_id' => 'nullable|exists:departments,department_id',
            'lecturer_id' => 'nullable|exists:users,user_id',
        ]);

        try {
            // Build task query
            $query = Task::with(['department', 'assignedUsers']);
            if ($request->filled('start_date')) {
                $query->whereDate('due_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('due_date', '<=', $request->end_date);
            }
            if ($request->filled('department_id')) {
                $query->where('department_id', $request->department_id);
            }
            if ($request->filled('lecturer_id')) {
                $query->whereHas('assignedUsers', function ($q) use ($request) {
                    $q->where('users.user_id', $request->lecturer_id);
                });
            }

            // Fetch tasks
            $tasks = $query->get();

            // Calculate metrics
            $totalTasks = $tasks->count();
            $completedOnTime = $tasks->filter(function ($task) {
                return in_array($task->status, ['Completed', 'Evaluated']) && ($task->updated_at <= $task->due_date);
            })->count();
            $overdueTasks = $tasks->filter(function ($task) {
                return !in_array($task->status, ['Completed', 'Evaluated']) && now()->gt($task->due_date);
            })->count();
            $completionRate = $totalTasks > 0 ? round(($completedOnTime / $totalTasks) * 100, 2) : 0;

            // Generate and download PDF
            $pdf = Pdf::loadView('dashboard.overview_pdf', compact(
                'tasks', 'totalTasks', 'completedOnTime', 'overdueTasks', 'completionRate'
            ));
            return $pdf->download('bao_cao_tong_quan.pdf');
        } catch (\Exception $e) {
            Log::error('Error exporting report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xuất báo cáo. Vui lòng thử lại sau.');
        }
    }

    /**
     * Generate an annual evaluation report for lecturers.
     *
     * @param  \App\Http\Requests\AnnualEvaluationReportRequest  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function annualEvaluationReport(AnnualEvaluationReportRequest $request)
    {
        try {
            // Clear cache to ensure fresh data
            Cache::flush();

            $user = Auth::user();
            $validated = $request->validated();
            $academic_year = $validated['academic_year'] ?? null;
            $department_id = $validated['department_id'] ?? null;
            $lecturer_id = $validated['lecturer_id'] ?? null;
            $sort_by = $validated['sort_by'] ?? 'name';
            $sort_order = $validated['sort_order'] ?? 'asc';

            // Fetch departments and lecturers
            $departments = Department::orderBy('name', 'asc')->get();
            $lecturers = User::where('role', 'Lecturer')
                            ->when($user->role === 'Department Head', function ($query) use ($user) {
                                $query->where('department_id', $user->department_id);
                            })
                            ->orderBy('name', 'asc')
                            ->get();

            // Build task query
            $query = Task::with(['assignedUsers', 'department'])
                        ->when($user->role === 'Department Head', function ($query) use ($user) {
                            $query->where('department_id', $user->department_id);
                        });

            // Apply filters
            if ($academic_year) {
                $start_year = $academic_year;
                $end_year = $academic_year + 1;
                $query->whereBetween('due_date', [
                    "$start_year-01-01",
                    "$end_year-12-31 23:59:59"
                ]);
            }
            if ($department_id) {
                $query->where('department_id', $department_id);
            }
            if ($lecturer_id) {
                $query->whereHas('assignedUsers', function ($q) use ($lecturer_id) {
                    $q->where('users.user_id', $lecturer_id);
                });
            }

            // Fetch tasks
            $tasks = $query->get();

            // Calculate lecturer statistics
            $lecturerStats = $tasks->flatMap(function ($task) {
                return $task->assignedUsers->map(function ($user) use ($task) {
                    return ['user_id' => $user->user_id, 'task' => $task];
                });
            })->groupBy('user_id')->map(function ($group) use ($sort_by, $sort_order) {
                $totalTasks = $group->count();
                $completedOnTime = $group->filter(function ($item) {
                    return in_array($item['task']->status, ['Completed', 'Evaluated']) && $item['task']->updated_at <= $item['task']->due_date;
                })->count();
                $overdueTasks = $group->filter(function ($item) {
                    return !in_array($item['task']->status, ['Completed', 'Evaluated']) && now()->gt($item['task']->due_date);
                })->count();
                $notCompleted = $group->filter(function ($item) {
                    return !in_array($item['task']->status, ['Completed', 'Evaluated']) && now()->lte($item['task']->due_date);
                })->count();
                $completionRate = $totalTasks > 0 ? round(($completedOnTime / $totalTasks) * 100, 2) : 0;

                // Calculate average evaluation score
                $evaluations = $group->pluck('task')->filter(function ($task) {
                    return $task->evaluation_level !== null;
                })->map(function ($task) {
                    $levels = [
                        'Không hoàn thành' => 0,
                        'Hoàn thành yếu' => 1,
                        'Hoàn thành' => 2,
                        'Hoàn thành tích cực' => 3,
                        'Hoàn thành tốt' => 4,
                        'Hoàn thành xuất sắc' => 5
                    ];
                    return $levels[$task->evaluation_level] ?? 0;
                });
                $averageEvaluation = $evaluations->count() > 0 ? $evaluations->avg() : null;

                $user = User::find($group->first()['user_id']);
                return [
                    'lecturer_id' => $user->user_id,
                    'name' => $user->name ?? 'Không xác định',
                    'department' => $user->department->name ?? 'Không xác định',
                    'total_tasks' => $totalTasks,
                    'completed_on_time' => $completedOnTime,
                    'overdue_tasks' => $overdueTasks,
                    'not_completed' => $notCompleted,
                    'completion_rate' => $completionRate,
                    'average_evaluation' => $averageEvaluation ? round($averageEvaluation, 2) : null,
                ];
            })->sortBy([[$sort_by, $sort_order]])->values();

            // Handle empty results
            if ($lecturerStats->isEmpty() && $request->filled(['academic_year', 'department_id', 'lecturer_id'])) {
                return view('dashboard.annual-evaluation', compact('departments', 'lecturers', 'lecturerStats'))
                    ->with('error', 'Không có dữ liệu cho năm này.');
            }

            return view('dashboard.annual-evaluation', compact(
                'departments', 'lecturers', 'lecturerStats', 'academic_year', 'department_id', 'lecturer_id', 'sort_by', 'sort_order'
            ));
        } catch (\Exception $e) {
            Log::error('Error displaying annual evaluation report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo báo cáo. Vui lòng thử lại sau.');
        }
    }

    /**
     * Export the annual evaluation report as a PDF.
     *
     * @param  \App\Http\Requests\AnnualEvaluationReportRequest  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function exportAnnualEvaluationReport(AnnualEvaluationReportRequest $request)
    {
        $validated = $request->validated();
        $academic_year = $validated['academic_year'] ?? null;
        $department_id = $validated['department_id'] ?? null;
        $lecturer_id = $validated['lecturer_id'] ?? null;

        try {
            $user = Auth::user();
            // Build task query
            $query = Task::with(['assignedUsers', 'department'])
                        ->when($user->role === 'Department Head', function ($query) use ($user) {
                            $query->where('department_id', $user->department_id);
                        });

            // Apply filters
            if ($academic_year) {
                $start_year = $academic_year;
                $end_year = $academic_year + 1;
                $query->whereBetween('due_date', [
                    "$start_year-01-01",
                    "$end_year-12-31 23:59:59"
                ]);
            }
            if ($department_id) {
                $query->where('department_id', $department_id);
            }
            if ($lecturer_id) {
                $query->whereHas('assignedUsers', function ($q) use ($lecturer_id) {
                    $q->where('users.user_id', $lecturer_id);
                });
            }

            // Fetch tasks
            $tasks = $query->get();

            // Calculate lecturer statistics
            $lecturerStats = $tasks->flatMap(function ($task) {
                return $task->assignedUsers->map(function ($user) use ($task) {
                    return ['user_id' => $user->user_id, 'task' => $task];
                });
            })->groupBy('user_id')->map(function ($group) {
                $totalTasks = $group->count();
                $completedOnTime = $group->filter(function ($item) {
                    return in_array($item['task']->status, ['Completed', 'Evaluated']) && $item['task']->updated_at <= $item['task']->due_date;
                })->count();
                $overdueTasks = $group->filter(function ($item) {
                    return !in_array($item['task']->status, ['Completed', 'Evaluated']) && now()->gt($item['task']->due_date);
                })->count();
                $notCompleted = $group->filter(function ($item) {
                    return !in_array($item['task']->status, ['Completed', 'Evaluated']) && now()->lte($item['task']->due_date);
                })->count();
                $completionRate = $totalTasks > 0 ? round(($completedOnTime / $totalTasks) * 100, 2) : 0;

                // Calculate average evaluation score
                $evaluations = $group->pluck('task')->filter(function ($task) {
                    return $task->evaluation_level !== null;
                })->map(function ($task) {
                    $levels = [
                        'Không hoàn thành' => 0,
                        'Hoàn thành yếu' => 1,
                        'Hoàn thành' => 2,
                        'Hoàn thành tích cực' => 3,
                        'Hoàn thành tốt' => 4,
                        'Hoàn thành xuất sắc' => 5
                    ];
                    return $levels[$task->evaluation_level] ?? 0;
                });
                $averageEvaluation = $evaluations->count() > 0 ? $evaluations->avg() : null;

                $user = User::find($group->first()['user_id']);
                return [
                    'lecturer_id' => $user->user_id,
                    'name' => $user->name ?? 'Không xác định',
                    'department' => $user->department->name ?? 'Không xác định',
                    'total_tasks' => $totalTasks,
                    'completed_on_time' => $completedOnTime,
                    'overdue_tasks' => $overdueTasks,
                    'not_completed' => $notCompleted,
                    'completion_rate' => $completionRate,
                    'average_evaluation' => $averageEvaluation ? round($averageEvaluation, 2) : null,
                ];
            })->values();

            // Generate and download PDF
            $pdf = Pdf::loadView('dashboard.annual-evaluation-pdf', compact(
                'lecturerStats', 'academic_year', 'department_id', 'lecturer_id'
            ));
            return $pdf->download('bao_cao_danh_gia_cuoi_nam.pdf');
        } catch (\Exception $e) {
            Log::error('Error exporting annual evaluation report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xuất báo cáo. Vui lòng thử lại sau.');
        }
    }
}