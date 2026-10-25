# 📋 ĐẶC TẢ CHI TIẾT CÔNG VIỆC CHO 4 THÀNH VIÊN & CHECKLIST NGHIỆM THU

Tài liệu này quy định chi tiết đầu vào (Input), quy tắc xử lý (Business Logic) và đầu ra (Output) cho từng thành viên để đảm bảo khi ghép code vào nhánh `main` mọi chức năng ăn khớp 100%.

---

## 👤 THÀNH VIÊN 1: CƠ SỞ DỮ LIỆU, QUẢN LÝ KHÓA HỌC, LỚP HỌC & TÌM KIẾM

### 1. Danh sách Task cần hoàn thành
* **Task 1.1 – Quản lý CSDL bằng Migration, Seeder & Eloquent:**
  * Kiểm tra và chạy thử 7 bảng trong `database/migrations/` và dữ liệu mẫu trong `database/seeders/DatabaseSeeder.php`.
  * Đảm bảo các mối quan hệ Eloquent hoạt động đúng: `Course` 1-N `ClassRoom`, `Teacher` 1-N `ClassRoom`, `ClassRoom` N-N `Student` (qua `enrollments`).
* **Task 1.2 – Lập trình CRUD Khóa học (`CourseController` & `resources/views/courses/`):**
  * Danh sách khóa học (`index.blade.php`): Hiển thị Mã khóa học, Tên khóa học (TOEIC, IELTS, Tiếng Anh giao tiếp...), Cấp độ, Thời lượng (giờ), Số lớp đang mở (`withCount('classRooms')`), Trạng thái.
  * Thêm/Sửa khóa học (`create.blade.php`, `edit.blade.php`): Validate `code` không trùng (`unique:courses,code`), `name` bắt buộc nhập.
  * Xóa khóa học (`destroy`): Kiểm tra nếu khóa học đã có lớp học (`$course->classRooms()->count() > 0`) thì trả về thông báo lỗi không cho xóa để bảo toàn dữ liệu.
* **Task 1.3 – Lập trình CRUD Lớp học (`ClassRoomController` & `resources/views/class_rooms/`):**
  * Form Thêm/Sửa lớp học phải có đầy đủ các trường theo đề bài:
    1. Mã lớp (`code`) & Tên lớp (`name`)
    2. Chọn Khóa học (`course_id` – Dropdown lấy từ bảng `courses`)
    3. Chọn Giáo viên phụ trách (`teacher_id` – Dropdown lấy từ bảng `teachers` kèm `user.name`)
    4. Ngày bắt đầu (`start_date`)
    5. Lịch học (`schedule` – VD: `Thứ 2-4-6 (18:00 - 20:00)`)
    6. Học phí (`tuition_fee`)
    7. Số lượng học viên tối đa (`max_students`)
    8. Trạng thái lớp (`upcoming`, `ongoing`, `completed`, `cancelled`)
* **Task 1.4 – Tìm kiếm và Lọc lớp học theo Khóa học hoặc Giáo viên:**
  * Tại trang `resources/views/class_rooms/index.blade.php`, thiết kế thanh tìm kiếm `GET /class-rooms` gồm:
    * Ô nhập từ khóa tên lớp / mã lớp (`keyword`).
    * Dropdown lọc theo Khóa học (`course_id`).
    * Dropdown lọc theo Giáo viên (`teacher_id`).
  * Sử dụng `ClassRoom::with(['course', 'teacher.user'])->withCount('enrollments')->filter($request->all())->paginate(10)`.
  * Hiển thị thêm nút **"Đăng ký học"** (nếu user đang đăng nhập là `student`) và nút **"Danh sách HV / Điểm danh / Nhập điểm"** (nếu user là `admin` hoặc `teacher` phụ trách lớp đó) trỏ sang các route của Thành viên 3.

---

## 👤 THÀNH VIÊN 2 (BẠN): ĐĂNG NHẬP, PHÂN QUYỀN, QUẢN LÝ TÀI KHOẢN, HỌC VIÊN & GIÁO VIÊN

Vì bạn vừa là người tạo Repo vừa đảm nhận vai trò **Thành viên 2**, dưới đây là toàn bộ đặc tả logic và code mẫu chuẩn để bạn hoàn thành nhanh phần việc của mình và làm mẫu cho cả nhóm:

