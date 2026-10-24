<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ClassRoomController extends Controller
{
    /**
     * Giao diện Khách hàng & Học viên: Xem danh sách Lớp học & Lịch khai giảng
     */
    public function publicIndex(Request $request): View
    {
        $filters = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
            'status' => ['nullable', Rule::in(array_keys(ClassRoom::STATUSES))],
        ]);
        $filters['keyword'] = trim($filters['keyword'] ?? '');

        $query = ClassRoom::with(['course', 'teacher.user'])->withCount('enrollments')
            ->filter($filters);

        if (empty($filters['status'])) {
            $query->whereIn('status', ['upcoming', 'ongoing']);
        }

        $classRooms = $query->orderBy('start_date')->paginate(12)->withQueryString();
        $courses = Course::has('classRooms')->orderBy('name')->get();
        $teachers = Teacher::has('classRooms')->with('user')->orderBy('teacher_code')->get();
        $statuses = ClassRoom::STATUSES;

        return view('learning.classes.index', compact('classRooms', 'courses', 'teachers', 'statuses'));
    }

    /**
     * Giao diện Khách hàng & Học viên: Xem chi tiết một lớp học
     */
    public function publicShow(ClassRoom $classRoom): View
    {
        $classRoom->load(['course', 'teacher.user', 'enrollments.student.user'])->loadCount('enrollments');
        $relatedClasses = ClassRoom::where('course_id', $classRoom->course_id)
            ->where('id', '!=', $classRoom->id)
            ->whereIn('status', ['upcoming', 'ongoing'])
            ->with(['teacher.user'])
            ->withCount('enrollments')
            ->limit(3)
            ->get();

        return view('learning.classes.show', compact('classRoom', 'relatedClasses'));
    }

    /**
     * Giao diện Khách hàng: Màn hình điền thông tin đăng ký nhập học
     */
    public function registerForm(ClassRoom $classRoom): View|RedirectResponse
    {
        $classRoom->load(['course', 'teacher.user'])->loadCount('enrollments');

        if (! in_array($classRoom->status, ['upcoming', 'ongoing'], true)) {
            return redirect()->route('public_classes.show', $classRoom)
                ->with('error', 'Lớp học này hiện không mở tuyển sinh hoặc đã kết thúc.');
        }

        $user = Auth::user();
        $student = $user?->student;

        if ($student && Enrollment::where('class_room_id', $classRoom->id)->where('student_id', $student->id)->exists()) {
            return redirect()->route('public_classes.show', $classRoom)
                ->with('info', 'Bạn đã đăng ký tham gia lớp học này rồi. Vui lòng kiểm tra trong mục Lớp học của tôi.');
        }

        return view('learning.classes.register', compact('classRoom', 'user', 'student'));
    }

    /**
     * Xử lý đăng ký nhập học & lưu thông tin học viên
     */
    public function processRegistration(Request $request, ClassRoom $classRoom): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+().\s-]+$/'],
            'email' => ['required', 'email', 'max:150'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string', 'max:255'],
            'learning_goals' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_method' => ['nullable', 'string', 'in:bank_transfer,center_payment'],
        ], [
            'name.required' => 'Vui lòng nhập họ và tên học viên.',
            'phone.required' => 'Vui lòng nhập số điện thoại liên hệ.',
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
            'email.required' => 'Vui lòng nhập email nhận phiếu đăng ký.',
            'email.email' => 'Địa chỉ email không hợp lệ.',
        ]);

        $enrollment = DB::transaction(function () use ($classRoom, $validated) {
            $lockedClass = ClassRoom::query()->lockForUpdate()->findOrFail($classRoom->id);

            if (! in_array($lockedClass->status, ['upcoming', 'ongoing'], true)) {
                throw ValidationException::withMessages(['class' => 'Lớp này hiện không nhận đăng ký nhập học.']);
            }

            if ($lockedClass->enrollments()->count() >= $lockedClass->max_students) {
                throw ValidationException::withMessages(['class' => 'Lớp đã đủ số lượng học viên tối đa.']);
            }

            // Đồng bộ hoặc tạo tài khoản User
            if (Auth::check()) {
                $user = Auth::user();
                $user->update([
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                ]);
            } else {
                $user = User::where('email', $validated['email'])->first();
                if (! $user) {
                    $user = User::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'phone' => $validated['phone'],
                        'password' => Hash::make('123456'),
                        'role' => 'student',
                        'is_active' => true,
                    ]);
                } else {
                    $user->update([
                        'name' => $validated['name'],
                        'phone' => $validated['phone'],
                    ]);
                }
                Auth::login($user);
            }

            // Đồng bộ hoặc tạo hồ sơ Student
            $student = $user->student;
            if (! $student) {
                $maxId = (int) Student::max('id');
                $code = sprintf('HV%04d', $maxId + 1);
                while (Student::where('student_code', $code)->exists()) {
                    $maxId++;
                    $code = sprintf('HV%04d', $maxId + 1);
                }
                $student = Student::create([
                    'user_id' => $user->id,
                    'student_code' => $code,
                    'date_of_birth' => $validated['date_of_birth'] ?? null,
                    'gender' => $validated['gender'] ?? null,
                    'address' => $validated['address'] ?? null,
                ]);
            } else {
                $student->update([
                    'date_of_birth' => $validated['date_of_birth'] ?? $student->date_of_birth,
                    'gender' => $validated['gender'] ?? $student->gender,
                    'address' => $validated['address'] ?? $student->address,
                ]);
            }

            if (Enrollment::where('class_room_id', $lockedClass->id)->where('student_id', $student->id)->exists()) {
                throw ValidationException::withMessages(['class' => 'Bạn đã đăng ký tham gia lớp học này rồi.']);
            }

            $method = $validated['payment_method'] ?? 'bank_transfer';
            $remarks = 'PTTT: ' . ($method === 'center_payment' ? 'Nộp tại quầy trung tâm' : 'Chuyển khoản QR');
            if (! empty($validated['learning_goals'])) {
                $remarks .= ' | Mục tiêu: ' . $validated['learning_goals'];
            }
            if (! empty($validated['notes'])) {
                $remarks .= ' | Ghi chú: ' . $validated['notes'];
            }

            return Enrollment::create([
                'class_room_id' => $lockedClass->id,
                'student_id' => $student->id,
                'enrollment_date' => now()->toDateString(),
                'paid_tuition' => 0,
                'payment_status' => 'unpaid',
                'result_status' => 'studying',
                'remarks' => $remarks,
            ]);
        }, 3);

        return redirect()->route('public_classes.confirmation', [
            'classRoom' => $classRoom->id,
            'enrollment' => $enrollment->id,
        ])->with('success', 'Đăng ký nhập học thành công! Vui lòng kiểm tra hướng dẫn thanh toán.');
    }

    /**
     * Giao diện Khách hàng: Màn hình xác nhận đăng ký thành công kèm mã QR thanh toán
     */
    public function registrationConfirmation(ClassRoom $classRoom, Request $request): View
    {
        $enrollmentId = $request->query('enrollment');
        $enrollment = Enrollment::with(['student.user', 'classRoom.course', 'classRoom.teacher.user'])
            ->where('class_room_id', $classRoom->id)
            ->when($enrollmentId, fn ($q) => $q->where('id', $enrollmentId))
            ->when(Auth::check() && Auth::user()->student, fn ($q) => $q->where('student_id', Auth::user()->student->id))
            ->latest('id')
            ->firstOrFail();

        return view('learning.classes.confirmation', compact('classRoom', 'enrollment'));
    }

    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
            'status' => ['nullable', Rule::in(array_keys(ClassRoom::STATUSES))],
        ]);
        $filters['keyword'] = trim($filters['keyword'] ?? '');
        $classRooms = ClassRoom::with(['course', 'teacher.user'])->withCount('enrollments')
            ->filter($filters)->latest('id')->paginate(10)->withQueryString();
        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('class_rooms._results', ['classRooms' => $classRooms, 'statuses' => ClassRoom::STATUSES])->render(),
                'total' => $classRooms->total(),
            ]);
        }

        $courses = Course::orderBy('name')->get();
        $teachers = Teacher::with('user')->orderBy('teacher_code')->get();
        $statuses = ClassRoom::STATUSES;

        return view('class_rooms.index', compact('classRooms', 'courses', 'teachers', 'statuses'));
    }

    public function show(ClassRoom $classRoom): View
    {
        $classRoom->load(['course', 'teacher.user', 'enrollments.student.user'])->loadCount('enrollments');

        $enrolledStudentIds = $classRoom->enrollments->pluck('student_id')->filter()->all();
        $availableStudents = Student::with('user')
            ->whereNotIn('id', $enrolledStudentIds)
            ->get()
            ->sortBy(fn($s) => $s->user->name ?? $s->student_code);

        return view('class_rooms.show', compact('classRoom', 'availableStudents'));
    }

    public function create(): View
    {
        return $this->form(new ClassRoom(['max_students' => 25, 'tuition_fee' => '0.00', 'status' => 'upcoming']));
    }

    public function edit(ClassRoom $classRoom): View
    {
        return $this->form($classRoom);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);
        $classRoom = DB::transaction(function () use ($validated): ClassRoom {
            Course::query()->lockForUpdate()->findOrFail($validated['course_id']);

            return ClassRoom::create($validated);
        }, 5);

        return to_route('class_rooms.show', $classRoom)->with('success', 'Thêm lớp học thành công.');
    }

    public function update(Request $request, ClassRoom $classRoom): RedirectResponse
    {
        $validated = $this->validated($request, $classRoom);
        DB::transaction(function () use ($classRoom, $validated): void {
            $current = ClassRoom::query()->lockForUpdate()->findOrFail($classRoom->id);
            $count = $current->enrollments()->count();
            if ($validated['max_students'] < $count) {
                throw ValidationException::withMessages(['max_students' => 'Sĩ số tối đa không được nhỏ hơn số học viên đã đăng ký.']);
            }
            $normalizedFee = (new ClassRoom(['tuition_fee' => $validated['tuition_fee']]))->tuition_fee;
            if ($count > 0 && ((int) $validated['course_id'] !== $current->course_id || $normalizedFee !== $current->tuition_fee)) {
                throw ValidationException::withMessages(['course_id' => 'Lớp đã có học viên nên không thể đổi khóa học hoặc học phí.']);
            }
            $current->update($validated);
        }, 5);

        return to_route('class_rooms.show', $classRoom)->with('success', 'Cập nhật lớp học thành công.');
    }

    public function destroy(ClassRoom $classRoom): RedirectResponse
    {
        return DB::transaction(function () use ($classRoom): RedirectResponse {
            $current = ClassRoom::query()->lockForUpdate()->findOrFail($classRoom->id);
            if ($current->enrollments()->exists() || $current->attendances()->exists()) {
                return to_route('class_rooms.index')->with('error', 'Lớp đã có dữ liệu học tập. Hãy đổi trạng thái thay vì xóa lịch sử.');
            }
            $current->delete();

            return to_route('class_rooms.index')->with('success', 'Xóa lớp học thành công.');
        }, 5);
    }

    private function form(ClassRoom $classRoom): View
    {
        $courses = Course::where(function (Builder $query) use ($classRoom): void {
            $query->where('is_active', true);
            if ($classRoom->exists) {
                $query->orWhereKey($classRoom->course_id);
            }
        })->orderBy('name')->get();
        $teachers = Teacher::with('user')->where(function (Builder $query) use ($classRoom): void {
            $query->whereHas('user', fn (Builder $user): Builder => $user->where('role', 'teacher')->where('is_active', true));
            if ($classRoom->exists) {
                $query->orWhereKey($classRoom->teacher_id);
            }
        })->orderBy('teacher_code')->get();
        $statuses = ClassRoom::STATUSES;

        return view('class_rooms.form', compact('classRoom', 'courses', 'teachers', 'statuses'));
    }

    private function validated(Request $request, ?ClassRoom $classRoom = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('class_rooms', 'code')->ignore($classRoom)],
            'name' => ['required', 'string', 'max:255'],
            'course_id' => ['required', 'integer', Rule::exists('courses', 'id')->where(function (QueryBuilder $query) use ($classRoom): void {
                $query->where(function (QueryBuilder $allowed) use ($classRoom): void {
                    $allowed->where('is_active', true);
                    if ($classRoom !== null) {
                        $allowed->orWhere('id', $classRoom->course_id);
                    }
                });
            })],
            'teacher_id' => ['required', 'integer', Rule::exists('teachers', 'id')->where(function (QueryBuilder $query) use ($classRoom): void {
                $query->where(function (QueryBuilder $allowed) use ($classRoom): void {
                    $allowed->whereIn('user_id', User::where('role', 'teacher')->where('is_active', true)->select('id'));
                    if ($classRoom !== null) {
                        $allowed->orWhere('id', $classRoom->teacher_id);
                    }
                });
            })],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'expected_end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'schedule' => ['required', 'string', 'max:255'],
            'tuition_fee' => ['required', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
            'max_students' => ['required', 'integer', 'between:1,65535'],
            'status' => ['required', Rule::in(array_keys(ClassRoom::STATUSES))],
        ], [
            'required' => 'Vui lòng nhập :attribute.',
            'code.unique' => 'Mã lớp học đã tồn tại.',
            'code.regex' => 'Mã chỉ gồm chữ không dấu, số, dấu gạch ngang hoặc gạch dưới.',
            'course_id.exists' => 'Khóa học không tồn tại hoặc đã ngừng hoạt động.',
            'teacher_id.exists' => 'Giáo viên không tồn tại hoặc tài khoản đã bị khóa.',
            'expected_end_date.after_or_equal' => 'Ngày kết thúc dự kiến phải bằng hoặc sau ngày bắt đầu.',
            'tuition_fee.min' => 'Học phí không được âm.',
            'tuition_fee.decimal' => 'Học phí có tối đa 2 chữ số thập phân.',
            'max_students.between' => 'Sĩ số tối đa phải từ 1 đến 65535.',
        ], ['code' => 'mã lớp', 'name' => 'tên lớp', 'course_id' => 'khóa học', 'teacher_id' => 'giáo viên', 'start_date' => 'ngày bắt đầu', 'expected_end_date' => 'ngày kết thúc dự kiến', 'schedule' => 'lịch học', 'tuition_fee' => 'học phí', 'max_students' => 'sĩ số tối đa', 'status' => 'trạng thái']);
    }
}
