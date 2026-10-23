@extends('layouts.admin')
@section('title', 'Cấu hình VietQR & Tài khoản Ngân hàng')
@section('content')
<section class="shell admin-page">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
        <div>
            <span class="eyebrow" style="color: #2563eb; font-weight: 700; font-size: 13px; text-transform: uppercase;">CỔNG THANH TOÁN VIETQR</span>
            <h1 style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 4px 0 0;">Cấu hình Tài khoản Ngân hàng Nhận tiền</h1>
            <p style="color: #64748b; font-size: 14px; margin: 4px 0 0;">Khi học viên mua khóa học, hệ thống sẽ tự động tạo mã VietQR theo tài khoản ngân hàng này.</p>
        </div>
        <a class="button button-outline" href="{{ route('shop_admin.orders.index') }}" style="text-decoration: none;">← Về danh sách đơn hàng</a>
    </div>

    @include('learning.admin.nav')

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 28px; align-items: start;">
        {{-- Mô phỏng thẻ ngân hàng --}}
        <div>
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: #ffffff; border-radius: 16px; padding: 24px; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.25); position: relative; overflow: hidden; min-height: 200px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                    <span style="font-size: 18px; font-weight: 800; letter-spacing: 0.05em;" id="cardBankNamePreview">{{ $bank['bank_name'] ?? 'NGÂN HÀNG' }}</span>
                    <span style="background: rgba(255,255,255,0.2); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700;">VietQR 24/7</span>
                </div>
                <div style="font-size: 22px; font-weight: 700; letter-spacing: 0.1em; font-family: monospace; margin-bottom: 20px;" id="cardNumberPreview">
                    {{ $bank['account_number'] ?? '•••• •••• ••••' }}
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <div>
                        <span style="font-size: 10px; opacity: 0.8; text-transform: uppercase; display: block;">Chủ tài khoản</span>
                        <strong style="font-size: 15px; letter-spacing: 0.05em;" id="cardNamePreview">{{ $bank['account_name'] ?? 'CHỦ TÀI KHOẢN' }}</strong>
                    </div>
                    <div style="text-align: right;">
                        <span style="font-size: 10px; opacity: 0.8; display: block;">MÃ BIN</span>
                        <strong style="font-size: 14px;" id="cardBinPreview">{{ $bank['bank_bin'] ?? '••••••' }}</strong>
                    </div>
                </div>
            </div>

            {{-- Danh sách ngân hàng chọn nhanh --}}
            <div style="margin-top: 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px;">
                <strong style="font-size: 13px; color: #1e293b; display: block; margin-bottom: 10px;">⚡ Chọn nhanh ngân hàng phổ biến:</strong>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <button type="button" class="tree-btn-sm" onclick="setQuickBank('970422', 'MBBank')" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">MBBank</button>
                    <button type="button" class="tree-btn-sm" onclick="setQuickBank('970436', 'Vietcombank')" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">Vietcombank</button>
                    <button type="button" class="tree-btn-sm" onclick="setQuickBank('970415', 'VietinBank')" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">VietinBank</button>
                    <button type="button" class="tree-btn-sm" onclick="setQuickBank('970407', 'Techcombank')" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">Techcombank</button>
                    <button type="button" class="tree-btn-sm" onclick="setQuickBank('970418', 'BIDV')" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">BIDV</button>
                    <button type="button" class="tree-btn-sm" onclick="setQuickBank('970432', 'VPBank')" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">VPBank</button>
                    <button type="button" class="tree-btn-sm" onclick="setQuickBank('970416', 'ACB')" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">ACB</button>
                </div>
            </div>
        </div>

        {{-- Form cài đặt thông tin --}}
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
            <form method="POST" action="{{ route('shop_admin.bank.save') }}">
                @csrf

                <div style="margin-bottom: 14px;">
                    <label for="bank_name" style="font-weight: 600; font-size: 13.5px;">Tên ngân hàng <span style="color: #dc2626;">*</span></label>
                    <input id="bank_name" name="bank_name" value="{{ old('bank_name', $bank['bank_name'] ?? '') }}" required placeholder="Ví dụ: MBBank, Vietcombank..." oninput="updateCardPreview()">
                </div>

                <div style="margin-bottom: 14px;">
                    <label for="bank_bin" style="font-weight: 600; font-size: 13.5px;">Mã BIN ngân hàng (6 chữ số) <span style="color: #dc2626;">*</span></label>
                    <input id="bank_bin" name="bank_bin" value="{{ old('bank_bin', $bank['bank_bin'] ?? '') }}" required pattern="[0-9]{6}" maxlength="6" placeholder="Ví dụ: 970422" oninput="updateCardPreview()">
                    <span style="font-size: 12px; color: #64748b;">Mã chuẩn NAPAS của ngân hàng để tạo mã QR.</span>
                </div>

                <div style="margin-bottom: 14px;">
                    <label for="account_number" style="font-weight: 600; font-size: 13.5px;">Số tài khoản nhận tiền <span style="color: #dc2626;">*</span></label>
                    <input id="account_number" name="account_number" value="{{ old('account_number', $bank['account_number'] ?? '') }}" required maxlength="19" placeholder="Ví dụ: 0389279885" oninput="updateCardPreview()">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="account_name" style="font-weight: 600; font-size: 13.5px;">Tên chủ tài khoản (Viết in hoa, không dấu) <span style="color: #dc2626;">*</span></label>
                    <input id="account_name" name="account_name" value="{{ old('account_name', $bank['account_name'] ?? '') }}" required maxlength="100" placeholder="Ví dụ: NGUYEN VAN A" oninput="updateCardPreview()" style="text-transform: uppercase;">
                    <span style="font-size: 12px; color: #64748b;">Hệ thống sẽ tự động chuyển thành chữ in hoa không dấu theo chuẩn ngân hàng.</span>
                </div>

                <button class="button" style="width: 100%; font-weight: 700; padding: 12px;">Lưu thông tin ngân hàng</button>
            </form>
        </div>
    </div>
</section>

<script>
function setQuickBank(bin, name) {
    document.getElementById('bank_bin').value = bin;
    document.getElementById('bank_name').value = name;
    updateCardPreview();
}
function updateCardPreview() {
    const bankName = document.getElementById('bank_name')?.value || 'NGÂN HÀNG';
    const bin = document.getElementById('bank_bin')?.value || '••••••';
    const num = document.getElementById('account_number')?.value || '•••• •••• ••••';
    const name = (document.getElementById('account_name')?.value || 'CHỦ TÀI KHOẢN').toUpperCase();

    document.getElementById('cardBankNamePreview').innerText = bankName;
    document.getElementById('cardBinPreview').innerText = bin;
    document.getElementById('cardNumberPreview').innerText = num;
    document.getElementById('cardNamePreview').innerText = name;
}
</script>
@endsection
