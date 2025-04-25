<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Trưởng Bộ môn - Hệ thống quản lý công việc</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <!-- Thanh điều hướng -->
    <nav class="bg-blue-800 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Hệ thống" class="h-10 mr-4">
                <span class="text-xl font-bold">Hệ thống quản lý công việc</span>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-sm">Xin chào, {{ Auth::user()->name }} (Trưởng Bộ môn)</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-white hover:underline transition duration-200">Đăng xuất</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Nội dung chính -->
    <div class="container mx-auto mt-8 px-4">
        <!-- Tiêu đề và nút phân công -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-4xl font-bold text-gray-800">Dashboard Trưởng Bộ môn</h2>
            <a href="{{ route('assign.tasks') }}" class="bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200 shadow-md transform hover:scale-105">Phân công công việc</a>
        </div>

        <!-- Thông báo -->
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 mb-6 rounded-lg shadow-sm animate-pulse">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-4 mb-6 rounded-lg shadow-sm animate-pulse">
                {{ session('error') }}
            </div>
        @endif

        <!-- Bảng danh sách công việc -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-semibold text-gray-700">Danh sách công việc được phân công (2 công việc)</h3>
                <button class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-200">Làm mới</button>
            </div>
            <table class="min-w-full bg-white border rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="py-4 px-6 border-b text-left">Tên công việc</th>
                        <th class="py-4 px-6 border-b text-left">Người thực hiện</th>
                        <th class="py-4 px-6 border-b text-left">Thời hạn</th>
                        <th class="py-4 px-6 border-b text-left">Tiến độ</th>
                        <th class="py-4 px-6 border-b text-left">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-gray-50 transition duration-200 border-b">
                        <td class="py-4 px-6">Chuẩn bị tài liệu giảng dạy</td>
                        <td class="py-4 px-6">Nguyễn Văn A</td>
                        <td class="py-4 px-6">30/04/2025</td>
                        <td class="py-4 px-6">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: 50%"></div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <a href="#" class="text-blue-600 hover:underline mr-2">Xem chi tiết</a>
                            <a href="#" class="text-green-600 hover:underline">Đánh giá</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition duration-200 border-b">
                        <td class="py-4 px-6">Tổ chức seminar</td>
                        <td class="py-4 px-6">Trần Thị B</td>
                        <td class="py-4 px-6">05/05/2025</td>
                        <td class="py-4 px-6">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: 30%"></div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <a href="#" class="text-blue-600 hover:underline mr-2">Xem chi tiết</a>
                            <a href="#" class="text-green-600 hover:underline">Đánh giá</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Thống kê -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-2xl font-semibold text-gray-700 mb-6">Thống kê công việc</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-100 p-6 rounded-lg shadow-md flex items-center space-x-4">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <div>
                        <p class="text-gray-700 font-semibold">Tổng số công việc</p>
                        <p class="text-3xl font-bold text-blue-600">10</p>
                    </div>
                </div>
                <div class="bg-green-100 p-6 rounded-lg shadow-md flex items-center space-x-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div>
                        <p class="text-gray-700 font-semibold">Công việc hoàn thành</p>
                        <p class="text-3xl font-bold text-green-600">5</p>
                    </div>
                </div>
                <div class="bg-yellow-100 p-6 rounded-lg shadow-md flex items-center space-x-4">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-gray-700 font-semibold">Công việc đang thực hiện</p>
                        <p class="text-3xl font-bold text-yellow-600">3</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>