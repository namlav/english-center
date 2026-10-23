@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Chi tiết hóa đơn '.$invoice->invoice_code)

@section('content')
@php
    $classRoom = $invoice->classRoom;
    $student = $invoice->student;
    $studentUser = $student?->user;
    $teacher = $classRoom?->teacher;
    $teacherUser = $teacher?->user;
    $paidAmount = $invoice->payment_status === 'paid' ? (float) $invoice->paid_tuition : 0;
@endphp

<div class="page-heading d-flex flex-wrap justify-content-between align-items-start gap-3">
    <div>
        <a href="{{ route('invoices.index') }}" class="text-decoration-none small fw-semibold"><i class="bi bi-arrow-left me-1"></i>Quay lại danh sách hóa đơn</a>
        <div class="page-eyebrow mt-3 mb-2">Chi tiết hóa đơn</div>
        <div class="d-flex flex-wrap align-items-center gap-3">
            <h1 class="h2 fw-bold mb-0 invoice-title">{{ $invoice->invoice_code }}</h1>
            @if($invoice->payment_status === 'paid')
                <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>Đã thanh toán</span>
            @else
                <span class="badge text-bg-warning"><i class="bi bi-clock me-1"></i>Chưa thanh toán</span>
            @endif
        </div>
        <p class="text-secondary mb-0 mt-2">Tạo lúc {{ $invoice->created_at?->format('H:i, d/m/Y') ?? '—' }}</p>
    </div>
    <div class="d-flex gap-2">
        @if($classRoom)
            <a href="{{ route('class_rooms.show', $classRoom) }}" class="btn btn-outline-primary"><i class="bi bi-journal-bookmark me-1"></i>Xem lớp học</a>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="row g-4">
            <div class="col-md-6">
                <section class="card h-100">
                    <div class="card-header bg-white py-3 px-4">
                        <h2 class="h6 fw-bold mb-0"><i class="bi bi-person-video3 text-primary me-2"></i>Giáo viên phụ trách</h2>
                    </div>
                    <div class="card-body px-4">
                        <div class="detail-name">{{ $teacherUser?->name ?? '—' }}</div>
                        <div class="small text-secondary mb-3">{{ $teacher?->teacher_code ? 'Mã GV: '.$teacher->teacher_code : 'Chưa có mã giáo viên' }}</div>
                        <dl class="detail-list mb-0">
                            <dt>Chuyên môn</dt><dd>{{ $teacher?->specialization ?: '—' }}</dd>
                            <dt>Học vị</dt><dd>{{ $teacher?->degree ?: '—' }}</dd>
                            <dt>Email</dt><dd>{{ $teacherUser?->email ?? '—' }}</dd>
                            <dt>Điện thoại</dt><dd>{{ $teacherUser?->phone ?: '—' }}</dd>
                        </dl>
                    </div>
                </section>
            </div>

            <div class="col-md-6">
                <section class="card h-100">
                    <div class="card-header bg-white py-3 px-4">
                        <h2 class="h6 fw-bold mb-0"><i class="bi bi-mortarboard text-primary me-2"></i>Học viên thanh toán</h2>
                    </div>
                    <div class="card-body px-4">
                        <div class="detail-name">{{ $studentUser?->name ?? '—' }}</div>
                        <div class="small text-secondary mb-3">{{ $student?->student_code ? 'Mã HV: '.$student->student_code : 'Chưa có mã học viên' }}</div>
                        <dl class="detail-list mb-0">
                            <dt>Email</dt><dd>{{ $studentUser?->email ?? '—' }}</dd>
                            <dt>Điện thoại</dt><dd>{{ $studentUser?->phone ?: '—' }}</dd>
                            <dt>Ngày sinh</dt><dd>{{ $student?->date_of_birth?->format('d/m/Y') ?? '—' }}</dd>
                            <dt>Địa chỉ</dt><dd>{{ $student?->address ?: '—' }}</dd>
                        </dl>
                    </div>
                </section>
            </div>

            <div class="col-12">
                <section class="card">
                    <div class="card-header bg-white py-3 px-4">
                        <h2 class="h6 fw-bold mb-0"><i class="bi bi-journal-bookmark text-primary me-2"></i>Thông tin lớp học</h2>
                    </div>
                    <div class="card-body px-4">
                        @if($classRoom)
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="detail-name">{{ $classRoom->name }}</div>
                                    <div class="small text-secondary">Mã lớp: {{ $classRoom->code }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="detail-label">Khóa học</div>
                                    <div class="fw-semibold">{{ $classRoom->course?->name ?? '—' }}</div>
                                    <div class="small text-secondary">{{ $classRoom->course?->code }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="detail-label">Trạng thái lớp</div>
                                    <span class="badge text-bg-{{ $classRoom->status === 'ongoing' ? 'primary' : ($classRoom->status === 'upcoming' ? 'warning' : 'secondary') }}">
                                        {{ \App\Models\ClassRoom::STATUSES[$classRoom->status] ?? $classRoom->status }}
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-label">Lịch học</div>
                                    <div class="fw-semibold">{{ $classRoom->schedule }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="detail-label">Bắt đầu</div>
                                    <div class="fw-semibold">{{ $classRoom->start_date?->format('d/m/Y') ?? '—' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="detail-label">Kết thúc dự kiến</div>
                                    <div class="fw-semibold">{{ $classRoom->expected_end_date?->format('d/m/Y') ?? '—' }}</div>
                                </div>
                            </div>
                        @else
                            <p class="text-secondary mb-0">Lớp học của hóa đơn không còn tồn tại.</p>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <section class="card invoice-summary">
            <div class="card-header bg-white py-3 px-4">
                <h2 class="h6 fw-bold mb-0"><i class="bi bi-receipt text-primary me-2"></i>Tổng kết hóa đơn</h2>
            </div>
            <div class="card-body px-4">
                <dl class="detail-list mb-0">
                    <dt>Mã hóa đơn</dt><dd class="fw-semibold">{{ $invoice->invoice_code }}</dd>
                    <dt>Ngày tạo</dt><dd>{{ $invoice->created_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    <dt>Trạng thái</dt>
                    <dd>{{ $invoice->payment_status === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}</dd>
                    <dt>Học phí lớp</dt><dd>{{ number_format((float) ($classRoom?->tuition_fee ?? 0), 0, ',', '.') }} ₫</dd>
                    <dt>Voucher</dt>
                    <dd>
                        <span class="text-secondary">Không có</span>
                        <span class="small text-secondary d-block">Giảm giá: 0 ₫</span>
                    </dd>
                </dl>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary">Tổng tiền hóa đơn</span>
                    <span class="invoice-grand-total">{{ number_format($invoice->invoice_total, 0, ',', '.') }} ₫</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-secondary">Đã thanh toán</span>
                    <span class="fw-semibold {{ $paidAmount > 0 ? 'text-success' : 'text-secondary' }}">{{ number_format($paidAmount, 0, ',', '.') }} ₫</span>
                </div>

                <div class="mt-4">
                    @if($invoice->payment_status === 'paid')
                        <div class="invoice-status-note invoice-status-note-paid"><i class="bi bi-check2-circle me-2"></i>Hóa đơn đã được thanh toán đầy đủ.</div>
                    @else
                        <div class="invoice-status-note invoice-status-note-unpaid"><i class="bi bi-exclamation-circle me-2"></i>Hóa đơn đang chờ thanh toán.</div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>

<style>
    .invoice-title { color:#172b4d; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; letter-spacing:-.03em; }
    .detail-name { color:#1c2e4d; font-size:1.05rem; font-weight:700; }
    .detail-label { margin-bottom:.2rem; color:#7b879b; font-size:.75rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; }
    .detail-list dt { color:#7b879b; font-size:.75rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    .detail-list dd { margin-bottom:.85rem; color:#2b3d5c; font-weight:600; overflow-wrap:anywhere; }
    .detail-list dd:last-child { margin-bottom:0; }
    .invoice-summary { border-color:#dbe4f7!important; }
    .invoice-grand-total { color:#3457d5; font-size:1.35rem; font-weight:750; letter-spacing:-.03em; }
    .invoice-status-note { border-radius:12px; padding:.8rem .9rem; font-size:.85rem; font-weight:600; }
    .invoice-status-note-paid { color:#17795a; background:#eefaf5; }
    .invoice-status-note-unpaid { color:#94610d; background:#fff8eb; }
</style>
@endsection
