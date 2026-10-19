@extends('layouts.app')

@section('title', 'Lớp học đã đăng ký')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div><p class="text-primary fw-semibold mb-1">KHU VỰC HỌC VIÊN</p><h1 class="h3 fw-bold mb-0">Lớp học của tôi</h1></div>
    <a href="{{ route('class_rooms.index') }}" class="btn btn-outline-primary"><i class="bi bi-search me-1"></i> Tìm lớp học</a>
</div>

<div class="card border-0 shadow-sm"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Lớp học</th><th>Khóa học</th><th>Giáo viên</th><th>Lịch học</th><th>Điểm danh</th><th>Kết quả</th><th>Học phí</th></tr></thead>
        <tbody>
        @forelse($enrollments as $enrollment)
            @php($classRoom = $enrollment->classRoom)
            <tr>
                <td><a class="fw-semibold text-decoration-none" href="{{ route('class_rooms.show', $classRoom) }}">{{ $classRoom->name }}</a><div class="small text-secondary">{{ $classRoom->code }}</div></td>
                <td>{{ $classRoom->course->name }}</td>
                <td>{{ $classRoom->teacher->user->name }}</td>
                <td>{{ $classRoom->schedule }}<div class="small text-secondary">Bắt đầu {{ $classRoom->start_date->format('d/m/Y') }}</div></td>
                <td><span class="text-success">{{ $enrollment->present_count }} có mặt</span><div class="small text-danger">{{ $enrollment->absent_count }} vắng</div></td>
                <td><span class="badge {{ $enrollment->result_status === 'passed' ? 'text-bg-success' : ($enrollment->result_status === 'failed' ? 'text-bg-danger' : 'text-bg-secondary') }}">{{ ['studying' => 'Đang học', 'passed' => 'Đạt', 'failed' => 'Chưa đạt'][$enrollment->result_status] }}</span><div class="small text-secondary mt-1">GK: {{ $enrollment->midterm_score ?? '—' }} · CK: {{ $enrollment->final_score ?? '—' }}</div></td>
                <td>{{ number_format((float) $enrollment->paid_tuition, 0, ',', '.') }} ₫<div class="small text-secondary">{{ $enrollment->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}</div></td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center py-5"><i class="bi bi-journal-x fs-2 text-secondary d-block mb-2"></i><span class="text-secondary">Bạn chưa đăng ký lớp nào.</span><div class="mt-3"><a class="btn btn-primary" href="{{ route('class_rooms.index') }}">Xem danh sách lớp</a></div></td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
@endsection
