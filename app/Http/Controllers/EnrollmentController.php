<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function enroll(Request $request, ClassRoom $classRoom)
    {
        // TODO: [Thành viên 3] Học viên đăng ký lớp học
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 3.');
    }

    public function myClasses()
    {
        // TODO: [Thành viên 3] Danh sách lớp học viên đã đăng ký
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 3.');
    }

    public function teacherClasses()
    {
        // TODO: [Thành viên 3] Danh sách lớp giáo viên phụ trách
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 3.');
    }

    public function classStudents(ClassRoom $classRoom)
    {
        // TODO: [Thành viên 3] Danh sách học viên theo lớp
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 3.');
    }
}
