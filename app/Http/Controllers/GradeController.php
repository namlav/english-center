<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(ClassRoom $classRoom)
    {
        // TODO: [Thành viên 3] Hiển thị bảng nhập điểm
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 3.');
    }

    public function updateAll(Request $request, ClassRoom $classRoom)
    {
        // TODO: [Thành viên 3] Cập nhật điểm cho tất cả học viên trong lớp
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 3.');
    }
}
