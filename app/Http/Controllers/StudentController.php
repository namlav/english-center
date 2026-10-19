<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('user')->orderBy('created_at', 'desc')->paginate(15);
        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'student_code' => 'required|string|max:30|unique:students,student_code',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'student',
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            Student::create([
                'user_id' => $user->id,
                'student_code' => $validated['student_code'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Thêm học viên thành công.');
    }

    public function show(Student $student)
    {
        $student->load('user', 'enrollments.classRoom.course');
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $student->load('user');
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($student->user_id)],
            'phone' => 'nullable|string|max:20',
            'student_code' => ['required', 'string', 'max:30', Rule::unique('students')->ignore($student->id)],
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $student) {
            $student->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]);

            $student->update([
                'student_code' => $validated['student_code'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'gender' => $validated['gender'] ?? null,
                'address' => $validated['address'] ?? null,
            ]);
        });

        return redirect()->route('students.index')->with('success', 'Cập nhật học viên thành công.');
    }

    public function destroy(Student $student)
    {
        if ($student->enrollments()->count() > 0) {
            return back()->with('error', 'Không thể xóa học viên đã đăng ký lớp học.');
        }

        DB::transaction(function () use ($student) {
            $student->user->delete();
        });

        return redirect()->route('students.index')->with('success', 'Đã xóa học viên.');
    }
}
