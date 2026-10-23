@extends('layouts.learning')
@section('title','Trang cá nhân — English Center')
@section('content')
<div class="shell account-layout">
    @include('learning.account.nav')
    <section class="account-content">
        <span class="eyebrow">TÀI KHOẢN CỦA BẠN</span>
        <h1>Trang cá nhân</h1>
        <p>Cập nhật thông tin để trung tâm hỗ trợ việc học của bạn.</p>
        
        <form class="portal-form" action="{{ route('account.update') }}" method="POST">
            @csrf
            @method('PATCH')
            <label for="profile-name">Họ và tên</label>
            <input id="profile-name" name="name" value="{{ old('name',$user->name) }}" required maxlength="255">

            <label for="profile-email">Email đăng nhập</label>
            <input id="profile-email" type="email" value="{{ $user->email }}" readonly style="background-color: #f8fafc; cursor: not-allowed;">

            <label for="profile-phone">Số điện thoại</label>
            <input id="profile-phone" name="phone" type="tel" maxlength="20" value="{{ old('phone',$user->phone) }}">

            <label for="profile-occupation">Nghề nghiệp / Bạn đang là</label>
            <input id="profile-occupation" name="occupation" maxlength="100" value="{{ old('occupation',$user->learnerProfile?->occupation) }}" placeholder="Ví dụ: Sinh viên">

            <button class="button" type="submit">Lưu thay đổi</button>
        </form>

        <div style="margin-top: 2.5rem; padding-top: 2rem; border-top: 1px solid var(--border-color, #e2e8f0);">
            <span class="eyebrow">BẢO MẬT</span>
            <h2 style="font-size: 1.35rem; margin-top: 0.25rem; margin-bottom: 0.5rem;">Đổi mật khẩu</h2>
            <p style="color: var(--text-muted, #64748b); margin-bottom: 1.25rem;">Nên sử dụng mật khẩu mạnh gồm chữ và số để bảo vệ tài khoản của bạn.</p>

            <form class="portal-form" action="{{ route('account.password') }}" method="POST">
                @csrf
                <label for="current_password">Mật khẩu hiện tại <span style="color:#e53e3e">*</span></label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password">

                <label for="new_password">Mật khẩu mới (tối thiểu 6 ký tự) <span style="color:#e53e3e">*</span></label>
                <input id="new_password" name="password" type="password" required autocomplete="new-password">

                <label for="password_confirmation">Xác nhận mật khẩu mới <span style="color:#e53e3e">*</span></label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

                <button class="button" type="submit" style="background-color: #1e293b; color: white;">Cập nhật mật khẩu</button>
            </form>
        </div>
    </section>
</div>
@endsection
