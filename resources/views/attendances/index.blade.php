@extends('layouts.app')

@section('title', 'Điểm danh lớp học')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4"><div><a class="small text-decoration-none" href="{{ route('enrollments.class_students', $classRoom) }}">← Danh sách học viên</a><h1 class="h3 fw-bold mt-2 mb-1">Điểm danh · {{ $classRoom->name }}</h1><div class="text-secondary">{{ $classRoom->code }} · {{ $classRoom->course->name }}</div></div>
    <div class="d-flex flex-wrap gap-2 align-items-end"><form method="GET" action="{{ route('attendances.index', $classRoom) }}" class="d-flex gap-2 align-items-end"><div><label class="form-label small fw-semibold" for="attendance_date">Ngày điểm danh</label><input class="form-control" type="date" id="attendance_date" name="attendance_date" value="{{ $date }}" required></div><button class="btn btn-outline-primary">Xem ngày</button></form><a class="btn btn-outline-success" href="{{ route('attendances.export', ['classRoom' => $classRoom, 'attendance_date' => $date]) }}"><i class="bi bi-file-earmark-excel me-1"></i>Xuất Excel</a></div></div>
<form method="POST" action="{{ route('attendances.store', $classRoom) }}">
    @csrf<input type="hidden" name="attendance_date" value="{{ $date }}">
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Học viên</th><th class="text-center">Trạng thái</th><th>Ghi chú</th></tr></thead><tbody>
        @forelse($classRoom->enrollments as $enrollment)
            @php($student = $enrollment->student)
            @php($record = $attendance->get($student->id))
            @php($selected = old("attendance.{$student->id}.status", $record->status ?? 'present'))
            <tr><td><div class="fw-semibold">{{ $student->user->name }}</div><div class="small text-secondary">{{ $student->student_code }}</div></td><td><div class="d-flex flex-wrap justify-content-center gap-3">
                @foreach(['present' => 'Có mặt', 'absent' => 'Vắng', 'late' => 'Đi trễ'] as $value => $label)
                    <label class="form-check"><input class="form-check-input" type="radio" name="attendance[{{ $student->id }}][status]" value="{{ $value }}" @checked($selected === $value) required><span class="form-check-label">{{ $label }}</span></label>
                @endforeach
            </div></td><td><input class="form-control" type="text" name="attendance[{{ $student->id }}][note]" value="{{ old("attendance.{$student->id}.note", $record->note ?? '') }}" maxlength="255" placeholder="Ghi chú tùy chọn"></td></tr>
        @empty<tr><td colspan="3" class="text-center py-5 text-secondary">Lớp chưa có học viên đăng ký.</td></tr>@endforelse
        </tbody></table></div><div class="card-footer bg-white d-flex justify-content-between align-items-center"><span class="small text-secondary">Lớp {{ $classRoom->code }} · {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span><button class="btn btn-primary" type="submit" @disabled($classRoom->enrollments->isEmpty())><i class="bi bi-check2-circle me-1"></i>Lưu điểm danh</button></div></div>
</form>
@endsection
