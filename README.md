# Nền tảng học tiếng Anh và luyện thi

Laravel 13 / PHP 8.3+. Đợt chuyển đổi đầu tiên bổ sung nền dữ liệu học online và luyện thi, dựa trên khảo sát các trang công khai của STUDY4. Giao diện hiện tại vẫn là giao diện quản lý trung tâm của dự án gốc.

Xem [phân tích, mô hình dữ liệu và kế hoạch chuyển đổi](docs/STUDY4-DATABASE.md).

Bổ sung ngày 22/10/2026: [hồ sơ học tập, mục tiêu điểm, giảng viên và bộ lọc thư viện đề](docs/STUDY4-REFERENCE-SCREENS.md).

Đã thu thập [nội dung công khai và 8 ảnh tham khảo STUDY4](docs/NHAP-NOI-DUNG-STUDY4.md). Sau bản nền 37 bảng, nhập thêm `database/sql/study4_public_content.sql` và chép các ảnh trong `public/images/references/study4` để thêm 2 bảng dữ liệu tham khảo. Với Laravel, có thể chạy migration và `Study4PublicReferenceSeeder` thay cho nhập SQL bổ sung.

## Dùng phpMyAdmin

Tạo database mới, rỗng tên `english_learning_db`, rồi dùng tab **Nhập / Import** để nhập [file SQL MySQL](database/sql/english_learning_phpmyadmin.sql). Xem [hướng dẫn từng bước](docs/NHAP-PHPMYADMIN.md). File này đã chứa bảng và dữ liệu demo, không cần chạy seeder sau khi nhập.

Nếu đã nhập bản 27 bảng trước đó, chọn database hiện tại và nhập [file nâng cấp ngày 22/10](database/sql/english_learning_upgrade_20261022.sql) một lần. Bản nâng cấp thêm 10 bảng và giữ dữ liệu cũ. Không dùng cả hai file trên cùng một database.

## Chạy cơ sở dữ liệu trên máy phát triển

```sh
composer install
```

Sao chép `.env.example` thành `.env`, tạo một database MySQL phát triển riêng và sửa các giá trị `DB_*` cho database đó. Chạy:

```sh
php artisan key:generate
php artisan migrate
php artisan db:seed --class=LearningReferenceScreensSeeder
```

Seeder online chỉ chạy khi `APP_ENV=local` hoặc `testing`. Có thể chạy lại; các dòng hiện có và lịch sử làm bài không bị viết đè. Seeder mặc định `DatabaseSeeder` vẫn dành riêng cho dữ liệu quản lý trung tâm cũ, không tự chạy seeder online.

Dữ liệu demo mới: 3 danh mục, 3 khóa online, 10 bài học, 4 đề với tổng 8 câu, 1 lượt làm bài có 4 câu trả lời, 1 bộ từ vựng với 3 thẻ và 3 mục tiêu điểm minh họa. Toàn bộ nội dung được tự biên soạn để kiểm tra lưu trữ; không phải đề thi chuẩn. `LearningReferenceScreensSeeder` gọi seeder online cơ bản rồi bổ sung dữ liệu tham khảo.

Tài khoản phát triển: `learner.demo@example.test` (học viên), `author.demo@example.test` (biên soạn); mật khẩu của cả hai là `DemoStudy2026!`. Các tài khoản này không bắt buộc có hồ sơ lớp học cũ.

Nếu dùng SQLite 3.35+ để thử nhanh, tạo một file SQLite rỗng và đặt `DB_CONNECTION=sqlite`, `DB_DATABASE` bằng đường dẫn tuyệt đối tới file đó. Bỏ `DB_URL` nếu đang được đặt, sau đó chạy các lệnh migration/seeder ở trên. MySQL 8.0.16+ được yêu cầu để thực thi CHECK; kiểm thử mặc định dùng SQLite với trigger tương đương.

## Kiểm thử

```sh
php artisan test --filter=LearningDatabaseTest
php artisan test
```

Các kiểm thử dùng SQLite trong bộ nhớ riêng, không chạy vào database phát triển. Có kiểm tra khóa ngoại ghép, dữ liệu không hợp lệ, đáp án riêng, snapshot, lịch ôn, chạy lại seeder, hồ sơ/mục tiêu và rollback rồi migrate lại mà giữ dữ liệu cũ. Kết quả ngày 22/10/2026: 130 kiểm thử đạt.

Trên database có dữ liệu cần giữ, sao lưu trước khi nâng cấp và dùng `php artisan migrate`. Lệnh `migrate:fresh` trong tài liệu cũ sẽ xóa các bảng, không phải cách nâng cấp. Rollback các migration online cũng xóa dữ liệu online của chúng; kiểm thử rollback chỉ chạy trên database thử nghiệm.

## 5 bộ bài tập có đáp án từ tài liệu mở

Bổ sung 5 bộ BC Reads với 60 câu hỏi, đáp án và lời giải tiếng Việt, được phép tái sử dụng theo CC BY 4.0. Nhập `database/sql/bc_reads_5_bo_60_cau.sql` vào database đã có cấu trúc học online, hoặc chạy `php artisan db:seed --class=BcReadsPracticeSeeder`. Xem `docs/NHAP-BO-CAU-HOI.md` để biết nguồn và cách nhập. Dữ liệu này bổ sung riêng, không thay đổi các câu demo cũ và không tự tạo giao diện làm bài.

## Bước tiếp theo

Xây dựng trang chủ học online, thư viện đề, dịch vụ bắt đầu/lưu/nộp/chấm bài và lịch sử kết quả, sau đó triển khai bài học, flashcard và quản trị nội dung. Cấu trúc dữ liệu đợt này chưa tự cung cấp các màn hình hoặc dịch vụ đó.
