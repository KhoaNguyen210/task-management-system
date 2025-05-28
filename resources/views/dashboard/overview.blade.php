<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo Tổng quan - Hệ thống quản lý công việc</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <nav class="bg-blue-800 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Hệ thống" class="h-10 mr-4">
                <a href="{{ route('home') }}" class="text-xl font-bold">Hệ thống quản lý công việc</a>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                    <span class="text-sm">Xin chào, {{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-white hover:underline transition duration-200">Đăng xuất</button>
                    </form>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-8 px-4 pb-16">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Báo cáo Tổng quan</h2>
            <a href="{{ route('dashboard.dean') }}"
               class="bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-200 shadow-md transform hover:scale-105 text-sm md:text-base">
                ← Quay lại Dashboard
            </a>
        </div>

        <!-- Thông báo -->
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Form lọc -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-xl md:text-2xl font-semibold text-gray-700 mb-4">Lọc Báo cáo</h3>
            <form action="{{ route('dashboard.overview') }}" method="GET">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Từ ngày</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}"
                               class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Đến ngày</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}"
                               class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    </div>
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Bộ môn</label>
                        <select name="department_id" id="department_id" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <option value="">-- Chọn bộ môn --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->department_id }}" {{ request('department_id') == $department->department_id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="lecturer_id" class="block text-sm font-medium text-gray-700 mb-2">Giảng viên</label>
                        <select name="lecturer_id" id="lecturer_id" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <option value="">-- Chọn giảng viên --</option>
                            @foreach ($lecturers as $lecturer)
                                <option value="{{ $lecturer->user_id }}" {{ request('lecturer_id') == $lecturer->user_id ? 'selected' : '' }}>
                                    {{ $lecturer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md transform hover:scale-105">
                    Lọc Báo cáo
                </button>
            </form>
        </div>

        <!-- Dashboard -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl md:text-2xl font-semibold text-gray-700 mb-4">Tổng quan Hiệu suất</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-100 p-4 rounded-lg text-center">
                    <h4 class="text-lg font-semibold text-blue-800">Tổng số công việc</h4>
                    <p class="text-2xl font-bold text-blue-600">{{ $totalTasks ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg text-center">
                    <h4 class="text-lg font-semibold text-green-800">Hoàn thành đúng hạn</h4>
                    <p class="text-2xl font-bold text-green-600">{{ $completedOnTime ?? 0 }}</p>
                </div>
                <div class="bg-red-100 p-4 rounded-lg text-center">
                    <h4 class="text-lg font-semibold text-red-800">Quá hạn</h4>
                    <p class="text-2xl font-bold text-red-600">{{ $overdueTasks ?? 0 }}</p>
                </div>
            </div>

            <!-- Biểu đồ -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-2">Tỷ lệ hoàn thành</h4>
                <canvas id="completionChart" class="w-full h-64"></canvas>
            </div>

            <!-- Bảng theo bộ môn -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-2">Tiến độ theo Bộ môn</h4>
                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Bộ môn</th>
                            <th class="py-3 px-6 text-center">Tổng công việc</th>
                            <th class="py-3 px-6 text-center">Hoàn thành</th>
                            <th class="py-3 px-6 text-center">Tỷ lệ hoàn thành</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light divide-y divide-gray-200">
                        @if ($departmentStats->isEmpty())
                            <tr>
                                <td colspan="4" class="py-3 px-6 text-center">Không có dữ liệu</td>
                            </tr>
                        @else
                            @foreach ($departmentStats as $stat)
                                <tr class="hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $stat['name'] }}</td>
                                    <td class="py-3 px-6 text-center">{{ $stat['total'] }}</td>
                                    <td class="py-3 px-6 text-center">{{ $stat['completed'] }}</td>
                                    <td class="py-3 px-6 text-center">{{ $stat['completion_rate'] }}%</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Bảng theo giảng viên -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-2">Hiệu suất Giảng viên</h4>
                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Giảng viên</th>
                            <th class="py-3 px-6 text-center">Tổng công việc</th>
                            <th class="py-3 px-6 text-center">Hoàn thành</th>
                            <th class="py-3 px-6 text-center">Tỷ lệ hoàn thành</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light divide-y divide-gray-200">
                        @if ($lecturerStats->isEmpty())
                            <tr>
                                <td colspan="4" class="py-3 px-6 text-center">Không có dữ liệu</td>
                            </tr>
                        @else
                            @foreach ($lecturerStats as $stat)
                                <tr class="hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $stat['name'] }}</td>
                                    <td class="py-3 px-6 text-center">{{ $stat['total'] }}</td>
                                    <td class="py-3 px-6 text-center">{{ $stat['completed'] }}</td>
                                    <td class="py-3 px-6 text-center">{{ $stat['completion_rate'] }}%</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Nút xuất báo cáo -->
            <form action="{{ route('dashboard.overview.export') }}" method="POST" class="flex space-x-4">
                @csrf
                <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                <input type="hidden" name="department_id" value="{{ request('department_id') }}">
                <input type="hidden" name="lecturer_id" value="{{ request('lecturer_id') }}">
                <button type="submit" name="format" value="pdf" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md transform hover:scale-105">
                    Xuất PDF
                </button>
                <button type="submit" name="format" value="excel" class="bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 transition duration-300 shadow-md transform hover:scale-105">
                    Xuất Excel
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('completionChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Hoàn thành đúng hạn', 'Quá hạn', 'Chưa hoàn thành'],
                    datasets: [{
                        data: [
                            {{ $completedOnTime ?? 0 }},
                            {{ $overdueTasks ?? 0 }},
                            {{ ($totalTasks ?? 0) - ($completedOnTime ?? 0) - ($overdueTasks ?? 0) }}
                        ],
                        backgroundColor: ['#34D399', '#EF4444', '#9CA3AF']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'top' }
                    }
                }
            });
        });
    </script>
</body>
</html>