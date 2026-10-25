# 🤖 AI AGENT MASTER SPECIFICATION – DỰ ÁN `english-center`

> **HƯỚNG DẪN DÀNH CHO AI CODING AGENT:**
> Bạn đang đóng vai trò là Senior Laravel Architect. Hãy đọc toàn bộ file đặc tả này và **tạo đầy đủ khung dự án Laravel hoàn chỉnh** bao gồm: Cấu hình `.env.example`, 7 file Migrations, 7 Eloquent Models (đầy đủ Relationships), `DatabaseSeeder`, Middleware `CheckRole`, cấu trúc tách 3 file Routes (`routes/member1.php`, `routes/member2.php`, `routes/member3.php`), toàn bộ Controllers (với code hoàn chỉnh cho phần Khung/Auth của Thành viên 2 và khung phương thức chuẩn cho Thành viên 1 & 3), cùng Master Layout Bootstrap 5.

---

## 1. CẤU TRÚC THƯ MỤC DỰ ÁN CẦN TẠO

```text
english-center/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php          # [Thành viên 2] Đăng nhập, Đăng xuất, Hồ sơ
│   │   │   ├── UserController.php          # [Thành viên 2] Quản lý tài khoản & phân quyền
│   │   │   ├── TeacherController.php       # [Thành viên 2] Quản lý giáo viên
│   │   │   ├── StudentController.php       # [Thành viên 2] Quản lý học viên
│   │   │   ├── CourseController.php        # [Thành viên 1] CRUD Khóa học
│   │   │   ├── ClassRoomController.php     # [Thành viên 1] CRUD Lớp học + Tìm kiếm/Lọc
│   │   │   ├── EnrollmentController.php    # [Thành viên 3] Đăng ký lớp & DS học viên theo lớp
│   │   │   ├── AttendanceController.php    # [Thành viên 3] Điểm danh
│   │   │   ├── GradeController.php         # [Thành viên 3] Nhập điểm / Kết quả học tập
│   │   │   └── ReportController.php        # [Thành viên 3] Thống kê học viên, lớp, doanh thu
│   │   └── Middleware/
│   │       └── CheckRole.php               # [Thành viên 2] Middleware phân quyền RBAC
│   └── Models/
│       ├── User.php
│       ├── Teacher.php
│       ├── Student.php
│       ├── Course.php
│       ├── ClassRoom.php
│       ├── Enrollment.php
│       └── Attendance.php
├── database/
│   ├── migrations/
│   │   ├── 2026_01_01_000001_create_users_table.php
│   │   ├── 2026_01_01_000002_create_teachers_table.php
│   │   ├── 2026_01_01_000003_create_students_table.php
│   │   ├── 2026_01_01_000004_create_courses_table.php
│   │   ├── 2026_01_01_000005_create_class_rooms_table.php
│   │   ├── 2026_01_01_000006_create_enrollments_table.php
│   │   └── 2026_01_01_000007_create_attendances_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php               # Giao diện Master Layout dùng chung (Bootstrap 5)
│       ├── auth/
│       │   └── login.blade.php             # [Thành viên 2]
│       ├── users/                          # [Thành viên 2]
│       ├── teachers/                       # [Thành viên 2]
│       ├── students/                       # [Thành viên 2]
│       ├── courses/                        # [Thành viên 1]
│       ├── class_rooms/                    # [Thành viên 1]
│       ├── enrollments/                    # [Thành viên 3]
│       ├── attendances/                    # [Thành viên 3]
│       ├── grades/                         # [Thành viên 3]
│       └── reports/                        # [Thành viên 3]
└── routes/
    ├── web.php                             # Nạp 3 file route của 3 thành viên
    ├── member1.php                         # Route riêng của Thành viên 1
    ├── member2.php                         # Route riêng của Thành viên 2
    └── member3.php                         # Route riêng của Thành viên 3
```

---

