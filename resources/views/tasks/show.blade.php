<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết công việc: {{ $task->title }}</title>
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
            <h2 class="text-3xl font-bold text-gray-800">Chi tiết công việc</h2>
            @php
                $backRoute = match(Auth::user()->role) {
                    'Department Head' => route('dashboard.department_head'),
                    'Lecturer' => route('dashboard.lecturer'),
                    default => route('home')
                };
            @endphp
            <a href="{{ $backRoute }}" class="bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition duration-200">
                ← Quay lại Dashboard
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-2xl font-semibold text-gray-700 mb-4 border-b pb-2">{{ $task->title }}</h3>

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

                    @if($task->evaluation_level)
                        <p class="mb-4"><strong class="font-medium text-gray-800">Mức độ hoàn thành:</strong>
                            <span class="py-1 px-3 rounded-full text-sm font-semibold
                                @switch($task->evaluation_level)
                                    @case('Không hoàn thành') bg-red-200 text-red-600 @break
                                    @case('Hoàn thành yếu') bg-orange-200 text-orange-600 @break
                                    @case('Hoàn thành') bg-yellow-200 text-yellow-600 @break
                                    @case('Hoàn thành tích cực') bg-green-200 text-green-600 @break
                                    @case('Hoàn thành tốt') bg-blue-200 text-blue-600 @break
                                    @case('Hoàn thành xuất sắc') bg-purple-200 text-purple-600 @break
                                    @default bg-gray-200 text-gray-600
                                @endswitch">
                                {{ $task->evaluation_level }}
                            </span>
                        </p>
                        @if($task->evaluation_comment)
                            <p class="mb-4"><strong class="font-medium text-gray-800">Nhận xét:</strong> {{ $task->evaluation_comment }}</p>
                        @endif
                        <p class="mb-4"><strong class="font-medium text-gray-800">Người đánh giá:</strong> {{ $task->evaluator->name ?? 'N/A' }} (vào {{ $task->updated_at->format('d/m/Y H:i') }})</p>
                    @endif

                    @if(Auth::user()->role === 'Lecturer' && $task->assignedUsers->contains(Auth::user()))
                        <div class="flex space-x-2 mt-4">
                             <a href="{{ route('tasks.update_progress_form', $task->id) }}" class="bg-yellow-500 text-white py-2 px-4 rounded-lg hover:bg-yellow-600 transition duration-200 text-sm">Cập nhật tiến độ</a>
                             <a href="{{ route('tasks.request_extension_form', $task->id) }}" class="bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition duration-200 text-sm">Đề nghị gia hạn</a>
                        </div>
                    @endif
                     @if(Auth::user()->role === 'Department Head' && Auth::user()->department_id === $task->department_id)
                         <div class="flex space-x-2 mt-4">
                            @if($task->status === 'Completed' && !$task->evaluation_level)
                                <a href="{{ route('tasks.evaluate_form', $task->id) }}" class="bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition duration-200 text-sm">Đánh giá</a>
                            @endif
                            <a href="#" class="bg-indigo-500 text-white py-2 px-4 rounded-lg hover:bg-indigo-600 transition duration-200 text-sm">Sửa công việc</a>
                            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa công việc này?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition duration-200 text-sm">Xóa công việc</button>
                            </form>
                         </div>
                    @endif

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

        <div class="bg-white rounded-lg shadow-lg p-6">
             <h4 class="text-xl font-semibold text-gray-700 mb-3">Lịch sử Đề nghị Gia hạn</h4>
             @if($task->extensionRequests->isNotEmpty())
                 <ul class="space-y-3">
                     @foreach($task->extensionRequests->sortByDesc('created_at') as $extension)
                         <li class="border-b pb-3 last:border-b-0">
                             <p><strong class="font-medium">{{ $extension->user->name ?? 'N/A' }}</strong> đã yêu cầu gia hạn đến <strong class="text-red-600">{{ $extension->new_due_date->format('d/m/Y') }}</strong> vào lúc {{ $extension->created_at->format('d/m/Y H:i') }}</p>
                             <p class="text-gray-600 mt-1 pl-4">Lý do: <span class="italic">"{{ $extension->reason }}"</span></p>
                             <p class="mt-1 pl-4">Trạng thái:
                                <span class="font-semibold
                                     @if($extension->status == 'Pending') text-yellow-600
                                     @elseif($extension->status == 'Approved') text-green-600
                                     @elseif($extension->status == 'Rejected') text-red-600
                                     @endif">
                                     {{ $extension->status }}
                                 </span>
                                 @if($extension->approved_by)
                                      (bởi {{ $extension->approver->name ?? 'N/A' }} vào {{ $extension->updated_at->format('d/m/Y H:i') }})
                                 @endif
                             </p>
                             @if($extension->comment)
                                 <p class="text-gray-600 mt-1 pl-4">Nhận xét: <span class="italic">"{{ $extension->comment }}"</span></p>
                             @endif
                         </li>
                     @endforeach
                 </ul>
             @else
                 <p class="text-gray-500 italic">Chưa có yêu cầu gia hạn nào.</p>
             @endif
         </div>

    </div>
</body>
</html>