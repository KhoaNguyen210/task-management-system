<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem xét Yêu cầu Gia hạn: {{ $extensionRequest->task->title }}</title>
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
            <h2 class="text-3xl font-bold text-gray-800">Xem xét Yêu cầu Gia hạn: {{ $extensionRequest->task->title }}</h2>
            <a href="{{ route('tasks.extension_requests') }}" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200">Quay lại Danh sách</a>
        </div>

        <!-- Thông báo -->
        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-4 mb-4 rounded-lg shadow-sm animate-pulse">
                {{ session('error') }}
            </div>
        @endif

        <!-- Chi tiết yêu cầu gia hạn -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2">Chi tiết Yêu cầu Gia hạn</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="mb-2"><strong class="font-medium text-gray-800">Tên Công việc:</strong> {{ $extensionRequest->task->title }}</p>
                    <p class="mb-2"><strong class="font-medium text-gray-800">Giảng viên:</strong> {{ $extensionRequest->user->name }}</p>
                    <p class="mb-2"><strong class="font-medium text-gray-800">Bộ môn:</strong> {{ $extensionRequest->task->department->name ?? 'N/A' }}</p>
                    <p class="mb-2"><strong class="font-medium text-gray-800">Thời hạn Hiện tại:</strong> {{ $extensionRequest->task->due_date->format('d/m/Y') }}</p>
                    <p class="mb-2"><strong class="font-medium text-gray-800">Thời hạn Mới Đề xuất:</strong> {{ $extensionRequest->new_due_date->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-600 mb-4"><strong class="font-medium text-gray-800">Lý do Gia hạn:</strong></p>
                    <div class="bg-gray-50 p-4 rounded border border-gray-200 mb-4">
                         <p class="text-gray-700 whitespace-pre-wrap">{{ $extensionRequest->reason }}</p>
                    </div>
                    <p class="mb-2"><strong class="font-medium text-gray-800">Ngày Yêu cầu:</strong> {{ $extensionRequest->created_at->format('d/m/Y H:i') }}</p>
                    <p class="mb-2"><strong class="font-medium text-gray-800">Trạng thái:</strong>
                        <span class="py-1 px-3 rounded-full text-sm font-semibold bg-yellow-200 text-yellow-600">{{ $extensionRequest->status }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Form phê duyệt -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-2xl font-semibold text-gray-700 mb-4">Phê duyệt Yêu cầu Gia hạn</h3>
            <form action="{{ route('tasks.extension_request.approve', $extensionRequest->id) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quyết định:</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="decision" value="approved" class="form-radio text-green-500" required>
                            <span class="ml-2 text-gray-700">Phê duyệt</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="decision" value="rejected" class="form-radio text-red-500" required>
                            <span class="ml-2 text-gray-700">Từ chối</span>
                        </label>
                    </div>
                    @if ($errors->has('decision'))
                        <p class="text-red-500 text-sm mt-1">{{ $errors->first('decision') }}</p>
                    @endif
                </div>

                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Nhận xét (Tùy chọn):</label>
                    <textarea name="comment" id="comment" rows="4" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200" placeholder="Nhập nhận xét của bạn">{{ old('comment') }}</textarea>
                    @if ($errors->has('comment'))
                        <p class="text-red-500 text-sm mt-1">{{ $errors->first('comment') }}</p>
                    @endif
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300 shadow-md transform hover:scale-105">Xử lý Yêu cầu</button>
                    <a href="{{ route('tasks.extension_requests') }}" class="bg-gray-500 text-white py-3 px-4 rounded-lg hover:bg-gray-600 transition duration-200">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>