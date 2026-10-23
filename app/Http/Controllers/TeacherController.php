<?php

namespace App\Http\Controllers;

use App\Models\PublicTeacher;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::with(['user', 'classRooms.course'])->orderBy('teacher_code')->paginate(15);
        $publicTeachers = Schema::hasTable('public_teachers') 
            ? PublicTeacher::orderBy('position')->get() 
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
        $maxPos = Schema::hasTable('public_teachers') 
            ? ((int) PublicTeacher::max('position') + 1) 
            : 1;

        $publicTeacher = new PublicTeacher([
            'is_active' => true,
            'position' => $maxPos,
            'provider' => 'English Center',
        ]);

        return view('teachers.form', [
            'teacher' => new Teacher,
            'publicTeacher' => $publicTeacher,
        ]);
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
            'provider' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'preset_image' => 'nullable|string|max:255',
            'show_on_banner' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0|max:9999',
        ]);

        $photoPath = $this->uploadImage($request);
        if (! $photoPath && ! empty($validated['preset_image'])) {
            $photoPath = $validated['preset_image'];
        }
        if (! $photoPath) {
            $photoPath = 'images/teachers/prep-33.png';
        }

        $showOnBanner = $request->has('show_on_banner') ? $request->boolean('show_on_banner') : true;

        DB::transaction(function () use ($validated, $photoPath, $showOnBanner) {
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

            if (Schema::hasTable('public_teachers')) {
                $slugBase = Str::slug($validated['name']);
                if (empty($slugBase)) {
                    $slugBase = 'giao-vien-' . strtolower($validated['teacher_code']);
                }
                $slug = $slugBase;
                $counter = 1;
                while (PublicTeacher::where('slug', $slug)->exists()) {
                    $slug = $slugBase . '-' . $counter++;
                }

                $qualifications = array_values(array_filter([
                    $validated['degree'] ?? null,
                    $validated['specialization'] ?? null,
                ]));

                $position = isset($validated['position']) && $validated['position'] !== '' 
                    ? (int) $validated['position'] 
                    : ((int) PublicTeacher::max('position') + 1);

                PublicTeacher::create([
                    'name' => $validated['name'],
                    'slug' => $slug,
                    'bio' => $validated['bio'] ?: ($validated['specialization'] . ' — ' . ($validated['degree'] ?? '')),
                    'photo_path' => $photoPath,
                    'provider' => $validated['provider'] ?: ($validated['degree'] ?: 'English Center'),
                    'qualifications' => $qualifications,
                    'position' => $position,
                    'is_active' => $showOnBanner,
                ]);
            }
        });

        return redirect()->route('teachers.index')->with('success', 'Thêm giáo viên và cập nhật hiển thị lên Banner website thành công.');
    }

    public function show(Teacher $teacher)
    {
        $teacher->load('user', 'classRooms.course');
        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $teacher->load('user');
        $publicTeacher = Schema::hasTable('public_teachers') 
            ? (PublicTeacher::where('name', $teacher->user->name ?? '')->first() ?? new PublicTeacher(['is_active' => true, 'position' => 1]))
            : null;

        return view('teachers.form', compact('teacher', 'publicTeacher'));
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
            'provider' => 'nullable|string|max:100',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'preset_image' => 'nullable|string|max:255',
            'show_on_banner' => 'nullable|boolean',
            'position' => 'nullable|integer|min:0|max:9999',
        ]);

        $photoPath = $this->uploadImage($request);
        if (! $photoPath && ! empty($validated['preset_image'])) {
            $photoPath = $validated['preset_image'];
        }

        $showOnBanner = $request->has('show_on_banner') ? $request->boolean('show_on_banner') : true;

        DB::transaction(function () use ($validated, $teacher, $photoPath, $showOnBanner) {
            $oldName = $teacher->user->name ?? '';

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

            if (Schema::hasTable('public_teachers')) {
                $pt = PublicTeacher::where('name', $oldName)->first()
                    ?: PublicTeacher::where('name', $validated['name'])->first();

                $qualifications = array_values(array_filter([
                    $validated['degree'] ?? null,
                    $validated['specialization'] ?? null,
                ]));

                $position = isset($validated['position']) && $validated['position'] !== '' 
                    ? (int) $validated['position'] 
                    : ($pt?->position ?? ((int) PublicTeacher::max('position') + 1));

                if ($pt) {
                    $updateData = [
                        'name' => $validated['name'],
                        'bio' => $validated['bio'] ?: ($validated['specialization'] . ' — ' . ($validated['degree'] ?? '')),
                        'provider' => $validated['provider'] ?: ($validated['degree'] ?: $pt->provider),
                        'qualifications' => $qualifications,
                        'position' => $position,
                        'is_active' => $showOnBanner,
                    ];
                    if ($photoPath) {
                        $updateData['photo_path'] = $photoPath;
                    }
                    $pt->update($updateData);
                } else {
                    $slugBase = Str::slug($validated['name']);
                    $slug = $slugBase ?: 'giao-vien-' . strtolower($validated['teacher_code']);
                    $counter = 1;
                    while (PublicTeacher::where('slug', $slug)->exists()) {
                        $slug = $slugBase . '-' . $counter++;
                    }

                    PublicTeacher::create([
                        'name' => $validated['name'],
                        'slug' => $slug,
                        'bio' => $validated['bio'] ?: ($validated['specialization'] . ' — ' . ($validated['degree'] ?? '')),
                        'photo_path' => $photoPath ?: 'images/teachers/prep-33.png',
                        'provider' => $validated['provider'] ?: ($validated['degree'] ?: 'English Center'),
                        'qualifications' => $qualifications,
                        'position' => $position,
                        'is_active' => $showOnBanner,
                    ]);
                }
            }
        });

        return redirect()->route('teachers.index')->with('success', 'Cập nhật giáo viên và Banner website thành công.');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->classRooms()->count() > 0) {
            return back()->with('error', 'Không thể xóa giáo viên đang phụ trách lớp học.');
        }

        DB::transaction(function () use ($teacher) {
            $name = $teacher->user->name ?? '';
            $teacher->user->delete();

            if (Schema::hasTable('public_teachers') && $name) {
                PublicTeacher::where('name', $name)->delete();
            }
        });

        return redirect()->route('teachers.index')->with('success', 'Đã xóa giáo viên khỏi hệ thống.');
    }

    private function uploadImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $directory = public_path('images/uploads');
        File::ensureDirectoryExists($directory);

        $name = bin2hex(random_bytes(16)) . '.' . $file->extension();
        $path = $directory . DIRECTORY_SEPARATOR . $name;

        if (File::put($path, File::get($file->getRealPath())) === false) {
            return null;
        }

        return 'images/uploads/' . $name;
    }
}