## 2. ĐẶC TẢ CHI TIẾT MIGRATIONS (7 BẢNG CƠ SỞ DỮ LIỆU)

### 2.1. `2026_01_01_000001_create_users_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student');
            $table->string('phone', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

### 2.2. `2026_01_01_000002_create_teachers_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('teacher_code', 30)->unique();
            $table->string('specialization'); // VD: IELTS 8.5, TOEIC 990
            $table->string('degree')->nullable(); // VD: Thạc sĩ TESOL
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
```

### 2.3. `2026_01_01_000003_create_students_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('student_code', 30)->unique();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
```

### 2.4. `2026_01_01_000004_create_courses_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // VD: TOEIC, IELTS, TAGT
            $table->string('name'); // VD: Luyện thi IELTS, Luyện thi TOEIC, Tiếng Anh giao tiếp
            $table->string('level')->nullable(); // VD: Cơ bản, Trung cấp, Nâng cao
            $table->integer('duration_hours')->default(48); // Tổng số giờ học
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
```

### 2.5. `2026_01_01_000005_create_class_rooms_table.php`
*(Lưu ý: Bắt buộc có đủ các cột: tên lớp, khóa học, giáo viên, ngày bắt đầu, lịch học, học phí, số lượng tối đa theo đúng yêu cầu đề bài)*.
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique(); // VD: IELTS-K01
            $table->string('name'); // Tên lớp
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade'); // Khóa học
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('restrict'); // Giáo viên phụ trách
            $table->date('start_date'); // Ngày bắt đầu
            $table->string('schedule'); // Lịch học (VD: Thứ 2-4-6, 18:00 - 20:00)
            $table->decimal('tuition_fee', 12, 2)->default(0); // Học phí (VNĐ)
            $table->unsignedInteger('max_students')->default(25); // Số lượng học viên tối đa
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};
```

### 2.6. `2026_01_01_000006_create_enrollments_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_room_id')->constrained('class_rooms')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('enrollment_date');
            $table->decimal('paid_tuition', 12, 2)->default(0); // Học phí thực đóng (dùng thống kê doanh thu)
            $table->enum('payment_status', ['paid', 'unpaid'])->default('paid');
            $table->decimal('midterm_score', 5, 2)->nullable(); // Điểm giữa kỳ
            $table->decimal('final_score', 5, 2)->nullable();   // Điểm cuối kỳ / Tổng kết
            $table->enum('result_status', ['studying', 'passed', 'failed'])->default('studying');
            $table->text('remarks')->nullable(); // Nhận xét kết quả học tập
            $table->timestamps();

            // 1 học viên chỉ được đăng ký 1 lần vào cùng 1 lớp học
            $table->unique(['class_room_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
```

### 2.7. `2026_01_01_000007_create_attendances_table.php`
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_room_id')->constrained('class_rooms')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late'])->default('present');
            $table->string('note')->nullable();
            $table->timestamps();

            // Mỗi học viên trong 1 lớp chỉ có 1 bản ghi điểm danh trong cùng 1 ngày
            $table->unique(['class_room_id', 'student_id', 'attendance_date'], 'uniq_class_student_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
```

---

## 3. ĐẶC TẢ ELOQUENT MODELS (`app/Models/`)

### 3.1. `app/Models/User.php`
```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'is_active'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }
}
```

### 3.2. `app/Models/Teacher.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id', 'teacher_code', 'specialization', 'degree', 'bio'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class);
    }
}
```

### 3.3. `app/Models/Student.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'student_code', 'date_of_birth', 'gender', 'address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function classRooms()
    {
        return $this->belongsToMany(ClassRoom::class, 'enrollments')
                    ->withPivot(['enrollment_date', 'paid_tuition', 'payment_status', 'midterm_score', 'final_score', 'result_status', 'remarks'])
                    ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
```

### 3.4. `app/Models/Course.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = [
        'code', 'name', 'level', 'duration_hours', 'description', 'is_active'
    ];

    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class);
    }
}
```

### 3.5. `app/Models/ClassRoom.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $fillable = [
        'code', 'name', 'course_id', 'teacher_id', 'start_date',
        'schedule', 'tuition_fee', 'max_students', 'status'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'enrollments')
                    ->withPivot(['id', 'enrollment_date', 'paid_tuition', 'payment_status', 'midterm_score', 'final_score', 'result_status', 'remarks'])
                    ->withTimestamps();
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Scope phục vụ Thành viên 1 lọc lớp theo khóa học hoặc giáo viên
    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['keyword'] . '%')
                  ->orWhere('code', 'like', '%' . $filters['keyword'] . '%');
            });
        }
        if (!empty($filters['course_id'])) {
            $query->where('course_id', $filters['course_id']);
        }
        if (!empty($filters['teacher_id'])) {
            $query->where('teacher_id', $filters['teacher_id']);
        }
        return $query;
    }
}
```

### 3.6. `app/Models/Enrollment.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    protected $fillable = [
        'class_room_id', 'student_id', 'enrollment_date',
        'paid_tuition', 'payment_status', 'midterm_score',
        'final_score', 'result_status', 'remarks'
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
```

### 3.7. `app/Models/Attendance.php`
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'class_room_id', 'student_id', 'attendance_date', 'status', 'note'
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
```

---

## 4. DỮ LIỆU MẪU (`database/seeders/DatabaseSeeder.php`)

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Course;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Attendance;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo tài khoản Admin
        User::create([
            'name' => 'Quản Trị Viên Hệ Thống',
            'email' => 'admin@englishcenter.vn',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'phone' => '0901000001',
            'is_active' => true,
        ]);

        // 2. Tạo 2 Giáo viên mẫu
        $gv1User = User::create([
            'name' => 'ThS. Nguyễn Quang Minh',
            'email' => 'gv.minh@englishcenter.vn',
            'password' => Hash::make('123456'),
            'role' => 'teacher',
            'phone' => '0902000001',
            'is_active' => true,
        ]);
        $gv1 = Teacher::create([
            'user_id' => $gv1User->id,
            'teacher_code' => 'GV001',
            'specialization' => 'IELTS Academic 8.5',
            'degree' => 'Thạc sĩ Lý luận & PPDH Tiếng Anh (TESOL)',
            'bio' => '8 năm kinh nghiệm luyện thi IELTS cấp tốc.',
        ]);

        $gv2User = User::create([
            'name' => 'Cô Trần Phương Lan',
            'email' => 'gv.lan@englishcenter.vn',
            'password' => Hash::make('123456'),
            'role' => 'teacher',
            'phone' => '0902000002',
            'is_active' => true,
        ]);
        $gv2 = Teacher::create([
            'user_id' => $gv2User->id,
            'teacher_code' => 'GV002',
            'specialization' => 'TOEIC 990 & Tiếng Anh Giao Tiếp',
            'degree' => 'Cử nhân Ngôn ngữ Anh ĐH Sư Phạm',
            'bio' => 'Chuyên gia luyện thi TOEIC 4 kỹ năng và phản xạ giao tiếp.',
        ]);

        // 3. Tạo 3 Học viên mẫu
        $studentsData = [
            ['name' => 'Lê Hoàng An', 'email' => 'hv.an@englishcenter.vn', 'code' => 'HV001', 'dob' => '2004-05-12', 'gender' => 'male'],
            ['name' => 'Phạm Thanh Bình', 'email' => 'hv.binh@englishcenter.vn', 'code' => 'HV002', 'dob' => '2005-08-20', 'gender' => 'female'],
            ['name' => 'Võ Minh Châu', 'email' => 'hv.chau@englishcenter.vn', 'code' => 'HV003', 'dob' => '2004-11-03', 'gender' => 'female'],
        ];
        $students = [];
        foreach ($studentsData as $idx => $st) {
            $u = User::create([
                'name' => $st['name'],
                'email' => $st['email'],
                'password' => Hash::make('123456'),
                'role' => 'student',
                'phone' => '090300000' . ($idx + 1),
                'is_active' => true,
            ]);
            $students[] = Student::create([
                'user_id' => $u->id,
                'student_code' => $st['code'],
                'date_of_birth' => $st['dob'],
                'gender' => $st['gender'],
                'address' => 'TP. Hồ Chí Minh',
            ]);
        }

        // 4. Tạo 3 Khóa học mẫu
        $courseIelts = Course::create([
            'code' => 'KH-IELTS',
            'name' => 'Luyện thi IELTS Intensive (6.5+)',
            'level' => 'Nâng cao',
            'duration_hours' => 60,
            'description' => 'Khóa học luyện thi IELTS 4 kỹ năng cam kết đầu ra 6.5+.',
        ]);
        $courseToeic = Course::create([
            'code' => 'KH-TOEIC',
            'name' => 'Luyện thi TOEIC 650+ Cấp tốc',
            'level' => 'Trung cấp',
            'duration_hours' => 45,
            'description' => 'Bứt phá điểm số TOEIC Listening & Reading trong 2 tháng.',
        ]);
        $courseComm = Course::create([
            'code' => 'KH-TAGT',
            'name' => 'Tiếng Anh Giao Tiếp Công Sở',
            'level' => 'Cơ bản - Trung cấp',
            'duration_hours' => 36,
            'description' => 'Thực hành phản xạ giao tiếp và thuyết trình bằng tiếng Anh.',
        ]);

        // 5. Tạo 3 Lớp học mẫu
        $class1 = ClassRoom::create([
            'code' => 'IELTS-2601',
            'name' => 'IELTS Intensive K01 - Tối 2-4-6',
            'course_id' => $courseIelts->id,
            'teacher_id' => $gv1->id,
            'start_date' => '2026-10-15',
            'schedule' => 'Thứ 2 - 4 - 6 (18:00 - 20:00)',
            'tuition_fee' => 4500000,
            'max_students' => 20,
            'status' => 'ongoing',
        ]);
        $class2 = ClassRoom::create([
            'code' => 'TOEIC-2601',
            'name' => 'TOEIC 650+ K01 - Tối 3-5-7',
            'course_id' => $courseToeic->id,
            'teacher_id' => $gv2->id,
            'start_date' => '2026-10-20',
            'schedule' => 'Thứ 3 - 5 - 7 (18:30 - 20:30)',
            'tuition_fee' => 2800000,
            'max_students' => 25,
            'status' => 'ongoing',
        ]);
        $class3 = ClassRoom::create([
            'code' => 'TAGT-2601',
            'name' => 'Giao Tiếp Công Sở K01 - Cuối tuần',
            'course_id' => $courseComm->id,
            'teacher_id' => $gv2->id,
            'start_date' => '2026-11-01',
            'schedule' => 'Thứ 7 - CN (08:30 - 10:30)',
            'tuition_fee' => 2200000,
            'max_students' => 15,
            'status' => 'upcoming',
        ]);

        // 6. Tạo Đăng ký lớp & Điểm mẫu
        Enrollment::create([
            'class_room_id' => $class1->id,
            'student_id' => $students[0]->id,
            'enrollment_date' => '2026-10-10',
            'paid_tuition' => 4500000,
            'payment_status' => 'paid',
            'midterm_score' => 7.0,
            'final_score' => 7.5,
            'result_status' => 'passed',
            'remarks' => 'Nắm vững cấu trúc Writing Task 2.',
        ]);
        Enrollment::create([
            'class_room_id' => $class1->id,
            'student_id' => $students[1]->id,
            'enrollment_date' => '2026-10-11',
            'paid_tuition' => 4500000,
            'payment_status' => 'paid',
            'midterm_score' => 6.5,
            'final_score' => null,
            'result_status' => 'studying',
        ]);
        Enrollment::create([
            'class_room_id' => $class2->id,
            'student_id' => $students[0]->id,
            'enrollment_date' => '2026-10-12',
            'paid_tuition' => 2800000,
            'payment_status' => 'paid',
        ]);

        // 7. Tạo Điểm danh mẫu
        Attendance::create([
            'class_room_id' => $class1->id,
            'student_id' => $students[0]->id,
            'attendance_date' => '2026-10-15',
            'status' => 'present',
            'note' => 'Đi học đúng giờ',
        ]);
        Attendance::create([
            'class_room_id' => $class1->id,
            'student_id' => $students[1]->id,
            'attendance_date' => '2026-10-15',
            'status' => 'late',
            'note' => 'Kẹt xe trễ 10 phút',
        ]);
    }
}
```

---

## 5. CẤU HÌNH MIDDLEWARE PHÂN QUYỀN & ROUTES KHÔNG XUNG ĐỘT

### 5.1. `app/Http/Middleware/CheckRole.php`
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để tiếp tục.');
        }

        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Tài khoản của bạn đã bị khóa.');
        }

        if (!in_array($user->role, $roles)) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}
```
*(Đăng ký alias `'role' => \App\Http\Middleware\CheckRole::class` trong `app/Http/Kernel.php` đối với Laravel 10 hoặc `bootstrap/app.php` đối với Laravel 11).*

