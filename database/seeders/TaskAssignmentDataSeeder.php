<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskAssignmentDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('task_assignments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('task_assignments')->insert([
            ['assignment_id' => 1, 'task_id' => 1, 'user_id' => 6], // Trịnh Hùng Cường
            ['assignment_id' => 2, 'task_id' => 2, 'user_id' => 6],
            ['assignment_id' => 3, 'task_id' => 3, 'user_id' => 9], // Nguyễn Quốc Bình
            ['assignment_id' => 4, 'task_id' => 4, 'user_id' => 14], // Trần Trung Tín
            ['assignment_id' => 5, 'task_id' => 5, 'user_id' => 14],
            ['assignment_id' => 6, 'task_id' => 6, 'user_id' => 22], // Huỳnh Ngọc Tú
            ['assignment_id' => 7, 'task_id' => 7, 'user_id' => 22],
            ['assignment_id' => 8, 'task_id' => 8, 'user_id' => 29], // Vũ Đình Hồng
            ['assignment_id' => 9, 'task_id' => 9, 'user_id' => 29],
            ['assignment_id' => 10, 'task_id' => 10, 'user_id' => 29],
        ]);
    }
}