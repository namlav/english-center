@extends('layouts.learning')
@section('title','Đơn hàng của tôi — English Center')
@section('content')
<section class="shell shop-page"><h1>Đơn hàng của tôi</h1><p>Theo dõi đơn hàng và trạng thái đăng ký khóa học của bạn.</p>@forelse($orders as $order)<article class="cart-row"><div><span class="status-pill">{{ $order->status==='paid'?'Đã thanh toán':'Chờ xác nhận' }}</span><h2>{{ $order->created_at->format('d/m/Y H:i') }} · {{ $order->items_count }} khóa học</h2><p>Mã đơn: {{ $order->number }}</p><strong>{{ number_format($order->total,0,',','.') }}đ</strong></div><a class="button button-outline" href="{{ route('orders.show',$order->number) }}">Xem đơn</a></article>@empty<div class="empty-state"><h2>Bạn chưa có đơn hàng nào</h2><a class="button" href="{{ route('catalog.index') }}">Chọn khóa học</a></div>@endforelse</section>
@include('learning.shop.pager',['records'=>$orders])
@endsection
