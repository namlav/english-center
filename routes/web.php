<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PracticeController;
use App\Http\Controllers\PublicCourseController;
use App\Http\Controllers\PracticeLibraryController;
use App\Http\Controllers\LearningAccountController;
use App\Http\Controllers\AdminContentController;
use App\Http\Controllers\PublicTeacherController;
use App\Http\Controllers\{ShopController,AdminShopController,AdminExamController};

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses/online', [ShopController::class, 'index'])->name('catalog.index');
Route::get('/reference-courses', [PublicCourseController::class, 'index'])->name('reference_catalog.index');
Route::get('/chuong-trinh/{course:slug}',[ShopController::class,'show'])->name('shop.show');
Route::get('/chon-khoa-hoc/{course}',[ShopController::class,'selectCourse'])->name('cart.select');
Route::get('/tests', [PracticeLibraryController::class, 'index'])->name('practice.index');
Route::get('/giao-vien/{publicTeacher:slug}', [PublicTeacherController::class, 'show'])->name('public_teachers.show');
Route::get('/khoa-hoc/{key}', [PublicCourseController::class, 'show'])->where('key', '(?:course|series)-[0-9]+')->name('course_catalog.show');
Route::get('/luyen-tap/{slug}', [PracticeController::class, 'show'])->name('practice.show');
Route::post('/luyen-tap/{slug}/kiem-tra', [PracticeController::class, 'check'])->middleware('throttle:20,1')->name('practice.check');

