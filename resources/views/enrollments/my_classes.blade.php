@extends('layouts.learning')

@section('title', 'Lớp học của tôi — Khu vực học viên')

@section('content')
<div class="learning-container py-4">
    {{-- Breadcrumb --}}
    <nav class="breadcrumb-nav mb-4" aria-label="breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span class="breadcrumb-separator">/</span>
        <a href="{{ route('account.show') }}">Tài khoản học tập</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Lớp học trực tiếp của tôi</span>
    </nav>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success mb-4" style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 14px 18px; border-radius: 10px; font-weight: 500;">
            {{ session('success') }}
        </div>
    @endif

    @if($refundDetails = session('refund_details'))
        <div class="alert alert-info mb-4" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 16px 20px; border-radius: 10px;">
            <h2 style="font-size: 15px; font-weight: 700; margin: 0 0 6px;">ℹ️ Hướng dẫn hoàn tiền hủy lớp</h2>
            <p style="margin: 0 0 8px; font-size: 14px;">
                Lớp <strong>{{ $refundDetails['class_name'] }}</strong> đã được hủy đăng ký. Hệ thống ghi nhận bạn đã thanh toán <strong>{{ number_format($refundDetails['amount'], 0, ',', '.') }} ₫</strong>. Vui lòng liên hệ Ban quản lý <strong>{{ config('app.management_contact_name', 'English Center Support') }}</strong> để được hướng dẫn hoàn tiền.
            </p>
            @php($managementEmail = config('app.management_contact_email'))
            @php($managementPhone = config('app.management_contact_phone'))
            @if($managementEmail || $managementPhone)
                <div style="font-size: 13px; display: flex; gap: 16px;">
                    @if($managementEmail)<span>✉️ <a href="mailto:{{ $managementEmail }}" style="color: #1d4ed8;">{{ $managementEmail }}</a></span>@endif
                    @if($managementPhone)<span>📞 <a href="tel:{{ preg_replace('/[^0-9+]/', '', $managementPhone) }}" style="color: #1d4ed8;">{{ $managementPhone }}</a></span>@endif
                </div>
            @endif
        </div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="font-size: 12px; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 0.5px;">Học viên English Center</span>
            <h1 style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 4px 0 0;">Lớp học trực tiếp của tôi</h1>
        </div>
        <a href="{{ route('public_classes.index') }}" class="button" style="text-decoration: none; font-weight: 700; font-size: 14px; padding: 10px 18px; border-radius: 8px; background: #2563eb; border-color: #2563eb; color: #fff;">
            + Tìm lớp học & Khai giảng mới
        </a>
    </div>

    @if($enrollments->isEmpty())
        <div class="card" style="background: #ffffff; border: 1px dashed #cbd5e1; border-radius: 14px; padding: 50px 20px; text-align: center;">
            <div style="font-size: 40px; margin-bottom: 12px;">📚</div>
            <h3 style="font-size: 18px; font-weight: 700; color: #1e293b; margin: 0 0 8px;">Bạn chưa đăng ký lớp học nào</h3>
            <p style="color: #64748b; font-size: 14px; max-width: 480px; margin: 0 auto 20px;">
                Đăng ký ngay các lớp học TOEIC, IELTS, Giao tiếp sắp khai giảng để được giảng viên kèm cặp trực tiếp và cam kết chuẩn đầu ra.
            </p>
            <a href="{{ route('public_classes.index') }}" class="button" style="font-size: 14.5px; font-weight: 700; padding: 11px 24px; text-decoration: none; background: #2563eb; border-color: #2563eb; color: #fff; border-radius: 8px;">
                Khám phá lịch khai giảng ➔
            </a>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 20px;">
            @foreach($enrollments as $enrollment)
                @php($classRoom = $enrollment->classRoom)
                <div class="card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px; box-shadow: 0 1px 4px rgba(0,0,0,0.04);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 14px; margin-bottom: 16px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                <span style="font-size: 12px; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 2px 8px; border-radius: 4px;">
                                    {{ $classRoom->code }}
                                </span>
                                <h3 style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 0;">
                                    <a href="{{ route('public_classes.show', $classRoom) }}" style="color: #0f172a; text-decoration: none;">
                                        {{ $classRoom->name }}
                                    </a>
                                </h3>
                            </div>
                            <span style="color: #64748b; font-size: 13.5px;">Chương trình: <strong>{{ $classRoom->course->name }}</strong></span>
                        </div>

                        {{-- Trạng thái học phí & duyệt đơn --}}
                        <div style="text-align: right;">
                            @if($enrollment->payment_status === 'paid')
                                <span style="display: inline-flex; align-items: center; gap: 6px; background: #ecfdf5; color: #047857; font-weight: 700; font-size: 13px; padding: 4px 12px; border-radius: 20px; border: 1px solid #a7f3d0;">
                                    ✓ Đã duyệt nhập học
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 6px; background: #fffbeb; color: #b45309; font-weight: 700; font-size: 13px; padding: 4px 12px; border-radius: 20px; border: 1px solid #fde68a;">
                                    ⏳ Chờ Admin duyệt học phí
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Thông tin chi tiết lớp --}}
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 14px; background: #f8fafc; border-radius: 8px; padding: 14px; font-size: 13.5px; margin-bottom: 16px;">
                        <div>
                            <span style="color: #64748b; display: block; font-size: 12px; margin-bottom: 2px;">Giảng viên:</span>
                            <strong style="color: #1e293b;">{{ $classRoom->teacher->user->name }}</strong>
                        </div>
                        <div>
                            <span style="color: #64748b; display: block; font-size: 12px; margin-bottom: 2px;">Lịch học & Phòng:</span>
                            <strong style="color: #1e293b;">{{ $classRoom->schedule }}</strong>
                        </div>
                        <div>
                            <span style="color: #64748b; display: block; font-size: 12px; margin-bottom: 2px;">Thời gian khóa học:</span>
                            <strong style="color: #1e293b;">{{ $classRoom->start_date->format('d/m/Y') }} → {{ $classRoom->expected_end_date?->format('d/m/Y') ?? 'Chưa chốt' }}</strong>
                        </div>
                        <div>
                            <span style="color: #64748b; display: block; font-size: 12px; margin-bottom: 2px;">Chuyên cần / Điểm danh:</span>
                            <strong style="color: #059669;">{{ $enrollment->present_count }} có mặt</strong> <span style="color: #dc2626;">({{ $enrollment->absent_count }} vắng)</span>
                        </div>
                        <div>
                            <span style="color: #64748b; display: block; font-size: 12px; margin-bottom: 2px;">Kết quả học tập:</span>
                            <strong style="color: #1e293b;">
                                {{ ['studying' => 'Đang học', 'passed' => 'Đạt chuẩn', 'failed' => 'Chưa đạt'][$enrollment->result_status] ?? 'Đang học' }}
                            </strong>
                            <span style="color: #64748b; font-size: 12px;">(GK: {{ $enrollment->midterm_score ?? '—' }}, CK: {{ $enrollment->final_score ?? '—' }})</span>
                        </div>
                        <div>
                            <span style="color: #64748b; display: block; font-size: 12px; margin-bottom: 2px;">Mã phiếu / Hóa đơn:</span>
                            <span style="font-family: monospace; font-weight: 700; color: #2563eb;">{{ $enrollment->invoice_code }}</span>
                        </div>
                    </div>

                    {{-- Footer thẻ lớp: Thao tác & Thanh toán --}}
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; border-top: 1px solid #f1f5f9; padding-top: 14px;">
                        <div style="font-size: 14px;">
                            <span style="color: #64748b;">Học phí:</span>
                            <strong style="color: #047857; font-size: 16px; margin-left: 4px;">
                                {{ number_format((float) ($classRoom->tuition_fee ?? $enrollment->paid_tuition), 0, ',', '.') }} ₫
                            </strong>
                            @if($enrollment->payment_status === 'unpaid')
                                <a href="{{ route('public_classes.confirmation', ['classRoom' => $classRoom->id, 'enrollment' => $enrollment->id]) }}" style="margin-left: 10px; color: #2563eb; font-weight: 600; text-decoration: underline; font-size: 13px;">
                                    📱 Xem hướng dẫn chuyển khoản QR
                                </a>
                            @endif
                        </div>

                        <div style="display: flex; gap: 10px;">
                            <a href="{{ route('public_classes.show', $classRoom) }}" class="button button-outline" style="font-size: 13px; font-weight: 600; padding: 7px 14px; text-decoration: none; border-radius: 6px;">
                                Xem thông tin lớp
                            </a>

                            @if($classRoom->status === 'upcoming' && $enrollment->attendance_count === 0)
                                <form action="{{ route('enrollments.cancel', $enrollment) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đăng ký lớp này?{{ $enrollment->payment_status === 'paid' ? ' Bạn đã thanh toán và sẽ cần liên hệ Ban quản lý để được hoàn tiền.' : '' }}');" style="margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button" style="font-size: 13px; font-weight: 600; padding: 7px 14px; background: transparent; border: 1px solid #fca5a5; color: #dc2626; border-radius: 6px; cursor: pointer;">
                                        Hủy đăng ký
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
