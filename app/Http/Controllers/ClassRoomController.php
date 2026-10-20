<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ClassRoomController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'teacher_id' => ['nullable', 'integer', 'exists:teachers,id'],
            'status' => ['nullable', Rule::in(array_keys(ClassRoom::STATUSES))],
        ]);
        $filters['keyword'] = trim($filters['keyword'] ?? '');
        $classRooms = ClassRoom::with(['course', 'teacher.user'])->withCount('enrollments')
            ->filter($filters)->latest('id')->paginate(10)->withQueryString();
        $courses = Course::orderBy('name')->get();
        $teachers = Teacher::with('user')->orderBy('teacher_code')->get();
        $statuses = ClassRoom::STATUSES;

        return view('class_rooms.index', compact('classRooms', 'courses', 'teachers', 'statuses'));
    }

    public function show(ClassRoom $classRoom): View
    {
        $classRoom->load(['course', 'teacher.user'])->loadCount('enrollments');

        return view('class_rooms.show', compact('classRoom'));
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
