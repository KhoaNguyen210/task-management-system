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

    // --- Overview Report Route (UC-09) ---
    Route::middleware(['check_auth_role:Dean'])->group(function () {
        Route::get('/dashboard/overview', [TaskController::class, 'overviewReport'])->name('dashboard.overview');
        Route::post('/dashboard/overview/export', [TaskController::class, 'exportReport'])->name('dashboard.overview.export');
    });

    // --- Task Routes ---
    Route::prefix('tasks')->name('tasks.')->group(function () {
        // Route phân công công việc (Chỉ cho Department Head)
        Route::middleware(['check_auth_role:Department Head'])->group(function () {
            Route::get('/assign', [TaskController::class, 'assignTasks'])->name('assign');
            Route::post('/assign', [TaskController::class, 'storeTask'])->name('store');
        });

        // Route cập nhật tiến độ và đề nghị gia hạn (Chỉ cho Lecturer)
        Route::middleware(['check_auth_role:Lecturer'])->group(function () {
            Route::get('/{taskId}/update-progress', [TaskController::class, 'showUpdateProgressForm'])->name('update_progress_form');
            Route::post('/{taskId}/update-progress', [TaskController::class, 'updateProgress'])->name('update_progress');
            Route::get('/{taskId}/request-extension', [TaskController::class, 'showRequestExtensionForm'])->name('request_extension_form');
            Route::post('/{taskId}/request-extension', [TaskController::class, 'requestExtension'])->name('request_extension');
        });

        // Route xem chi tiết công việc (Chung cho Head và Lecturer)
        Route::get('/{taskId}/show', [TaskController::class, 'show'])->name('show');

        // Route đánh giá công việc, xử lý yêu cầu gia hạn và xóa công việc (Chỉ cho Department Head)
        Route::middleware(['check_auth_role:Department Head'])->group(function () {
            Route::get('/{taskId}/evaluate', [TaskController::class, 'showEvaluationForm'])->name('evaluate_form');
            Route::post('/{taskId}/evaluate', [TaskController::class, 'evaluateTask'])->name('evaluate');
            Route::delete('/{taskId}', [TaskController::class, 'destroy'])->name('destroy');
            // Route xử lý yêu cầu gia hạn (UC-07)
            Route::get('/extension-requests', [TaskController::class, 'listExtensionRequests'])->name('extension_requests');
            Route::get('/extension-request/{requestId}', [TaskController::class, 'showExtensionRequest'])->name('extension_request.show');
            Route::post('/extension-request/{requestId}/approve', [TaskController::class, 'approveExtensionRequest'])->name('extension_request.approve');
        });

        // Route tìm kiếm công việc (Chỉ cho Dean và Department Head)
        Route::middleware(['check_auth_role:Dean,Department Head'])->group(function () {
            Route::get('/search', [TaskController::class, 'searchForm'])->name('search_form');
            Route::post('/search', [TaskController::class, 'search'])->name('search');
        });
    });

    // --- Default Authenticated Route ---
    // Route gốc '/' sẽ chuyển hướng đến dashboard phù hợp
    Route::get('/', function () {
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
                Auth::logout();
                return redirect('/login')->with('error', 'Vai trò không hợp lệ.');
        }
    })->name('home');
});