@extends('layouts.app')

@section('title', 'Dashboard Trưởng Khoa')

@section('content')
    <div class="container mx-auto mt-8 px-4 pb-16">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div class="flex items-center space-x-3">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-6h6v6m-6 0h6m-9-5h12m-6-6V4m0 0h4m-4 0H7"></path>
                </svg>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Dashboard Trưởng Khoa</h2>
            </div>
        </div>

        <!-- Thông báo -->
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 animate-pulse"
                role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 animate-pulse"
                role="alert">
                <strong class="font-bold">Thành công!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Chào mừng và Banner -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-xl md:text-2xl font-semibold text-gray-700 mb-3">Chào mừng {{ Auth::user()->name }}!</h3>
            <p class="text-gray-600 mb-4">Quản lý hiệu quả công việc của Khoa với các báo cáo chi tiết và công cụ tìm kiếm.
            </p>
            <!-- Banner tóm tắt -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-100 p-4 rounded-lg shadow-sm text-center">
                    <h4 class="text-lg font-semibold text-blue-800">Tổng công việc</h4>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalTasks }}</p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg shadow-sm text-center">
                    <h4 class="text-lg font-semibold text-green-800">Số lượng giảng viên</h4>
                    <p class="text-2xl font-bold text-green-600">{{ $totalLecturers }}</p>
                </div>
                <div class="bg-teal-100 p-4 rounded-lg shadow-sm text-center">
                    <h4 class="text-lg font-semibold text-teal-800">Tỷ lệ hoàn thành</h4>
                    <p class="text-2xl font-bold text-teal-600">{{ $completionRate }}%</p>
                </div>
            </div>
        </div>

        <!-- Nút chức năng -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl md:text-2xl font-semibold text-gray-700 mb-4">Chức năng Quản lý</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <!-- Tìm kiếm Công việc -->
                <a href="{{ route('tasks.search_form') }}"
                    class="bg-teal-600 text-black py-4 px-6 rounded-lg hover:bg-teal-700 transition duration-300 shadow-md transform hover:scale-105 flex items-center justify-center text-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Tìm kiếm Công việc
                </a>
                <!-- Xem Báo cáo Tổng quan (UC-09) -->
                <a href="{{ route('dashboard.overview') }}"
                    class="bg-blue-600 text-white py-4 px-6 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md transform hover:scale-105 flex items-center justify-center text-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-6h6v6m-6 0h6m-9-5h12m-6-6V4m0 0h4m-4 0H7"></path>
                    </svg>
                    Xem Báo cáo Tổng quan
                </a>
                <!-- Báo cáo Đánh giá Cuối Năm (UC-12) -->
                <a href="{{ route('dashboard.annual-evaluation') }}"
                    class="bg-green-600 text-white py-4 px-6 rounded-lg hover:bg-green-700 transition duration-300 shadow-md transform hover:scale-105 flex items-center justify-center text-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Báo cáo Đánh giá Cuối Năm
                </a>
            </div>
        </div>
    </div>
@endsection