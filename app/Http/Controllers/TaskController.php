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
            $task = Task::with(['creatorUser', 'assignedUsers', 'department', 'progressUpdates.user', 'extensionRequests.user', 'evaluator'])
                        ->findOrFail($taskId);
    
            $isAssignedLecturer = $user->role === 'Lecturer' && $task->assignedUsers->contains($user->user_id);

            $isDepartmentHeadOfTask = $user->role === 'Department Head' && $user->department_id === $task->department_id;
    
            if ($isAssignedLecturer || $isDepartmentHeadOfTask) {
                return view('tasks.show', compact('task'));
            } else {
                return redirect()->route($routeName)->with('error', 'Bạn không có quyền xem chi tiết công việc này.');
            }
    
        } catch (ModelNotFoundException $e) {
            return redirect()->route($routeName)->with('error', 'Không tìm thấy công việc yêu cầu.');
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
        if (!$task->assignedUsers->contains(Auth::id())) {
            return redirect()->back()->with('error', 'Bạn không được phân công cho công việc này.');
        }
        return view('tasks.request_extension', compact('task'));
    }

    public function requestExtension(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        if (!$task->assignedUsers->contains(Auth::id())) {
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

        // Kiểm tra trạng thái công việc trước khi đánh giá
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
            // Redirect với thông báo thành công, không cho phép đánh giá lại
            return redirect()->route('dashboard.department_head')->with('success', 'Đánh giá đã được lưu thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error evaluating task: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi lưu đánh giá. Vui lòng thử lại.')->withInput();
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
}