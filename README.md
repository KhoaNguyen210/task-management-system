# Hệ thống Phân công Công việc Khoa CNTT

<!-- Project overview -->
Hệ thống quản lý và phân công công việc được phát triển dành riêng cho Khoa Công nghệ Thông tin, giúp quản lý công việc hiệu quả giữa Trưởng Khoa, Trưởng Bộ môn và Giảng viên. Dự án được xây dựng bằng **Laravel Framework** với mục tiêu cung cấp giao diện thân thiện và quy trình làm việc trực quan.

## Yêu cầu Hệ thống

<!-- System requirements for running the project -->
Để cài đặt và chạy dự án trên máy cục bộ, bạn cần đảm bảo các phần mềm sau đã được cài đặt:

- **PHP**: Phiên bản 8.1 hoặc cao hơn.
- **Composer**: Công cụ quản lý dependency PHP. [Tải Composer](https://getcomposer.org/download/).
- **Node.js và npm**: Môi trường chạy JavaScript và quản lý gói. [Tải Node.js](https://nodejs.org/).
- **Git**: Hệ thống quản lý phiên bản. [Tải Git](https://git-scm.com/downloads).
- **MySQL**: Cơ sở dữ liệu, khuyến nghị sử dụng **XAMPP** (bao gồm Apache, MySQL, PHP). [Tải XAMPP](https://www.apachefriends.org/).
- **Trình duyệt Web**: Chrome, Firefox, hoặc Edge.

## Hướng dẫn Cài đặt

<!-- Step-by-step installation guide -->
Vui lòng làm theo các bước sau để thiết lập dự án:

1. **Clone Repository**  
   Tải mã nguồn từ GitHub về máy:  
   ```bash
   git clone https://github.com/KhoaNguyen210/task-management-system.git
   ```

2. **Di chuyển vào thư mục dự án**  
   ```bash
   cd task-management-system
   ```

3. **Cài đặt Dependencies PHP**  
   Cài đặt các thư viện PHP cần thiết bằng Composer:  
   ```bash
   composer install
   ```

4. **Cài đặt Dependencies JavaScript**  
   Cài đặt các gói JavaScript (bao gồm Vite) bằng npm:  
   ```bash
   npm install
   ```

5. **Tạo file cấu hình môi trường**  
   Sao chép file `.env.example` để tạo file `.env`:  
   ```bash
   cp .env.example .env
   ```
   *Lưu ý*: Trên Windows, sử dụng `copy .env.example .env`.

6. **Tạo Khóa ứng dụng**  
   Tạo khóa mã hóa duy nhất cho ứng dụng:  
   ```bash
   php artisan key:generate
   ```

7. **Cấu hình Kết nối Cơ sở dữ liệu**  
   - Mở file `.env` bằng trình soạn thảo văn bản.  
   - Cập nhật các thông tin kết nối MySQL (`DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).  
   - Ví dụ cho XAMPP mặc định:  
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=task_management_db
     DB_USERNAME=root
     DB_PASSWORD=
     ```
   - Ghi nhớ tên database (ví dụ: `task_management_db`).

8. **Tạo Cơ sở dữ liệu**  
   - Truy cập công cụ quản lý MySQL (ví dụ: phpMyAdmin tại `http://localhost/phpmyadmin`).  
   - Tạo database mới khớp với tên `DB_DATABASE` trong file `.env`.  
   - Chọn **Collation**: `utf8mb4_unicode_ci`.

9. **Chạy Migrations và Seeders**  
   Tạo cấu trúc bảng và nạp dữ liệu mẫu (tài khoản, bộ môn):  
   ```bash
   php artisan migrate:fresh --seed
   ```

10. **Tạo Symbolic Link cho Storage**  
    Tạo liên kết tượng trưng để truy cập file tải lên (ảnh, tệp đính kèm):  
    ```bash
    php artisan storage:link
    ```

11. **Biên dịch Tài nguyên Frontend**  
    Biên dịch CSS và JavaScript bằng Vite:  
    ```bash
    npm run build
    ```
    *Lưu ý*: Chạy `npm run dev` nếu muốn server Vite tự động cập nhật khi chỉnh sửa frontend (giữ Terminal mở).

## Chạy Ứng dụng

<!-- Instructions to run the application -->
1. **Khởi động Server Laravel**  
   Chạy server phát triển:  
   ```bash
   php artisan serve
   ```

2. **Truy cập Ứng dụng**  
   Mở trình duyệt và truy cập `http://127.0.0.1:8000` để vào trang đăng nhập.

## Tài khoản Đăng nhập Mẫu

<!-- Sample user accounts for testing -->
Sử dụng các tài khoản sau để kiểm tra các vai trò (mật khẩu mặc định: `password123`):

- **Trưởng Khoa (Dean)**:  
  - Username: `phamvanhuy`

- **Trưởng Bộ môn**:  
  - Username: `leanhcuong` (BM Khoa học máy tính)  
  - Username: `truongdinhtu` (BM Mạng máy tính)  
  - Username: `tranthanhphuoc` (BM Hệ thống thông tin)  
  - Username: `levanvang` (BM Công nghệ phần mềm)

- **Giảng viên**:  
  - Username: `trinhhungcuong` (BM Khoa học máy tính)  
  - Username: `trantrungtin` (BM Mạng máy tính)  
  - Username: `huynhngoctu` (BM Hệ thống thông tin)  
  - Username: `vudinhhong` (BM Công nghệ phần mềm)  
  - *Thêm các tài khoản khác trong `UserSeeder.php`*

- **Mật khẩu**: `password123`

## Công nghệ Sử dụng

<!-- Technologies used in the project -->
- **Backend**: PHP 8.1+, Laravel Framework 11.x  
- **Frontend**: HTML, Tailwind CSS, JavaScript, Vite  
- **Database**: MySQL  
- **Web Server (Development)**: Laravel Artisan Serve  
- **Version Control**: Git  

## Thành viên Thực hiện

<!-- Project contributors -->
- Lê Minh Triết – 521H0173  
- Nguyễn Ngô Đăng Khoa – 521H0084  

## Hướng dẫn Quản lý và Bảo trì

<!-- Common commands for maintenance -->
### 1. Thiết lập Ban đầu
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
npm install
npm run build
```

### 2. Lệnh Artisan Thường Dùng
| Lệnh                         | Chức năng                                       |
|------------------------------|-------------------------------------------------|
| `php artisan config:clear`   | Xóa cache cấu hình `.env` và `config/*.php`     |
| `php artisan cache:clear`    | Xóa cache dữ liệu ứng dụng                      |
| `php artisan route:clear`    | Xóa cache route đã biên dịch                    |
| `php artisan view:clear`     | Xóa cache view Blade                            |
| `php artisan optimize:clear` | Xóa toàn bộ cache (config, route, view, events) |

> **Lưu ý**: Chạy các lệnh trên khi thay đổi `.env`, routes, hoặc views để tránh lỗi cache.

### 3. Quản lý Composer
| Lệnh                     | Chức năng                                              |
|--------------------------|--------------------------------------------------------|
| `composer dump-autoload` | Tạo lại danh sách autoload khi thêm class mới         |

### 4. Quản lý Frontend (NPM)
| Lệnh            | Chức năng                                                    |
|-----------------|-------------------------------------------------------------|
| `npm install`   | Cài đặt các package trong `package.json`                    |
| `npm run dev`   | Biên dịch CSS/JS cho phát triển (real-time)                |
| `npm run build` | Biên dịch CSS/JS tối ưu cho production                     |

### 5. Reset Môi trường Cache
```bash
php artisan optimize:clear
composer dump-autoload
```

### 6. Khởi chạy Server
```bash
php artisan serve
```

* Truy cập: `http://127.0.0.1:8000`

## Lưu ý

<!-- Additional notes for users -->
- Nếu gặp lỗi liên quan đến cấu hình, hãy kiểm tra file `.env` và chạy `php artisan optimize:clear`.
- Để tối ưu hiệu suất, sử dụng `npm run build` thay vì `npm run dev` khi triển khai production.
- Liên hệ nhóm phát triển (Lê Minh Triết hoặc Nguyễn Ngô Đăng Khoa) nếu cần hỗ trợ thêm.
