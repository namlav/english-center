<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ClassRoomController;

// Xem danh sách lớp & Tìm kiếm/Lọc lớp học (Ai đã đăng nhập cũng xem được)
Route::middleware(['auth'])->group(function () {
    Route::get('/class-rooms', [ClassRoomController::class, 'index'])->name('class_rooms.index');
    Route::get('/class-rooms/{classRoom}', [ClassRoomController::class, 'show'])->name('class_rooms.show');
});

// CRUD Khóa học & Lớp học (Chỉ Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('courses', CourseController::class);
    Route::resource('class-rooms', ClassRoomController::class)->except(['index', 'show'])->names('class_rooms');
});
