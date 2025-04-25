<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ (tùy chọn)
        DB::table('users')->delete();

        // Tạo danh sách users
        $users = [
            // Khoa
            [
                'user_id' => 1,
                'username' => 'phamvanhuy',
                'password' => Hash::make('password123'),
                'name' => 'Phạm Văn Huy',
                'email' => 'phamvanhuy@tdtu.edu.vn',
                'role' => 'Dean',
                'department_id' => 1,
                'last_login_time' => null,
                'failed_login_attempts' => 0,
                'is_locked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Trưởng Bộ môn
            [
                'user_id' => 2,
                'username' => 'truongdinhtu',
                'password' => Hash::make('password123'),
                'name' => 'Trương Đình Tú',
                'email' => 'truongdinhtu@tdtu.edu.vn',
                'role' => 'Department Head',
                'department_id' => 2,
                'last_login_time' => null,
                'failed_login_attempts' => 0,
                'is_locked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 3,
                'username' => 'leanhcuong',
                'password' => Hash::make('password123'),
                'name' => 'Lê Anh Cường',
                'email' => 'leanhcuong@tdtu.edu.vn',
                'role' => 'Department Head',
                'department_id' => 1,
                'last_login_time' => null,
                'failed_login_attempts' => 0,
                'is_locked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'username' => 'tranthanhphuoc',
                'password' => Hash::make('password123'),
                'name' => 'Trần Thanh Phước',
                'email' => 'tranthanhphuoc@tdtu.edu.vn',
                'role' => 'Department Head',
                'department_id' => 3,
                'last_login_time' => null,
                'failed_login_attempts' => 0,
                'is_locked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 5,
                'username' => 'levanvang',
                'password' => Hash::make('password123'),
                'name' => 'Lê Văn Vang',
                'email' => 'levanvang@tdtu.edu.vn',
                'role' => 'Department Head',
                'department_id' => 4,
                'last_login_time' => null,
                'failed_login_attempts' => 0,
                'is_locked' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Giảng viên & Nhân viên (Thêm tuần tự ID và department_id)
            // Bộ môn Khoa học Máy tính (ID: 1)
            [
                'user_id' => 6, 'username' => 'trinhhungcuong', 'name' => 'Trịnh Hùng Cường', 'email' => 'trinhhungcuong@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 1, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
             [
                 'user_id' => 7, 'username' => 'nguyenchithien', 'name' => 'Nguyễn Chí Thiện', 'email' => 'nguyenchithien@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 1, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 8, 'username' => 'keunhoryu', 'name' => 'Keun Ho Ryu', 'email' => 'keunhoryu@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 1, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 9, 'username' => 'nguyenquocbinh', 'name' => 'Nguyễn Quốc Bình', 'email' => 'nguyenquocbinh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 1, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 10, 'username' => 'tranluongquocdai', 'name' => 'Trần Lương Quốc Đại', 'email' => 'tranluongquocdai@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 1, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
             [
                'user_id' => 11, 'username' => 'nguyenthanhan', 'name' => 'Nguyễn Thành An', 'email' => 'nguyenthanhan@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 1, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 12, 'username' => 'nguyenthidiemhang', 'name' => 'Nguyễn Thị Diễm Hằng', 'email' => 'nguyenthidiemhang@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 1, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 13, 'username' => 'luongthingockhanh_khmt', 'name' => 'Lương Thị Ngọc Khánh', 'email' => 'luongthingockhanh.khmt@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 1, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],

            // Bộ môn Mạng máy tính (ID: 2)
             [
                'user_id' => 14, 'username' => 'trantrungtin', 'name' => 'Trần Trung Tín', 'email' => 'trantrungtin@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 2, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
             [
                 'user_id' => 15, 'username' => 'buiquyanh', 'name' => 'Bùi Quy Anh', 'email' => 'buiquyanh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 2, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 16, 'username' => 'levietthanh', 'name' => 'Lê Viết Thanh', 'email' => 'levietthanh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 2, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 17, 'username' => 'phutrantin', 'name' => 'Phù Trần Tín', 'email' => 'phutrantin@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 2, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 18, 'username' => 'tranthanhnam', 'name' => 'Trần Thanh Nam', 'email' => 'tranthanhnam1@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 2, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
             [
                 'user_id' => 19, 'username' => 'tranchithien', 'name' => 'Trần Chí Thiện', 'email' => 'tranchithien@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 2, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 20, 'username' => 'hovanthai', 'name' => 'Hồ Văn Thái', 'email' => 'hovanthai@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 2, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 21, 'username' => 'tongthanhvan', 'name' => 'Tống Thanh Văn', 'email' => 'tongthanhvan@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 2, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],

            // Bộ môn Hệ thống Thông tin (ID: 3)
            [
                'user_id' => 22, 'username' => 'huynhngoctu', 'name' => 'Huỳnh Ngọc Tú', 'email' => 'huynhngoctu@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 3, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
             [
                 'user_id' => 23, 'username' => 'hothilinh', 'name' => 'Hồ Thị Linh', 'email' => 'hothilinh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 3, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 24, 'username' => 'luongthingockhanh_httt', 'name' => 'Lương Thị Ngọc Khánh', 'email' => 'luongthingockhanh.httt@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 3, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
             [
                 'user_id' => 25, 'username' => 'duonghuuphuc', 'name' => 'Dương Hữu Phúc', 'email' => 'duonghuuphuc@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 3, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 26, 'username' => 'dungcamquang', 'name' => 'Dung Cẩm Quang', 'email' => 'dungcamquang@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 3, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
             [
                 'user_id' => 27, 'username' => 'vothikimanh', 'name' => 'Võ Thị Kim Anh', 'email' => 'vothikimanh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 3, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],

            // Bộ môn Công nghệ phần mềm (ID: 4)
             [
                 'user_id' => 28, 'username' => 'vudinhhong', 'name' => 'Vũ Đình Hồng', 'email' => 'vudinhhong@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 4, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
             [
                 'user_id' => 29, 'username' => 'maivanmanh', 'name' => 'Mai Văn Mạnh', 'email' => 'maivanmanh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 4, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
             [
                 'user_id' => 30, 'username' => 'dangminhthang', 'name' => 'Đặng Minh Thắng', 'email' => 'dangminhthang@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 4, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
             [
                 'user_id' => 31, 'username' => 'vovanthanh', 'name' => 'Võ Văn Thành', 'email' => 'vovanthanh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 4, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'user_id' => 32, 'username' => 'doanxuanthanh', 'name' => 'Doãn Xuân Thanh', 'email' => 'doanxuanthanh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => 4, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
        ];

        // Insert users vào database
        DB::table('users')->insert($users);

        // Cập nhật head_id cho các department dựa trên user_id đã gán ở trên
        DB::table('departments')->where('department_id', 1)->update(['head_id' => 3]); // KHMT - Lê Anh Cường (user_id=3)
        DB::table('departments')->where('department_id', 2)->update(['head_id' => 2]); // MMT - Trương Đình Tú (user_id=2)
        DB::table('departments')->where('department_id', 3)->update(['head_id' => 4]); // HTTT - Trần Thanh Phước (user_id=4)
        DB::table('departments')->where('department_id', 4)->update(['head_id' => 5]); // CNPM - Lê Văn Vang (user_id=5)
    }
}