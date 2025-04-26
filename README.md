# Hệ thống Phân công Công việc Khoa CNTT

Đây là dự án xây dựng hệ thống quản lý và phân công công việc dành cho Khoa Công nghệ Thông tin, được phát triển bằng **Laravel Framework**.

## Yêu cầu Hệ thống

Để cài đặt và chạy dự án này trên máy cục bộ, bạn cần đảm bảo đã cài đặt các phần mềm sau:

- **PHP**: Phiên bản 8.1 hoặc cao hơn.
- **Composer**: Công cụ quản lý dependency cho PHP. [Hướng dẫn cài đặt Composer](https://getcomposer.org/download/).
- **Node.js và npm**: Môi trường chạy JavaScript và trình quản lý gói Node.js. [Tải Node.js](https://nodejs.org/).
- **Git**: Hệ thống quản lý phiên bản phân tán. [Tải Git](https://git-scm.com/downloads).
- **Cơ sở dữ liệu MySQL**: **XAMPP**: Gói phần mềm bao gồm Apache, MySQL, PHP. [Tải XAMPP](https://www.apachefriends.org/).
- **Trình duyệt Web**: Chrome, Firefox, Edge,...

## Hướng dẫn Cài đặt

Vui lòng thực hiện các bước sau theo thứ tự:

1. **Clone Repository**:
   Mở Terminal (hoặc Command Prompt/Git Bash) và chạy lệnh sau để tải mã nguồn từ GitHub về máy:
   ```bash
   git clone https://github.com/KhoaNguyen210/task-management-system.git
   ```

2. **Di chuyển vào thư mục dự án**:
   ```bash
   cd task-management-system
   ```

3. **Cài đặt Dependencies PHP**:
   Chạy Composer để cài đặt các thư viện PHP cần thiết:
   ```bash
   composer install
   ```

4. **Cài đặt Dependencies JavaScript**:
   Chạy npm để cài đặt các gói JavaScript (bao gồm Vite và các thư viện frontend):
   ```bash
   npm install
   ```

5. **Tạo file cấu hình môi trường .env**:
   Sao chép file cấu hình mẫu:
   ```bash
   cp .env.example .env
   ```
   (Trên Windows, có thể dùng `copy .env.example .env`)

6. **Tạo Khóa ứng dụng (Application Key)**:
   Chạy lệnh Artisan để tạo khóa mã hóa duy nhất cho ứng dụng:
   ```bash
   php artisan key:generate
   ```

7. **Cấu hình Kết nối Cơ sở dữ liệu**:
   - Mở file `.env` vừa tạo bằng trình soạn thảo văn bản.
   - Tìm đến các dòng cấu hình database (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) và chỉnh sửa phù hợp với thông tin kết nối MySQL trên máy của bạn.
   - Ví dụ nếu dùng XAMPP với cài đặt mặc định:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=task_management_db
     DB_USERNAME=root
     DB_PASSWORD=
     ```
   - **Quan trọng**: Ghi nhớ tên `DB_DATABASE` bạn đã đặt (ví dụ: `task_management_db`).

8. **Tạo Cơ sở dữ liệu**:
   - Mở công cụ quản lý MySQL (ví dụ: phpMyAdmin qua `http://localhost/phpmyadmin` nếu dùng XAMPP).
   - Tạo một database mới với tên chính xác bạn đã cấu hình trong `DB_DATABASE` (ví dụ: `task_management_db`).
   - Nên chọn **Collation** là `utf8mb4_unicode_ci`.

9. **Chạy Migrations và Seeders**:
   Chạy lệnh sau để tạo cấu trúc các bảng trong database và nạp dữ liệu mẫu (tài khoản, phòng ban):
   ```bash
   php artisan migrate:fresh --seed
   ```
   Lệnh này sẽ xóa các bảng cũ (nếu có), chạy lại tất cả migrations và thực thi các seeders.

10. **Biên dịch Tài nguyên Frontend**:
    Chạy lệnh sau để biên dịch các file CSS và JavaScript bằng Vite:
    ```bash
    npm run build
    ```
    (Hoặc chạy `npm run dev` nếu muốn server Vite chạy nền để tự động cập nhật khi sửa code frontend, nhưng cần giữ cửa sổ Terminal mở).

## Chạy Ứng dụng

1. **Khởi động Server Phát triển Laravel**:
   Trong Terminal, chạy lệnh:
   ```bash
   php artisan serve
   ```
   Lệnh này sẽ khởi động server và hiển thị địa chỉ truy cập (thường là `http://127.0.0.1:8000`).

2. **Truy cập Ứng dụng**:
   Mở trình duyệt web và truy cập vào địa chỉ trên (ví dụ: `http://127.0.0.1:8000`). Bạn sẽ thấy trang đăng nhập.

## Tài khoản Đăng nhập Mẫu

Bạn có thể sử dụng các tài khoản sau để đăng nhập và kiểm tra các vai trò khác nhau (mật khẩu mặc định cho tất cả là `password123`):

- **Trưởng Khoa (Dean)**:
  - Username: `phamvanhuy`

- **Trưởng Bộ môn**:
  - Username: `leanhcuong` (Trưởng BM Khoa học máy tính)
  - Username: `truongdinhtu` (Trưởng BM Mạng máy tính)
  - Username: `tranthanhphuoc` (Trưởng BM Hệ thống thông tin)
  - Username: `levanvang` (Trưởng BM Công nghệ phần mềm)

- **Giảng viên**:
  - Username: `trinhhungcuong` (BM Khoa học máy tính)
  - Username: `trantrungtin` (BM Mạng máy tính)
  - Username: `huynhngoctu` (BM Hệ thống thông tin)
  - Username: `vudinhhong` (BM Công nghệ phần mềm)
  - (Và các giảng viên khác đã được tạo trong `UserSeeder.php`)

- **Mật khẩu**: `password123` (cho tất cả tài khoản trên)

## Công nghệ sử dụng

- **Backend**: PHP 8.1+, Laravel Framework 11.x
- **Frontend**: HTML, Tailwind CSS, JavaScript, Vite
- **Database**: MySQL
- **Web Server (Development)**: Artisan Serve (hoặc Apache/Nginx nếu cấu hình riêng)
- **Version Control**: Git

## Thành viên thực hiện

- Lê Minh Triết – 521H0173
- Nguyễn Ngô Đăng Khoa – 521H0084
