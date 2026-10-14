<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ReportController;

// Chức năng dành cho Học viên: Đăng ký lớp & Xem lớp đã đăng ký
Route::middleware(['auth', 'role:student'])->group(function () {
    Route::post('/class-rooms/{classRoom}/enroll', [EnrollmentController::class, 'enroll'])->name('enrollments.enroll');
    Route::get('/my-classes', [EnrollmentController::class, 'myClasses'])->name('enrollments.my_classes');
});

// Chức năng dành cho Giáo viên & Admin: Xem DS học viên theo lớp, Điểm danh, Nhập điểm
Route::middleware(['auth', 'role:admin,teacher'])->group(function () {
    Route::get('/teacher/my-classes', [EnrollmentController::class, 'teacherClasses'])->name('teacher.classes');
    Route::get('/class-rooms/{classRoom}/students', [EnrollmentController::class, 'classStudents'])->name('enrollments.class_students');

    // Điểm danh
    Route::get('/class-rooms/{classRoom}/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/class-rooms/{classRoom}/attendances', [AttendanceController::class, 'store'])->name('attendances.store');

    // Nhập điểm / kết quả học tập
    Route::get('/class-rooms/{classRoom}/grades', [GradeController::class, 'index'])->name('grades.index');
    Route::post('/class-rooms/{classRoom}/grades', [GradeController::class, 'updateAll'])->name('grades.update_all');
});

// Chức năng Thống kê số học viên, số lớp, doanh thu học phí (Chỉ Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/reports/dashboard', [ReportController::class, 'index'])->name('reports.index');
});
