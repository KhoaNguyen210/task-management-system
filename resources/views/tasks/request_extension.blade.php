<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đề nghị gia hạn công việc - Hệ thống quản lý công việc</title>
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
                <span class="text-sm">Xin chào, {{ Auth::user()->name }} (Giảng viên)</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-white hover:underline transition duration-200">Đăng xuất</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Nội dung chính -->
    <div class="container mx-auto mt-8 px-4">
        <!-- Tiêu đề -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Đề nghị gia hạn công việc</h2>
            <a href="{{ route('dashboard.lecturer') }}" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200">Quay lại</a>
        </div>

        <!-- Thông báo -->
        @if (session('success'))
            <div class="bg-green-100 text-green-700 p-4 mb-4 rounded-lg shadow-sm animate-pulse">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-4 mb-4 rounded-lg shadow-sm animate-pulse">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 mb-4 rounded-lg shadow-sm animate-pulse">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form đề nghị gia hạn -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Đề nghị gia hạn: {{ $task->title }}</h3>
            <form action="{{ route('tasks.request_extension', $task->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Lý do gia hạn</label>
                    <textarea name="reason" id="reason" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Nhập lý do gia hạn" rows="4" required>{{ old('reason') }}</textarea>
                </div>
                <div class="mb-4">
                    <label for="new_due_date" class="block text-sm font-medium text-gray-700 mb-2">Thời hạn mới</label>
                    <input type="date" name="new_due_date" id="new_due_date" value="{{ old('new_due_date') }}" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md transform hover:scale-105">Gửi yêu cầu</button>
            </form>
        </div>
    </div>
</body>
</html>