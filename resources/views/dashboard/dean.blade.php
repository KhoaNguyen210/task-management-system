@extends('layouts.app')

@section('title', 'Dashboard Trưởng Khoa')

@section('content')
    <div class="bg-white p-6 rounded shadow-md">
        <h2 class="text-2xl font-bold mb-4">Dashboard Trưởng Khoa</h2>
        <p>Chào mừng {{ Auth::user()->name }}! Đây là dashboard tổng quan dành cho Trưởng Khoa.</p>
        <div class="mt-4">
            <a href="{{ route('tasks.search_form') }}" class="bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition duration-200 shadow-md transform hover:scale-105 text-sm md:text-base">Tìm kiếm Công việc</a>
        </div>
    </div>
@endsection