@extends('layouts.admin')

@section('title', 'Chi tiết học viên')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <span class="badge bg-primary-subtle text-primary mb-2">{{ $student->student_code }}</span>
        <h2 class="fw-bold mb-1">{{ $student->user?->name ?? 'N/A' }}</h2>
        <span class="badge {{ $student->user?->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $student->user?->is_active ? 'Hoạt động' : 'Bị khóa' }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Về danh sách</a>
        <a href="{{ route('students.edit', $student) }}" class="btn btn-primary">Sửa học viên</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Email</p><strong class="text-break">{{ $student->user?->email ?? '—' }}</strong></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Số điện thoại</p><strong>{{ $student->user?->phone ?? '—' }}</strong></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Ngày sinh</p><strong>{{ $student->date_of_birth?->format('d/m/Y') ?? '—' }}</strong></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Giới tính</p><strong>{{ ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'][$student->gender] ?? '—' }}</strong></div></div></div>
</div>

<div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
    <h3 class="h5 mb-3">Địa chỉ</h3>
    <p class="mb-0">{{ $student->address ?? 'Chưa cập nhật' }}</p>
</div></div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h3 class="h5 mb-0">Lớp đã đăng ký</h3></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Mã lớp</th>
                    <th>Tên lớp</th>
                    <th>Khóa học</th>
                    <th>Ngày đăng ký</th>
                    <th>Thanh toán</th>
                    <th>Kết quả</th>
                </tr>
            </thead>
            <tbody>
                @forelse($student->enrollments as $enrollment)
                    <tr>
                        <td class="fw-bold">{{ $enrollment->classRoom?->code }}</td>
                        <td>{{ $enrollment->classRoom?->name }}</td>
                        <td>{{ $enrollment->classRoom?->course?->name ?? '—' }}</td>
                        <td>{{ $enrollment->enrollment_date?->format('d/m/Y') }}</td>
                        <td>
                            @if($enrollment->payment_status === 'paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @else
                                <span class="badge bg-warning text-dark">Chưa thanh toán</span>
                            @endif
                        </td>
                        <td>
                            @switch($enrollment->result_status)
                                @case('passed')
                                    <span class="badge bg-success">Đạt</span>
                                    @break
                                @case('failed')
                                    <span class="badge bg-danger">Không đạt</span>
                                    @break
                                @default
                                    <span class="badge bg-info text-dark">Đang học</span>
                            @endswitch
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-secondary py-4">Học viên chưa đăng ký lớp nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
