@extends('layouts.learning')
@section('title','Quản lý đơn hàng')
@section('content')
<section class="shell admin-page"><h1>Quản lý đơn hàng</h1>@include('learning.admin.nav')<p class="demo-notice">Đơn QR ngân hàng: kiểm tra giao dịch thực nhận trước khi xác nhận. Đơn mô phỏng cũ được ghi rõ trong chi tiết.</p><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Đơn hàng / Người học</th><th>Tổng</th><th>Hình thức</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody>@foreach($orders as $order)<tr><td>{{ $order->buyer_name }}<br>{{ $order->number }}</td><td>{{ number_format($order->total,0,',','.') }}đ</td><td>{{ $order->payment?->method==='bank_transfer'?'QR ngân hàng':'Mô phỏng cũ' }}</td><td>{{ $order->status==='paid'?'Đã xác nhận':'Chờ xác nhận' }}</td><td><a href="{{ route('shop_admin.orders.show',$order->number) }}">Xem chi tiết</a></td></tr>@endforeach</tbody></table></div></section>
@include('learning.shop.pager',['records'=>$orders])
@endsection
