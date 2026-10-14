<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(ClassRoom $classRoom)
    {
        // TODO: [Thành viên 3] Hiển thị form điểm danh theo ngày
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 3.');
    }

    public function store(Request $request, ClassRoom $classRoom)
    {
        // TODO: [Thành viên 3] Lưu điểm danh
        abort(501, 'Chức năng đang được phát triển bởi Thành viên 3.');
    }
}
