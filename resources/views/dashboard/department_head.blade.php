@extends('layouts.app')

@section('title', 'Department Head Dashboard')

@section('content')
    <div class="bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-bold mb-4">Dashboard Trưởng Bộ môn</h2>
        <p>Chào mừng {{ Auth::user()->name }}! Đây là trang quản lý công việc dành cho Trưởng Bộ môn.</p>
    </div>
@endsection