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

        // Truyền biến $tasks sang view
        return view('dashboard.department_head', compact('tasks'));
    }

    public function lecturerDashboard()
    {
        $lecturer = Auth::user();

        $tasks = $lecturer->assignedTasks()
                         ->orderBy('due_date', 'asc')
                         ->paginate(10);

        // Truyền biến $tasks sang view
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
