# 🎓 HỆ THỐNG QUẢN LÝ TRUNG TÂM NGOẠI NGỮ (ENGLISH CENTER)

Dự án môn học **Lập Trình Mã Nguồn Mở** – Hệ thống quản lý khóa học, lớp học, giáo viên, học viên, điểm danh, kết quả học tập và thống kê doanh thu tại trung tâm ngoại ngữ.

---

## 🛠️ 1. Công nghệ sử dụng (Tech Stack)
* **Backend Framework:** PHP 8.1+ (Laravel 10.x / 11.x)
* **ORM & Database Management:** Eloquent ORM, Laravel Migrations & Seeders
* **Frontend UI:** Blade Template Engine + Bootstrap 5 + Bootstrap Icons
* **Môi trường Local Server:** Laragon (Apache + MySQL)
* **Công cụ quản trị CSDL:** phpMyAdmin (`http://localhost/phpmyadmin`)

---

## 👥 2. Phân công công việc & Phạm vi sở hữu File (Tránh xung đột Git)

Để 3 lập trình viên code song song mà **không bao giờ bị xung đột (Git Merge Conflict)**, dự án đã được tách riêng file Route, Controller và thư mục View cho từng thành viên:

| Thành viên | Vai trò | Chức năng phụ trách | File Route riêng | Controllers sở hữu | Thư mục View sở hữu |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Thành viên 1** | Lập trình viên | Migration, Seeder, Eloquent Model; CRUD Khóa học, Lớp học; Tìm kiếm & lọc lớp theo khóa học hoặc giáo viên. | `routes/member1.php` | `CourseController`, `ClassRoomController` | `resources/views/courses/`, `resources/views/class_rooms/` |
| **Thành viên 2 (Repo)** | Lập trình viên | Đăng nhập, đăng xuất; Phân quyền (`Admin`, `Giáo viên`, `Học viên`); Quản lý tài khoản, học viên và giáo viên. | `routes/member2.php` | `AuthController`, `UserController`, `TeacherController`, `StudentController` | `resources/views/auth/`, `resources/views/users/`, `resources/views/teachers/`, `resources/views/students/` |
| **Thành viên 3** | Lập trình viên | Học viên đăng ký lớp; Danh sách học viên theo lớp; Điểm danh; Nhập điểm/kết quả học tập; Thống kê học viên, lớp, doanh thu học phí. | `routes/member3.php` | `EnrollmentController`, `AttendanceController`, `GradeController`, `ReportController` | `resources/views/enrollments/`, `resources/views/attendances/`, `resources/views/grades/`, `resources/views/reports/` |
| **Thành viên 4** | Tổng hợp báo cáo Word | Soạn thảo báo cáo Word theo đề cương; thu thập sơ đồ, ảnh giao diện, kết quả kiểm thử; lập mục lục, tài liệu tham khảo, phụ lục. | N/A | Kiểm thử toàn hệ thống | Thư mục `docs/report/` |

---

## ⚙️ 3. Hướng dẫn cài đặt môi trường Local (Laragon + phpMyAdmin)

Tất cả thành viên trong nhóm thực hiện đúng 5 bước sau để chạy dự án và truy vấn Database ngay tại máy cá nhân:

### Bước 1: Khởi động Laragon & Tạo Database trong phpMyAdmin
1. Mở **Laragon**, nhấn **Start All** để bật Apache và MySQL (Port `3306`).
2. Mở trình duyệt truy cập: `http://localhost/phpmyadmin`
   * **Username:** `root`
   * **Password:** *(Để trống)*
3. Nhấn **New (Mới)** ở cột bên trái -> Đặt tên Database: `english_center_db` -> Chọn Collation: `utf8mb4_unicode_ci` -> Nhấn **Create**.

### Bước 2: Clone Repository về thư mục `C:\laragon\www`
Mở Terminal của Laragon (**Menu -> Laragon -> Terminal**) và chạy lệnh:
```bash
cd C:/laragon/www
git clone <URL_REPO_CUA_NHOM> english-center
cd english-center
```

### Bước 3: Cài đặt thư viện & Cấu hình file `.env`
```bash
composer install
cp .env.example .env
php artisan key:generate
```
Mở file `.env` và kiểm tra cụm cấu hình kết nối Database (đã được thiết lập sẵn cho Laragon):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=english_center_db
DB_USERNAME=root
DB_PASSWORD=
```

### Bước 4: Chạy Migration & Seeder để tạo bảng và dữ liệu mẫu
```bash
php artisan migrate:fresh --seed
```
> **Lưu ý:** Bất kỳ khi nào **Thành viên 1** cập nhật cấu trúc bảng hoặc dữ liệu mẫu, các thành viên khác chỉ cần `git pull` và chạy lại lệnh `php artisan migrate:fresh --seed` là có ngay Database chuẩn mới nhất trên phpMyAdmin local.

### Bước 5: Khởi chạy ứng dụng
```bash
php artisan serve
```
Truy cập ứng dụng tại: `http://127.0.0.1:8000` (hoặc dùng Virtual Host của Laragon: `http://english-center.test`).

---

## 🌐 4. Cấu hình truy vấn chung 1 Database qua mạng LAN / Radmin VPN (Khi ghép bài)

