<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\ExamPaper;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $hasOrders = Schema::hasTable('orders');
        $hasCourses = Schema::hasTable('courses');
        $hasLessons = Schema::hasTable('lessons');
        $hasExams = Schema::hasTable('exam_papers');

        $totalRevenue = $hasOrders ? (int) Order::where('status', 'paid')->sum('total') : 0;
        $totalOrders = $hasOrders ? Order::count() : 0;
        $pendingOrdersCount = $hasOrders ? Order::where('status', 'pending')->count() : 0;
        $paidOrdersCount = $hasOrders ? Order::where('status', 'paid')->count() : 0;

        $totalStudents = User::where('role', 'student')->count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalUsers = User::count();

        $totalCourses = $hasCourses ? Course::count() : 0;
        $forSaleCourses = $hasCourses ? Course::where('is_for_sale', true)->count() : 0;
        $totalLessons = $hasLessons ? Lesson::count() : 0;
        $totalExams = $hasExams ? ExamPaper::count() : 0;

        $recentOrders = $hasOrders 
            ? Order::with('payment')->latest('id')->take(6)->get() 
            : collect();

        $recentStudents = User::where('role', 'student')
            ->latest('id')
            ->take(5)
            ->get();

        $topCourses = $hasCourses 
            ? Course::withCount('lessons')->orderByDesc('id')->take(5)->get() 
            : collect();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'pendingOrdersCount',
            'paidOrdersCount',
            'totalStudents',
            'totalTeachers',
            'totalUsers',
            'totalCourses',
            'forSaleCourses',
            'totalLessons',
            'totalExams',
            'recentOrders',
            'recentStudents',
            'topCourses'
        ));
    }
}
