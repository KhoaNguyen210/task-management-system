<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\TaskDataSeeder;
use Database\Seeders\TaskAssignmentDataSeeder;
use Database\Seeders\TaskProgressDataSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Gọi DepartmentSeeder trước để tạo các bộ môn
        $this->call([
            DepartmentSeeder::class,
        ]);

        // Gọi UserSeeder sau để tạo người dùng và cập nhật head_id cho departments
        $this->call([
            UserSeeder::class,
        ]);

        // Gọi các seeder mới cho tasks, task_assignments, task_progress
        $this->call([
            TaskDataSeeder::class,
            TaskAssignmentDataSeeder::class,
            TaskProgressDataSeeder::class,
        ]);
    }
}