<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Trưởng Bộ môn - Hệ thống quản lý công việc</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <!-- Navigation bar -->
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

    <!-- Main content -->
    <div class="container mx-auto mt-8 px-4 pb-16">
        <!-- Page title and actions -->
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800">Dashboard Trưởng bộ môn</h2>
            <div class="flex space-x-4">
                <a href="{{ route('tasks.assign') }}" class="bg-blue-600 text-white py-2 px-4 md:py-3 md:px-6 rounded-lg hover:bg-blue-700 transition duration-200 shadow-md transform hover:scale-105 text-sm md:text-base">Phân công công việc</a>
                <a href="{{ route('tasks.extension_requests') }}" class="bg-yellow-600 text-white py-2 px-4 md:py-3 md:px-6 rounded-lg hover:bg-yellow-700 transition duration-200 shadow-md transform hover:scale-105 text-sm md:text-base">Xem các yêu cầu gia hạn</a>
                <a href="{{ route('tasks.search_form') }}" class="bg-green-600 text-white py-2 px-4 md:py-3 md:px-6 rounded-lg hover:bg-green-700 transition duration-200 shadow-md transform hover:scale-105 text-sm md:text-base">Tìm kiếm Công việc</a>
            </div>
        </div>

        <!-- Success message -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Thành công!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg onclick="this.parentElement.style.display='none';" class="fill-current h-6 w-6 text-green-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Đóng</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif

        <!-- Error message -->
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg onclick="this.parentElement.style.display='none';" class="fill-current h-6 w-6 text-red-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Đóng</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif

        <!-- Assigned tasks table -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6 overflow-x-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl md:text-2xl font-semibold text-gray-700">Danh sách công việc đã phân công</h3>
                <button onclick="window.location.reload();" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-200 text-sm">Làm mới</button>
            </div>
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
                    @forelse ($tasks as $task)
                        <tr class="hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap font-medium">{{ $task->title }}</td>
                            <td class="py-3 px-6 text-left">
                                @if($task->assignedUsers->isNotEmpty())
                                    {{ $task->assignedUsers->pluck('name')->implode(', ') }}
                                @else
                                    <span class="text-gray-400 italic">Chưa giao</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                {{ $task->due_date->format('d/m/Y') }}
                                @php
                                    $daysLeft = now()->diffInDays($task->due_date, false);
                                @endphp
                                @if($daysLeft > 7 && !in_array($task->status, ['Completed', 'Evaluated']))
                                    <!-- Trống nếu còn > 7 ngày -->
                                @elseif($daysLeft > 0 && $daysLeft <= 7 && !in_array($task->status, ['Completed', 'Evaluated']))
                                    <span class="ml-1 bg-yellow-400 text-gray-900 py-1 px-2 rounded-full text-xs">Sắp hạn</span>
                                @elseif($daysLeft <= 0 && !in_array($task->status, ['Completed', 'Evaluated']))
                                    <span class="ml-1 bg-red-500 text-white py-1 px-2 rounded-full text-xs">Quá hạn</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span class="py-1 px-3 rounded-full text-xs font-semibold
                                    @switch($task->status)
                                        @case('Not Started') bg-gray-200 text-gray-600 @break
                                        @case('In Progress') bg-blue-200 text-blue-600 @break
                                        @case('Completed') bg-green-200 text-green-600 @break
                                        @case('Overdue') bg-red-200 text-red-600 @break
                                        @case('Pending Extension') bg-yellow-200 text-yellow-600 @break
                                        @case('Evaluated') bg-purple-200 text-purple-600 @break
                                        @default bg-gray-200 text-gray-600
                                    @endswitch">
                                    {{ $task->status }}
                                </span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center space-x-2">
                                    <a href="{{ route('tasks.show', $task->id) }}" class="text-blue-600 hover:text-blue-900" title="Xem chi tiết">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                    @if($task->status === 'Completed' && !$task->evaluation_level)
                                        <a href="{{ route('tasks.evaluate_form', $task->id) }}" class="text-green-600 hover:text-green-900" title="Đánh giá">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400" title="Không thể đánh giá">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </span>
                                    @endif
                                    @if(!in_array($task->status, ['Completed', 'Evaluated']))
                                        <a href="{{ route('tasks.edit', $task->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Sửa công việc">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400" title="Không thể sửa công việc đã hoàn thành">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </span>
                                    @endif
                                    <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này?');" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Xóa công việc">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 px-6 text-center text-gray-500 italic">Chưa có công việc nào được phân công trong bộ môn.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $tasks->links() }}
            </div>
        </div>

        <!-- Task statistics -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-2xl font-semibold text-gray-700 mb-6">Thống kê công việc</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-100 p-6 rounded-lg shadow-md flex items-center space-x-4">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <div>
                        <p class="text-gray-700 font-semibold">Tổng số công việc</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $totalTasks }}</p>
                    </div>
                </div>
                <div class="bg-green-100 p-6 rounded-lg shadow-md flex items-center space-x-4">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <div>
                        <p class="text-gray-700 font-semibold">Công việc hoàn thành</p>
                        <p class="text-3xl font-bold text-green-600">{{ $completedTasks }}</p>
                    </div>
                </div>
                <div class="bg-yellow-100 p-6 rounded-lg shadow-md flex items-center space-x-4">
                    <svg class="h-8 w-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-gray-700 font-semibold">Công việc đang thực hiện</p>
                        <p class="text-3xl font-bold text-yellow-600">{{ $inProgressTasks }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>