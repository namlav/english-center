@extends('layouts.learning')

@section('title', 'Đăng ký thành công — ' . $classRoom->name)

@section('content')
<div class="learning-container py-5" style="max-width: 820px; margin: 0 auto;">
    {{-- Card thông báo thành công --}}
    <div class="card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 40px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.06); margin-bottom: 28px;">
        <div style="width: 72px; height: 72px; background: #ecfdf5; color: #10b981; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 38px; margin-bottom: 20px;">
            ✓
        </div>

        <h1 style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 8px;">
            Đăng ký nhập học thành công!
        </h1>
        <p style="color: #64748b; font-size: 15px; margin: 0 0 24px; max-width: 600px; margin-left: auto; margin-right: auto;">
            Hệ thống đã tiếp nhận hồ sơ đăng ký của bạn cho lớp <strong>{{ $classRoom->name }}</strong>. Vui lòng hoàn tất bước thanh toán bên dưới để được Ban tuyển sinh duyệt và kích hoạt lớp chính thức.
        </p>

        {{-- Hộp thông tin tóm tắt phiếu đăng ký --}}
        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 20px; text-align: left; margin-bottom: 28px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; font-size: 14px;">
                <div>
                    <span style="color: #64748b; display: block; font-size: 12.5px; margin-bottom: 3px;">Mã hồ sơ / Hóa đơn:</span>
                    <strong style="color: #2563eb; font-family: monospace; font-size: 15px;">{{ $enrollment->invoice_code }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 12.5px; margin-bottom: 3px;">Học viên đăng ký:</span>
                    <strong style="color: #1e293b;">{{ $enrollment->student?->user?->name }} ({{ $enrollment->student?->student_code }})</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 12.5px; margin-bottom: 3px;">Lớp đăng ký:</span>
                    <strong style="color: #1e293b;">{{ $classRoom->name }} ({{ $classRoom->code }})</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 12.5px; margin-bottom: 3px;">Ngày khai giảng:</span>
                    <strong style="color: #1e293b;">{{ $classRoom->start_date->format('d/m/Y') }}</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 12.5px; margin-bottom: 3px;">Học phí cần thanh toán:</span>
                    <strong style="color: #047857; font-size: 16px;">{{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫</strong>
                </div>
                <div>
                    <span style="color: #64748b; display: block; font-size: 12.5px; margin-bottom: 3px;">Trạng thái hồ sơ:</span>
                    @if($enrollment->payment_status === 'paid')
                        <span style="display: inline-block; background: #ecfdf5; color: #047857; font-weight: 700; font-size: 12.5px; padding: 2px 10px; border-radius: 4px;">
                            ✓ Đã thanh toán & Duyệt nhập học
                        </span>
                    @else
                        <span style="display: inline-block; background: #fffbeb; color: #b45309; font-weight: 700; font-size: 12.5px; padding: 2px 10px; border-radius: 4px;">
                            ⏳ Đang chờ Admin duyệt học phí
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Hướng dẫn thanh toán VietQR nếu là unpaid --}}
        @if($enrollment->payment_status !== 'paid')
            @php
                $transferSyntax = $classRoom->code . ' ' . ($enrollment->student?->user?->phone ?? $enrollment->student?->student_code);
                $qrUrl = 'https://img.vietqr.io/image/MB-0987654321-compact2.png?amount=' . (int)$classRoom->tuition_fee . '&addInfo=' . urlencode($transferSyntax) . '&accountName=' . urlencode('ENGLISH CENTER');
            @endphp

            <div style="background: #ffffff; border: 2px solid #2563eb; border-radius: 14px; padding: 28px; margin-bottom: 28px; text-align: left;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 22px;">📱</span>
                        <h2 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0;">
                            Hướng dẫn chuyển khoản qua mã VietQR
                        </h2>
                    </div>
                    <span style="background: #eff6ff; color: #1d4ed8; font-size: 12px; font-weight: 700; padding: 3px 8px; border-radius: 4px;">Tự động chuẩn NAPAS</span>
                </div>

                <div style="display: grid; grid-template-columns: 200px 1fr; gap: 24px; align-items: center;">
                    <div style="text-align: center;">
                        <img src="{{ $qrUrl }}" alt="VietQR Thanh toán học phí" style="width: 100%; max-width: 200px; border-radius: 10px; border: 1px solid #e2e8f0; display: block; margin: 0 auto 8px;">
                        <span style="font-size: 12px; color: #64748b;">Quét bằng app ngân hàng bất kỳ</span>
                    </div>

                    <div style="font-size: 14px; display: flex; flex-direction: column; gap: 10px;">
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #f1f5f9; padding-bottom: 6px;">
                            <span style="color: #64748b;">Ngân hàng thụ hưởng:</span>
                            <strong style="color: #0f172a;">MB Bank (Ngân hàng Quân Đội)</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #f1f5f9; padding-bottom: 6px;">
                            <span style="color: #64748b;">Số tài khoản:</span>
                            <strong style="color: #2563eb; font-family: monospace; font-size: 16px;">0987654321</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #f1f5f9; padding-bottom: 6px;">
                            <span style="color: #64748b;">Chủ tài khoản:</span>
                            <strong style="color: #0f172a;">CONG TY TNHH ENGLISH CENTER</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #f1f5f9; padding-bottom: 6px;">
                            <span style="color: #64748b;">Số tiền học phí:</span>
                            <strong style="color: #047857; font-size: 17px;">{{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-bottom: 1px dashed #f1f5f9; padding-bottom: 6px; background: #fffbeb; padding: 6px 8px; border-radius: 6px;">
                            <span style="color: #92400e; font-weight: 600;">Nội dung chuyển khoản:</span>
                            <strong style="color: #b45309; font-family: monospace; font-size: 15px;">{{ $transferSyntax }}</strong>
                        </div>
                    </div>
                </div>

                <div style="background: #f8fafc; border-radius: 8px; padding: 12px 16px; margin-top: 18px; font-size: 13px; color: #475569;">
                    ℹ️ <strong>Quy trình duyệt:</strong> Sau khi bạn chuyển khoản, hệ thống sẽ gửi thông báo đến Quản trị viên (Admin). Admin sẽ kiểm tra sao kê và bấm <strong>Duyệt đơn</strong> trong trang Quản lý hóa đơn để kích hoạt trạng thái "Đang học" cho tài khoản của bạn.
                </div>
            </div>
        @endif

        {{-- Nút điều hướng --}}
        <div style="display: flex; flex-wrap: wrap; gap: 14px; justify-content: center;">
            <a href="{{ route('enrollments.my_classes') }}" class="button" style="font-size: 15px; font-weight: 700; padding: 12px 24px; text-decoration: none; background: #2563eb; border-color: #2563eb; color: #fff; border-radius: 8px;">
                🎓 Đến Lớp học của tôi →
            </a>
            <a href="{{ route('public_classes.index') }}" class="button button-outline" style="font-size: 15px; font-weight: 600; padding: 12px 20px; text-decoration: none; border-radius: 8px;">
                Xem thêm lớp học khác
            </a>
            <a href="{{ route('home') }}" class="button button-outline" style="font-size: 15px; font-weight: 600; padding: 12px 20px; text-decoration: none; border-radius: 8px;">
                🏠 Về Trang chủ
            </a>
        </div>
    </div>
</div>
@endsection
