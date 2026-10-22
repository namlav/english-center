# Cập nhật trang chủ và thông tin khóa học

Gói dùng: **cap_nhat_trang_chu_khoa_hoc.zip**. Đây là bản thay cho gói trang chủ trước, dành cho dự án English Center đã có database 39 bảng.

## Cách cài

1. Sao lưu thư mục website hiện tại trước khi chép đè code.
2. Giải nén gói cập nhật vào đúng thư mục dự án chứa **artisan** và **composer.json**, đồng ý thay thế các tệp trùng tên. Các thư mục app, public, resources, routes, database và tests nằm cùng cấp với artisan.
3. Giữ nguyên **.env**, **vendor** và cấu hình kết nối MySQL của bạn. Database trong ảnh trước là **english_center_db**. Không cần Import SQL lại, không cần xóa bảng và không chạy `migrate:fresh`.
4. Mở terminal tại thư mục dự án, chạy:

```sh
php artisan optimize:clear
php artisan serve
```

Mở địa chỉ terminal hiển thị, thường là **http://127.0.0.1:8000**. Nếu đang dùng Apache/XAMPP thì mở lại địa chỉ website đang chạy. Giao diện dùng CSS/JS có sẵn trong public, không cần npm hoặc Vite. Nếu dự án chưa có vendor thì cài đúng composer.lock bằng `composer install`.

## Những gì đã làm

- Trang chủ nền sáng, màu xanh, banner ảnh thật, menu máy tính/điện thoại và chân trang nhiều cột theo bố cục ảnh tham khảo.
- 24 khóa học tham khảo: 5 combo, 2 TOEIC, 5 IELTS Academic, 2 IELTS General, 5 tiếng Anh cơ bản và 5 HSK. Mỗi khóa có ảnh bìa thật, tên, học phí và số liệu đánh giá tại nguồn.
- Chọn danh mục, tìm khóa học theo từ khóa và mở trang thông tin riêng tại `/khoa-hoc/course-15` hoặc các mã khóa khác.
- Trang chi tiết có banner, ảnh bìa, giá, mục tiêu, thông số chương trình, đề mục học công khai, các khóa trong combo và khóa liên quan. Khóa có video giới thiệu công khai sẽ có liên kết YouTube.
- Ví dụ IELTS Listening: 799.000đ, 210 giờ, 198 bài học, 739 bài tập theo thông tin nguồn được ghi nhận.
- Giữ phần 5 bộ bài luyện/60 câu từ database hiện có; mở câu hỏi, tự kiểm tra và xem đáp án/lời giải. Menu và khu quản lý trung tâm cũ tiếp tục dùng các đường dẫn có sẵn.

## Nguồn và phạm vi chức năng

Ảnh bìa, ảnh banner và thông tin khóa học lấy từ các trang giới thiệu công khai của STUDY4, ghi nhận ngày **22/10/2026**:

- https://study4.com/courses/online/
- https://study4.com/courses/15/ielts-intensive-listening/

Mỗi khóa có liên kết tới trang gốc. Giá, lượt học và lượt đánh giá được ghi rõ là thông tin tại STUDY4; đây không phải các đơn hàng hoặc học viên của English Center. Nút xem khóa học mở website nhà cung cấp. Không có chức năng thu tiền hoặc cấp quyền vào khóa STUDY4 trên website của bạn. Nội dung bài học trả phí và ảnh hội thoại riêng của học viên không nằm trong gói.

Danh mục tham khảo được kèm sẵn trong **database/data/study4-course-catalog.json**, ảnh lưu trong **public/images/catalog/study4/** nên hiển thị ngay mà không phải nhập thêm SQL. Nếu các bản ghi tương ứng đã có trong reference_pages/reference_images thì website ưu tiên phần tiêu đề, mô tả và thông tin bạn chỉnh trong database.

Tùy chọn cho người phát triển: chạy `php artisan db:seed --class=PublicCourseCatalogSeeder` để lưu 24 bản ghi tham khảo vào hai bảng reference hiện có. Không bắt buộc để dùng giao diện. Seeder có thể chạy lại và giữ những chỉnh sửa đã có; không tạo tài khoản, lượt đăng ký hoặc khóa học bán hàng giả.

Phần luyện bài miễn phí hiện trả điểm và lời giải sau khi nộp, chưa lưu lịch sử làm bài hoặc tiến độ. Quản trị khóa học của riêng English Center, thanh toán và cấp quyền học là các chức năng tiếp theo.

## Kiểm tra

148 kiểm thử / 1.063 assertions đạt, gồm toàn bộ chức năng cũ, 24 trang khóa học, ảnh bìa đúng định dạng và mã kiểm tra tệp, tìm kiếm, nguồn thông tin, seeder chạy lặp và các bài luyện.

Đã kiểm tra trình duyệt ở 1440px và 390px: không tràn ngang, menu điện thoại mở/đóng, danh mục chuyển đúng, tìm IELTS Intensive Listening ra một khóa, trang chi tiết hiển thị ảnh/giá/thông số. Không thấy lỗi console trong lượt kiểm tra. Kiểm tra giao diện dùng database thử nghiệm riêng, không sửa MySQL của bạn.

Gói không chứa .env, vendor, dữ liệu SQLite thử nghiệm hoặc file SQL. Các migration đi kèm là mã nguồn của cấu trúc đã có; chép chúng vào dự án không tự chạy thay đổi database.
