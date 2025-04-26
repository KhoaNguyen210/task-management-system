<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại trước khi xóa dữ liệu
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Xóa sạch dữ liệu cũ trong bảng departments
        DB::table('departments')->truncate();
        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Thêm dữ liệu cho các bộ môn
        DB::table('departments')->insert([
            [
                'name' => 'Khoa học máy tính',
                'head_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mạng máy tính',
                'head_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Hệ thống thông tin',
                'head_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Công nghệ phần mềm',
                'head_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
