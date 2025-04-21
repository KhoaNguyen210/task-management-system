<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Giảng viên - Hệ thống quản lý công việc</title>
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
            <h2 class="text-3xl font-bold text-gray-800">Dashboard Giảng viên</h2>
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

        <!-- Bảng danh sách công việc -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Danh sách công việc được giao</h3>
            <table class="min-w-full bg-white border rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="py-3 px-4 border-b text-left">Tên công việc</th>
                        <th class="py-3 px-4 border-b text-left">Người phân công</th>
                        <th class="py-3 px-4 border-b text-left">Thời hạn</th>
                        <th class="py-3 px-4 border-b text-left">Tiến độ</th>
                        <th class="py-3 px-4 border-b text-left">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="py-3 px-4 border-b">Chuẩn bị tài liệu giảng dạy</td>
                        <td class="py-3 px-4 border-b">Nguyễn Văn C</td>
                        <td class="py-3 px-4 border-b">30/04/2025</td>
                        <td class="py-3 px-4 border-b">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: 50%"></div>
                            </div>
                        </td>
                        <td class="py-3 px-4 border-b">
                            <a href="#" class="text-blue-600 hover:underline">Cập nhật tiến độ</a>
                            <a href="#" class="text-yellow-600 hover:underline ml-2">Đề nghị gia hạn</a>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="py-3 px-4 border-b">Tổ chức seminar</td>
                        <td class="py-3 px-4 border-b">Nguyễn Văn C</td>
                        <td class="py-3 px-4 border-b">05/05/2025</td>
                        <td class="py-3 px-4 border-b">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: 30%"></div>
                            </div>
                        </td>
                        <td class="py-3 px-4 border-b">
                            <a href="#" class="text-blue-600 hover:underline">Cập nhật tiến độ</a>
                            <a href="#" class="text-yellow-600 hover:underline ml-2">Đề nghị gia hạn</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Form cập nhật tiến độ -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Cập nhật tiến độ công việc</h3>
            <form action="#" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="task_id" class="block text-sm font-medium text-gray-700 mb-2">Chọn công việc</label>
                        <select name="task_id" id="task_id" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <option value="task1">Chuẩn bị tài liệu giảng dạy</option>
                            <option value="task2">Tổ chức seminar</option>
                        </select>
                    </div>
                    <div>
                        <label for="progress" class="block text-sm font-medium text-gray-700 mb-2">Tiến độ (%)</label>
                        <input type="number" name="progress" id="progress" min="0" max="100" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Nhập tiến độ" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                    <textarea name="notes" id="notes" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Nhập ghi chú (nếu có)"></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md transform hover:scale-105">Cập nhật</button>
            </form>
        </div>
    </div>
</body>
</html>