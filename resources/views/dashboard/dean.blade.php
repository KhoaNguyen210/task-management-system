@extends('layouts.app')

@section('title', 'Dashboard Trưởng Khoa')

@section('content')
    <div class="container mx-auto mt-8 px-4 pb-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Dashboard Trưởng Khoa</h2>
        </div>

        <!-- Thông báo -->
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Thành công!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl md:text-2xl font-semibold text-gray-700 mb-4">Chào mừng {{ Auth::user()->name }}!</h3>
            <p class="text-gray-600 mb-6">Đây là dashboard tổng quan dành cho Trưởng Khoa. Bạn có thể tìm kiếm công việc hoặc xem báo cáo tổng quan về hiệu suất của Khoa.</p>

            <!-- Nút chức năng -->
            <div class="flex space-x-4">
                <a href="{{ route('tasks.search_form') }}"
                   class="bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md transform hover:scale-105 text-sm md:text-base">
                    Tìm kiếm Công việc
                </a>
                <a href="{{ route('dashboard.overview') }}"
                   class="bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md transform hover:scale-105 text-sm md:text-base">
                    Xem Báo cáo Tổng quan
                </a>
            </div>
        </div>
    </div>
@endsection