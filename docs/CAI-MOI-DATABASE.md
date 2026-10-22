# Cài mới database đầy đủ, sửa trường hợp thiếu bảng courses

Lỗi `#1146 - Table 'english_center_db.courses' doesn't exist` nghĩa là database được chọn chưa có bảng courses của dự án gốc. File nâng cấp 7 → 39 bảng không dùng được cho database thiếu các bảng gốc. Lần Import thất bại có thể đã tạo một số bảng mới; không tiếp tục nhập chồng các file.

Dùng file **CAI_MOI_DAY_DU_39_BANG.sql** cho một database mới, rỗng:

1. Trong phpMyAdmin, bấm **Mới / New**.
2. Tạo database **english_center_online**, bảng mã **utf8mb4_unicode_ci**. Nếu tên này đã có và chứa bảng, chọn một tên mới khác.
3. Chọn database mới ở cột bên trái → **Import / Nhập** → chọn `CAI_MOI_DAY_DU_39_BANG.sql` → **Thực hiện**.
4. File đã có đủ 39 bảng, gồm bảng gốc, bảng học online, 5 bộ với 60 câu hỏi, 60 đáp án, 63 lựa chọn và một tài khoản admin mẫu. Không nhập thêm file nâng cấp hay file 60 câu.
5. Trong file `.env` ở thư mục website đang chạy, đặt:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=english_center_online
DB_USERNAME=root
DB_PASSWORD=
```

Giữ đúng tài khoản và mật khẩu MySQL của máy bạn nếu chúng khác ví dụ. Nếu đã chọn tên database khác ở bước 2, dùng chính tên đó trong DB_DATABASE. Nếu website đã lưu cấu hình cũ, chạy `php artisan config:clear` ở thư mục website rồi tải lại trang.

Đăng nhập thử: **admin@englishcenter.vn** / **123456**. Nếu đã Import bản cũ có mật khẩu DemoStudy2026!, chỉ Import thêm `DOI_MAT_KHAU_ADMIN_123456.sql` vào đúng database đang dùng; không Import lại toàn bộ file cài mới. Đổi mật khẩu tài khoản mẫu khi đưa website vào sử dụng. Database mới chỉ có một tài khoản admin; các dữ liệu lớp và tài khoản ở database cũ không tự được chuyển sang. Giữ database cũ để có thể đối chiếu hoặc chuyển dữ liệu sau.

Đây là thay đổi cấu hình kết nối, không phải thay đổi cấu trúc code. Các trang quản lý trung tâm có sẵn đọc database mới; các màn hình học online/làm bài vẫn cần triển khai thêm code.

## Những file không dùng cho lần cài mới này

Không nhập `nang_cap_github_7_bang_len_39_bang.sql`, `english_learning_upgrade_20261022.sql`, `english_learning_phpmyadmin.sql` hoặc `bc_reads_5_bo_60_cau.sql` sau file cài mới. Các file này phục vụ những trạng thái database khác nhau. `study4_public_content.sql` là gói tham khảo ảnh/giới thiệu riêng, không cần để có 5 bộ câu hỏi.

File mới không xóa database cũ, không chứa lệnh DROP TABLE hoặc DROP DATABASE. Phần học online có đủ bảng nhưng chưa có khóa học thương mại hay hình ảnh mẫu. Không gọi `migrate:fresh` sau khi nhập vì lệnh đó xóa dữ liệu. Đã kiểm tra cài mới, tài khoản admin, số câu và khóa ngoại trên SQLite trong bộ nhớ; chưa thực hiện Import trên máy chủ MySQL/MariaDB thật. Yêu cầu MySQL 8.0.16+ hoặc MariaDB 10.4+.

Nguồn 60 câu: BC Reads: Adult Literacy Fundamental English – Course Pack 6, Shantel Ivits, BCcampus, 2015, CC BY 4.0. https://opentextbc.ca/abealf6/ ; https://creativecommons.org/licenses/by/4.0/ . Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ . Các bài được chuyển định dạng, thêm hướng dẫn và giải thích tiếng Việt; giữ nguồn và giấy phép trong dữ liệu.