Route::middleware('auth')->group(function (): void {
    Route::get('/gio-hang',[ShopController::class,'cart'])->name('cart.index');
    Route::post('/gio-hang/{course}',[ShopController::class,'add'])->name('cart.add');
    Route::delete('/gio-hang/{course}',[ShopController::class,'remove'])->name('cart.remove');
    Route::delete('/gio-hang',[ShopController::class,'clearCart'])->name('cart.clear');
    Route::get('/thanh-toan',[ShopController::class,'checkoutForm'])->name('checkout.form');
    Route::post('/thanh-toan',[ShopController::class,'checkout'])->middleware('throttle:10,1')->name('checkout');
    Route::get('/my-account/orders',[ShopController::class,'orders'])->name('orders.index');
    Route::get('/don-hang/{order:number}',[ShopController::class,'order'])->name('orders.show');
    Route::get('/don-hang/{order:number}/qr',[ShopController::class,'qrReview'])->name('orders.qr');
    Route::post('/don-hang/{order:number}/qr',[ShopController::class,'confirmQr'])->middleware('throttle:10,1')->name('orders.qr.confirm');
    Route::post('/don-hang/{order:number}/bank-qr',[ShopController::class,'bankQr'])->middleware('throttle:10,1')->name('orders.bank-qr');
    Route::post('/don-hang/{order:number}/report-transfer',[ShopController::class,'reportTransfer'])->middleware('throttle:10,1')->name('orders.report-transfer');
    Route::get('/don-hang/{order:number}/status',[ShopController::class,'status'])->name('orders.status');
    Route::post('/don-hang/{order:number}/confirm-demo',[ShopController::class,'confirm'])->middleware('throttle:10,1')->name('orders.confirm');
    Route::get('/hoc/{course:slug}',[ShopController::class,'learn'])->name('learn.start');
    Route::get('/hoc/{course:slug}/{lesson}',[ShopController::class,'lesson'])->name('learn.lesson');
    Route::post('/hoc/{course:slug}/{lesson}/complete',[ShopController::class,'complete'])->name('learn.complete');
    Route::get('/my-account', [LearningAccountController::class, 'show'])->name('account.show');
    Route::patch('/my-account', [LearningAccountController::class, 'update'])->name('account.update');
    Route::get('/my-account/courses', [LearningAccountController::class, 'courses'])->name('account.courses');
    Route::get('/my-account/schedule', [LearningAccountController::class, 'schedule'])->name('account.schedule');
});
Route::middleware(['auth','role:admin'])->prefix('admin/website/shop')->name('shop_admin.')->group(function(){
    Route::get('/',[AdminShopController::class,'index'])->name('index');
    Route::get('/upgrade-sql',[AdminShopController::class,'upgrade'])->name('upgrade');
    Route::get('/courses/create',[AdminShopController::class,'create'])->name('courses.create');
    Route::post('/courses',[AdminShopController::class,'store'])->name('courses.store');
    Route::get('/courses/{course}/edit',[AdminShopController::class,'edit'])->name('courses.edit');
    Route::patch('/courses/{course}',[AdminShopController::class,'update'])->name('courses.update');
    Route::get('/courses/{course}/lessons/create',[AdminShopController::class,'createLesson'])->name('lessons.create');
    Route::post('/courses/{course}/lessons',[AdminShopController::class,'storeLesson'])->name('lessons.store');
    Route::get('/courses/{course}/lessons/{lesson}/edit',[AdminShopController::class,'editLesson'])->name('lessons.edit');
    Route::patch('/courses/{course}/lessons/{lesson}',[AdminShopController::class,'updateLesson'])->name('lessons.update');
    Route::get('/bank-settings',[AdminShopController::class,'bankSettings'])->name('bank.edit');
    Route::post('/bank-settings',[AdminShopController::class,'saveBankSettings'])->name('bank.save');
    Route::get('/orders',[AdminShopController::class,'orders'])->name('orders.index');
    Route::get('/orders/{order:number}',[AdminShopController::class,'order'])->name('orders.show');
    Route::post('/orders/{order:number}/confirm-demo',[AdminShopController::class,'confirm'])->name('orders.confirm');
});
Route::middleware(['auth','role:admin'])->prefix('admin/website/exams')->name('exam_admin.')->group(function(){
    Route::get('/',[AdminExamController::class,'index'])->name('index');
    Route::get('/create',[AdminExamController::class,'create'])->name('create');
    Route::post('/',[AdminExamController::class,'store'])->name('store');
    Route::get('/{paper}/edit',[AdminExamController::class,'edit'])->name('edit');
    Route::patch('/{paper}',[AdminExamController::class,'update'])->name('update');
    Route::get('/{paper}/questions/create',[AdminExamController::class,'createQuestion'])->name('questions.create');
    Route::post('/{paper}/questions',[AdminExamController::class,'storeQuestion'])->name('questions.store');
    Route::get('/{paper}/questions/{question}/edit',[AdminExamController::class,'editQuestion'])->name('questions.edit');
    Route::patch('/{paper}/questions/{question}',[AdminExamController::class,'updateQuestion'])->name('questions.update');
});
Route::middleware(['auth','role:admin'])->prefix('admin/website')->name('website.')->group(function (): void {
    Route::get('/', [AdminContentController::class, 'index'])->name('index');
    Route::get('/upgrade-sql', [AdminContentController::class, 'upgradeSql'])->name('upgrade');
    Route::post('/import', [AdminContentController::class, 'import'])->name('import');
    Route::get('/courses/{referencePage}/edit', [AdminContentController::class, 'editCourse'])->name('courses.edit');
    Route::patch('/courses/{referencePage}', [AdminContentController::class, 'updateCourse'])->name('courses.update');
    Route::get('/teachers/create', [AdminContentController::class, 'createTeacher'])->name('teachers.create');
    Route::post('/teachers', [AdminContentController::class, 'storeTeacher'])->name('teachers.store');
    Route::get('/teachers/{publicTeacher}/edit', [AdminContentController::class, 'editTeacher'])->name('teachers.edit');
    Route::patch('/teachers/{publicTeacher}', [AdminContentController::class, 'updateTeacher'])->name('teachers.update');
    Route::get('/home', [AdminContentController::class, 'editHome'])->name('home.edit');
    Route::patch('/home', [AdminContentController::class, 'updateHome'])->name('home.update');
});

// Nạp file route riêng của từng thành viên để tránh Git Conflict
require __DIR__ . '/member1.php';
require __DIR__ . '/member2.php';
require __DIR__ . '/member3.php';
