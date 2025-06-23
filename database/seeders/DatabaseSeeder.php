<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\DepartmentSeeder;
use Database\Seeders\UserSeeder;

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
    }
}