# Cập nhật QR chuyển khoản ngân hàng

QR dùng thông tin được đọc từ ảnh bạn gửi: MB Bank, số tài khoản 0389279885; tên chủ tài khoản NGUYEN QUOC TRUNG theo bạn cung cấp. Mã QR được lấy từ VietQR Quick Link dạng SVG, gồm số tiền đơn hàng và nội dung chuyển khoản riêng. Xem [tài liệu Quick Link VietQR](https://www.vietqr.io/qr-dong).

## Cập nhật vào website

1. Giải nén `cap_nhat_qr_ngan_hang.zip` vào một thư mục tạm.
2. Chép các thư mục `app`, `resources`, `routes`, `public`, `database` vào thư mục dự án Laravel có file `artisan`, chọn thay thế file trùng tên. Giữ nguyên `.env`.
3. Trong phpMyAdmin, chọn đúng database đang dùng, mở **Import**, chọn duy nhất `CAP_NHAT_QR_NGAN_HANG.sql` trong gói và bấm **Import/Thực hiện**. File nâng cấp giữ dữ liệu cũ, không xóa đơn hàng/khóa học và không sửa mật khẩu.
4. Đăng nhập tài khoản admin, mở **Quản lý website → QR ngân hàng**. Kiểm tra ngân hàng MB, số tài khoản và tên chủ tài khoản.
5. Tạo đơn mới thử giao diện QR. Không cần thanh toán đơn thử. Admin chỉ xác nhận đơn thật sau khi kiểm tra giao dịch trong tài khoản ngân hàng.

Người học bấm **Tôi đã chuyển khoản** chỉ gửi thông báo cho admin. Việc quét QR và gửi thông báo không tự xác nhận tiền, không mở khóa học. Admin phải đối chiếu đúng số tiền và nội dung trước khi xác nhận. Mỗi lần tạo QR mới sinh mã chuyển khoản khác và lưu cả mã cũ để admin tra lại.

Thông tin tên chủ tài khoản không có trong mã QR ban đầu; trang cài đặt admin cho phép sửa tên nếu ngân hàng hiển thị khác. Thay đổi thông tin áp dụng cho đơn mới, QR đã phát hành giữ nguyên người nhận.
