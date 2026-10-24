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
        <span class="breadcrumb-current">Thanh toán</span>
    </nav>

    {{-- Alert lỗi nếu có --}}
    @if($errors->any())
        <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13.5px;">
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form id="checkout-form" action="{{ route('public_classes.submit_register', $classRoom) }}" method="POST">
        @csrf
        <input type="hidden" name="payment_method" value="bank_transfer">

        <div class="class-checkout-grid" style="display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start;">
            {{-- CỘT TRÁI: THÔNG TIN & CHUYỂN KHOẢN --}}
            <div>
                {{-- Khối 1: Thông tin học viên --}}
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; margin-bottom: 16px;">
                    <h2 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 12px;">
                        Thông tin học viên
                    </h2>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <div>
                            <label for="name" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 4px;">
                                Họ và tên <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" id="name" name="name" class="input"
                                   style="width: 100%; border-radius: 6px; padding: 9px 12px; border: 1px solid #cbd5e1; font-size: 14px;"
                                   value="{{ old('name', $user->name ?? '') }}" placeholder="Họ và tên" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <div>
                                <label for="phone" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 4px;">
                                    Số điện thoại <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="tel" id="phone" name="phone" class="input"
                                       style="width: 100%; border-radius: 6px; padding: 9px 12px; border: 1px solid #cbd5e1; font-size: 14px;"
                                       value="{{ old('phone', $user->phone ?? '') }}" placeholder="09xxxxxxxx" required>
                            </div>
                            <div>
                                <label for="email" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 4px;">
                                    Email <span style="color: #dc2626;">*</span>
                                </label>
                                <input type="email" id="email" name="email" class="input"
                                       style="width: 100%; border-radius: 6px; padding: 9px 12px; border: 1px solid #cbd5e1; font-size: 14px;"
                                       value="{{ old('email', $user->email ?? '') }}" placeholder="name@example.com" required {{ Auth::check() ? 'readonly' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Khối 2: Chuyển khoản (CK) --}}
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px;">
                    <h2 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 12px;">
                        Chuyển khoản ngân hàng (VietQR)
                    </h2>

                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; font-size: 13px; display: flex; flex-direction: column; gap: 8px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Ngân hàng:</span>
                            <strong style="color: #0f172a;">MB Bank</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #64748b;">Số tài khoản:</span>
                            <div style="display: flex; align-items: center; gap: 6px;">
                                <strong style="color: #2563eb; font-family: monospace; font-size: 14.5px;">0987654321</strong>
                                <button type="button" onclick="navigator.clipboard.writeText('0987654321'); alert('Đã sao chép STK!');" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 11px; padding: 1px 6px; border-radius: 4px; cursor: pointer;">Copy</button>
                            </div>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Chủ tài khoản:</span>
                            <strong style="color: #0f172a;">CONG TY TNHH ENGLISH CENTER</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Học phí:</span>
                            <strong style="color: #047857; font-size: 14.5px;">{{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; background: #fffbeb; padding: 6px 8px; border-radius: 6px;">
                            <span style="color: #92400e; font-weight: 600;">Cú pháp:</span>
                            <strong style="color: #b45309; font-family: monospace; font-size: 13.5px;">{{ $classRoom->code }} [SĐT]</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: TÓM TẮT & NÚT THANH TOÁN --}}
            <div>
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px; position: sticky; top: 90px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-size: 12px; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 2px 6px; border-radius: 4px;">
                            {{ $classRoom->code }}
                        </span>
                        <span style="font-size: 12px; color: #059669; font-weight: 600;">
                            {{ \App\Models\ClassRoom::STATUSES[$classRoom->status] ?? 'Tuyển sinh' }}
                        </span>
                    </div>

                    <h3 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0 0 12px; line-height: 1.4;">
                        {{ $classRoom->name }}
                    </h3>

                    <div style="border-top: 1px dashed #e2e8f0; border-bottom: 1px dashed #e2e8f0; padding: 10px 0; margin-bottom: 12px; font-size: 13px; display: flex; flex-direction: column; gap: 6px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #64748b;">Giáo viên:</span>
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
                    </div>

                    {{-- Giá tiền --}}
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 12px;">
                        <span style="font-weight: 600; color: #0f172a; font-size: 13.5px;">Tổng cộng:</span>
                        <span style="color: #047857; font-weight: 800; font-size: 19px;">{{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫</span>
                    </div>

                    {{-- Nút Thanh toán --}}
                    <button type="submit" form="checkout-form" class="button" style="width: 100%; font-size: 15px; font-weight: 800; padding: 12px; text-align: center; background: #2563eb; border-color: #2563eb; color: #ffffff; border-radius: 6px; cursor: pointer;">
                        Thanh toán
                    </button>
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
