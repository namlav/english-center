@extends('layouts.app')

@section('title', 'Danh sách học viên')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4"><div><a href="{{ route(auth()->user()->isAdmin() ? 'class_rooms.index' : 'teacher.classes') }}" class="small text-decoration-none">← Quay lại</a><h1 class="h3 fw-bold mt-2 mb-1">{{ $classRoom->name }}</h1><div class="text-secondary">{{ $classRoom->code }} · {{ $classRoom->course->name }} · {{ $classRoom->teacher->user->name }}</div></div>
    <div class="d-flex gap-2"><a class="btn btn-outline-secondary" href="{{ route('attendances.index', $classRoom) }}">Điểm danh</a><a class="btn btn-outline-warning" href="{{ route('grades.index', $classRoom) }}">Nhập điểm</a></div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><strong>{{ $classRoom->enrollments->count() }}</strong> học viên · Lịch: {{ $classRoom->schedule }}</div><div class="table-responsive"><table class="table table-hover align-middle mb-0">
    <thead class="table-light"><tr><th>Học viên</th><th>Mã học viên</th><th>Email</th><th>Ngày đăng ký</th><th>Điểm danh</th><th>Điểm</th><th>Kết quả</th></tr></thead><tbody>
    @forelse($classRoom->enrollments as $enrollment)
        @php($student = $enrollment->student)
        @php($counts = $attendanceCounts->get($student->id))
        <tr><td class="fw-semibold">{{ $student->user->name }}</td><td>{{ $student->student_code }}</td><td>{{ $student->user->email }}</td><td>{{ $enrollment->enrollment_date->format('d/m/Y') }}</td><td><span class="text-success">{{ (int) ($counts->present_count ?? 0) }} có mặt</span><div class="small text-danger">{{ (int) ($counts->absent_count ?? 0) }} vắng · {{ (int) ($counts->late_count ?? 0) }} trễ</div></td><td>{{ $enrollment->midterm_score ?? '—' }} / {{ $enrollment->final_score ?? '—' }}</td><td>{{ ['studying' => 'Đang học', 'passed' => 'Đạt', 'failed' => 'Chưa đạt'][$enrollment->result_status] }}</td></tr>
    @empty<tr><td colspan="7" class="text-center py-5 text-secondary">Lớp chưa có học viên đăng ký.</td></tr>@endforelse
    </tbody></table></div></div>
@endsection
