<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;

class ReportController extends Controller
{
    public function index()
    {
        $summary = [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'classes' => ClassRoom::count(),
            'ongoing' => ClassRoom::where('status', 'ongoing')->count(),
            'upcoming' => ClassRoom::where('status', 'upcoming')->count(),
            'completed' => ClassRoom::where('status', 'completed')->count(),
            'revenue' => Enrollment::where('payment_status', 'paid')->sum('paid_tuition'),
        ];

        $courses = Course::with(['classRooms' => function ($query) {
            $query->withCount('enrollments')
                ->withSum(['enrollments as paid_revenue' => fn ($enrollments) => $enrollments->where('payment_status', 'paid')], 'paid_tuition')
                ->orderBy('name');
        }])->orderBy('name')->get();

        return view('reports.index', compact('summary', 'courses'));
    }
}
