<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function departmentHeadDashboard()
    {
        $departmentId = Auth::user()->department_id;

        // Lấy các công việc thuộc bộ môn đó
        $tasks = Task::where('department_id', $departmentId)
                     ->with(['assignedUsers', 'creatorUser'])
                     ->orderBy('due_date', 'asc')
                     ->paginate(10);

        // Tính toán thống kê
        $totalTasks = Task::where('department_id', $departmentId)->count();
        // Đếm cả công việc có trạng thái Completed và Evaluated
        $completedTasks = Task::where('department_id', $departmentId)
                              ->whereIn('status', ['Completed', 'Evaluated'])
                              ->count();
        $inProgressTasks = Task::where('department_id', $departmentId)
                               ->where('status', 'In Progress')
                               ->count();

        // Truyền các biến sang view
        return view('dashboard.department_head', compact('tasks', 'totalTasks', 'completedTasks', 'inProgressTasks'));
    }

    public function lecturerDashboard()
    {
        $lecturer = Auth::user();

        $tasks = $lecturer->assignedTasks()
                         ->orderBy('due_date', 'asc')
                         ->paginate(10);

        return view('dashboard.lecturer', compact('tasks'));
    }

    public function deanDashboard()
    {
        // Lấy tổng số công việc trong toàn hệ thống
        $totalTasks = Task::count();

        // Lấy tổng số giảng viên (giả sử giảng viên có vai trò 'lecturer' hoặc tương tự)
        // Bạn cần điều chỉnh 'lecturer' nếu vai trò của giảng viên trong bảng `users` là khác
        $totalLecturers = User::where('role', 'lecturer')->count();

        // Tính tỷ lệ hoàn thành công việc trong toàn hệ thống
        // Đếm cả công việc có trạng thái Completed và Evaluated
        $completedTasks = Task::whereIn('status', ['Completed', 'Evaluated'])->count();
        $completionRate = ($totalTasks > 0) ? round(($completedTasks / $totalTasks) * 100) : 0;

        // Lấy các công việc chính để hiển thị bảng (nếu cần)
        $tasks = Task::with(['assignedUsers', 'department', 'creatorUser'])
                     ->orderBy('due_date', 'asc')
                     ->paginate(15);

        // Truyền tất cả các biến cần thiết sang view
        return view('dashboard.dean', compact('tasks', 'totalTasks', 'totalLecturers', 'completionRate'));
    }

    public function secretaryDashboard()
    {
         $tasks = Task::with(['assignedUsers', 'department'])
                     ->orderBy('created_at', 'desc')
                     ->paginate(15);

        return view('dashboard.secretary', compact('tasks'));
    }
}