<?php

use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::pattern('course', '[0-9]+');
Route::pattern('classRoom', '[0-9]+');

Route::middleware('auth')->group(function (): void {
    Route::get('/class-rooms', [ClassRoomController::class, 'index'])->name('class_rooms.index');
    Route::get('/class-rooms/{classRoom}', [ClassRoomController::class, 'show'])->name('class_rooms.show');
});

Route::middleware(['auth', 'role:admin'])->group(function (): void {
    Route::resource('courses', CourseController::class);
    Route::resource('class-rooms', ClassRoomController::class)
        ->parameters(['class-rooms' => 'classRoom'])
        ->except(['index', 'show'])->names('class_rooms');
});