### 1. Chức năng Đăng nhập, Đăng xuất & Điều hướng theo phân quyền (`AuthController`)
* **Logic xử lý đăng nhập (`POST /login`):**
  ```php
  public function login(Request $request)
  {
      $credentials = $request->validate([
          'email' => ['required', 'email'],
          'password' => ['required'],
      ]);

      if (Auth::attempt($credentials, $request->boolean('remember'))) {
          $request->session()->regenerate();
          $user = Auth::user();

          if (!$user->is_active) {
              Auth::logout();
              return back()->with('error', 'Tài khoản đã bị vô hiệu hóa.');
          }

          // Điều hướng theo đúng vai trò
          return match ($user->role) {
              'admin'   => redirect()->route('reports.index')->with('success', 'Xin chào Quản trị viên!'),
              'teacher' => redirect()->route('teacher.classes')->with('success', 'Xin chào Giáo viên!'),
              default   => redirect()->route('class_rooms.index')->with('success', 'Đăng nhập thành công!'),
          };
      }

      return back()->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.'])->onlyInput('email');
  }
  ```

### 2. Chức năng Quản lý Tài khoản (`UserController` & `resources/views/users/`)
* Hiển thị danh sách toàn bộ tài khoản trong bảng `users` (lọc được theo `role`: Admin, Giáo viên, Học viên).
* Cho phép Admin tạo tài khoản mới, cập nhật thông tin, đổi mật khẩu (nếu ô password được nhập thì `Hash::make()`, nếu bỏ trống thì giữ nguyên mật khẩu cũ), và bật/tắt trạng thái hoạt động (`is_active`).

### 3. Chức năng Quản lý Giáo viên (`TeacherController` & `resources/views/teachers/`)
* **Khi Thêm mới Giáo viên (`store`):** Sử dụng `DB::transaction` để vừa tạo tài khoản trong bảng `users` (với `role = 'teacher'`), vừa tạo hồ sơ trong bảng `teachers` (`teacher_code`, `specialization`, `degree`, `bio`):
  ```php
  DB::transaction(function () use ($validated) {
      $user = User::create([
          'name' => $validated['name'],
          'email' => $validated['email'],
          'password' => Hash::make($validated['password'] ?? '123456'),
          'phone' => $validated['phone'] ?? null,
          'role' => 'teacher',
          'is_active' => true,
      ]);

      Teacher::create([
          'user_id' => $user->id,
          'teacher_code' => $validated['teacher_code'],
          'specialization' => $validated['specialization'],
          'degree' => $validated['degree'] ?? null,
          'bio' => $validated['bio'] ?? null,
      ]);
  });
  ```
* **Khi Sửa/Xóa Giáo viên:** Cập nhật đồng thời cả 2 bảng `users` và `teachers`. Trước khi xóa giáo viên, kiểm tra nếu giáo viên đang phụ trách lớp học (`$teacher->classRooms()->exists()`) thì báo lỗi yêu cầu chuyển lớp sang giáo viên khác trước khi xóa.

### 4. Chức năng Quản lý Học viên (`StudentController` & `resources/views/students/`)
* Tương tự Giáo viên, sử dụng `DB::transaction` để tạo đồng thời bản ghi `users` (`role = 'student'`) và bản ghi `students` (`student_code`, `date_of_birth`, `gender`, `address`).
* Trang chi tiết học viên (`show.blade.php`) hiển thị thông tin hồ sơ kèm danh sách các lớp mà học viên đó đã đăng ký.

---

## 👤 THÀNH VIÊN 3: ĐĂNG KÝ LỚP, ĐIỂM DANH, NHẬP ĐIỂM & THỐNG KÊ

### 1. Danh sách Task & Quy tắc nghiệp vụ
* **Task 3.1 – Học viên đăng ký lớp (`EnrollmentController@enroll`):**
  * Kiểm tra 1: Lớp học có đang ở trạng thái cho phép đăng ký (`upcoming` hoặc `ongoing`) không?
  * Kiểm tra 2: Học viên đã đăng ký lớp này chưa (`Enrollment::where('class_room_id', $id)->where('student_id', $student->id)->exists()`)?
  * Kiểm tra 3: Sĩ số hiện tại của lớp đã chạm mốc tối đa (`$classRoom->enrollments()->count() >= $classRoom->max_students`) chưa?
  * Nếu hợp lệ: Lưu bản ghi vào bảng `enrollments` với `enrollment_date = now()`, `paid_tuition = $classRoom->tuition_fee`, `payment_status = 'paid'`.
* **Task 3.2 – Xem lớp của Giáo viên, Lớp của Học viên & Danh sách Học viên theo từng lớp:**
  * `myClasses()`: Học viên xem danh sách các lớp mình đã đăng ký, kèm lịch học, giáo viên giảng dạy, số buổi có mặt/vắng và điểm số (`midterm_score`, `final_score`, `result_status`).
  * `teacherClasses()`: Giáo viên chỉ xem danh sách các lớp do chính mình phụ trách (`where('teacher_id', auth()->user()->teacher->id)`).
  * `classStudents(ClassRoom $classRoom)`: Hiển thị bảng danh sách toàn bộ học viên của lớp đó. (Nếu người xem là `teacher`, kiểm tra chắc chắn `$classRoom->teacher_id === auth()->user()->teacher->id`).
