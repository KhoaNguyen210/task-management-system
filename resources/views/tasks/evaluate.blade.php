<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh giá công việc: {{ $task->title }}</title>
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
            <h2 class="text-3xl font-bold text-gray-800">Đánh giá công việc: {{ $task->title }}</h2>
            <a href="{{ route('dashboard.department_head') }}" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200">
                ← Quay lại Dashboard
            </a>
        </div>

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Lỗi!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                 <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg onclick="this.parentElement.style.display='none';" class="fill-current h-6 w-6 text-red-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Đóng</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2">Thông tin công việc</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-600 mb-4"><strong class="font-medium text-gray-800">Mô tả:</strong></p>
                    <div class="bg-gray-50 p-4 rounded border border-gray-200 mb-4">
                         <p class="text-gray-700 whitespace-pre-wrap">{{ $task->description }}</p>
                    </div>

                    <p class="mb-2"><strong class="font-medium text-gray-800">Người giao việc:</strong> {{ $task->creatorUser->name ?? 'N/A' }}</p>
                    <p class="mb-2"><strong class="font-medium text-gray-800">Người thực hiện:</strong>
                        @forelse($task->assignedUsers as $assignee)
                            {{ $assignee->name }}{{ !$loop->last ? ', ' : '' }}
                        @empty
                            Chưa giao
                        @endforelse
                    </p>
                    <p class="mb-2"><strong class="font-medium text-gray-800">Bộ môn:</strong> {{ $task->department->name ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="mb-2"><strong class="font-medium text-gray-800">Ngày giao:</strong> {{ $task->created_at->format('d/m/Y H:i') }}</p>
                    <p class="mb-2">
                        <strong class="font-medium text-gray-800">Thời hạn:</strong>
                        <span class="{{ $task->due_date->isPast() && $task->status !== 'Completed' ? 'text-red-600 font-semibold' : '' }}">
                            {{ $task->due_date->format('d/m/Y') }}
                        </span>
                        @if($task->due_date->isPast() && !in_array($task->status, ['Completed', 'Evaluated']))
                            <span class="ml-1 bg-red-100 text-red-600 py-0.5 px-2 rounded-full text-xs">Quá hạn</span>
                        @elseif($task->due_date->isFuture() && $task->due_date->diffInDays(now()) <= 3 && !in_array($task->status, ['Completed', 'Evaluated']))
                             <span class="ml-1 bg-yellow-100 text-yellow-600 py-0.5 px-2 rounded-full text-xs">Sắp hạn</span>
                        @endif
                    </p>
                    <p class="mb-4"><strong class="font-medium text-gray-800">Trạng thái hiện tại:</strong>
                         <span class="py-1 px-3 rounded-full text-sm font-semibold
                            @switch($task->status)
                                @case('Not Started') bg-gray-200 text-gray-700 @break
                                @case('In Progress') bg-blue-200 text-blue-600 @break
                                @case('Completed') bg-green-200 text-green-600 @break
                                @case('Overdue') bg-red-200 text-red-600 @break
                                @case('Pending Extension') bg-yellow-200 text-yellow-600 @break
                                @case('Evaluated') bg-purple-200 text-purple-600 @break
                                @default bg-gray-200 text-gray-600
                            @endswitch">
                            {{ $task->status }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h4 class="text-xl font-semibold text-gray-700 mb-3">Lịch sử Cập nhật Tiến độ</h4>
            @if($task->progressUpdates->isNotEmpty())
                <ul class="space-y-3">
                    @foreach($task->progressUpdates->sortByDesc('created_at') as $progress)
                        <li class="border-b pb-3 last:border-b-0">
                            <p><strong class="font-medium">{{ $progress->user->name ?? 'N/A' }}</strong> đã cập nhật trạng thái thành <strong class="text-blue-600">{{ $progress->status }}</strong> vào lúc {{ $progress->created_at->format('d/m/Y H:i') }}</p>
                            @if($progress->comment)
                                <p class="text-gray-600 mt-1 pl-4 italic">"{{ $progress->comment }}"</p>
                            @endif
                             @if($progress->attachment)
                                <p class="text-gray-600 mt-1 pl-4">
                                    <a href="{{ Storage::url($progress->attachment) }}" target="_blank" class="text-blue-500 hover:underline">Xem file đính kèm</a>
                                </p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 italic">Chưa có cập nhật tiến độ nào.</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-2xl font-semibold text-gray-700 mb-4">Đánh giá công việc</h3>

            <form action="{{ route('tasks.evaluate', $task->id) }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="evaluation_level" class="block text-gray-700 font-medium mb-2">Mức độ hoàn thành:</label>
                    <select name="evaluation_level" id="evaluation_level" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" {{ old('evaluation_level') ? '' : 'selected' }} disabled>Chọn mức độ hoàn thành</option>
                        <option value="Không hoàn thành" {{ old('evaluation_level') === 'Không hoàn thành' ? 'selected' : '' }}>Không hoàn thành</option>
                        <option value="Hoàn thành yếu" {{ old('evaluation_level') === 'Hoàn thành yếu' ? 'selected' : '' }}>Hoàn thành yếu</option>
                        <option value="Hoàn thành" {{ old('evaluation_level') === 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
                        <option value="Hoàn thành tích cực" {{ old('evaluation_level') === 'Hoàn thành tích cực' ? 'selected' : '' }}>Hoàn thành tích cực</option>
                        <option value="Hoàn thành tốt" {{ old('evaluation_level') === 'Hoàn thành tốt' ? 'selected' : '' }}>Hoàn thành tốt</option>
                        <option value="Hoàn thành xuất sắc" {{ old('evaluation_level') === 'Hoàn thành xuất sắc' ? 'selected' : '' }}>Hoàn thành xuất sắc</option>
                    </select>
                    @error('evaluation_level')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="evaluation_comment" class="block text-gray-700 font-medium mb-2">Nhận xét (tùy chọn):</label>
                    <textarea name="evaluation_comment" id="evaluation_comment" rows="4" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('evaluation_comment') }}</textarea>
                    @error('evaluation_comment')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex space-x-2">
                    <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition duration-200">Lưu đánh giá</button>
                    <a href="{{ route('dashboard.department_head') }}" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>