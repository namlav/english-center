# Bổ sung database theo các màn hình tham khảo ngày 22/10/2026

Phạm vi được người dùng chọn: **bổ sung database MySQL**, chưa triển khai giao diện. Bản này thêm 10 bảng bằng 3 migration, giữ các bảng và dữ liệu cũ.

## Màn hình được đối chiếu

- Ảnh người dùng gửi: danh sách đề dạng thẻ và biểu mẫu hồ sơ trước khi vào khóa học.
- [Thư viện đề](https://study4.com/tests/): tìm kiếm đề, thông tin thời lượng, số phần, số câu và nhãn kỹ năng.
- [Chi tiết đề Listening](https://study4.com/tests/6842/ielts-collection-1-listening-test-1/): chọn phần luyện, chọn thời gian, full test và nhãn dạng câu hỏi của từng phần.
- [Trang giới thiệu TOEIC Speaking & Writing](https://study4.com/courses/36/toeic-sw/): mục tiêu học, nhiều giảng viên và nội dung chia theo hai kỹ năng.
- [Trang học Complete TOEIC](https://study4.com/courses/28/complete-toeic/learn/): truy cập công khai chuyển đến đăng nhập; phần biểu mẫu và thanh mục lục được đối chiếu từ ảnh người dùng gửi. Không truy cập nội dung khóa học trả phí.

## Ánh xạ vào database

| Thông tin trên màn hình | Nơi lưu |
| --- | --- |
| Họ tên, email, số điện thoại | `users.name`, `users.email`, `users.phone` có sẵn |
| “Bạn đang là” | `learner_profiles.occupation`, duy nhất theo tài khoản |
| Lý do học và thời điểm hoàn tất hồ sơ cho từng khóa | `course_learning_profiles` |
| Đồng ý nhận tư vấn | `consultation_opt_in`, `consented_at` trong hồ sơ khóa học |
| Điểm hiện tại, điểm mục tiêu, ngày dự kiến thi | `course_learning_goals`, gắn hồ sơ và thang điểm |
| Phân biệt thang điểm L&R, Speaking, Writing, IELTS | `learning_score_scales` |
| Khóa học có nhiều giảng viên | `course_instructors` |
| Khóa học có nhiều kỹ năng | `course_skills` |
| Danh sách kết quả đạt được sau khóa học | `courses.learning_outcomes` dạng JSON array |
| Bộ đề và năm xuất bản | `exam_collections`, `exam_papers.exam_collection_id` |
| Nhãn dạng câu hỏi/chủ đề/trình độ của phần thi | `exam_tags`, `exam_section_tags` |
| Bài học liên kết đến đề luyện Speaking/Writing hoặc bài luyện khác | `lesson_exam_papers` |
| Cấu trúc hiển thị nhóm câu hỏi: bảng, nhãn, yêu cầu ghi âm | `question_groups.response_format`, `rendering_data`, `answer_word_limit` |

Các phần đã có từ bản trước tiếp tục dùng: mục lục `course_sections` → `lessons`; cờ học thử `lessons.is_preview`; quyền học và thời hạn trong `course_enrollments`; nội dung đề → phần → nhóm → câu hỏi; các lượt làm bài và câu trả lời có bản chụp.

`rendering_data` chỉ chứa cấu trúc hiển thị công khai. Đáp án, rubric và transcript dùng để chấm vẫn nằm trong dữ liệu riêng, không đưa vào cấu trúc hiển thị.

## Quy tắc hồ sơ và mục tiêu

- Hồ sơ chung chỉ lưu nghề nghiệp/nhóm người học. Không tạo bản sao họ tên, email và điện thoại ở nhiều bảng.
- Mỗi tài khoản chỉ có một hồ sơ cho mỗi khóa. Hồ sơ hoặc điểm mục tiêu không tự cấp quyền học và không tự tạo thanh toán.
- Mỗi hồ sơ có thể có nhiều mục tiêu, mỗi mục tiêu theo một thang điểm. Speaking và Writing được lưu riêng, không gộp vào thang L&R 990 điểm.
- Người chưa có điểm hiện tại để `current_score=NULL`; với L&R không dùng 0 thay cho “chưa thi”. Có thể lưu riêng điểm hiện tại hoặc mục tiêu, nhưng không tạo dòng mục tiêu nếu cả hai đều trống.
- Các cột giới hạn và bước điểm trong mục tiêu có khóa ngoại ghép đến `learning_score_scales`. CHECK/trigger kiểm tra cả khoảng điểm và bước điểm; không thể sửa giới hạn ở một mục tiêu để bỏ qua kiểm tra.
- Mặc định không đồng ý nhận tư vấn. Chỉ lưu đồng ý cùng thời điểm khi người học chủ động chọn; rút lại thì đặt false và bỏ thời điểm đồng ý hiện tại. Đây là trạng thái hiện tại, chưa phải hệ thống nhật ký đồng ý qua nhiều lần thay đổi.
- `profile_completed_at` để trống cho đến khi backend xác nhận đủ các trường bắt buộc. Database không tự đánh dấu hoàn tất từ việc có một mục tiêu điểm.

| Mã thang điểm | Khoảng | Bước |
| --- | --- | --- |
| `toeic_lr` | 10–990 | 5 |
| `toeic_speaking` | 0–200 | 10 |
| `toeic_writing` | 0–200 | 10 |
| `ielts_band` | 0–9 | 0,5 |

Thang điểm tham khảo [ETS Global về L&R](https://www.etsglobal.org/gf/en/content/understanding-toeic-tests-scores), [ETS về Speaking/Writing và bước điểm](https://www.ets.org/content/dam/ets-org/pdfs/toeic/toeic-mapping-cefr-reference.pdf), [IELTS về thang điểm và nửa band](https://ielts.org/cdn/ielts-guides/guide-to-ielts-scores-2025.pdf). Các thang này phục vụ nhập mục tiêu; không phải bảng tự quy đổi số câu đúng thành điểm thi chính thức.

## Bộ lọc và thông tin trên thẻ đề

`ExamPaper::published()->catalogFilter(...)` hỗ trợ kết hợp `keyword`, `category` (slug), `skill`, `collection` (slug), `tag` (slug). Từ khóa tìm trong tên đề, tên bộ đề và nhãn phần thi. Dùng điều kiện tồn tại để không nhân đôi đề khi nhiều phần cùng mang một nhãn. Bộ đề bị tắt sẽ không xuất hiện qua scope công khai.

Số phần lấy từ `sections()->count()`, số câu từ các phần và câu hỏi liên quan; thời lượng lấy `duration_seconds / 60`. Số lượt làm và bình luận lấy từ bảng lịch sử/bình luận thực tế. Không chép các số liệu học viên hoặc lượt luyện hiển thị trên STUDY4 vào dữ liệu demo.

Liên kết bài học/đề luyện cho phép dùng cấu trúc câu hỏi `speaking` và `essay` có sẵn. Câu trả lời dạng JSON có thể lưu nội dung viết hoặc tham chiếu file ghi âm riêng do backend quản lý. Việc upload, kiểm tra file, bấm thời gian, chấm thủ công/AI và báo cáo kết quả chưa được triển khai ở đợt database này.

## SQL để nhập bằng phpMyAdmin

- **Đã nhập file 27 bảng gửi trước đó:** chọn database đang dùng, nhập `database/sql/english_learning_upgrade_20261022.sql` **một lần**. File thêm 10 bảng, cột, chỉ mục, ràng buộc và 4 thang điểm hệ thống; không xóa/ghi đè dữ liệu người học và không thêm hồ sơ demo vào database đó. Có ghi nhận 3 migration mới để Laravel không chạy lại chúng.
- **Chưa nhập database:** tạo database mới, rỗng rồi nhập `database/sql/english_learning_phpmyadmin.sql`. Bản đầy đủ hiện có 37 bảng và dữ liệu demo mới.
- Chọn một cách phù hợp; không nhập file nâng cấp sau khi đã nhập bản đầy đủ mới. Nếu đã nâng cấp bằng `php artisan migrate` thì không nhập lại SQL nâng cấp.

File nâng cấp dùng cho đúng bản 27 bảng đã gửi trong chat. Không phải file chuyển đổi tùy ý từ một database khác. Hướng dẫn thao tác ở [NHAP-PHPMYADMIN.md](NHAP-PHPMYADMIN.md).

## Kiểm tra

- 130 kiểm thử / 359 assertions đạt trên SQLite thử nghiệm, gồm tính tương thích với chức năng cũ, điểm mục tiêu hợp lệ/không hợp lệ, chống đổi giới hạn, hồ sơ trùng, bộ lọc kết hợp và liên kết bài học.
- Kiểm tra rollback 3 migration bổ sung rồi chạy lại: bản chụp câu trả lời và các trigger kiểm tra đề thi cũ được giữ nguyên. Bộ kiểm thử cũng kiểm tra rollback toàn bộ 7 migration học online mà giữ dữ liệu trung tâm cũ.
- Chạy 3 migration mới trên database demo, chạy `LearningReferenceScreensSeeder` hai lần không nhân đôi dữ liệu. Demo hiện có 3 khóa, 10 bài học, 4 đề, 8 câu hỏi và 3 mục tiêu điểm minh họa.
- File SQL được biên dịch bằng MySQL grammar của Laravel, dữ liệu đầy đủ xuất từ fixture đã kiểm tra khóa ngoại. Chưa chạy Import trên MySQL/MariaDB thực tế vì máy làm việc không có máy chủ đang chạy.

Phạm vi tiếp theo khi được yêu cầu: xây dựng giao diện, validation/form hồ sơ, quyền truy cập bài học và API làm bài. Gói chấm AI, combo thương mại và thanh toán cần quy trình và bảng giao dịch riêng; chưa thêm dữ liệu giả để làm như các tính năng đó đã hoạt động.
