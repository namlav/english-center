# Sửa lỗi Import đã tạo một phần bảng

Lỗi **#1050 - Table 'learning_categories' already exists** nghĩa là bảng đã có, thường do lần Import trước đã chạy được một phần. Không xóa bảng đó để tiếp tục đoán từng lỗi.

Chỉ dùng file **SUA_LOI_IMPORT.sql** cho database thuộc dự án english-center trong cuộc trò chuyện này:

1. Trong phpMyAdmin, chọn đúng database website đang sử dụng. Tên này phải khớp `DB_DATABASE` trong `.env` của website.
2. Chọn **Export / Xuất** để lưu bản sao hiện tại.
3. Chọn **Import / Nhập**, chọn `SUA_LOI_IMPORT.sql`, bấm **Thực hiện**.
4. Không nhập tiếp các file cài mới/nâng cấp cũ. Kết quả cuối file hiển thị 5 bộ, lần lượt 13, 14, 8, 15 và 10 câu; tổng 60 câu với đáp án.

Tài khoản admin mẫu: **admin@englishcenter.vn** / **123456**. File tạo tài khoản này nếu chưa có, hoặc đặt lại mật khẩu của đúng email này theo yêu cầu người dùng; không thay mật khẩu các tài khoản khác.

## File sửa những gì?

- Tạo bảng còn thiếu, bỏ qua bảng đã có: đủ 39 bảng của dự án, không tính thêm những bảng riêng bạn tự tạo.
- Kiểm tra trước khi thêm từng cột, chỉ mục, khóa ngoại và quy tắc dữ liệu. Hoàn tất cả trường hợp đã tạo bảng nhưng chưa thêm đủ khóa ngoại.
- Bổ sung 4 thang điểm nếu thiếu; giữ các dòng đã có.
- Bổ sung các câu, lựa chọn và đáp án còn thiếu trong 5 bộ BC Reads, kể cả khi lần nhập trước dừng giữa một bộ. Không ghi đè nội dung câu hỏi/lời giải đã được chỉnh sửa.
- Ghi nhận 17 migration nếu chưa có, không nhân đôi các dòng migration đã tồn tại.

Không có lệnh DROP TABLE, DROP DATABASE, DELETE hoặc TRUNCATE dữ liệu. Không tắt kiểm tra khóa ngoại và không sửa các file code website. Các khóa ngoại cũ thuộc dự án có quy tắc xóa CASCADE được đổi sang RESTRICT theo migration hiện có để bảo vệ lịch sử liên quan. Các bảng hoặc ràng buộc tự tạo khác ngoài dự án không nằm trong phạm vi sửa này.

Nếu dữ liệu hiện có đã vi phạm một quy tắc (ví dụ mã trùng hoặc quan hệ không hợp lệ), file có thể dừng với thông báo cụ thể; nó không tự xóa/sửa giá trị người dùng để ép chạy qua. Khi đó giữ nguyên trạng thái và gửi toàn bộ thông báo lỗi để kiểm tra.

## Kiểm tra thực tế

Đã chạy trực tiếp trên **MariaDB 10.11.14** trong môi trường thử nghiệm riêng, không kết nối database của người dùng:

| Tình huống | Kết quả |
| --- | --- |
| Database trống | 39 bảng, 5 bộ, 60 câu/đáp án |
| Chỉ có learning_categories; nhập file cài mới tái hiện lỗi #1050 | File sửa hoàn tất thành công |
| Bản cũ 7 bảng nghiệp vụ, có dữ liệu lớp/điểm danh | Giữ nguyên các giá trị cũ |
| Import dừng sau 7 câu trong bộ đầu | Bổ sung đủ 60 câu; giữ câu hỏi/lời giải đã biên tập |
| Nhập file sửa lần hai trong cả 4 trường hợp | Không nhân đôi dữ liệu, vẫn 17 migration và 52 khóa ngoại |

Đã kiểm tra đăng nhập bằng hash mật khẩu admin 123456. Đây là kiểm tra database; không tự tạo màn hình học/làm bài. Yêu cầu MySQL 8.0.16+ hoặc MariaDB 10.4+; chưa chạy riêng trên MySQL 8.

Nguồn bài tập: BC Reads: Adult Literacy Fundamental English – Course Pack 6, Shantel Ivits, BCcampus, 2015; CC BY 4.0. https://opentextbc.ca/abealf6/ ; https://creativecommons.org/licenses/by/4.0/ . Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ . Hướng dẫn, định dạng và giải thích tiếng Việt được bổ sung trong bản chuyển đổi; nguồn và giấy phép được giữ trong dữ liệu.
