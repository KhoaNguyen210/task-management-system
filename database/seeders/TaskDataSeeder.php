<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tasks')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('tasks')->insert([
            [
                'title' => 'Soạn giáo trình Java',
                'description' => 'Chuẩn bị tài liệu Java cơ bản.',
                'due_date' => '2025-05-10',
                'status' => 'Completed',
                'created_by' => 2,
                'department_id' => 1,
                'created_at' => '2025-05-01 08:00:00',
                'updated_at' => '2025-05-08 10:00:00',
            ],
            [
                'title' => 'Thiết kế bài kiểm tra Python',
                'description' => 'Xây dựng bài kiểm tra Python.',
                'due_date' => '2025-05-15',
                'status' => 'Completed',
                'created_by' => 2,
                'department_id' => 1,
                'created_at' => '2025-05-01 09:00:00',
                'updated_at' => '2025-05-14 12:00:00', // Sửa từ 2025-05-20
            ],
            [
                'title' => 'Tổ chức hội thảo AI',
                'description' => 'Chuẩn bị hội thảo về trí tuệ nhân tạo.',
                'due_date' => '2025-05-20',
                'status' => 'In Progress',
                'created_by' => 2,
                'department_id' => 1,
                'created_at' => '2025-05-01 10:00:00',
                'updated_at' => '2025-05-18 14:00:00',
            ],
            [
                'title' => 'Cập nhật hệ thống mạng',
                'description' => 'Nâng cấp hệ thống mạng nội bộ.',
                'due_date' => '2025-05-12',
                'status' => 'Completed',
                'created_by' => 3,
                'department_id' => 2,
                'created_at' => '2025-05-01 11:00:00',
                'updated_at' => '2025-05-10 15:00:00',
            ],
            [
                'title' => 'Tổ chức workshop CCNA',
                'description' => 'Chuẩn bị nội dung workshop CCNA.',
                'due_date' => '2025-05-18',
                'status' => 'In Progress',
                'created_by' => 3,
                'department_id' => 2,
                'created_at' => '2025-05-01 12:00:00',
                'updated_at' => '2025-05-17 16:00:00',
            ],
            [
                'title' => 'Phát triển hệ thống ERP',
                'description' => 'Xây dựng module ERP cho Khoa.',
                'due_date' => '2025-06-01',
                'status' => 'Completed',
                'created_by' => 4,
                'department_id' => 3,
                'created_at' => '2025-05-01 13:00:00',
                'updated_at' => '2025-05-28 09:00:00',
            ],
            [
                'title' => 'Cập nhật dữ liệu học vụ',
                'description' => 'Sửa dữ liệu học vụ trên CMS.',
                'due_date' => '2025-06-05',
                'status' => 'Not Started',
                'created_by' => 4,
                'department_id' => 3,
                'created_at' => '2025-05-01 14:00:00',
                'updated_at' => '2025-05-01 14:00:00',
            ],
            [
                'title' => 'Phát triển ứng dụng web',
                'description' => 'Xây dựng ứng dụng quản lý công việc.',
                'due_date' => '2025-05-14',
                'status' => 'Completed',
                'created_by' => 5,
                'department_id' => 4,
                'created_at' => '2025-05-01 15:00:00',
                'updated_at' => '2025-05-12 11:00:00',
            ],
            [
                'title' => 'Soạn tài liệu DevOps',
                'description' => 'Chuẩn bị giáo trình DevOps.',
                'due_date' => '2025-05-20',
                'status' => 'In Progress',
                'created_by' => 5,
                'department_id' => 4,
                'created_at' => '2025-05-01 16:00:00',
                'updated_at' => '2025-05-20 13:00:00',
            ],
            [
                'title' => 'Hướng dẫn thực tập sinh',
                'description' => 'Hỗ trợ thực tập sinh CNTT.',
                'due_date' => '2025-06-10',
                'status' => 'Not Started',
                'created_by' => 5,
                'department_id' => 4,
                'created_at' => '2025-05-01 17:00:00',
                'updated_at' => '2025-05-01 17:00:00',
            ],
        ]);
    }
}