### 5.2. `routes/web.php` (Tập tin nạp chung)
```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('class_rooms.index');
});

// Nạp file route riêng của từng thành viên để tránh Git Conflict
require __DIR__ . '/member1.php';
require __DIR__ . '/member2.php';
require __DIR__ . '/member3.php';
```

### 5.3. `routes/member1.php` (Dành riêng cho Thành viên 1)
```php
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
```

### 5.4. `routes/member2.php` (Dành riêng cho Thành viên 2 - Bạn phụ trách)
```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;

// Xác thực Đăng nhập / Đăng xuất
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Quản lý Tài khoản, Giáo viên, Học viên (Chỉ Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('students', StudentController::class);
});
```

### 5.5. `routes/member3.php` (Dành riêng cho Thành viên 3)
```php
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
```

---

## 6. GIAO DIỆN MASTER LAYOUT DÙNG CHUNG (`resources/views/layouts/app.blade.php`)

AI Agent cần tạo file `resources/views/layouts/app.blade.php` sử dụng Bootstrap 5.3 CDN để cả 3 thành viên `@extends('layouts.app')` và `@section('content')`:

```html
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Hệ Thống Quản Lý Trung Tâm Ngoại Ngữ')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('class_rooms.index') }}">
                <i class="bi bi-mortarboard-fill me-1"></i> ENGLISH CENTER
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                @auth
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('class_rooms.index') }}">Tra cứu Lớp học</a>
                    </li>

                    @if(auth()->user()->isAdmin())
                        <li class="nav-item"><a class="nav-link" href="{{ route('courses.index') }}">Khóa học</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('teachers.index') }}">Giáo viên</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('students.index') }}">Học viên</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">Tài khoản</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold text-warning" href="{{ route('reports.index') }}">Thống kê</a></li>
                    @endif

                    @if(auth()->user()->isTeacher())
                        <li class="nav-item">
                            <a class="nav-link fw-semibold text-warning" href="{{ route('teacher.classes') }}">Lớp tôi phụ trách</a>
                        </li>
                    @endif

                    @if(auth()->user()->isStudent())
                        <li class="nav-item">
                            <a class="nav-link fw-semibold text-warning" href="{{ route('enrollments.my_classes') }}">Lớp đã đăng ký</a>
                        </li>
                    @endif
                </ul>

                <div class="d-flex align-items-center text-white">
                    <span class="me-3">
                        <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                        <span class="badge bg-warning text-dark text-uppercase ms-1">{{ auth()->user()->role }}</span>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-light" type="submit">Đăng xuất</button>
                    </form>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```