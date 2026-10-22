# Tạo các bảng học online bằng một file SQL

File: **nang_cap_github_7_bang_len_39_bang.sql**.

Dành cho bản `namlav/english-center`, nhánh `dev`, đã kiểm tra ở commit `4f28a139ca46fa29c3f0e448c0f05df53c648bef`. Database đang có 7 bảng nghiệp vụ: users, teachers, students, courses, class_rooms, enrollments, attendances; và bảng migrations của Laravel. Các migration cũ, gồm ngày kết thúc dự kiến của lớp và quy tắc quản lý trung tâm, đã được chạy.

## Nhập trong phpMyAdmin

1. Chọn database đang dùng, ví dụ `english_center_db`.
2. Chọn **Export / Xuất** để lưu một bản sao database hiện tại.
3. Chọn **Import / Nhập** và chọn `nang_cap_github_7_bang_len_39_bang.sql`.
4. Bấm **Thực hiện**. Chỉ nhập file này một lần.

File thêm 31 bảng mới, thành 39 bảng tính cả migrations; mở rộng bảng courses; thêm 4 thang điểm; thêm 5 bộ luyện với 60 câu hỏi, đáp án và giải thích. Các bảng tham khảo hình ảnh được tạo sẵn nhưng để trống; file này không cần chép ảnh vào website. Dữ liệu tài khoản, giáo viên, học viên, khóa học, lớp, ghi danh, điểm và điểm danh cũ được giữ nguyên. Không tạo tài khoản demo hoặc lịch sử làm bài mẫu. Không có lệnh xóa bảng hay xóa dữ liệu.

**Không dùng file này cho database rỗng hoặc database đã được nâng cấp lên 27/37/39 bảng.** Nếu đã có đầy đủ bảng, chỉ cần file `bc_reads_5_bo_60_cau.sql` để thêm bộ câu hỏi, không nhập bản nâng cấp này. Nếu Import báo lỗi, dừng và kiểm tra trạng thái đã chạy trước khi thao tác tiếp; thay đổi cấu trúc MySQL không được hoàn tác toàn bộ bằng một transaction.

## Có thay đổi code không?

Import SQL chỉ thay đổi database. Nó không sửa các file PHP, giao diện, routes hoặc cấu trúc thư mục của website. Các cột mới trên bảng courses có giá trị mặc định hoặc được phép để trống để phù hợp với thao tác khóa học cũ.

Website hiện tại tiếp tục có các màn hình quản lý trung tâm. Để người học thấy thư viện đề, bấm làm bài, nộp bài, nhận điểm, xem lời giải hoặc học khóa online, vẫn cần bổ sung code giao diện và xử lý cho các chức năng đó. Những chức năng mới không tự xuất hiện sau khi nhập SQL.

Các khóa ngoại mới bảo vệ dữ liệu liên quan: nếu sau này một khóa học hoặc tài khoản đã có dữ liệu học online, thao tác xóa từ code cũ có thể bị database chặn. Khi bổ sung giao diện học online, cần xử lý thông báo này hoặc chuyển sang ngừng hoạt động thay vì xóa dữ liệu có lịch sử.

## Nguồn câu hỏi và kiểm tra

5 bộ: đại từ 13 câu; câu hoàn chỉnh 14 câu; cấu tạo từ 8 câu; hòa hợp chủ ngữ/động từ 15 câu; từ vựng 10 câu. Nguồn: BC Reads: Adult Literacy Fundamental English – Course Pack 6, Shantel Ivits, BCcampus, 2015. CC BY 4.0: https://opentextbc.ca/abealf6/ ; https://creativecommons.org/licenses/by/4.0/ . Download for free from the B.C. Open Collection: https://collection.bccampus.ca/ . Đã chuyển định dạng bài, bổ sung hướng dẫn và giải thích tiếng Việt; không quy đổi điểm IELTS/TOEIC.

SQL được biên dịch bằng MySQL grammar từ các migration bổ sung, giữ khóa ngoại, không gán cứng ID câu hỏi. Đã kiểm tra bộ câu hỏi qua SQLite và các kiểm thử dự án. Chưa Import trên máy chủ MySQL/MariaDB thật. Yêu cầu MySQL 8.0.16+ hoặc MariaDB 10.4+ để thực thi các ràng buộc dữ liệu.
