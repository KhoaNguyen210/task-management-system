<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách Yêu cầu Gia hạn - Hệ thống Quản lý Công việc</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <!-- Thanh điều hướng -->
    <nav class="bg-blue-800 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Hệ thống" class="h-10 mr-4">
                <span class="text-xl font-bold">Hệ thống Quản lý Công việc</span>
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
            <h2 class="text-3xl font-bold text-gray-800">Danh sách Yêu cầu Gia hạn</h2>
            <a href="{{ route('dashboard.department_head') }}" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200">Quay lại Dashboard</a>
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

        <!-- Bảng danh sách yêu cầu gia hạn -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6 overflow-x-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-700">Danh sách Yêu cầu Gia hạn</h3>
                <button onclick="window.location.reload();" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-300 transition duration-200">Làm mới</button>
            </div>
            <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Tên Công việc</th>
                        <th class="py-3 px-6 text-left">Giảng viên</th>
                        <th class="py-3 px-6 text-center">Thời hạn Hiện tại</th>
                        <th class="py-3 px-6 text-center">Thời hạn Mới Đề xuất</th>
                        <th class="py-3 px-6 text-center">Ngày Yêu cầu</th>
                        <th class="py-3 px-6 text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light divide-y divide-gray-200">
                    @forelse ($extensionRequests as $request)
                        <tr class="hover:bg-gray-100">
                            <td class="py-3 px-6 text-left whitespace-nowrap font-medium">{{ $request->task->title }}</td>
                            <td class="py-3 px-6 text-left">{{ $request->user->name }}</td>
                            <td class="py-3 px-6 text-center">{{ $request->task->due_date->format('d/m/Y') }}</td>
                            <td class="py-3 px-6 text-center">{{ $request->new_due_date->format('d/m/Y') }}</td>
                            <td class="py-3 px-6 text-center">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex item-center justify-center space-x-2">
                                    <a href="{{ route('tasks.extension_request.show', $request->id) }}" class="text-blue-600 hover:text-blue-900" title="Xem và Xử lý">
                                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-4 px-6 text-center text-gray-500 italic">Hiện không có yêu cầu gia hạn nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-4">
                {{ $extensionRequests->links() }}
            </div>
        </div>
    </div>
</body>
</html>