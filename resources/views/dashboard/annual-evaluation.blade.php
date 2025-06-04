<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Báo cáo Đánh giá Cuối Năm - Hệ thống quản lý công việc</title>
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
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Báo cáo Đánh giá Cuối Năm</h2>
            <a href="{{ auth()->user()->role === 'Dean' ? route('dashboard.dean') : route('dashboard.department_head') }}"
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
            <form action="{{ route('dashboard.annual-evaluation') }}" method="GET">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">Năm học</label>
                        <input type="number" name="academic_year" id="academic_year" value="{{ $academic_year ?? '' }}"
                               placeholder="VD: 2025" min="2000" max="2100"
                               class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    </div>
                    @if (auth()->user()->role === 'Dean')
                        <div>
                            <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Bộ môn</label>
                            <select name="department_id" id="department_id" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                <option value="">-- Chọn bộ môn --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->department_id }}" {{ $department_id == $department->department_id ? 'selected' : '' }}>
                                        {{ $department->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div>
                        <label for="lecturer_id" class="block text-sm font-medium text-gray-700 mb-2">Giảng viên</label>
                        <select name="lecturer_id" id="lecturer_id" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <option value="">-- Chọn giảng viên --</option>
                            @foreach ($lecturers as $lecturer)
                                <option value="{{ $lecturer->user_id }}" {{ $lecturer_id == $lecturer->user_id ? 'selected' : '' }}>
                                    {{ $lecturer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp theo</label>
                        <select name="sort_by" id="sort_by" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <option value="name" {{ $sort_by == 'name' ? 'selected' : '' }}>Tên giảng viên</option>
                            <option value="total_tasks" {{ $sort_by == 'total_tasks' ? 'selected' : '' }}>Tổng công việc</option>
                            <option value="completion_rate" {{ $sort_by == 'completion_rate' ? 'selected' : '' }}>Tỷ lệ hoàn thành</option>
                            <option value="average_evaluation" {{ $sort_by == 'average_evaluation' ? 'selected' : '' }}>Đánh giá trung bình</option>
                        </select>
                    </div>
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">Thứ tự</label>
                        <select name="sort_order" id="sort_order" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <option value="asc" {{ $sort_order == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                            <option value="desc" {{ $sort_order == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md transform hover:scale-105">
                    Lọc Báo cáo
                </button>
            </form>
        </div>

        <!-- Báo cáo -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl md:text-2xl font-semibold text-gray-700 mb-4">Thống kê Hiệu suất Giảng viên</h3>

            <!-- Biểu đồ -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-2">Tỷ lệ Hoàn thành</h4>
                <canvas id="completionChart" class="w-full h-64"></canvas>
            </div>

            <!-- Bảng thống kê -->
            <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-700 mb-2">Chi tiết Hiệu suất</h4>
                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Giảng viên</th>
                            <th class="py-3 px-6 text-left">Bộ môn</th>
                            <th class="py-3 px-6 text-center">Tổng công việc</th>
                            <th class="py-3 px-6 text-center">Hoàn thành đúng hạn</th>
                            <th class="py-3 px-6 text-center">Quá hạn</th>
                            <th class="py-3 px-6 text-center">Chưa hoàn thành</th>
                            <th class="py-3 px-6 text-center">Tỷ lệ hoàn thành (%)</th>
                            <th class="py-3 px-6 text-center">Đánh giá trung bình</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm font-light divide-y divide-gray-200">
                        @if ($lecturerStats->isEmpty())
                            <tr>
                                <td colspan="8" class="py-3 px-6 text-center">Không có dữ liệu</td>
                            </tr>
                        @else
                            @foreach ($lecturerStats as $stat)
                                <tr class="hover:bg-gray-100">
                                    <td class="py-3 px-6 text-left">{{ $stat['name'] }}</td>
                                    <td class="py-3 px-6 text-left">{{ $stat['department'] }}</td>
                                    <td class="py-3 px-6 text-center">{{ $stat['total_tasks'] }}</td>
                                    <td class="py-3 px-6 text-center">{{ $stat['completed_on_time'] }}</td>
                                    <td class="py-3 px-6 text-center">{{ $stat['overdue_tasks'] }}</td>
                                    <td class="py-3 px-6 text-center">{{ $stat['not_completed'] }}</td>
                                    <td class="py-3 px-6 text-center">{{ $stat['completion_rate'] }}</td>
                                    <td class="py-3 px-6 text-center">{{ $stat['average_evaluation'] ?? 'Chưa đánh giá' }}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Nút xuất báo cáo -->
            <form action="{{ route('dashboard.annual-evaluation.export') }}" method="POST">
                @csrf
                <input type="hidden" name="academic_year" value="{{ $academic_year ?? '' }}">
                <input type="hidden" name="department_id" value="{{ $department_id ?? '' }}">
                <input type="hidden" name="lecturer_id" value="{{ $lecturer_id ?? '' }}">
                <input type="hidden" name="format" value="pdf">
                <button type="submit" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md transform hover:scale-105">
                    Xuất PDF
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('completionChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [@foreach ($lecturerStats as $stat)'{{ $stat['name'] }}', @endforeach],
                    datasets: [{
                        label: 'Tỷ lệ hoàn thành (%)',
                        data: [@foreach ($lecturerStats as $stat){{ $stat['completion_rate'] }}, @endforeach],
                        backgroundColor: '#34D399',
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Tỷ lệ hoàn thành (%)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Giảng viên'
                            }
                        }
                    },
                    plugins: {
                        legend: { display: true, position: 'top' }
                    }
                }
            });
        });
    </script>
</body>
</html>