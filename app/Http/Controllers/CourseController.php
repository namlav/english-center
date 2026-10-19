<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['0', '1'])],
        ]);
        $keyword = trim($filters['keyword'] ?? '');
        $status = $filters['status'] ?? '';
        $courses = Course::withCount('classRooms')
            ->when($keyword !== '', fn (Builder $query): Builder => $query->where(function (Builder $search) use ($keyword): void {
                $search->where('code', 'like', '%'.$keyword.'%')->orWhere('name', 'like', '%'.$keyword.'%');
            }))
            ->when($status !== '', fn (Builder $query): Builder => $query->where('is_active', (int) $status))
            ->latest('id')->paginate(10)->withQueryString();

        return view('courses.index', compact('courses', 'keyword', 'status'));
    }

    public function create(): View
    {
        return view('courses.form', ['course' => new Course(['is_active' => true, 'duration_hours' => 48])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $course = Course::create($this->validated($request));

        return to_route('courses.show', $course)->with('success', 'Thêm khóa học thành công.');
    }

    public function show(Course $course): View
    {
        $course->loadCount('classRooms');
        $classRooms = $course->classRooms()->with('teacher.user')->latest('id')->paginate(10);

        return view('courses.show', compact('course', 'classRooms'));
    }

    public function edit(Course $course): View
    {
        return view('courses.form', compact('course'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $course->update($this->validated($request, $course));

        return to_route('courses.show', $course)->with('success', 'Cập nhật khóa học thành công.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        return DB::transaction(function () use ($course): RedirectResponse {
            $course = Course::query()->lockForUpdate()->findOrFail($course->id);
            if ($course->classRooms()->exists()) {
                return to_route('courses.index')->with('error', 'Khóa học đã có lớp. Hãy ngừng hoạt động khóa học để giữ lịch sử.');
            }
            $course->delete();

            return to_route('courses.index')->with('success', 'Xóa khóa học thành công.');
        }, 5);
    }

    private function validated(Request $request, ?Course $course = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:30', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('courses', 'code')->ignore($course)],
            'name' => ['required', 'string', 'max:255'],
            'level' => ['nullable', 'string', 'max:255'],
            'duration_hours' => ['required', 'integer', 'between:1,2147483647'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['required', 'boolean'],
        ], [
            'required' => 'Vui lòng nhập :attribute.',
            'code.unique' => 'Mã khóa học đã tồn tại.',
            'code.regex' => 'Mã chỉ gồm chữ không dấu, số, dấu gạch ngang hoặc gạch dưới.',
            'duration_hours.between' => 'Thời lượng phải là số giờ nguyên lớn hơn 0.',
        ], ['code' => 'mã khóa học', 'name' => 'tên khóa học', 'duration_hours' => 'thời lượng', 'is_active' => 'trạng thái']);
    }
}
