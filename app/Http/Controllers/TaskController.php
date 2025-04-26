<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class TaskController extends Controller
{
    public function assignTasks()
    {
        // Lấy department_id của Trưởng bộ môn đang đăng nhập
        $departmentId = Auth::user()->department_id;

        // Lấy danh sách Giảng viên (role: Lecturer) trong cùng bộ môn
        $lecturers = User::where('department_id', $departmentId)
                         ->where('role', 'Lecturer')
                         ->where('user_id', '!=', Auth::id())
                         ->orderBy('name', 'asc')
                         ->get();

        // Trả về view 'tasks.assign' và truyền biến $lecturers sang view
        return view('tasks.assign', compact('lecturers'));
    }

    public function storeTask(Request $request)
    {
        // Validate dữ liệu đầu vào từ form
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_users' => 'required|array|min:1',
            'assigned_users.*' => 'required|exists:users,user_id',
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        // Bắt đầu transaction
        DB::beginTransaction();

        try {
            // Lấy department_id từ Trưởng bộ môn (người tạo task)
            $departmentId = Auth::user()->department_id;

            // 1. Tạo task mới trong bảng 'tasks'
            $task = Task::create([
                'title' => $validatedData['title'],
                'description' => $validatedData['description'],
                'due_date' => $validatedData['due_date'],
                'status' => 'Not Started',
                'created_by' => Auth::id(),
                'department_id' => $departmentId,
            ]);

            // 2. Gán task cho các giảng viên đã chọn
            // Sử dụng phương thức attach() của quan hệ belongsToMany (assignedUsers)
            $task->assignedUsers()->attach($validatedData['assigned_users']);

            // Nếu tất cả thành công, commit transaction
            DB::commit();

            // Chuyển hướng về trang trước với thông báo thành công
            return redirect()->back()->with('success', 'Phân công công việc thành công!');

        } catch (\Exception $e) {
            // Nếu có lỗi, rollback transaction
            DB::rollBack();
            // Ghi log lỗi
            Log::error('Error creating task or assigning users: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            // Chuyển hướng về trang trước với thông báo lỗi và giữ lại dữ liệu form
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi phân công công việc. Vui lòng thử lại.')->withInput();
        }
    }
}