@extends('layouts.learning')
@section('title', 'Đăng ký tài khoản — English Center')
@section('content')
<section class="shell login-page">
    <div class="login-card">
        <span class="eyebrow">THÀNH VIÊN MỚI</span>
        <h1>Tạo tài khoản học viên</h1>
        <p>Đăng ký để học trực tuyến, làm đề thi và lưu lộ trình học tập của bạn.</p>
        <form class="portal-form" action="{{ route('register.post') }}" method="POST">
            @csrf
            <label for="register-name">Họ và tên <span style="color:#e53e3e">*</span></label>
            <input id="register-name" name="name" type="text" value="{{ old('name') }}" placeholder="Ví dụ: Nguyễn Văn A" required autofocus autocomplete="name">

            <label for="register-email">Địa chỉ Email <span style="color:#e53e3e">*</span></label>
            <input id="register-email" name="email" type="email" value="{{ old('email') }}" placeholder="email@example.com" required autocomplete="username">

            <label for="register-phone">Số điện thoại</label>
            <input id="register-phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="0987654321" autocomplete="tel">

            <label for="register-password">Mật khẩu (tối thiểu 6 ký tự) <span style="color:#e53e3e">*</span></label>
            <input id="register-password" name="password" type="password" required autocomplete="new-password">

            <label for="register-password-confirmation">Xác nhận lại mật khẩu <span style="color:#e53e3e">*</span></label>
            <input id="register-password-confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

            <button class="button" type="submit">Đăng ký tài khoản</button>
        </form>
        <div style="margin-top: 1.5rem; text-align: center; font-size: 0.95rem;">
            <p>Đã có tài khoản? <a class="text-link" href="{{ route('login') }}" style="font-weight: 600;">Đăng nhập ngay</a></p>
            <a class="text-link" href="{{ route('home') }}">← Về trang chủ</a>
        </div>
    </div>
</section>
@endsection
