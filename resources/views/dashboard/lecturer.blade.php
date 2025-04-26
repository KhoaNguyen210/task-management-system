<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Giảng viên - Hệ thống quản lý công việc</title>
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
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">Dashboard Giảng viên</h2>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Thành công!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                 <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg onclick="this.parentElement.style.display='none';" class="fill-current h-6 w-6 text-green-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Đóng</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif
         @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                 <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg onclick="this.parentElement.style.display='none';" class="fill-current h-6 w-6 text-red-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Đóng</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-6 mb-6 overflow-x-auto">
            <h3 class="text-xl font-semibold text-gray-700 mb-4">Danh sách công việc được giao</h3>
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Tên công việc</th>
                        <th class="py-3 px-6 text-left">Mô tả</th>
                        <th class="py-3 px-6 text-center">Thời hạn</th>
                        <th class="py-3 px-6 text-center">Trạng thái</th>
                        <th class="py-3 px-6 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm font-light divide-y divide-gray-200">
                    @forelse ($tasks as $task)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 text-left whitespace-nowrap font-medium">{{ $task->title }}</td>
                            <td class="py-3 px-6 text-left max-w-xs truncate" title="{{ $task->description }}">
                                {{ Illuminate\Support\Str::limit($task->description, 80) }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                {{ $task->due_date->format('d/m/Y') }}
                                @if($task->due_date->isPast() && !in_array($task->status, ['Completed', 'Evaluated']))
                                    <span class="ml-1 bg-red-200 text-red-600 py-1 px-2 rounded-full text-xs">Quá hạn</span>
                                @elseif($task->due_date->isFuture() && $task->due_date->diffInDays(now()) <= 3 && !in_array($task->status, ['Completed', 'Evaluated']))
                                     <span class="ml-1 bg-yellow-200 text-yellow-600 py-1 px-2 rounded-full text-xs">Sắp hạn</span>
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
                                    <a href="#" {{-- href="{{ route('tasks.show', $task->id) }}" --}} class="text-blue-600 hover:text-blue-900" title="Xem chi tiết">
                                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                     <a href="#" {{-- href="{{ route('tasks.progress.edit', $task->id) }}" --}} class="text-yellow-600 hover:text-yellow-900" title="Cập nhật tiến độ">
                                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </a>
                                     <a href="#" {{-- href="{{ route('tasks.extension.request', $task->id) }}" --}} class="text-orange-600 hover:text-orange-900" title="Đề nghị gia hạn">
                                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 px-6 text-center text-gray-500 italic">Bạn chưa được giao công việc nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $tasks->links() }}
            </div>
        </div>
    </div>

</body>
</html>
