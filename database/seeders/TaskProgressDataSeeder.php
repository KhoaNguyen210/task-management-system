<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskProgressDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('task_progress')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('task_progress')->insert([
            [
                'task_id' => 1,
                'status' => 'Completed',
                'comment' => 'Hoàn thành giáo trình Java.',
                'user_id' => 6,
                'updated_at' => '2025-05-08 10:00:00',
            ],
            [
                'task_id' => 2,
                'status' => 'Completed',
                'comment' => 'Bài kiểm tra Python đã xong.',
                'user_id' => 6,
                'updated_at' => '2025-05-14 12:00:00', // Sửa từ 2025-05-20
            ],
            [
                'task_id' => 3,
                'status' => 'In Progress',
                'comment' => 'Đang chuẩn bị nội dung hội thảo.',
                'user_id' => 9,
                'updated_at' => '2025-05-18 14:00:00',
            ],
            [
                'task_id' => 4,
                'status' => 'Completed',
                'comment' => 'Hệ thống mạng đã được nâng cấp.',
                'user_id' => 14,
                'updated_at' => '2025-05-10 15:00:00',
            ],
            [
                'task_id' => 5,
                'status' => 'In Progress',
                'comment' => 'Đang chuẩn bị nội dung workshop.',
                'user_id' => 14,
                'updated_at' => '2025-05-17 16:00:00',
            ],
            [
                'task_id' => 6,
                'status' => 'Completed',
                'comment' => 'Module ERP đã hoàn thành.',
                'user_id' => 22,
                'updated_at' => '2025-05-28 09:00:00',
            ],
            [
                'task_id' => 8,
                'status' => 'Completed',
                'comment' => 'Ứng dụng web đã xong.',
                'user_id' => 29,
                'updated_at' => '2025-05-12 11:00:00',
            ],
            [
                'task_id' => 9,
                'status' => 'In Progress',
                'comment' => 'Đang soạn tài liệu DevOps.',
                'user_id' => 29,
                'updated_at' => '2025-05-20 13:00:00',
            ],
        ]);
    }
}