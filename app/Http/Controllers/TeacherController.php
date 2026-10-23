<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with(['user', 'classRooms.course'])->orderBy('teacher_code')->paginate(15);
        $publicTeachers = \Illuminate\Support\Facades\Schema::hasTable('public_teachers') 
            ? \App\Models\PublicTeacher::orderBy('position')->get() 
            : collect();

        $ptMap = $publicTeachers->keyBy('name');
        foreach ($teachers as $teacher) {
            $name = $teacher->user->name ?? '';
            $pt = $ptMap->get($name);
            if (! $pt) {
                $pt = $publicTeachers->first(function ($item) use ($name) {
                    return str_contains($name, $item->name) || str_contains($item->name, $name);
                });
            }
            $teacher->publicTeacher = $pt;
        }

        return view('teachers.index', compact('teachers', 'publicTeachers'));
    }

    public function create()
    {
        return view('teachers.form', ['teacher' => new Teacher]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'teacher_code' => 'required|string|max:30|unique:teachers,teacher_code',
            'specialization' => 'required|string|max:255',
            'degree' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'teacher',
                'phone' => $validated['phone'] ?? null,
                'is_active' => true,
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'teacher_code' => $validated['teacher_code'],
                'specialization' => $validated['specialization'],
                'degree' => $validated['degree'] ?? null,
                'bio' => $validated['bio'] ?? null,
            ]);
        });

        return redirect()->route('teachers.index')->with('success', 'Thêm giáo viên thành công.');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load('user', 'classRooms.course');
        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        return view('teachers.form', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($teacher->user_id)],
            'phone' => 'nullable|string|max:20',
            'teacher_code' => ['required', 'string', 'max:30', Rule::unique('teachers')->ignore($teacher->id)],
            'specialization' => 'required|string|max:255',
            'degree' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $teacher) {
            $teacher->user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
            ]);

            $teacher->update([
                'teacher_code' => $validated['teacher_code'],
                'specialization' => $validated['specialization'],
                'degree' => $validated['degree'] ?? null,
                'bio' => $validated['bio'] ?? null,
            ]);
        });

        return redirect()->route('teachers.index')->with('success', 'Cập nhật giáo viên thành công.');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->classRooms()->count() > 0) {
            return back()->with('error', 'Không thể xóa giáo viên đang phụ trách lớp học.');
        }

        DB::transaction(function () use ($teacher) {
            $teacher->user->delete();
        });

        return redirect()->route('teachers.index')->with('success', 'Đã xóa giáo viên.');
    }
}
