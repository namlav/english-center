<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        // TODO: [Thành viên 1] Hiển thị danh sách khóa học với withCount('classRooms')
        $courses = Course::withCount('classRooms')->orderBy('created_at', 'desc')->paginate(10);
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        // TODO: [Thành viên 1] Form thêm khóa học
        return view('courses.create');
    }

    public function store(Request $request)
    {
        // TODO: [Thành viên 1] Validate & lưu khóa học mới
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 1.');
    }

    public function show(Course $course)
    {
        // TODO: [Thành viên 1] Xem chi tiết khóa học
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        // TODO: [Thành viên 1] Form sửa khóa học
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course)
    {
        // TODO: [Thành viên 1] Validate & cập nhật khóa học
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 1.');
    }

    public function destroy(Course $course)
    {
        // TODO: [Thành viên 1] Kiểm tra classRooms trước khi xóa
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 1.');
    }
}
