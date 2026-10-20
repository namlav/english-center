@extends('layouts.app')

@section('title', 'Lớp học đã đăng ký')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div><p class="text-primary fw-semibold mb-1">KHU VỰC HỌC VIÊN</p><h1 class="h3 fw-bold mb-0">Lớp học của tôi</h1></div>
    <a href="{{ route('class_rooms.index') }}" class="btn btn-outline-primary"><i class="bi bi-search me-1"></i> Tìm lớp học</a>
</div>

@if($refundDetails = session('refund_details'))
    <div class="alert alert-info border-0 shadow-sm" role="status">
        <h2 class="h6 fw-bold"><i class="bi bi-cash-coin me-1"></i> Hướng dẫn hoàn tiền</h2>
        <p class="mb-2">Lớp <strong>{{ $refundDetails['class_name'] }}</strong> đã được hủy đăng ký. Hệ thống ghi nhận bạn đã thanh toán {{ number_format($refundDetails['amount'], 0, ',', '.') }} ₫. Vui lòng liên hệ người phụ trách <strong>{{ config('app.management_contact_name') }}</strong> để được hướng dẫn hoàn tiền.</p>
        @php($managementEmail = config('app.management_contact_email'))
        @php($managementPhone = config('app.management_contact_phone'))
        @if($managementEmail || $managementPhone)
            <div class="d-flex flex-wrap gap-3">
                @if($managementEmail)<span><i class="bi bi-envelope me-1"></i><a href="mailto:{{ $managementEmail }}">{{ $managementEmail }}</a></span>@endif
                @if($managementPhone)<span><i class="bi bi-telephone me-1"></i><a href="tel:{{ preg_replace('/[^0-9+]/', '', $managementPhone) }}">{{ $managementPhone }}</a></span>@endif
            </div>
        @endif
    </div>
@endif

<div class="card border-0 shadow-sm"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Lớp học</th><th>Khóa học</th><th>Giáo viên</th><th>Lịch học</th><th>Điểm danh</th><th>Kết quả</th><th>Học phí</th><th>Thao tác</th></tr></thead>
        <tbody>
        @forelse($enrollments as $enrollment)
            @php($classRoom = $enrollment->classRoom)
            <tr>
                <td><a class="fw-semibold text-decoration-none" href="{{ route('class_rooms.show', $classRoom) }}">{{ $classRoom->name }}</a><div class="small text-secondary">{{ $classRoom->code }}</div></td>
                <td>{{ $classRoom->course->name }}</td>
                <td>{{ $classRoom->teacher->user->name }}</td>
                <td>{{ $classRoom->schedule }}<div class="small text-secondary">Bắt đầu {{ $classRoom->start_date->format('d/m/Y') }} · Kết thúc dự kiến {{ $classRoom->expected_end_date?->format('d/m/Y') ?? 'Chưa cập nhật' }}</div></td>
                <td><span class="text-success">{{ $enrollment->present_count }} có mặt</span><div class="small text-danger">{{ $enrollment->absent_count }} vắng</div></td>
                <td><span class="badge {{ $enrollment->result_status === 'passed' ? 'text-bg-success' : ($enrollment->result_status === 'failed' ? 'text-bg-danger' : 'text-bg-secondary') }}">{{ ['studying' => 'Đang học', 'passed' => 'Đạt', 'failed' => 'Chưa đạt'][$enrollment->result_status] }}</span><div class="small text-secondary mt-1">GK: {{ $enrollment->midterm_score ?? '—' }} · CK: {{ $enrollment->final_score ?? '—' }}</div></td>
                <td>{{ number_format((float) $enrollment->paid_tuition, 0, ',', '.') }} ₫<div class="small text-secondary">{{ $enrollment->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}</div></td>
                <td>
                    @if($classRoom->status === 'upcoming' && $enrollment->attendance_count === 0)
                        <form action="{{ route('enrollments.cancel', $enrollment) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đăng ký lớp này?{{ $enrollment->payment_status === 'paid' ? ' Bạn đã thanh toán và sẽ cần liên hệ Ban quản lý để được hoàn tiền.' : '' }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm text-nowrap"><i class="bi bi-x-circle me-1"></i>Hủy đăng ký</button>
                        </form>
                    @elseif($classRoom->status === 'ongoing')
                        <span class="text-secondary small">Không thể hủy khi lớp đang học</span>
                    @else
                        <span class="text-secondary small">Không thể hủy</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center py-5"><i class="bi bi-journal-x fs-2 text-secondary d-block mb-2"></i><span class="text-secondary">Bạn chưa đăng ký lớp nào.</span><div class="mt-3"><a class="btn btn-primary" href="{{ route('class_rooms.index') }}">Xem danh sách lớp</a></div></td></tr>
        @endforelse
        </tbody>
    </table>
</div></div>
@endsection
