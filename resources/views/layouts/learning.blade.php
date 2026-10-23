<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#3657b2">
    <meta name="description" content="Tìm hiểu khóa học IELTS, TOEIC và tiếng Anh nền tảng. Luyện tập ngữ pháp, từ vựng miễn phí với câu hỏi, đáp án và lời giải.">
    <title>@yield('title', 'English Center — Học từng ngày, vững tương lai')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/english-center-mark.svg') }}">
    <link rel="stylesheet" href="{{ asset('css/learning.css') }}">
    <link rel="stylesheet" href="{{ asset('css/catalog.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <script src="{{ asset('js/learning.js') }}" defer></script>
    <script src="{{ asset('js/portal.js') }}" defer></script><link rel="stylesheet" href="{{ asset('css/shop.css') }}">
    <link rel="stylesheet" href="{{ asset('css/commerce.css') }}">
</head>
<body>
@php($headerCartCount=auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('cart_items') ? \Illuminate\Support\Facades\DB::table('cart_items')->where('user_id',auth()->id())->count() : 0)
    <a class="skip-link" href="#main-content">Đi đến nội dung chính</a>

    @if(auth()->check() && auth()->user()->isAdmin())
        <div class="admin-topbar" style="background: #0f172a; color: #f8fafc; font-size: 13px; padding: 8px 0; border-bottom: 2px solid #2563eb; position: relative; z-index: 30;">
            <div class="shell" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="background: #dc2626; color: #ffffff; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 800; letter-spacing: 0.05em;">ADMIN CONTROL</span>
                    <span>Xin chào, <strong>{{ auth()->user()->name }}</strong> (Toàn quyền Quản trị viên)</span>
                </div>
                <div style="display: flex; align-items: center; gap: 16px; font-weight: 600; font-size: 13px; flex-wrap: wrap;">
                    <a href="{{ route('admin.dashboard') }}" style="background: #2563eb; color: #ffffff; padding: 4px 12px; border-radius: 6px; text-decoration: none; font-weight: 700;">📊 Vào Trang Quản Trị Metis →</a>
                    <a href="{{ route('shop_admin.index') }}" style="color: #cbd5e1; text-decoration: none;">📚 Khóa học</a>
                    <a href="{{ route('shop_admin.orders.index') }}" style="color: #cbd5e1; text-decoration: none;">📦 Đơn hàng</a>
                    <a href="{{ route('class_rooms.index') }}" style="color: #cbd5e1; text-decoration: none;">🏫 Lớp học</a>
                    <a href="{{ route('users.index') }}" style="color: #cbd5e1; text-decoration: none;">👥 Người dùng</a>
                </div>
            </div>
        </div>
    @endif

    <header class="site-header">
        <div class="shell header-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="English Center — Trang chủ">
                <span class="brand-symbol"><x-study-icon name="book" /></span>
                <span>ENGLISH<span class="brand-word">CENTER<span class="brand-dot">.</span></span></span>
            </a>
            <button class="nav-toggle" type="button" aria-controls="site-navigation" aria-expanded="false" aria-label="Mở menu"><x-study-icon name="menu" /></button>
            <nav id="site-navigation" class="site-navigation" aria-label="Điều hướng chính">
                <a href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif>Trang chủ</a>
                <a href="{{ route('catalog.index') }}" @if(request()->routeIs('catalog.index','course_catalog.show','shop.show')) aria-current="page" @endif>Chương trình học</a>
                <a href="{{ route('practice.index') }}" @if(request()->routeIs('practice.*')) aria-current="page" @endif>Đề thi online</a>
                <a href="{{ route('account.courses') }}" @if(request()->routeIs('account.courses')) aria-current="page" @endif>Khóa học của tôi</a>
                @if(auth()->check() && auth()->user()->isAdmin())
                    <a href="{{ route('website.index') }}" style="color: #2563eb; font-weight: 700;">⚙️ Quản trị Admin</a>
                @else
                    <a href="{{ route('cart.index') }}" @if(request()->routeIs('cart.*')) aria-current="page" @endif class="header-cart-link"><x-study-icon name="cart"/>Giỏ hàng<span class="cart-count-badge">{{ $headerCartCount }}</span></a>
                @endif
                <div class="mobile-account">
                    @auth <a href="{{ route('account.show') }}">Trang cá nhân</a>
                    @else <a href="{{ route('login') }}">Đăng nhập</a> <a href="{{ route('register') }}">Đăng ký</a> @endauth
                </div>
            </nav>
            <div class="header-account portal-header-account">
                @if(!auth()->check() || !auth()->user()->isAdmin())
                    <a class="mobile-cart-shortcut" href="{{ route('cart.index') }}" aria-label="Giỏ hàng"><x-study-icon name="cart"/><span>{{ $headerCartCount }}</span></a>
                @endif
                @auth
                    <div class="account-dropdown"><button class="account-trigger" type="button" aria-expanded="false" aria-controls="account-panel" aria-label="Mở menu tài khoản"><span class="account-avatar-small">{{ mb_strtoupper(mb_substr(auth()->user()->name,0,1)) }}</span><span class="account-trigger-name">{{ auth()->user()->name }}</span><span>⌄</span></button><div id="account-panel" class="account-panel" hidden><div class="account-panel-heading"><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->email }}</span></div><a href="{{ route('account.courses') }}">Khóa học của tôi</a><a href="{{ route('account.schedule') }}">Lịch học của tôi</a><a href="{{ route('orders.index') }}">Đơn hàng của tôi</a><a href="{{ route('account.show') }}">Trang cá nhân</a>@if(auth()->user()->isAdmin())<a href="{{ route('admin.dashboard') }}" style="color: #2563eb; font-weight: 700;">📊 Trang Quản Trị Metis</a>@endif<a href="{{ route('class_rooms.index') }}">Quản lý lớp học</a><form action="{{ route('logout') }}" method="POST">@csrf<button type="submit">Đăng xuất</button></form></div></div>
                @else
                    <a class="login-link" href="{{ route('login') }}">Đăng nhập</a>
                    <a class="button button-small" href="{{ route('register') }}">Đăng ký</a>
                @endauth
            </div>
        </div>
    </header>
    <main id="main-content">
        @if(session('success'))<div class="shell"><div class="notice notice-success" role="status">{{ session('success') }}</div></div>@endif
        @if(session('error'))<div class="shell"><div class="notice notice-error" role="alert">{{ session('error') }}</div></div>@endif
        @if($errors->any())
            <div class="shell"><div class="notice notice-error" role="alert"><strong>Vui lòng kiểm tra lại thông tin.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>
        @endif
        @yield('content')
    </main>
    <footer class="portal-footer" id="contact"><div class="shell"><div class="footer-columns"><div class="footer-identity"><a class="brand" href="{{ route('home') }}"><span class="brand-symbol"><x-study-icon name="book" /></span><span>ENGLISH<span class="brand-word">CENTER.</span></span></a><p>Khám phá lộ trình phù hợp.<br>Học tập và tiến bộ mỗi ngày.</p><div class="footer-contact-icons"><a href="mailto:{{ config('app.management_contact_email') }}" aria-label="Gửi email cho trung tâm">@</a><a href="tel:{{ preg_replace('/[^+0-9]/','',config('app.management_contact_phone')) }}" aria-label="Gọi điện cho trung tâm">☎</a></div></div><nav aria-label="Về English Center"><h3>Về English Center</h3><a href="{{ route('home') }}#intro">Giới thiệu</a><a href="{{ route('home') }}#contact">Liên hệ</a><a href="{{ route('home') }}#how-it-works">Hướng dẫn bắt đầu</a></nav><nav aria-label="Tài nguyên học tập"><h3>Tài nguyên</h3><a href="{{ route('practice.index') }}">Thư viện bài luyện</a><a href="{{ route('catalog.index') }}">Danh mục khóa học</a><a href="https://opentextbc.ca/abealf6/" target="_blank" rel="noopener noreferrer">Học liệu BC Reads ↗</a><a href="{{ route('account.courses') }}">Khóa học của tôi</a></nav><nav aria-label="Hướng dẫn và hỗ trợ"><h3>Hướng dẫn và hỗ trợ</h3><a href="{{ route('home') }}#how-it-works">Cách sử dụng bài luyện</a><a href="{{ route('login') }}">Đăng nhập tài khoản</a><a href="{{ route('class_rooms.index') }}">Quản lý lớp học</a><a href="https://creativecommons.org/licenses/by/4.0/" target="_blank" rel="noopener noreferrer">Giấy phép học liệu mở ↗</a></nav></div><div class="footer-organization"><h3>Thông tin liên hệ</h3><p>Người phụ trách: {{ config('app.management_contact_name') }}</p><p>Điện thoại: <a href="tel:{{ preg_replace('/[^+0-9]/','',config('app.management_contact_phone')) }}">{{ config('app.management_contact_phone') }}</a></p><p>Email: <a href="mailto:{{ config('app.management_contact_email') }}">{{ config('app.management_contact_email') }}</a></p><p class="footer-source-credit">Chương trình học, bài học và đề luyện được lưu trong hệ thống. Các bộ luyện BC Reads có ghi nguồn và giấy phép trong từng bài.</p></div><div class="portal-copyright">© {{ date('Y') }} English Center · Học từng ngày, vững từng bước.</div></div></footer>
    <nav class="floating-tools" aria-label="Công cụ nhanh"><a href="{{ route('practice.index') }}" title="Bài luyện"><x-study-icon name="book" /><span>Bài luyện</span></a><a href="mailto:{{ config('app.management_contact_email') }}" title="Liên hệ"><x-study-icon name="user" /><span>Liên hệ</span></a><a href="#main-content" aria-label="Về đầu trang">⌃</a></nav>
</body>
</html>




