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
        $completedTasks = Task::where('department_id', $departmentId)
                              ->where('status', 'Completed')
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
        $tasks = Task::with(['assignedUsers', 'department', 'creatorUser'])
                     ->orderBy('due_date', 'asc')
                     ->paginate(15);

        return view('dashboard.dean', compact('tasks'));
    }

    public function secretaryDashboard()
    {
         $tasks = Task::with(['assignedUsers', 'department'])
                     ->orderBy('created_at', 'desc')
                     ->paginate(15);

        return view('dashboard.secretary', compact('tasks'));
    }
}