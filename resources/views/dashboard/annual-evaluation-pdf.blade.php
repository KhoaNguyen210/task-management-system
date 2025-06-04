<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo Đánh giá Cuối Năm</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { text-align: center; color: #2c3e50; }
        h3 { color: #34495e; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { margin-bottom: 20px; }
        .filters { margin-bottom: 20px; font-style: italic; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Báo cáo Đánh giá Cuối Năm</h1>
        @if ($academic_year)
            <p style="text-align: center;">Năm học: {{ $academic_year }}-{{ $academic_year + 1 }}</p>
        @endif
    </div>

    <div class="filters">
        <p><strong>Tiêu chí lọc:</strong></p>
        <ul>
            @if ($academic_year)
                <li>Năm học: {{ $academic_year }}-{{ $academic_year + 1 }}</li>
            @endif
            @if ($department_id)
                <li>Bộ môn: {{ \App\Models\Department::find($department_id)->name ?? 'Không xác định' }}</li>
            @endif
            @if ($lecturer_id)
                <li>Giảng viên: {{ \App\Models\User::find($lecturer_id)->name ?? 'Không xác định' }}</li>
            @endif
        </ul>
    </div>

    <h3>Thống kê Hiệu suất Giảng viên</h3>
    <table>
        <thead>
            <tr>
                <th>Giảng viên</th>
                <th>Bộ môn</th>
                <th>Tổng công việc</th>
                <th>Hoàn thành đúng hạn</th>
                <th>Quá hạn</th>
                <th>Chưa hoàn thành</th>
                <th>Tỷ lệ hoàn thành (%)</th>
                <th>Đánh giá trung bình</th>
            </tr>
        </thead>
        <tbody>
            @if ($lecturerStats->isEmpty())
                <tr>
                    <td colspan="8">Không có dữ liệu</td>
                </tr>
            @else
                @foreach ($lecturerStats as $stat)
                    <tr>
                        <td>{{ $stat['name'] }}</td>
                        <td>{{ $stat['department'] }}</td>
                        <td>{{ $stat['total_tasks'] }}</td>
                        <td>{{ $stat['completed_on_time'] }}</td>
                        <td>{{ $stat['overdue_tasks'] }}</td>
                        <td>{{ $stat['not_completed'] }}</td>
                        <td>{{ $stat['completion_rate'] }}</td>
                        <td>{{ $stat['average_evaluation'] ?? 'Chưa đánh giá' }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>