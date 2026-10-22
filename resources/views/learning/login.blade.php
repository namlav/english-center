@extends('layouts.learning')
@section('title','Đăng nhập — English Center')
@section('content')
<section class="shell login-page"><div class="login-card"><span class="eyebrow">CHÀO MỪNG BẠN TRỞ LẠI</span><h1>Đăng nhập hệ thống</h1><p>Tiếp tục học và truy cập tài khoản của bạn.</p><form class="portal-form" action="{{ route('login.post') }}" method="POST">@csrf<label for="email">Email</label><input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" autofocus><label for="password">Mật khẩu</label><input id="password" name="password" type="password" required autocomplete="current-password"><button class="button" type="submit">Đăng nhập</button></form><a class="text-link" href="{{ route('home') }}">← Về trang chủ</a></div></section>
@endsection
