<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ClassRoomController extends Controller
{
    public function index(Request $request)
    {
        // TODO: [Thành viên 1] Danh sách + Tìm kiếm/Lọc lớp học
        $classRooms = ClassRoom::with(['course', 'teacher.user'])
            ->withCount('enrollments')
            ->filter($request->all())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $courses = Course::where('is_active', true)->get();
        $teachers = Teacher::with('user')->get();

        return view('class_rooms.index', compact('classRooms', 'courses', 'teachers'));
    }

    public function show(ClassRoom $classRoom)
    {
        // TODO: [Thành viên 1] Chi tiết lớp học
        $classRoom->load(['course', 'teacher.user', 'enrollments.student.user']);
        return view('class_rooms.show', compact('classRoom'));
    }

    public function create()
    {
        // TODO: [Thành viên 1] Form thêm lớp học
        $courses = Course::where('is_active', true)->get();
        $teachers = Teacher::with('user')->get();
        return view('class_rooms.create', compact('courses', 'teachers'));
    }

    public function store(Request $request)
    {
        // TODO: [Thành viên 1] Validate & lưu lớp học mới
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 1.');
    }

    public function edit(ClassRoom $classRoom)
    {
        // TODO: [Thành viên 1] Form sửa lớp học
        $courses = Course::where('is_active', true)->get();
        $teachers = Teacher::with('user')->get();
        return view('class_rooms.edit', compact('classRoom', 'courses', 'teachers'));
    }

    public function update(Request $request, ClassRoom $classRoom)
    {
        // TODO: [Thành viên 1] Validate & cập nhật lớp học
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 1.');
    }

    public function destroy(ClassRoom $classRoom)
    {
        // TODO: [Thành viên 1] Kiểm tra enrollments trước khi xóa
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 1.');
    }
}
