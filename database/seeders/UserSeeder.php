<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Department;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Tắt kiểm tra khóa ngoại trước khi xóa dữ liệu
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // Xóa dữ liệu user cũ và bảng liên kết task_assignments
        DB::table('users')->truncate();
        DB::table('task_assignments')->truncate(); // Xóa cả bảng liên kết
        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $departments = Department::pluck('department_id', 'name');
        $khmtId = $departments['Khoa học máy tính'] ?? null;
        $mmtId = $departments['Mạng máy tính'] ?? null;
        $htttId = $departments['Hệ thống thông tin'] ?? null;
        $cnpmId = $departments['Công nghệ phần mềm'] ?? null;

        if (!$khmtId || !$mmtId || !$htttId || !$cnpmId) {
            throw new \Exception('Could not find required department IDs in UserSeeder. Make sure DepartmentSeeder ran correctly.');
        }

        // Mảng chứa dữ liệu các user mẫu
        $users = [
            // Trưởng Khoa
            [
                'username' => 'phamvanhuy', 'password' => Hash::make('password123'), 'name' => 'Phạm Văn Huy', 'email' => 'phamvanhuy@tdtu.edu.vn', 'role' => 'Dean', 'department_id' => $khmtId, 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            // Trưởng Bộ môn
            [
                'username' => 'leanhcuong', 'password' => Hash::make('password123'), 'name' => 'Lê Anh Cường', 'email' => 'leanhcuong@tdtu.edu.vn', 'role' => 'Department Head', 'department_id' => $khmtId, 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'truongdinhtu', 'password' => Hash::make('password123'), 'name' => 'Trương Đình Tú', 'email' => 'truongdinhtu@tdtu.edu.vn', 'role' => 'Department Head', 'department_id' => $mmtId, 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'tranthanhphuoc', 'password' => Hash::make('password123'), 'name' => 'Trần Thanh Phước', 'email' => 'tranthanhphuoc@tdtu.edu.vn', 'role' => 'Department Head', 'department_id' => $htttId, 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'levanvang', 'password' => Hash::make('password123'), 'name' => 'Lê Văn Vang', 'email' => 'levanvang@tdtu.edu.vn', 'role' => 'Department Head', 'department_id' => $cnpmId, 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            // Giảng viên Khoa học Máy tính (department_id = $khmtId)
            [
                'username' => 'trinhhungcuong', 'name' => 'Trịnh Hùng Cường', 'email' => 'trinhhungcuong@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $khmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'nguyenchithien', 'name' => 'Nguyễn Chí Thiện', 'email' => 'nguyenchithien@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $khmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'keunhoryu', 'name' => 'Keun Ho Ryu', 'email' => 'keunhoryu@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $khmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'nguyenquocbinh', 'name' => 'Nguyễn Quốc Bình', 'email' => 'nguyenquocbinh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $khmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'tranluongquocdai', 'name' => 'Trần Lương Quốc Đại', 'email' => 'tranluongquocdai@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $khmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'nguyenthanhan', 'name' => 'Nguyễn Thành An', 'email' => 'nguyenthanhan@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $khmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'nguyenthidiemhang', 'name' => 'Nguyễn Thị Diễm Hằng', 'email' => 'nguyenthidiemhang@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $khmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'luongthingockhanh_khmt', 'name' => 'Lương Thị Ngọc Khánh', 'email' => 'luongthingockhanh.khmt@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $khmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],

            // Giảng viên Mạng máy tính (department_id = $mmtId)
            [
                'username' => 'trantrungtin', 'name' => 'Trần Trung Tín', 'email' => 'trantrungtin@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $mmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'buiquyanh', 'name' => 'Bùi Quy Anh', 'email' => 'buiquyanh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $mmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'levietthanh', 'name' => 'Lê Viết Thanh', 'email' => 'levietthanh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $mmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'phutrantin', 'name' => 'Phù Trần Tín', 'email' => 'phutrantin@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $mmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'tranthanhnam', 'name' => 'Trần Thanh Nam', 'email' => 'tranthanhnam1@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $mmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'tranchithien', 'name' => 'Trần Chí Thiện', 'email' => 'tranchithien@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $mmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'hovanthai', 'name' => 'Hồ Văn Thái', 'email' => 'hovanthai@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $mmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'tongthanhvan', 'name' => 'Tống Thanh Văn', 'email' => 'tongthanhvan@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $mmtId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],

            // Giảng viên Hệ thống Thông tin (department_id = $htttId)
            [
                'username' => 'huynhngoctu', 'name' => 'Huỳnh Ngọc Tú', 'email' => 'huynhngoctu@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $htttId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'hothilinh', 'name' => 'Hồ Thị Linh', 'email' => 'hothilinh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $htttId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'luongthingockhanh_httt', 'name' => 'Lương Thị Ngọc Khánh', 'email' => 'luongthingockhanh.httt@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $htttId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'duonghuuphuc', 'name' => 'Dương Hữu Phúc', 'email' => 'duonghuuphuc@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $htttId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'dungcamquang', 'name' => 'Dung Cẩm Quang', 'email' => 'dungcamquang@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $htttId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'vothikimanh', 'name' => 'Võ Thị Kim Anh', 'email' => 'vothikimanh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $htttId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],

            // Giảng viên Công nghệ phần mềm (department_id = $cnpmId)
            [
                'username' => 'vudinhhong', 'name' => 'Vũ Đình Hồng', 'email' => 'vudinhhong@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $cnpmId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'maivanmanh', 'name' => 'Mai Văn Mạnh', 'email' => 'maivanmanh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $cnpmId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'dangminhthang', 'name' => 'Đặng Minh Thắng', 'email' => 'dangminhthang@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $cnpmId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'vovanthanh', 'name' => 'Võ Văn Thành', 'email' => 'vovanthanh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $cnpmId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
            [
                'username' => 'doanxuanthanh', 'name' => 'Doãn Xuân Thanh', 'email' => 'doanxuanthanh@tdtu.edu.vn', 'role' => 'Lecturer', 'department_id' => $cnpmId, 'password' => Hash::make('password123'), 'created_at' => now(), 'updated_at' => now(), 'last_login_time' => null, 'failed_login_attempts' => 0, 'is_locked' => false
            ],
        ];

        // Insert users vào database
        DB::table('users')->insert($users);

        // Lấy ID của các trưởng bộ môn vừa tạo (dựa vào username là duy nhất)
        $leanhcuongId = DB::table('users')->where('username', 'leanhcuong')->value('user_id');
        $truongdinhtuId = DB::table('users')->where('username', 'truongdinhtu')->value('user_id');
        $tranthanhphuocId = DB::table('users')->where('username', 'tranthanhphuoc')->value('user_id');
        $levanvangId = DB::table('users')->where('username', 'levanvang')->value('user_id');

        // Cập nhật head_id cho các department tương ứng
        if ($leanhcuongId && $khmtId) DB::table('departments')->where('department_id', $khmtId)->update(['head_id' => $leanhcuongId]);
        if ($truongdinhtuId && $mmtId) DB::table('departments')->where('department_id', $mmtId)->update(['head_id' => $truongdinhtuId]);
        if ($tranthanhphuocId && $htttId) DB::table('departments')->where('department_id', $htttId)->update(['head_id' => $tranthanhphuocId]);
        if ($levanvangId && $cnpmId) DB::table('departments')->where('department_id', $cnpmId)->update(['head_id' => $levanvangId]);

    }
}
