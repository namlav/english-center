# Cập nhật giao diện trang chủ English Center

Gói **cap_nhat_trang_chu.zip** dùng cho dự án Laravel english-center đang có database 39 bảng đã Import thành công. Gói này là **code giao diện và xử lý**, không phải file SQL.

## Cách dùng trên website hiện tại

1. Sao lưu thư mục code website đang chạy.
2. Giải nén `cap_nhat_trang_chu.zip`. Chép các thư mục **app**, **public**, **resources**, **routes** vào đúng thư mục website đang có file **artisan** và **composer.json**; đồng ý thay thế các tệp trùng tên.
3. Giữ nguyên file **.env** và thư mục **vendor** của website. Gói cập nhật không chứa hoặc thay các phần này. Không cần Import lại SQL và không cần tạo thêm bảng.
4. Kiểm tra `DB_DATABASE` trong `.env` trùng tên database bạn đang sử dụng trong phpMyAdmin. Ảnh trước đó đang hiển thị database `english_center_db`; nếu bạn dùng tên khác thì giữ đúng tên thực tế đó.
5. Mở terminal ở thư mục có file artisan, chạy:

```sh
php artisan optimize:clear
php artisan serve
```

Mở địa chỉ được terminal hiển thị, thường là **http://127.0.0.1:8000**. Nếu đang chạy qua Apache/XAMPP, mở lại đường dẫn website hiện tại sau khi xóa bộ nhớ cấu hình. Trang `/` nay là trang chủ học online; khu quản lý lớp vẫn ở `/class-rooms` và yêu cầu đăng nhập như cũ.

Nếu chưa có vendor, cài các gói theo composer.lock của dự án bằng `composer install` trước. Giao diện mới dùng CSS/JS trong public nên không cần cài Node, chạy npm hay Vite.

## Đã có trong bản này

- Trang chủ công khai mang thương hiệu English Center, có phần giới thiệu, hình minh họa, hướng dẫn học và chân trang.
- Số bộ đề, số câu hỏi và danh sách bài lấy từ database. Với dữ liệu đã Import trong ảnh sẽ hiện 5 bộ và 60 câu.
- Tìm kiếm theo từ khóa và lọc danh mục; tìm nhanh theo chủ đề; phân trang khi có nhiều bộ; trạng thái khi chưa có bài hoặc không tìm thấy.
- Bố cục máy tính và điện thoại, menu thu gọn, thao tác bàn phím và liên kết bỏ qua menu để đến nội dung.
- Nút mở bài dẫn đến trang câu hỏi thật, có hướng dẫn, điền câu trả lời hoặc chọn lựa chọn, tự kiểm tra và xem lời giải sau khi nộp.
- Tự kiểm tra áp dụng các câu có đáp án dạng điền từ, một/nhiều lựa chọn, đúng/sai. Tôn trọng quy tắc phân biệt hoa/thường, các đáp án được chấp nhận và điểm tối đa trong database. Câu chưa làm được tính chưa đúng.
- Đáp án và lời giải không được gửi trong trang câu hỏi trước khi người học bấm tự kiểm tra. Chỉ các bộ đã xuất bản, đến ngày công bố và thuộc danh mục/bộ sưu tập đang hoạt động mới được truy cập công khai.
- Đăng nhập tiếp tục dùng trang có sẵn; người đã đăng nhập có liên kết về quản lý lớp và nút đăng xuất. Nhấn logo trong khu quản lý để trở về trang chủ.

Phần tự kiểm tra hiện hiển thị điểm/lời giải trong lần gửi đó, **chưa lưu lịch sử làm bài hoặc đếm ngược thời gian**. Thời lượng trên thẻ là thời gian gợi ý. Bài nói/viết chưa được chấm tự động. Quản trị nội dung online, tiến độ khóa học, flashcard và thanh toán là các bước tiếp theo.

## Các tệp chính

- `routes/web.php`: trang chủ và hai đường dẫn xem/tự kiểm tra bài.
- `app/Http/Controllers/HomeController.php`: danh sách, số lượng và tìm kiếm.
- `app/Http/Controllers/PracticeController.php`: đọc câu hỏi và tự kiểm tra.
- `resources/views/layouts/learning.blade.php`, `resources/views/learning/`: giao diện công khai.
- `resources/views/components/study-icon.blade.php`: biểu tượng SVG dùng chung.
- `public/css/learning.css`, `public/js/learning.js`, `public/images/english-center-mark.svg`: giao diện và menu, không phụ thuộc dịch vụ ngoài.
- Gói kèm các model của database đã bổ sung, vì bản GitHub gốc chưa có các model đề thi/câu hỏi.

Các model được cập nhật và file routes/layout cần được đối chiếu trước nếu bạn đã tự sửa khác bản trong cuộc trò chuyện này. Không có thay đổi composer.json/composer.lock, database hoặc dữ liệu cá nhân trong gói cập nhật.

## Xác minh

143 kiểm thử / 851 assertions đạt trên SQLite thử nghiệm, gồm chức năng trung tâm cũ, dữ liệu học online, trang chủ, tìm kiếm, ẩn đề nháp/chưa công bố, không lộ đáp án trước khi gửi, đáp án sai/để trống/biến thể chính tả và từ chối câu trả lời thuộc bộ khác.

Đã mở bằng trình duyệt, kiểm tra máy tính 1440px và điện thoại 390px: hiển thị đúng 5 bộ/60 câu, không tràn ngang; menu mở/đóng; tìm một bộ theo từ khóa; trả lời 2/8 câu và nhận điểm cùng lời giải đúng; không ghi lượt làm bài giả. Không thấy lỗi console trong lượt kiểm tra. Database MySQL của bạn không bị truy cập hoặc sửa trong quá trình xây giao diện này.
