<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'/*, 'role:Dean'*/])->get('/dashboard/dean', function () { return view('dashboard.dean'); })->name('dashboard.dean');
Route::middleware(['auth'/*, 'role:Department Head'*/])->get('/dashboard/department_head', function () { return view('dashboard.department_head'); })->name('dashboard.department_head');
Route::middleware(['auth'/*, 'role:Lecturer'*/])->get('/dashboard/lecturer', function () { return view('dashboard.lecturer'); })->name('dashboard.lecturer');
Route::middleware(['auth'/*, 'role:Secretary'*/])->get('/dashboard/secretary', function () { return view('dashboard.secretary'); })->name('dashboard.secretary');
