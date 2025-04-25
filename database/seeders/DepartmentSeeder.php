<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ (tùy chọn)
        DB::table('departments')->delete();

        DB::table('departments')->insert([
            [
                'department_id' => 1, // ID tạm thời
                'name' => 'Khoa học máy tính',
                'head_id' => null, // Sẽ được cập nhật sau trong UserSeeder
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => 2,
                'name' => 'Mạng máy tính',
                'head_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => 3,
                'name' => 'Hệ thống thông tin',
                'head_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => 4,
                'name' => 'Công nghệ phần mềm',
                'head_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}