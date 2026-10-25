# Nhập 5 bộ câu hỏi có đáp án vào phpMyAdmin

File cần nhập: **bc_reads_5_bo_60_cau.sql**.

1. Mở phpMyAdmin, chọn database của dự án ở cột trái.
2. Chọn **Import / Nhập** → **Chọn tệp** → chọn `bc_reads_5_bo_60_cau.sql`.
3. Giữ định dạng SQL và bảng mã UTF-8, bấm **Import / Thực hiện**.
4. Mở bảng `exam_papers`: tìm 5 đề có đường dẫn bắt đầu bằng `bc-reads-`. Câu hỏi ở `questions`, lựa chọn ở `question_options`, đáp án và lời giải ở `question_answer_keys`.

Database cần có cấu trúc học online phiên bản 37 hoặc 39 bảng đã gửi trước đó. Nếu chưa có, nhập `english_learning_phpmyadmin.sql` trước. Nếu đang có bản cũ 27 bảng, nhập `english_learning_upgrade_20261022.sql` trước. Không cần nhập lại file cấu trúc vào database đã có đầy đủ bảng.

## Nội dung

| Bộ | Chủ đề | Số câu | Thời gian gợi ý |
| --- | --- | ---: | ---: |
| 01 | Đại từ chủ ngữ và tân ngữ | 13 | 15 phút |
| 02 | Câu hoàn chỉnh và câu thiếu thành phần | 14 | 15 phút |
| 03 | Cấu tạo từ | 8 | 10 phút |
| 04 | Hòa hợp chủ ngữ và động từ | 15 | 15 phút |
| 05 | Từ vựng theo định nghĩa | 10 | 15 phút |

Tổng: **5 bộ độc lập, 60 câu, 60 đáp án/lời giải, 63 lựa chọn trắc nghiệm**. Mỗi câu 1 điểm; không quy đổi điểm IELTS/TOEIC. Các thời gian do dự án đề xuất. Bài gốc không gắn mức CEFR nên không tự gắn A1/B1.

Có thể xem toàn bộ câu hỏi và đáp án trong `BC-READS-CAU-HOI-DAP-AN.md`. File này dành cho người quản trị; không đưa nguyên file đáp án ra trang làm bài trước khi nộp.

## Nguồn và quyền sử dụng

BC Reads: Adult Literacy Fundamental English – Course Pack 6, Shantel Ivits, BCcampus, 2015, **CC BY 4.0**:

- Sách: https://opentextbc.ca/abealf6/
- Chương 1 và đáp án: https://opentextbc.ca/abealf6/chapter/chapter-1/
- Chương 2 và đáp án: https://opentextbc.ca/abealf6/chapter/whats-your-story/
- Giấy phép: https://creativecommons.org/licenses/by/4.0/

BC Reads: Adult Literacy Fundamental English – Course Pack 6 by Shantel Ivits (2015), BCcampus, is used under CC BY 4.0.

Download for free from the B.C. Open Collection: https://collection.bccampus.ca/

Chọn bài tập và đối chiếu đáp án xuất bản trong hai chương; chuyển một số câu sang điền chỗ trống/trắc nghiệm, thêm hướng dẫn và giải thích tiếng Việt. Bộ 05 thêm ngân hàng từ để không phụ thuộc bài đọc gốc. Chấp nhận thêm các biến thể chính tả categorise, dramatise, honor. Số câu và đường dẫn nguồn được lưu trong dữ liệu; giữ phần ghi công khi hiển thị hoặc chia sẻ bài. Đây là bài luyện từ tài liệu mở, không phải đề gốc STUDY4.

## Dành cho người phát triển

Không thêm bảng mới. File chỉ chèn danh mục nền tảng (nếu thiếu), một bộ sưu tập, 5 đề, 5 phần, 5 nhóm câu, 60 câu, 63 lựa chọn và 60 đáp án. Không thêm tài khoản hay lịch sử làm bài. ID được tìm theo slug và thứ tự thay vì gán cứng, không tắt khóa ngoại. Chạy lại sẽ bỏ qua toàn bộ đề có slug đã tồn tại, giữ nội dung đã biên tập; nếu muốn thay nội dung đã nhập, sửa qua chức năng quản trị riêng.

Seeder: `php artisan db:seed --class=BcReadsPracticeSeeder`.

Xuất lại SQL sau khi seed bản dữ liệu mẫu SQLite: `php tools/export-bc-reads.php`. Có thể truyền đường dẫn SQLite ở tham số đầu tiên. Dữ liệu nguồn có cấu trúc nằm trong `database/data/bc-reads-practice.json`.

Đã kiểm thử liên kết câu hỏi/đáp án, đáp án nguồn, quyền ẩn đáp án khi xuất model và chạy lại seeder trên SQLite. File SQL sinh theo cú pháp MySQL/MariaDB; chưa thực hiện Import trên máy chủ MySQL/MariaDB thật. Database đã có dữ liệu quan trọng nên được sao lưu trước khi nhập. Việc nhập dữ liệu chưa tự tạo màn hình làm bài trên website.
