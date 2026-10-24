@extends('layouts.learning')

@section('title', 'Đăng ký thành công — ' . $classRoom->name)

@section('content')
<div class="learning-container py-4" style="max-width: 600px; margin: 0 auto;">
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 32px 28px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); text-align: center;">
        
        {{-- Icon & Tiêu đề --}}
        <div style="width: 56px; height: 56px; background: #ecfdf5; color: #059669; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 28px; margin-bottom: 14px;">
            ✓
        </div>

        <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px;">
            Đăng ký giữ chỗ thành công!
        </h1>
        <p style="color: #64748b; font-size: 13.5px; margin: 0 0 20px;">
            Hồ sơ mã <strong style="color: #2563eb; font-family: monospace;">{{ $enrollment->invoice_code }}</strong> cho lớp <strong>{{ $classRoom->name }}</strong>.
        </p>

        {{-- Khối VietQR --}}
        @php
            $transferSyntax = $classRoom->code . ' ' . ($enrollment->student?->user?->phone ?? $enrollment->student?->student_code);
            $qrUrl = 'https://img.vietqr.io/image/MB-0987654321-compact2.png?amount=' . (int)$classRoom->tuition_fee . '&addInfo=' . urlencode($transferSyntax) . '&accountName=' . urlencode('ENGLISH CENTER');
        @endphp

        <div style="background: #f8fafc; border: 2px dashed #2563eb; border-radius: 12px; padding: 20px; margin-bottom: 22px;">
            <span style="font-size: 13px; font-weight: 700; color: #1e3a8a; display: block; margin-bottom: 12px;">
                QUÉT MÃ VIETQR ĐỂ HOÀN TẤT HỌC PHÍ
            </span>

            <img src="{{ $qrUrl }}" alt="VietQR" style="width: 190px; height: 190px; border-radius: 8px; border: 1px solid #cbd5e1; display: block; margin: 0 auto 14px;">

            <div style="text-align: left; font-size: 13px; display: flex; flex-direction: column; gap: 8px; background: #ffffff; border-radius: 8px; padding: 12px 14px; border: 1px solid #e2e8f0;">
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">Ngân hàng:</span>
                    <strong style="color: #0f172a;">MB Bank (Quân Đội)</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="color: #64748b;">Số tài khoản:</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <strong style="color: #2563eb; font-family: monospace; font-size: 14px;">0987654321</strong>
                        <button type="button" onclick="navigator.clipboard.writeText('0987654321'); alert('Đã sao chép STK!');" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 11px; padding: 1px 6px; border-radius: 4px; cursor: pointer;">Copy</button>
                    </div>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">Chủ tài khoản:</span>
                    <strong style="color: #0f172a;">CONG TY TNHH ENGLISH CENTER</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: #64748b;">Số tiền:</span>
                    <strong style="color: #047857; font-size: 15px;">{{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫</strong>
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center; background: #fffbeb; padding: 6px 8px; border-radius: 6px;">
                    <span style="color: #92400e; font-weight: 600;">Nội dung CK:</span>
                    <div style="display: flex; align-items: center; gap: 6px;">
                        <strong style="color: #b45309; font-family: monospace; font-size: 13.5px;">{{ $transferSyntax }}</strong>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $transferSyntax }}'); alert('Đã sao chép nội dung CK!');" style="background: #fef3c7; border: 1px solid #fde68a; color: #92400e; font-size: 11px; padding: 1px 6px; border-radius: 4px; cursor: pointer;">Copy</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Trạng thái đơn --}}
        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 10px 14px; margin-bottom: 22px; font-size: 12.5px; color: #92400e; text-align: left;">
            ⏳ <strong>Trạng thái:</strong> Đang chờ Admin duyệt học phí. Sau khi bạn chuyển khoản, Admin sẽ kiểm tra và kích hoạt lớp học vào tài khoản của bạn.
        </div>

        {{-- Nút bấm điều hướng --}}
        <div style="display: flex; gap: 10px; justify-content: center;">
            <a href="{{ route('enrollments.my_classes') }}" class="button" style="text-decoration: none; font-weight: 700; font-size: 14px; padding: 11px 20px; background: #2563eb; border-color: #2563eb; color: #fff; border-radius: 8px;">
                🎓 Đến Lớp học của tôi →
            </a>
            <a href="{{ route('home') }}" class="button button-outline" style="text-decoration: none; font-weight: 600; font-size: 14px; padding: 11px 18px; border-radius: 8px;">
                Về Trang chủ
            </a>
        </div>
    </div>
</div>
@endsection
