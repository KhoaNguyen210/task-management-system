<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'user_id' => 1,
                'username' => 'dean',
                'password' => Hash::make('password123'),
                'name' => 'Dean User',
                'email' => 'dean@tdtu.edu.vn',
                'role' => 'Dean',
                'department_id' => 1,
                'last_login_time' => null,
                'failed_login_attempts' => 0,
                'is_locked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 2,
                'username' => 'dept_head',
                'password' => Hash::make('password123'),
                'name' => 'Department Head User',
                'email' => 'depthead@tdtu.edu.vn',
                'role' => 'Department Head',
                'department_id' => 1,
                'last_login_time' => null,
                'failed_login_attempts' => 0,
                'is_locked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'username' => 'lecturer',
                'password' => Hash::make('password123'),
                'name' => 'Lecturer User',
                'email' => 'lecturer@tdtu.edu.vn',
                'role' => 'Lecturer',
                'department_id' => 1,
                'last_login_time' => null,
                'failed_login_attempts' => 0,
                'is_locked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'username' => 'secretary',
                'password' => Hash::make('password123'),
                'name' => 'Secretary User',
                'email' => 'secretary@tdtu.edu.vn',
                'role' => 'Secretary',
                'department_id' => 2,
                'last_login_time' => null,
                'failed_login_attempts' => 0,
                'is_locked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Update head_id for departments
        DB::table('departments')->where('department_id', 1)->update(['head_id' => 2]);
    }
}