<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;

// --- Authentication Routes ---
// Các route dành cho khách (chưa đăng nhập)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route đăng xuất (yêu cầu đã đăng nhập)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// --- Authenticated Routes ---
// Các route yêu cầu người dùng phải đăng nhập
Route::middleware('auth')->group(function () {

    // --- Dashboard Routes ---
    // Phân quyền dựa trên middleware CheckAuthRole
    Route::middleware(['check_auth_role:Dean'])->get('/dashboard/dean', [DashboardController::class, 'deanDashboard'])->name('dashboard.dean');
    Route::middleware(['check_auth_role:Department Head'])->get('/dashboard/department_head', [DashboardController::class, 'departmentHeadDashboard'])->name('dashboard.department_head');
    Route::middleware(['check_auth_role:Lecturer'])->get('/dashboard/lecturer', [DashboardController::class, 'lecturerDashboard'])->name('dashboard.lecturer');
    Route::middleware(['check_auth_role:Secretary,Educational Staff'])->get('/dashboard/secretary', [DashboardController::class, 'secretaryDashboard'])->name('dashboard.secretary');

    // --- Task Routes ---
    Route::prefix('tasks')->name('tasks.')->group(function () {
        // Route phân công công việc (Chỉ cho Department Head)
        Route::middleware(['check_auth_role:Department Head'])->group(function () {
            Route::get('/assign', [TaskController::class, 'assignTasks'])->name('assign');
            Route::post('/assign', [TaskController::class, 'storeTask'])->name('store');
        });
    });


    // --- Default Authenticated Route ---
    // Route gốc '/' sẽ chuyển hướng đến dashboard phù hợp
    Route::get('/', function () {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();
         switch ($user->role) {
            case 'Dean': return redirect()->route('dashboard.dean');
            case 'Department Head': return redirect()->route('dashboard.department_head');
            case 'Lecturer': return redirect()->route('dashboard.lecturer');
            case 'Secretary':
            case 'Educational Staff': return redirect()->route('dashboard.secretary');
            default:
                Auth::logout(); // Đăng xuất nếu role không hợp lệ
                return redirect('/login')->with('error', 'Vai trò không hợp lệ.');
        }
    })->name('home'); // Đặt tên cho route gốc

});
