<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departments')->insert([
            [
                'department_id' => 1,
                'name' => 'Computer Science',
                'head_id' => null, // Will be updated later
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'department_id' => 2,
                'name' => 'Software Engineering',
                'head_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}