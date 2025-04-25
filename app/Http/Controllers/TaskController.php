<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function assignTasks()
    {
        // Lấy danh sách Giảng viên (role: Lecturer)
        $lecturers = User::where('role', 'Lecturer')->get();
        return view('tasks.assign', compact('lecturers'));
    }

    public function storeTask(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'assigned_to' => 'required|exists:users,user_id',
            'deadline' => 'required|date|after:today',
            'requirements' => 'nullable|string',
        ]);

        Task::create([
            'name' => $request->name,
            'assigned_to' => $request->assigned_to,
            'deadline' => $request->deadline,
            'requirements' => $request->requirements,
        ]);

        return redirect()->back()->with('success', 'Phân công công việc thành công.');
    }
}