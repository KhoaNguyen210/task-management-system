<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phân công công việc - Hệ thống quản lý công việc</title>
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
        <!-- Tiêu đề -->
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Phân công công việc</h2>
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

        <!-- Form phân công công việc -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Phân công công việc mới</h3>
            <form action="{{ route('store.task') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên công việc</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Nhập tên công việc" required>
                    </div>
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">Giao cho</label>
                        <select name="assigned_to" id="assigned_to" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required>
                            <option value="">Chọn giảng viên</option>
                            @foreach ($lecturers as $lecturer)
                                <option value="{{ $lecturer->user_id }}" {{ old('assigned_to') == $lecturer->user_id ? 'selected' : '' }}>{{ $lecturer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="deadline" class="block text-sm font-medium text-gray-700 mb-2">Thời hạn</label>
                        <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">Yêu cầu cụ thể</label>
                    <textarea name="requirements" id="requirements" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Nhập yêu cầu cụ thể (nếu có)">{{ old('requirements') }}</textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md transform hover:scale-105">Phân công</button>
            </form>
        </div>
    </div>
</body>
</html>