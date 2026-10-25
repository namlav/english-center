# Nhập database bằng phpMyAdmin

SQL là ngôn ngữ dùng để tạo bảng, thêm dữ liệu và truy vấn cơ sở dữ liệu. MySQL/MariaDB là hệ quản trị lưu dữ liệu. phpMyAdmin là giao diện web để bạn quản lý MySQL/MariaDB; nó có thể đọc file `.sql` để tạo các bảng và dữ liệu.

## File cần dùng

Chọn file theo tình trạng database. Cả hai dùng cho MySQL 8.0.16+ hoặc MariaDB 10.4+:

| Tình trạng | File nhập |
| --- | --- |
| Bạn đã nhập bản 27 bảng gửi trước đó | `database/sql/english_learning_upgrade_20261022.sql` |
| Bạn chưa nhập database | `database/sql/english_learning_phpmyadmin.sql` |

**Nếu đã có bản 27 bảng:** chọn đúng database đó ở cột trái, chọn **Nhập / Import**, chọn file `english_learning_upgrade_20261022.sql`, rồi thực hiện một lần. Bản nâng cấp thêm 10 bảng và giữ dữ liệu hiện có; không thêm hồ sơ demo. Sau khi nâng cấp sẽ có 37 bảng. Nếu đã nâng cấp bằng Laravel thì không nhập lại file nâng cấp.

**Nếu chưa có database:** làm các bước bên dưới để tạo mới bằng file đầy đủ. Bản đầy đủ mới chứa 36 bảng nghiệp vụ, 1 bảng theo dõi migration và dữ liệu demo. Không nhập cả hai file vào cùng một database.

## Các bước trong phpMyAdmin

1. Mở phpMyAdmin như bạn vẫn dùng.
2. Chọn **Mới / New**, tạo database **english_learning_db**, chọn đối chiếu **utf8mb4_unicode_ci**.
3. Nhấn vào database vừa tạo ở cột bên trái.
4. Chọn tab **Nhập / Import**, chọn file **english_learning_phpmyadmin.sql**. Giữ định dạng **SQL** và bảng mã **utf-8**.
5. Nhấn **Nhập / Import / Go** tùy ngôn ngữ giao diện. Sau khi hoàn tất sẽ có 37 bảng.

File này dùng cho database mới, rỗng. Nếu bạn đang có dữ liệu trong database cũ thì giữ nguyên database đó; đây là bản tạo mới để thử nghiệm, không phải file nâng cấp dữ liệu cũ. File không có lệnh xóa database hoặc xóa bảng.

## Kết nối mã PHP sau khi nhập

Trong `.env` của Laravel, đặt tên database trùng với tên bạn vừa tạo:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=english_learning_db
DB_USERNAME=root
DB_PASSWORD=
```

`root` và mật khẩu trống là ví dụ cho một số máy Laragon/XAMPP. Thay bằng tài khoản MySQL bạn đang dùng; không phải tài khoản đăng nhập website học tiếng Anh. Nếu máy chủ hiển thị **MariaDB**, dùng `DB_CONNECTION=mariadb`. Nếu bạn đã đặt `DB_URL`, bỏ dòng đó hoặc sửa cho khớp kết nối này.

Sau khi sửa `.env`, chạy `php artisan config:clear`. File nhập đã có bảng `migrations`, nên không cần chạy seeder hoặc tạo lại bảng. Không chạy `migrate:fresh` sau khi nhập vì lệnh đó xóa các bảng.

Tài khoản demo website: `learner.demo@example.test` hoặc `author.demo@example.test`. Mật khẩu: `DemoStudy2026!`. Giao diện hiện tại của dự án vẫn là giao diện quản lý trung tâm; màn học và luyện thi chưa được triển khai.

## Đã kiểm tra

File SQL được xuất từ migration bằng bộ biên dịch MySQL của Laravel và lấy dữ liệu mẫu đã kiểm tra khóa ngoại. Bản đầy đủ mới có 37 bảng và 132 dòng dữ liệu, trong đó 16 dòng ghi nhận migration. Dữ liệu cha được nhập trước dữ liệu con, không tắt kiểm tra khóa ngoại. Máy làm việc hiện tại chưa có máy chủ MySQL/MariaDB, nên chưa chạy thử thao tác Import trên máy chủ thật. Xem [chi tiết bổ sung ngày 22/10](STUDY4-REFERENCE-SCREENS.md).

Hướng dẫn tab Import tham khảo [tài liệu chính thức phpMyAdmin](https://docs.phpmyadmin.net/en/latest/import_export.html).
