@extends('layouts.learning')
@section('title','Cập nhật cửa hàng khóa học')
@section('content')
<section class="shell shop-page"><h1>Cửa hàng đang được cập nhật</h1><div class="detail-panel"><p>Phần khóa học, ảnh, video và giỏ hàng cần cập nhật dữ liệu trước khi sử dụng.</p>@auth @if(auth()->user()->isAdmin())<p>Trong phpMyAdmin, chọn database hiện tại và Import file bên dưới. File bổ sung lên 45 bảng, giữ tài khoản và dữ liệu đã có.</p><a class="button" href="{{ route('shop_admin.upgrade') }}">Tải CAP_NHAT_QR_NGAN_HANG.sql</a>@endif @endauth<a class="text-link" href="{{ route('home') }}">Về trang chủ →</a></div></section>
@endsection

