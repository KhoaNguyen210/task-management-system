<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskProgress;
use App\Models\TaskExtensionRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\TaskExtensionRequested;
use App\Notifications\TaskExtensionDecision;
use App\Notifications\TaskEvaluated;

class TaskController extends Controller
{
    public function assignTasks()
    {
        $departmentId = Auth::user()->department_id;
        $lecturers = User::where('department_id', $departmentId)
                         ->where('role', 'Lecturer')
                         ->where('user_id', '!=', Auth::id())
                         ->orderBy('name', 'asc')
                         ->get();
        return view('tasks.assign', compact('lecturers'));
    }

    public function storeTask(Request $request)
    {
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
            $task = Task::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'due_date' => $validatedData['due_date'],
                'status' => 'Not Started',
                'created_by' => Auth::id(),
                'department_id' => $departmentId,
            ]);
            $task->assignedUsers()->attach($validatedData['assigned_users']);
            DB::commit();
            return redirect()->back()->with('success', 'Phân công công việc thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating task or assigning users: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi phân công công việc. Vui lòng thử lại.')->withInput();
        }
    }

    public function show($taskId)
    {
        $user = Auth::user();
        $routeName = match ($user->role) {
            'Department Head' => 'dashboard.department_head',
            'Lecturer' => 'dashboard.lecturer',
            'Dean' => 'dashboard.dean',
            default => 'home',
        };
        try {
            $task = Task::with(['creatorUser', 'assignedUsers', 'department', 'progressUpdates.user', 'extensionRequests.user', 'extensionRequests.approver', 'evaluator'])
                        ->findOrFail($taskId);
    
            $isAssignedLecturer = $user->role === 'Lecturer' && $task->assignedUsers->contains($user->user_id);
            $isDepartmentHeadOfTask = $user->role === 'Department Head' && $user->department_id === $task->department_id;
    
            if ($isAssignedLecturer || $isDepartmentHeadOfTask) {
                return view('tasks.show', compact('task'));
            } else {
                return redirect()->route($routeName)->with('error', 'Bạn không có quyền xem chi tiết công việc này.');
            }
    
        } catch (\Exception $e) {
            Log::error('Error showing task details: ' . $e->getMessage());
            return redirect()->route($routeName)->with('error', 'Đã xảy ra lỗi khi xem chi tiết công việc.');
        }
    }

    public function showUpdateProgressForm($taskId)
    {
        $task = Task::findOrFail($taskId);
        if (!$task->assignedUsers->contains(Auth::id())) {
            return redirect()->back()->with('error', 'Bạn không được phân công cho công việc này.');
        }
        return view('tasks.update_progress', compact('task'));
    }

    public function updateProgress(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        if (!$task->assignedUsers->contains(Auth::id())) {
            return redirect()->back()->with('error', 'Bạn không được phân công cho công việc này.');
        }

        $request->validate([
            'status' => 'required|string|in:Not Started,In Progress,Completed',
            'comment' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $progress = new TaskProgress();
            $progress->task_id = $taskId;
            $progress->user_id = Auth::id();
            $progress->status = $request->status;
            $progress->comment = $request->comment;

            if ($request->hasFile('attachment')) {
                $path = $request->file('attachment')->store('attachments', 'public');
                $progress->attachment = $path;
            }

            $progress->save();
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

    public function showRequestExtensionForm($taskId)
    {
        $task = Task::findOrFail($taskId);
        if (!$task->assignedUsers->contains(Auth::user())) {
            return redirect()->back()->with('error', 'Bạn không được phân công cho công việc này.');
        }
        return view('tasks.request_extension', compact('task'));
    }

    public function requestExtension(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        if (!$task->assignedUsers->contains(Auth::user())) {
            return redirect()->back()->with('error', 'Bạn không được phân công cho công việc này.');
        }

        if ($task->extensionRequests()->where('status', 'Pending')->exists()) {
            return redirect()->back()->with('error', 'Công việc này đã có yêu cầu gia hạn đang chờ duyệt.');
        }

        $request->validate([
            'reason' => 'required|string|max:1000',
            'new_due_date' => 'required|date|after:today',
        ]);

        DB::beginTransaction();
        try {
            $extensionRequest = new TaskExtensionRequest();
            $extensionRequest->task_id = $taskId;
            $extensionRequest->user_id = Auth::id();
            $extensionRequest->reason = $request->reason;
            $extensionRequest->new_due_date = $request->new_due_date;
            $extensionRequest->status = 'Pending';
            $extensionRequest->save();

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

    public function showEvaluationForm($taskId)
    {
        $user = Auth::user();
        if ($user->role !== 'Department Head') {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền đánh giá công việc.');
        }

        $task = Task::with(['creatorUser', 'assignedUsers', 'department', 'progressUpdates.user'])
                    ->findOrFail($taskId);

        if ($user->department_id !== $task->department_id) {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền đánh giá công việc này.');
        }

        if ($task->status !== 'Completed' || $task->evaluation_level) {
            return redirect()->route('dashboard.department_head')->with('error', 'Công việc này không thể được đánh giá.');
        }

        return view('tasks.evaluate', compact('task'));
    }

    public function evaluateTask(Request $request, $taskId)
    {
        $user = Auth::user();
        if ($user->role !== 'Department Head') {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền đánh giá công việc.');
        }

        $task = Task::with(['assignedUsers'])->findOrFail($taskId);

        if ($user->department_id !== $task->department_id) {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền đánh giá công việc này.');
        }

        if ($task->status !== 'Completed' || $task->evaluation_level) {
            Log::warning('Task ID: ' . $taskId . ' cannot be evaluated. Status: ' . $task->status . ', Evaluation Level: ' . $task->evaluation_level);
            return redirect()->route('dashboard.department_head')->with('error', 'Công việc này không thể được đánh giá.');
        }

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

            $task->evaluation_level = $request->evaluation_level;
            $task->evaluation_comment = $request->evaluation_comment;
            $task->evaluated_by = $user->user_id;
            $task->status = 'Evaluated';
            $task->save();

            Log::info('Task ID: ' . $taskId . ' evaluated successfully with evaluated_by: ' . $task->evaluated_by);

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

    // Hiển thị danh sách yêu cầu gia hạn (FR-46)
    public function listExtensionRequests()
    {
        $user = Auth::user();
        if ($user->role !== 'Department Head') {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xem danh sách yêu cầu gia hạn.');
        }

        $extensionRequests = TaskExtensionRequest::whereHas('task', function ($query) use ($user) {
            $query->where('department_id', $user->department_id);
        })
        ->where('status', 'Pending')
        ->with(['task', 'user'])
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('tasks.extension_requests', compact('extensionRequests'));
    }

    // Hiển thị chi tiết yêu cầu gia hạn và form phê duyệt/từ chối (FR-47)
    public function showExtensionRequest($requestId)
    {
        $user = Auth::user();
        if ($user->role !== 'Department Head') {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xem chi tiết yêu cầu gia hạn.');
        }

        $extensionRequest = TaskExtensionRequest::with(['task', 'user'])
                                                ->findOrFail($requestId);

        if ($extensionRequest->task->department_id !== $user->department_id) {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xem chi tiết yêu cầu này.');
        }

        if ($extensionRequest->status !== 'Pending') {
            return redirect()->route('tasks.extension_requests')->with('error', 'Yêu cầu này đã được xử lý.');
        }

        return view('tasks.approve_extension', compact('extensionRequest'));
    }

    // Xử lý phê duyệt/từ chối yêu cầu gia hạn (FR-48 đến FR-53)
    public function approveExtensionRequest(Request $request, $requestId)
    {
        $user = Auth::user();
        if ($user->role !== 'Department Head') {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xử lý yêu cầu gia hạn.');
        }

        $extensionRequest = TaskExtensionRequest::with(['task', 'user'])
                                                ->findOrFail($requestId);

        if ($extensionRequest->task->department_id !== $user->department_id) {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xử lý yêu cầu này.');
        }

        if ($extensionRequest->status !== 'Pending') {
            return redirect()->route('tasks.extension_requests')->with('error', 'Yêu cầu này đã được xử lý.');
        }

        $request->validate([
            'decision' => 'required|in:approved,rejected',
            'comment' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $decision = $request->decision;
            $extensionRequest->status = $decision === 'approved' ? 'Approved' : 'Rejected';
            $extensionRequest->approved_by = $user->user_id;
            $extensionRequest->comment = $request->comment;
            $extensionRequest->save();

            if ($decision === 'approved') {
                $task = $extensionRequest->task;
                $task->due_date = $extensionRequest->new_due_date;
                $task->save();
            }

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

    public function destroy($taskId)
    {
        $user = Auth::user();
        if ($user->role !== 'Department Head') {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xóa công việc.');
        }

        $task = Task::findOrFail($taskId);

        if ($user->department_id !== $task->department_id) {
            return redirect()->route('dashboard.department_head')->with('error', 'Bạn không có quyền xóa công việc này.');
        }

        DB::beginTransaction();
        try {
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

    // Hiển thị form tìm kiếm công việc (FR-84, FR-85)
    public function searchForm()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['Dean', 'Department Head'])) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập chức năng tìm kiếm.');
        }

        // Lấy danh sách giảng viên cho select box
        $lecturers = User::where('role', 'Lecturer')
                        ->when($user->role === 'Department Head', function ($query) use ($user) {
                            $query->where('department_id', $user->department_id);
                        })
                        ->orderBy('name', 'asc')
                        ->get();

        // Truyền biến $tasks mặc định là collection rỗng
        $tasks = collect([]);

        return view('tasks.search', compact('lecturers', 'tasks'));
    }

    // Xử lý tìm kiếm công việc (FR-86, FR-87, FR-88, FR-89, FR-90, FR-91)
    public function search(Request $request)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['Dean', 'Department Head'])) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập chức năng tìm kiếm.');
        }

        $request->validate([
            'due_date_from' => 'nullable|date',
            'due_date_to' => 'nullable|date|after_or_equal:due_date_from',
            'assignee' => 'nullable|exists:users,user_id',
            'keyword' => 'nullable|string|max:255',
        ]);

        try {
            $query = Task::with(['assignedUsers', 'department'])
                        ->when($user->role === 'Department Head', function ($query) use ($user) {
                            $query->where('department_id', $user->department_id);
                        });

            // Lọc theo khoảng thời gian đến hạn
            if ($request->filled('due_date_from')) {
                $query->whereDate('due_date', '>=', $request->due_date_from);
            }
            if ($request->filled('due_date_to')) {
                $query->whereDate('due_date', '<=', $request->due_date_to);
            }

            // Lọc theo giảng viên được giao
            if ($request->filled('assignee')) {
                $query->whereHas('assignedUsers', function ($q) use ($request) {
                    $q->where('task_assignments.user_id', $request->assignee);
                });
            }

            // Lọc theo từ khóa trong tiêu đề
            if ($request->filled('keyword')) {
                $query->where('title', 'like', '%' . $request->keyword . '%');
            }

            // Lấy danh sách công việc, phân trang 10 công việc mỗi trang
            $tasks = $query->orderBy('due_date', 'asc')->paginate(10);

            // Lấy lại danh sách giảng viên để hiển thị trong form
            $lecturers = User::where('role', 'Lecturer')
                            ->when($user->role === 'Department Head', function ($query) use ($user) {
                                $query->where('department_id', $user->department_id);
                            })
                            ->orderBy('name', 'asc')
                            ->get();

            // Nếu không có kết quả
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
}