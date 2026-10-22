# Nhập nội dung và ảnh tham khảo từ STUDY4

Đã lấy thông tin công khai từ [TOEIC Speaking & Writing](https://study4.com/courses/36/toeic-sw/), [Complete TOEIC](https://study4.com/courses/28/complete-toeic/) và [trang giới thiệu đề Listening](https://study4.com/tests/6842/ielts-collection-1-listening-test-1/).

Gói này có 3 bản ghi trang tham khảo, gồm tiêu đề, giới thiệu ngắn được viết lại, chủ đề mục lục và thông tin đề. Có 8 file ảnh WebP: 2 bìa khóa học và 6 ảnh minh họa công khai. Mỗi ảnh có đường dẫn nguồn, đường dẫn trong dự án, kích thước và mã kiểm tra file. Không lấy ảnh người học trong phần đánh giá, mã theo dõi hoặc nút mạng xã hội.

## Cách dùng với phpMyAdmin

1. Giải nén `study4-noi-dung-va-hinh-anh.zip`.
2. Trong phpMyAdmin, chọn database dự án đã có 37 bảng của bản trước, vào **Nhập / Import**, chọn `study4_public_content.sql` trong gói vừa giải nén.
3. Chép thư mục `public/images/references/study4` từ gói vào đúng thư mục `public/images/references/study4` của dự án Laravel (thư mục gốc dự án có file `artisan`).
4. Trong phpMyAdmin sẽ có thêm `reference_pages` và `reference_images`. Database lúc này có 39 bảng. Xem `reference_pages` để đọc thông tin, `reference_images.local_path` để lấy đường dẫn ảnh.

Nếu database vẫn ở bản 27 bảng, nhập file nâng cấp `english_learning_upgrade_20261022.sql` trước. Nếu tạo mới, nhập `english_learning_phpmyadmin.sql` (bản nền 37 bảng) trước rồi nhập `study4_public_content.sql`. File nội dung này bổ sung dữ liệu, không cần xóa database hoặc nhập lại bản nền.

Ảnh là file trên ổ đĩa; database lưu đường dẫn, không chứa toàn bộ ảnh dạng BLOB. Chỉ nhập SQL mà không chép các file ảnh thì giao diện không có ảnh để hiển thị. Việc hiện dữ liệu này lên trang học cần nối phần giao diện với các bảng tham khảo; chưa có giao diện đó trong đợt database.

## Phân biệt dữ liệu tham khảo và nội dung học thật

- `reference_pages` ghi rõ nguồn STUDY4 và thời điểm thu thập. Đây là dữ liệu tham khảo từ website bên ngoài; không thay thế các khóa học và đề tự biên soạn đang có.
- Số câu và số phần ở metadata là thông tin được công bố ở trang nguồn. Chưa nhập 40 câu hỏi, đáp án hay âm thanh của đề thi. `questions_imported` được ghi là 0.
- Không truy cập các bài học cần đăng nhập. Không đưa toàn bộ giáo trình, lời giải, video hoặc tài khoản cá nhân vào gói này. Có thể nhập thêm nội dung gốc do người dùng cung cấp vào cấu trúc học/luyện thi hiện có.
- Các ảnh giữ thương hiệu/nguồn gốc của website tham khảo; gói không gán chúng là ảnh do dự án tự tạo.

## Dành cho người chạy Laravel

```sh
php artisan migrate
php artisan db:seed --class=Study4PublicReferenceSeeder
```

Seeder đọc dữ liệu đã thu thập trong `database/data/study4-public-reference.json`, không gửi yêu cầu mạng. Seeder kiểm tra file ảnh thật trước khi lưu metadata. Chạy lại không tạo bản ghi trùng và không ghi đè các trường đã chỉnh sửa. SQL bổ sung cũng dùng khóa duy nhất để giữ bản ghi hiện có.

Kiểm tra: 133 kiểm thử / 411 assertions đạt trên SQLite; các file ảnh đã kiểm tra định dạng, kích thước, hash và xem trực tiếp hai ảnh bìa. Chưa nhập thử file SQL trên máy chủ MySQL/MariaDB thật.
