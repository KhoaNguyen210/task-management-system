<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo Tổng quan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #1E40AF; }
        h2 { color: #1E40AF; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #E5E7EB; }
        .summary { margin-bottom: 20px; }
        .summary div { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Báo cáo Tổng quan Hiệu suất</h1>
    <div class="summary">
        <div><strong>Tổng số công việc:</strong> {{ $totalTasks }}</div>
        <div><strong>Hoàn thành đúng hạn:</strong> {{ $completedOnTime }}</div>
        <div><strong>Quá hạn:</strong> {{ $overdueTasks }}</div>
        <div><strong>Tỷ lệ hoàn thành:</strong> {{ $completionRate }}%</div>
    </div>

    <h2>Chi tiết Công việc</h2>
    <table>
        <thead>
            <tr>
                <th>Tiêu đề</th>
                <th>Bộ môn</th>
                <th>Người được giao</th>
                <th>Thời hạn</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tasks as $task)
                <tr>
                    <td>{{ $task->title }}</td>
                    <td>{{ $task->department->name }}</td>
                    <td>{{ $task->assignedUsers->pluck('name')->implode(', ') }}</td>
                    <td>{{ $task->due_date->format('d/m/Y') }}</td>
                    <td>{{ $task->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>