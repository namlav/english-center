@extends('layouts.learning')

@section('title', 'Đăng ký nhập học — ' . $classRoom->name)

@section('content')
<div class="learning-container py-4" style="max-width: 940px; margin: 0 auto;">
    {{-- Breadcrumb --}}
    <nav class="breadcrumb-nav mb-3" aria-label="breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span class="breadcrumb-separator">/</span>
        <a href="{{ route('public_classes.index') }}">Lớp học</a>
        <span class="breadcrumb-separator">/</span>
        <a href="{{ route('public_classes.show', $classRoom) }}">{{ $classRoom->code }}</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Đăng ký &amp; Thanh toán</span>
    </nav>

    {{-- Alert lỗi nếu có --}}
    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; font-size: 14px;">
            <strong style="display: block; margin-bottom: 4px;">Vui lòng kiểm tra lại:</strong>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="checkout-form" action="{{ route('public_classes.submit_register', $classRoom) }}" method="POST">
        @csrf
        <input type="hidden" name="payment_method" value="bank_transfer">

        <div class="class-checkout-grid" style="display: grid; grid-template-columns: 1fr 340px; gap: 28px; align-items: start;">
            {{-- CỘT TRÁI: THÔNG TIN & CHUYỂN KHOẢN --}}
            <div>
                <div style="margin-bottom: 20px;">
                    <span style="display: inline-block; background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 12px; padding: 3px 10px; border-radius: 6px; text-transform: uppercase; margin-bottom: 6px;">
                        Đăng ký nhập học
                    </span>
                    <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 4px;">
                        Thông tin học viên &amp; Chuyển khoản
                    </h1>
                    <p style="color: #64748b; font-size: 13.5px; margin: 0;">
                        Nhập thông tin liên hệ và chuyển khoản học phí để xác nhận giữ chỗ.
                    </p>
                </div>

                {{-- Khối 1: Thông tin học viên --}}
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 22px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <h2 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px; display: flex; align-items: center; gap: 8px;">
                        <span style="display: inline-flex; width: 22px; height: 22px; background: #2563eb; color: #fff; border-radius: 50%; font-size: 12px; align-items: center; justify-content: center; font-weight: 700;">1</span>
                        Thông tin học viên
                    </h2>

                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <div>
                            <label for="name" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 5px;">
                                Họ và tên học viên <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" id="name" name="name" class="input"
                                   style="width: 100%; border-radius: 8px; padding: 10px 14px; border: 1px solid #cbd5e1; font-size: 14px;"
                                   value="{{ old('name', $user->name ?? '') }}" placeholder="Nhập họ và tên" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                            <div>
                                <label for="phone" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 5px;">
                                    Số điện thoại <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="tel" id="phone" name="phone" class="input"
                                       style="width: 100%; border-radius: 8px; padding: 10px 14px; border: 1px solid #cbd5e1; font-size: 14px;"
                                       value="{{ old('phone', $user->phone ?? '') }}" placeholder="09xxxxxxxx" required>
                            </div>
                            <div>
                                <label for="email" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 5px;">
                                    Email nhận xác nhận <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="email" id="email" name="email" class="input"
                                       style="width: 100%; border-radius: 8px; padding: 10px 14px; border: 1px solid #cbd5e1; font-size: 14px;"
                                       value="{{ old('email', $user->email ?? '') }}" placeholder="email@example.com" required {{ Auth::check() ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Khối 2: Chuyển khoản (CK) --}}
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                        <h2 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 8px;">
                            <span style="display: inline-flex; width: 22px; height: 22px; background: #2563eb; color: #fff; border-radius: 50%; font-size: 12px; align-items: center; justify-content: center; font-weight: 700;">2</span>
                            Chuyển khoản học phí (VietQR)
                        </h2>
                        <span style="background: #ecfdf5; color: #059669; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 4px;">
                            Chuyển khoản 24/7
                        </span>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; font-size: 13.5px; display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Ngân hàng thụ hưởng:</span>
                            <strong style="color: #0f172a;">MB Bank (Ngân hàng Quân Đội)</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #64748b;">Số tài khoản:</span>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <strong style="color: #2563eb; font-family: monospace; font-size: 15px;">0987654321</strong>
                                <button type="button" onclick="navigator.clipboard.writeText('0987654321'); alert('Đã sao chép STK: 0987654321');" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 4px; cursor: pointer;">Copy</button>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Chủ tài khoản:</span>
                            <strong style="color: #0f172a;">CONG TY TNHH ENGLISH CENTER</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Số tiền học phí:</span>
                            <strong style="color: #047857; font-size: 15px;">{{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; background: #fffbeb; padding: 6px 10px; border-radius: 6px; border: 1px dashed #fde68a;">
                            <span style="color: #92400e; font-weight: 600;">Cú pháp CK:</span>
                            <strong style="color: #b45309; font-family: monospace; font-size: 14px;">{{ $classRoom->code }} [SĐT của bạn]</strong>
                        </div>
                    </div>

                    <p style="font-size: 12px; color: #64748b; margin: 10px 0 0;">
                        💡 Bấm nút <strong>Thanh toán</strong> ở cột bên phải để hoàn tất tạo hồ sơ và quét mã QR. Admin sẽ kiểm tra và duyệt lớp ngay khi nhận được chuyển khoản.
                    </p>
                </div>
            </div>

            {{-- CỘT PHẢI: TÓM TẮT LỚP HỌC & NÚT THANH TOÁN --}}
            <div>
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.04); position: sticky; top: 90px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                        <span style="font-size: 12px; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 2px 8px; border-radius: 4px;">
                            {{ $classRoom->code }}
                        </span>
                        <span style="font-size: 12px; font-weight: 600; color: #059669; background: #ecfdf5; padding: 2px 8px; border-radius: 4px;">
                            {{ \App\Models\ClassRoom::STATUSES[$classRoom->status] ?? 'Tuyển sinh' }}
                        </span>
                    </div>

                    <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 4px; line-height: 1.4;">
                        {{ $classRoom->name }}
                    </h3>
                    <div style="font-size: 13px; color: #64748b; margin-bottom: 14px;">
                        {{ $classRoom->course->name }}
                    </div>

                    <div style="border-top: 1px dashed #e2e8f0; border-bottom: 1px dashed #e2e8f0; padding: 12px 0; margin-bottom: 14px; font-size: 13px; display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Giảng viên:</span>
                            <strong style="color: #1e293b;">{{ $classRoom->teacher->user->name }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Lịch học:</span>
                            <strong style="color: #1e293b;">{{ $classRoom->schedule }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Khai giảng:</span>
                            <strong style="color: #2563eb;">{{ $classRoom->start_date->format('d/m/Y') }}</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Chỗ còn lại:</span>
                            <strong style="color: #059669;">{{ max(0, $classRoom->max_students - $classRoom->enrollments_count) }} chỗ</strong>
                        </div>
                    </div>

                    {{-- Giá tiền --}}
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 14px;">
                        <span style="font-weight: 700; color: #0f172a; font-size: 14px;">Tổng học phí:</span>
                        <span style="color: #047857; font-weight: 800; font-size: 20px;">{{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫</span>
                    </div>

                    {{-- Nút Thanh toán (chuyển sang bên dưới giá tiền) --}}
                    <button type="submit" form="checkout-form" class="button" style="width: 100%; font-size: 16px; font-weight: 800; padding: 13px; text-align: center; background: #2563eb; border-color: #2563eb; color: #ffffff; border-radius: 8px; cursor: pointer; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); margin-bottom: 14px;">
                        Thanh toán
                    </button>

                    <div style="font-size: 12px; color: #64748b; line-height: 1.5;">
                        ✓ Cam kết chuẩn đầu ra<br>
                        ✓ Tặng bộ giáo trình &amp; học liệu<br>
                        ✓ Giảng viên theo sát từng buổi
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
@media (max-width: 768px) {
    .class-checkout-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection
