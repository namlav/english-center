<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('class_rooms.index');
});

// Nạp file route riêng của từng thành viên để tránh Git Conflict
require __DIR__ . '/member1.php';
require __DIR__ . '/member2.php';
require __DIR__ . '/member3.php';
