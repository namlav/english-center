@extends('layouts.app')

@section('title', 'Bảng điểm lớp học')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-2 mb-4"><div><a class="small text-decoration-none" href="{{ route('enrollments.class_students', $classRoom) }}">← Danh sách học viên</a><h1 class="h3 fw-bold mt-2 mb-1">Bảng điểm · {{ $classRoom->name }}</h1><div class="text-secondary">{{ $classRoom->code }} · {{ $classRoom->course->name }}</div></div><div class="d-flex align-items-center gap-2"><span class="small text-secondary me-2">Thang điểm 10</span><a class="btn btn-outline-success btn-sm" href="{{ route('grades.export', $classRoom) }}"><i class="bi bi-file-earmark-excel me-1"></i>Xuất Excel</a><a class="btn btn-outline-secondary btn-sm" href="{{ route('grades.print', $classRoom) }}" target="_blank"><i class="bi bi-printer me-1"></i>In / Xuất PDF</a></div></div>
<form method="POST" action="{{ route('grades.update_all', $classRoom) }}">@csrf
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Học viên</th><th style="min-width:130px">Giữa kỳ</th><th style="min-width:130px">Cuối kỳ</th><th style="min-width:160px">Kết quả</th><th style="min-width:240px">Nhận xét</th></tr></thead><tbody>
        @forelse($classRoom->enrollments as $enrollment)
            @php($student = $enrollment->student)
            <tr><td><div class="fw-semibold">{{ $student->user->name }}</div><div class="small text-secondary">{{ $student->student_code }}</div></td>
                <td><input class="form-control" type="number" name="enrollments[{{ $student->id }}][midterm_score]" value="{{ old("enrollments.{$student->id}.midterm_score", $enrollment->midterm_score) }}" min="0" max="10" step="0.01" aria-label="Điểm giữa kỳ của {{ $student->user->name }}"></td>
                <td><input class="form-control" type="number" name="enrollments[{{ $student->id }}][final_score]" value="{{ old("enrollments.{$student->id}.final_score", $enrollment->final_score) }}" min="0" max="10" step="0.01" aria-label="Điểm cuối kỳ của {{ $student->user->name }}"></td>
                <td><select class="form-select" name="enrollments[{{ $student->id }}][result_status]" required>@foreach(['studying' => 'Đang học', 'passed' => 'Đạt', 'failed' => 'Chưa đạt'] as $value => $label)<option value="{{ $value }}" @selected(old("enrollments.{$student->id}.result_status", $enrollment->result_status) === $value)>{{ $label }}</option>@endforeach</select></td>
                <td><input class="form-control" type="text" name="enrollments[{{ $student->id }}][remarks]" value="{{ old("enrollments.{$student->id}.remarks", $enrollment->remarks) }}" maxlength="2000" placeholder="Nhận xét"></td></tr>
        @empty<tr><td colspan="5" class="text-center py-5 text-secondary">Lớp chưa có học viên đăng ký.</td>@endforelse
        </tbody></table></div><div class="card-footer bg-white d-flex justify-content-between align-items-center"><span class="small text-secondary">{{ $classRoom->enrollments->count() }} học viên</span><button class="btn btn-primary" type="submit" @disabled($classRoom->enrollments->isEmpty())><i class="bi bi-save me-1"></i>Lưu bảng điểm</button></div></div>
</form>
@endsection