Trong trường hợp nhóm ngồi chung phòng Lab/Wi-Fi hoặc dùng Radmin VPN và muốn **cả 4 máy cùng truy vấn vào 1 Database duy nhất** đặt trên máy của Thành viên 1 hoặc Thành viên 2:

1. **Trên máy làm Máy chủ DB (Host):**
   * Mở `http://localhost/phpmyadmin` -> Chọn tab **User accounts** -> **Add user account**.
   * Nhập **User name:** `team_user` | **Host name:** Chọn `Any host (%)` | **Password:** `123456`.
   * Tích chọn **Grant all privileges on wildcard name (username\_%)** và cấp toàn quyền trên bảng `english_center_db` -> Nhấn **Go**.
   * Mở Terminal gõ `ipconfig` để lấy địa chỉ IPv4 (ví dụ: `192.168.1.50`).
2. **Trên máy các thành viên còn lại:**
   * Đổi thông số trong file `.env`:
     ```env
     DB_HOST=192.168.1.50
     DB_DATABASE=english_center_db
     DB_USERNAME=team_user
     DB_PASSWORD=123456
     ```
   * Chạy `php artisan config:clear` để nhận kết nối mới.

---

## 🔑 5. Tài khoản mặc định sau khi chạy Seeder

| Vai trò | Email đăng nhập | Mật khẩu | Quyền hạn chính |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@englishcenter.vn` | `123456` | Quản lý toàn bộ hệ thống, tài khoản, giáo viên, học viên, khóa học, lớp học, thống kê. |
| **Giáo viên 1** | `gv.minh@englishcenter.vn` | `123456` | Xem các lớp mình phụ trách, xem danh sách học viên, điểm danh, nhập điểm/kết quả. |
| **Giáo viên 2** | `gv.lan@englishcenter.vn` | `123456` | Xem các lớp mình phụ trách, xem danh sách học viên, điểm danh, nhập điểm/kết quả. |
| **Học viên 1** | `hv.an@englishcenter.vn` | `123456` | Tìm kiếm/lọc lớp học, đăng ký lớp học, xem lớp đã đăng ký, lịch học, điểm danh và điểm số. |
| **Học viên 2** | `hv.binh@englishcenter.vn` | `123456` | Tìm kiếm/lọc lớp học, đăng ký lớp học, xem lớp đã đăng ký, lịch học, điểm danh và điểm số. |

---

## 📅 6. Lộ trình thực hiện 20 ngày (Sprint Plan)

* **Giai đoạn 1 (Ngày 1 – Ngày 3): Khởi tạo Khung dự án & Cơ sở dữ liệu**
  * **Thành viên 2:** Khởi tạo khung Laravel theo file `docs/SCAFFOLD.md`, push lên nhánh `main`, tạo sẵn cấu trúc phân quyền và đăng nhập.
  * **Thành viên 1:** Kiểm tra, hoàn thiện toàn bộ Migrations, Models, Relationships và Seeders.
  * **Thành viên 4:** Khởi tạo file báo cáo Word, viết Chương 1 (Tổng quan đề tài) và Chương 2 (Sơ đồ Use Case, ERD).
* **Giai đoạn 2 (Ngày 4 – Ngày 9): Xây dựng các chức năng Quản trị & Danh mục cốt lõi**
  * **Thành viên 1 (nhánh `feat/member1-courses-classes`):** Hoàn thành CRUD Khóa học và CRUD Lớp học.
  * **Thành viên 2 (nhánh `feat/member2-auth-users`):** Hoàn thành Đăng nhập/Đăng xuất, Middleware phân quyền, CRUD Tài khoản, CRUD Giáo viên, CRUD Học viên.
  * **Thành viên 3 (nhánh `feat/member3-academics`):** Hoàn thành chức năng Học viên đăng ký lớp học và Xem danh sách học viên theo từng lớp.
* **Giai đoạn 3 (Ngày 10 – Ngày 15): Nghiệp vụ Giảng dạy, Tìm kiếm & Thống kê**
  * **Thành viên 1:** Hoàn thành chức năng Tìm kiếm và Lọc lớp học theo khóa học hoặc giáo viên.
  * **Thành viên 2:** Rà soát phân quyền trên toàn bộ giao diện, trang Hồ sơ cá nhân (Profile).
  * **Thành viên 3:** Hoàn thành chức năng Điểm danh theo ngày, Nhập điểm/Kết quả học tập và Dashboard Thống kê (số học viên, số lớp, doanh thu học phí).
* **Giai đoạn 4 (Ngày 16 – Ngày 20): Ghép code (Merge), Kiểm thử & Hoàn thiện Báo cáo Word**
  * **Ngày 16 – 17:** Merge toàn bộ nhánh của Thành viên 1, 2, 3 vào `main`. Fix lỗi giao diện và luồng dữ liệu.
  * **Ngày 18 – 19:** **Thành viên 4** chạy kiểm thử toàn bộ chức năng trên máy local, chụp ảnh màn hình giao diện, hoàn thành bảng Test Cases, mục lục, tài liệu tham khảo và phụ lục.
  * **Ngày 20:** Đóng gói Source Code + File SQL backup + Báo cáo Word hoàn chỉnh để nộp bài.