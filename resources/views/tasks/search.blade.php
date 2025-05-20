<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm Công việc - Hệ thống quản lý công việc</title>
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
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Tìm kiếm Công việc</h2>
            <a href="{{ Auth::user()->role === 'Dean' ? route('dashboard.dean') : route('dashboard.department_head') }}"
               class="bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-200 shadow-md transform hover:scale-105 text-sm md:text-base">
                ← Quay lại Dashboard
            </a>
        </div>

        <!-- Thông báo -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Thành công!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form tìm kiếm -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-xl md:text-2xl font-semibold text-gray-700 mb-4">Tìm kiếm Công việc</h3>
            <form action="{{ route('tasks.search') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="due_date_from" class="block text-sm font-medium text-gray-700 mb-2">Từ ngày</label>
                        <input type="date" name="due_date_from" id="due_date_from" value="{{ old('due_date_from') }}"
                               class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    </div>
                    <div>
                        <label for="due_date_to" class="block text-sm font-medium text-gray-700 mb-2">Đến ngày</label>
                        <input type="date" name="due_date_to" id="due_date_to" value="{{ old('due_date_to') }}"
                               class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    </div>
                    <div>
                        <label for="assignee" class="block text-sm font-medium text-gray-700 mb-2">Giảng viên</label>
                        <select name="assignee" id="assignee" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                            <option value="">-- Chọn giảng viên --</option>
                            @foreach ($lecturers as $lecturer)
                                <option value="{{ $lecturer->user_id }}" {{ old('assignee') == $lecturer->user_id ? 'selected' : '' }}>
                                    {{ $lecturer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="keyword" class="block text-sm font-medium text-gray-700 mb-2">Từ khóa tiêu đề</label>
                        <input type="text" name="keyword" id="keyword" value="{{ old('keyword') }}"
                               class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                               placeholder="Nhập từ khóa">
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md transform hover:scale-105">
                    Tìm kiếm
                </button>
            </form>
        </div>

        <!-- Kết quả tìm kiếm -->
        <div class="bg-white rounded-lg shadow-lg p-6 overflow-x-auto">
            <h3 class="text-xl md:text-2xl font-semibold text-gray-700 mb-4">Kết quả Tìm kiếm</h3>
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Tên công việc</th>
                        <th class="py-3 px-6 text-left">Người thực hiện</th>
                        <th class="py-3 px-6 text-center">Thời hạn</th>
                        <th class="py-3 px-6 text-center">Trạng thái</th>
                        <th class="py-3 px-6 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light divide-y divide-gray-200">
                    @if (isset($tasks) && $tasks->isNotEmpty())
                        @foreach ($tasks as $task)
                            <tr class="hover:bg-gray-100">
                                <td class="py-3 px-6 text-left whitespace-nowrap font-medium">{{ $task->title }}</td>
                                <td class="py-3 px-6 text-left">
                                    @if ($task->assignedUsers->isNotEmpty())
                                        {{ $task->assignedUsers->pluck('name')->implode(', ') }}
                                    @else
                                        <span class="text-gray-400 italic">Chưa giao</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-center">
                                    {{ $task->due_date->format('d/m/Y') }}
                                    @if ($task->due_date->isPast() && !in_array($task->status, ['Completed', 'Evaluated']))
                                        <span class="ml-1 bg-red-200 text-red-600 py-1 px-2 rounded-full text-xs">Quá hạn</span>
                                    @elseif ($task->due_date->isFuture() && $task->due_date->diffInDays(now()) <= 3 && !in_array($task->status, ['Completed', 'Evaluated']))
                                        <span class="ml-1 bg-yellow-200 text-yellow-600 py-1 px-2 rounded-full text-xs">Sắp hạn</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <span class="py-1 px-3 rounded-full text-xs font-semibold
                                        @switch($task->status)
                                            @case('Not Started') bg-gray-200 text-gray-600 @break
                                            @case('In Progress') bg-blue-200 text-blue-600 @break
                                            @case('Completed') bg-green-200 text-green-600 @break
                                            @case('Evaluated') bg-purple-200 text-purple-600 @break
                                            @default bg-gray-200 text-gray-600
                                        @endswitch">
                                        {{ $task->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <a href="{{ route('tasks.show', $task->id) }}" class="text-blue-600 hover:text-blue-900" title="Xem chi tiết">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="py-4 px-6 text-center text-gray-500 italic">
                                Chưa có công việc nào để hiển thị.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            @if (isset($tasks) && $tasks instanceof \Illuminate\Pagination\LengthAwarePaginator && $tasks->hasPages())
                <div class="mt-4">
                    {{ $tasks->links() }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>