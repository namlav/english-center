@extends('layouts.learning')

@section('title', 'Đăng ký nhập học — ' . $classRoom->name)

@section('content')
<div class="learning-container py-4">
    {{-- Breadcrumb --}}
    <nav class="breadcrumb-nav mb-4" aria-label="breadcrumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span class="breadcrumb-separator">/</span>
        <a href="{{ route('public_classes.index') }}">Lớp học & Khai giảng</a>
        <span class="breadcrumb-separator">/</span>
        <a href="{{ route('public_classes.show', $classRoom) }}">{{ $classRoom->code }}</a>
        <span class="breadcrumb-separator">/</span>
        <span class="breadcrumb-current">Phiếu đăng ký nhập học</span>
    </nav>

    {{-- Alert Messages --}}
    @if(session('error'))
        <div class="alert alert-danger mb-4" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 10px; font-weight: 500;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger mb-4" style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 14px 18px; border-radius: 10px;">
            <strong style="display: block; margin-bottom: 6px;">Vui lòng kiểm tra lại thông tin:</strong>
            <ul style="margin: 0; padding-left: 20px; font-size: 13.5px;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="class-reg-grid" style="display: grid; grid-template-columns: 1fr 380px; gap: 32px; align-items: start;">
        {{-- CỘT TRÁI: FORM ĐIỀN THÔNG TIN --}}
        <div>
            <div style="margin-bottom: 24px;">
                <span style="display: inline-block; background: #eff6ff; color: #1d4ed8; font-weight: 700; font-size: 12px; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">
                    Tuyển sinh trực tiếp
                </span>
                <h1 style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 8px;">
                    Phiếu đăng ký nhập học & Giữ chỗ
                </h1>
                <p style="color: #64748b; font-size: 14.5px; margin: 0;">
                    Vui lòng cung cấp đầy đủ thông tin học viên để English Center liên hệ xếp lớp, kích hoạt tài khoản học tập và gửi tài liệu khai giảng.
                </p>
            </div>

            @guest
                <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 16px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; font-size: 13.5px; color: #92400e;">
                    <span>💡 Đã có tài khoản tại trung tâm? Đăng nhập để tự động điền hồ sơ nhanh hơn.</span>
                    <a href="{{ route('login') }}" style="color: #b45309; font-weight: 700; text-decoration: underline; white-space: nowrap; margin-left: 12px;">Đăng nhập ngay →</a>
                </div>
            @endguest

            <form action="{{ route('public_classes.submit_register', $classRoom) }}" method="POST">
                @csrf

                {{-- Khối 1: THÔNG TIN HỌC VIÊN --}}
                <div class="card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h2 style="font-size: 17px; font-weight: 700; color: #1e293b; margin: 0 0 18px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <span style="display: inline-flex; width: 24px; height: 24px; background: #2563eb; color: #fff; border-radius: 50%; font-size: 13px; align-items: center; justify-content: center; font-weight: 700;">1</span>
                        Thông tin học viên
                    </h2>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label for="name" style="display: block; font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">
                                Họ và tên học viên <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" id="name" name="name" class="input" style="width: 100%; border-radius: 8px; padding: 10px 14px; border: 1px solid #cbd5e1; font-size: 14.5px;"
                                   value="{{ old('name', $user->name ?? '') }}" placeholder="Ví dụ: Nguyễn Văn A" required>
                        </div>
                        <div>
                            <label for="phone" style="display: block; font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">
                                Số điện thoại liên hệ <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="tel" id="phone" name="phone" class="input" style="width: 100%; border-radius: 8px; padding: 10px 14px; border: 1px solid #cbd5e1; font-size: 14.5px;"
                                   value="{{ old('phone', $user->phone ?? '') }}" placeholder="Ví dụ: 0912345678" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label for="email" style="display: block; font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">
                                Địa chỉ Email <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="email" id="email" name="email" class="input" style="width: 100%; border-radius: 8px; padding: 10px 14px; border: 1px solid #cbd5e1; font-size: 14.5px;"
                                   value="{{ old('email', $user->email ?? '') }}" placeholder="name@example.com" required {{ Auth::check() ? 'readonly' : '' }}>
                            @guest
                                <span style="font-size: 12px; color: #64748b; margin-top: 4px; display: block;">Hệ thống sẽ cấp tài khoản tự động tới email này.</span>
                            @endguest
                        </div>
                        <div>
                            <label for="date_of_birth" style="display: block; font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">
                                Ngày sinh
                            </label>
                            <input type="date" id="date_of_birth" name="date_of_birth" class="input" style="width: 100%; border-radius: 8px; padding: 10px 14px; border: 1px solid #cbd5e1; font-size: 14.5px;"
                                   value="{{ old('date_of_birth', optional($student?->date_of_birth)->format('Y-m-d')) }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 160px 1fr; gap: 16px;">
                        <div>
                            <label for="gender" style="display: block; font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">
                                Giới tính
                            </label>
                            <select id="gender" name="gender" class="input" style="width: 100%; border-radius: 8px; padding: 10px 14px; border: 1px solid #cbd5e1; font-size: 14.5px; background: #fff;">
                                <option value="">-- Chọn --</option>
                                <option value="male" @selected(old('gender', $student?->gender) === 'male')>Nam</option>
                                <option value="female" @selected(old('gender', $student?->gender) === 'female')>Nữ</option>
                                <option value="other" @selected(old('gender', $student?->gender) === 'other')>Khác</option>
                            </select>
                        </div>
                        <div>
                            <label for="address" style="display: block; font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">
                                Địa chỉ liên hệ / Nơi ở
                            </label>
                            <input type="text" id="address" name="address" class="input" style="width: 100%; border-radius: 8px; padding: 10px 14px; border: 1px solid #cbd5e1; font-size: 14.5px;"
                                   value="{{ old('address', $student?->address ?? '') }}" placeholder="Quận/Huyện, Tỉnh/Thành phố">
                        </div>
                    </div>
                </div>

                {{-- Khối 2: MỤC TIÊU & GHI CHÚ --}}
                <div class="card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h2 style="font-size: 17px; font-weight: 700; color: #1e293b; margin: 0 0 18px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <span style="display: inline-flex; width: 24px; height: 24px; background: #2563eb; color: #fff; border-radius: 50%; font-size: 13px; align-items: center; justify-content: center; font-weight: 700;">2</span>
                        Mục tiêu học tập & Nguyện vọng
                    </h2>

                    <div style="margin-bottom: 16px;">
                        <label for="learning_goals" style="display: block; font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">
                            Mục tiêu đào tạo / Điểm số kỳ vọng
                        </label>
                        <input type="text" id="learning_goals" name="learning_goals" class="input" style="width: 100%; border-radius: 8px; padding: 10px 14px; border: 1px solid #cbd5e1; font-size: 14.5px;"
                               value="{{ old('learning_goals') }}" placeholder="Ví dụ: Đạt TOEIC 650+ để xét tốt nghiệp, Thi IELTS 6.5 vào cuối năm...">
                    </div>

                    <div>
                        <label for="notes" style="display: block; font-weight: 600; font-size: 13.5px; color: #334155; margin-bottom: 6px;">
                            Ghi chú bổ sung cho Ban tuyển sinh
                        </label>
                        <textarea id="notes" name="notes" rows="2" class="input" style="width: 100%; border-radius: 8px; padding: 10px 14px; border: 1px solid #cbd5e1; font-size: 14.5px;"
                                  placeholder="Ví dụ: Cần kiểm tra trình độ xếp lớp trước, học viên có thể vào muộn 15p buổi thứ 4...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- Khối 3: HÌNH THỨC XÁC NHẬN & THANH TOÁN --}}
                <div class="card" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h2 style="font-size: 17px; font-weight: 700; color: #1e293b; margin: 0 0 18px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <span style="display: inline-flex; width: 24px; height: 24px; background: #2563eb; color: #fff; border-radius: 50%; font-size: 13px; align-items: center; justify-content: center; font-weight: 700;">3</span>
                        Chọn hình thức thanh toán & Giữ chỗ
                    </h2>

                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        {{-- Option 1: Chuyển khoản QR --}}
                        <label style="border: 2px solid {{ old('payment_method', 'bank_transfer') === 'bank_transfer' ? '#2563eb' : '#e2e8f0' }}; background: {{ old('payment_method', 'bank_transfer') === 'bank_transfer' ? '#f8faff' : '#ffffff' }}; border-radius: 10px; padding: 16px; display: flex; align-items: flex-start; gap: 14px; cursor: pointer; transition: all 0.2s;" id="label-bank-transfer">
                            <input type="radio" name="payment_method" value="bank_transfer" @checked(old('payment_method', 'bank_transfer') === 'bank_transfer') style="margin-top: 3px;" onchange="updatePaymentMethodBorder()">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <strong style="color: #0f172a; font-size: 15px;">Chuyển khoản Ngân hàng qua VietQR (Khuyên dùng)</strong>
                                    <span style="background: #10b981; color: #fff; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Ưu tiên giữ chỗ</span>
                                </div>
                                <p style="font-size: 13px; color: #64748b; margin: 6px 0 0;">
                                    Hệ thống tự động sinh mã VietQR chuẩn NAPAS. Chuyển khoản trực tiếp với nội dung định sẵn để Admin duyệt kích hoạt lớp tức thì.
                                </p>
                            </div>
                        </label>

                        {{-- Option 2: Nộp tại trung tâm --}}
                        <label style="border: 2px solid {{ old('payment_method') === 'center_payment' ? '#2563eb' : '#e2e8f0' }}; background: {{ old('payment_method') === 'center_payment' ? '#f8faff' : '#ffffff' }}; border-radius: 10px; padding: 16px; display: flex; align-items: flex-start; gap: 14px; cursor: pointer; transition: all 0.2s;" id="label-center-payment">
                            <input type="radio" name="payment_method" value="center_payment" @checked(old('payment_method') === 'center_payment') style="margin-top: 3px;" onchange="updatePaymentMethodBorder()">
                            <div style="flex: 1;">
                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                    <strong style="color: #0f172a; font-size: 15px;">Đăng ký Giữ chỗ & Nộp học phí tại Trung tâm</strong>
                                    <span style="background: #f1f5f9; color: #475569; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px;">Giữ chỗ 48h</span>
                                </div>
                                <p style="font-size: 13px; color: #64748b; margin: 6px 0 0;">
                                    Đến trực tiếp văn phòng tuyển sinh English Center để làm bài test đầu vào, nhận giáo trình và thanh toán trước ngày khai giảng.
                                </p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Điều khoản & Nút Đăng ký --}}
                <div style="margin-bottom: 24px;">
                    <label style="display: flex; align-items: flex-start; gap: 10px; font-size: 13.5px; color: #475569; cursor: pointer;">
                        <input type="checkbox" required checked style="margin-top: 3px;">
                        <span>Tôi cam kết các thông tin khai báo trên là chính xác và đồng ý với <a href="#" style="color: #2563eb; text-decoration: underline;">Quy chế tuyển sinh & Nội quy học tập</a> của trung tâm.</span>
                    </label>
                </div>

                <button type="submit" class="button" style="width: 100%; font-size: 17px; font-weight: 800; padding: 16px; text-align: center; background: #2563eb; border-color: #2563eb; color: #ffffff; border-radius: 10px; cursor: pointer; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);">
                    ✓ Xác nhận Đăng ký & Nhập học ngay ({{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫)
                </button>
            </form>
        </div>

        {{-- CỘT PHẢI: TÓM TẮT LỚP HỌC (STICKY SIDEBAR) --}}
        <div>
            <div class="card" style="position: sticky; top: 90px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.06);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                    <span style="font-size: 12px; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 3px 8px; border-radius: 5px;">
                        {{ $classRoom->code }}
                    </span>
                    <span style="font-size: 12px; font-weight: 600; color: #059669; background: #ecfdf5; padding: 3px 8px; border-radius: 5px;">
                        {{ \App\Models\ClassRoom::STATUSES[$classRoom->status] ?? 'Tuyển sinh' }}
                    </span>
                </div>

                <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 6px; line-height: 1.4;">
                    {{ $classRoom->name }}
                </h3>
                <div style="font-size: 13.5px; color: #64748b; margin-bottom: 18px;">
                    {{ $classRoom->course->name }}
                </div>

                <div style="border-top: 1px dashed #e2e8f0; border-bottom: 1px dashed #e2e8f0; padding: 14px 0; margin-bottom: 18px; font-size: 13.5px; display: flex; flex-direction: column; gap: 10px;">
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
                        <span style="color: #64748b;">Kết thúc dự kiến:</span>
                        <strong style="color: #1e293b;">{{ $classRoom->expected_end_date?->format('d/m/Y') ?? 'Chưa cập nhật' }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #64748b;">Tình trạng chỗ:</span>
                        <strong style="color: #059669;">Còn trống {{ max(0, $classRoom->max_students - $classRoom->enrollments_count) }} chỗ</strong>
                    </div>
                </div>

                {{-- Bảng chi tiết học phí --}}
                <div style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px;">
                        <span style="color: #64748b;">Học phí niêm yết:</span>
                        <span style="color: #1e293b; font-weight: 600;">{{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 8px;">
                        <span style="color: #64748b;">Giáo trình & Tài liệu:</span>
                        <span style="color: #059669; font-weight: 600;">0 ₫ (Tặng kèm)</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: baseline; border-top: 2px solid #0f172a; padding-top: 12px; margin-top: 12px;">
                        <span style="color: #0f172a; font-weight: 800; font-size: 15px;">Tổng học phí:</span>
                        <span style="color: #047857; font-weight: 900; font-size: 24px;">{{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫</span>
                    </div>
                </div>

                {{-- Cam kết quyền lợi --}}
                <div style="background: #f8fafc; border-radius: 8px; padding: 14px; font-size: 12.5px; color: #475569;">
                    <strong style="display: block; color: #0f172a; margin-bottom: 6px;">Quyền lợi học viên:</strong>
                    <ul style="margin: 0; padding-left: 18px; line-height: 1.6;">
                        <li>Cam kết chuẩn đầu ra bằng văn bản.</li>
                        <li>Được kiểm tra đầu vào và tư vấn lộ trình riêng.</li>
                        <li>Đổi lớp / Bảo lưu linh hoạt nếu có việc bận.</li>
                        <li>Tặng tài khoản học trực tuyến luyện đề 24/7.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updatePaymentMethodBorder() {
    const bankTransfer = document.querySelector('input[value="bank_transfer"]');
    const labelBank = document.getElementById('label-bank-transfer');
    const labelCenter = document.getElementById('label-center-payment');

    if (bankTransfer.checked) {
        labelBank.style.borderColor = '#2563eb';
        labelBank.style.background = '#f8faff';
        labelCenter.style.borderColor = '#e2e8f0';
        labelCenter.style.background = '#ffffff';
    } else {
        labelCenter.style.borderColor = '#2563eb';
        labelCenter.style.background = '#f8faff';
        labelBank.style.borderColor = '#e2e8f0';
        labelBank.style.background = '#ffffff';
    }
}
</script>

<style>
@media (max-width: 900px) {
    .class-reg-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>
@endsection