* **Task 3.3 – Điểm danh theo lớp và ngày (`AttendanceController`):**
  * Giao diện chọn ngày điểm danh (`attendance_date`, mặc định là ngày hôm nay).
  * Hiển thị danh sách toàn bộ học viên trong lớp, mỗi dòng có 3 Radio Button: `Có mặt (present)`, `Vắng (absent)`, `Đi trễ (late)` và ô nhập `Ghi chú (note)`.
  * Khi nhấn **Lưu điểm danh**, dùng vòng lặp kết hợp `Attendance::updateOrCreate()` theo bộ khóa `['class_room_id', 'student_id', 'attendance_date']` để vừa điểm danh mới vừa sửa lại điểm danh cũ dễ dàng.
* **Task 3.4 – Nhập điểm / Kết quả học tập (`GradeController`):**
  * Hiển thị bảng danh sách học viên của lớp kèm các cột input: Điểm giữa kỳ (`midterm_score`), Điểm cuối kỳ (`final_score`), Xếp loại kết quả (`studying`, `passed`, `failed`) và Nhận xét (`remarks`).
  * Cho phép Giáo viên/Admin nhập điểm cho cả lớp cùng lúc và bấm **Lưu bảng điểm**.
* **Task 3.5 – Thống kê số học viên, số lớp, doanh thu học phí (`ReportController@index`):**
  * Hiển thị 4 thẻ tổng quan (Summary Cards):
    1. **Tổng số Học viên:** `Student::count()`
    2. **Tổng số Giáo viên:** `Teacher::count()`
    3. **Tổng số Lớp học:** `ClassRoom::count()` (kèm chi tiết số lớp đang học, sắp mở, đã hoàn thành)
    4. **Tổng Doanh thu Học phí:** Tính theo công thức tổng học phí của các lượt đăng ký đã thanh toán:
       $$\text{Tổng Doanh Thu} = \sum \text{paid\_tuition} \quad (\text{với } \text{payment\_status} = \text{'paid'})$$
  * Bảng thống kê chi tiết doanh thu và sĩ số theo từng Khóa học và từng Lớp học.

---

## 👤 THÀNH VIÊN 4: ĐỀ CƯƠNG BÁO CÁO WORD & KỊCH BẢN KIỂM THỬ

Thành viên 4 chuẩn bị file báo cáo Word theo cấu trúc chuẩn sau đây để điền nội dung song song trong lúc 3 thành viên code:

### 1. Cấu trúc Đề cương Báo cáo Word chuẩn
* **Lời mở đầu & Phân công nhiệm vụ nhóm** (Đưa bảng phân công 4 thành viên vào).
* **Chương 1: Tổng quan đề tài & Khảo sát bài toán**
  * 1.1. Lý do chọn đề tài Quản lý Trung tâm Ngoại ngữ.
  * 1.2. Mô tả bài toán và quy trình nghiệp vụ thực tế.
  * 1.3. Xác định các tác nhân (Admin, Giáo viên, Học viên) và phạm vi hệ thống.
* **Chương 2: Phân tích và Thiết kế hệ thống**
  * 2.1. Biểu đồ Use Case tổng quát và phân rã (Use Case Admin, Giáo viên, Học viên).
  * 2.2. Thiết kế Cơ sở dữ liệu: Sơ đồ thực thể kết hợp (ERD) gồm 7 bảng.
  * 2.3. Đặc tả chi tiết 7 bảng CSDL (Lấy trực tiếp từ Phần 2 của file `AGENT_SCAFFOLD_SPEC.md`).
* **Chương 3: Công nghệ sử dụng & Cài đặt hệ thống**
  * 3.1. Giới thiệu PHP, Laravel Framework (Kiến trúc MVC, Eloquent ORM, Migration, Seeder), Laragon & phpMyAdmin.
  * 3.2. Kết quả xây dựng giao diện và chức năng (Chụp ảnh màn hình từng chức năng của Thành viên 1, 2, 3 kèm giải thích).
* **Chương 4: Kiểm thử hệ thống (Testing)**
  * Bảng Test Cases kiểm thử các luồng chính:
    * `TC01`: Đăng nhập đúng/sai mật khẩu và kiểm tra chặn quyền truy cập trái phép (`403 Forbidden`).
    * `TC02`: Thêm/Sửa/Xóa Khóa học và Lớp học.
    * `TC03`: Tìm kiếm và lọc lớp học theo khóa học TOEIC/IELTS hoặc theo Giáo viên.
    * `TC04`: Học viên đăng ký lớp học (trường hợp thành công, trường hợp đăng ký trùng, trường hợp lớp đã đầy sĩ số).
    * `TC05`: Giáo viên điểm danh và nhập điểm kết quả học tập cho học viên.
    * `TC06`: Đối chiếu số liệu thống kê doanh thu học phí trên Dashboard.
* **Kết luận, Tài liệu tham khảo & Phụ lục.**