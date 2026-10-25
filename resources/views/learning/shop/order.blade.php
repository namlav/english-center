@extends('layouts.learning')
@section('title','Thanh toán QR — English Center')
@section('content')
@if($order->payment->method==='bank_transfer')
@include('learning.shop.bank-order')
@else
@php
    $adminView=$adminView??false;
    $qrExpires=now()->addDay();
    $qrPath=\Illuminate\Support\Facades\URL::temporarySignedRoute('orders.qr',$qrExpires,['order'=>$order->number],absolute:false);
    $qrUrl=url($qrPath);
@endphp
<div class="commerce-surface"><section class="shell commerce-page"><span class="eyebrow">{{ $adminView?'QUẢN LÝ ĐƠN HÀNG':'ĐƠN HÀNG CỦA BẠN' }}</span><h1>{{ $order->status==='paid'?'Đăng ký học thành công':'Thanh toán bằng QR' }}</h1><p class="order-number">Mã đơn: {{ $order->number }}</p><p class="demo-notice">Đây là giao dịch mô phỏng. Không chuyển tiền và không quét bằng ứng dụng ngân hàng.</p>
@if($order->status==='paid')<section class="commerce-paid-panel"><span class="empty-cart-symbol"><x-study-icon name="check"/></span><h2>Đã xác nhận QR mô phỏng</h2><p>Quyền học đã được cấp vào tài khoản người mua.</p><p>{{ $order->paid_at?->copy()->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</p><a class="commerce-button" href="{{ route('account.courses') }}">Đến khóa học của tôi <x-study-icon name="arrow"/></a></section>
@else<div class="commerce-payment-grid">
<section class="commerce-qr-panel">@if(!$adminView)<div class="commerce-panel-heading"><span class="commerce-step-number">1</span><div><h2>Quét mã QR để xác nhận</h2><p>Dùng camera điện thoại hoặc mở ngay trên máy này.</p></div></div><div class="order-qr-frame"><div class="order-qr" data-order-qr data-qr-value="{{ $qrUrl }}" role="img" aria-label="Mã QR mô phỏng cho đơn hàng"><span>Đang tạo mã QR…</span></div></div><div class="qr-controls"><button type="button" data-download-qr disabled><x-study-icon name="download"/>Lưu mã QR</button><button type="button" data-copy-value="{{ $qrUrl }}">Chép liên kết QR</button></div><a class="qr-open-link" href="{{ $qrUrl }}">Mở bước xác nhận trên máy này →</a>@if(in_array(request()->getHost(),['127.0.0.1','localhost','::1']))<p class="qr-local-note">Nếu điện thoại không mở được địa chỉ trên máy tính, dùng nút mở bước xác nhận ở trên.</p>@endif<div class="qr-status" data-order-status-url="{{ route('orders.status',$order->number) }}"><span class="qr-status-dot"></span>Chờ xác nhận QR mô phỏng<p>QR có hiệu lực đến {{ $qrExpires->copy()->timezone('Asia/Ho_Chi_Minh')->format('H:i d/m/Y') }}. Quét QR chưa làm thay đổi trạng thái đơn.</p></div><noscript><p>Mở liên kết xác nhận ở trên để tiếp tục. Trình duyệt cần bật JavaScript để hiển thị QR.</p></noscript>@else<h2>Đơn hàng đang chờ xác nhận</h2><p>Người học xác nhận bằng QR của đơn hàng. Admin có thể xác nhận giao dịch mô phỏng tại đây.</p><form method="POST" action="{{ route('shop_admin.orders.confirm',$order->number) }}">@csrf<button class="commerce-button" type="submit">Admin xác nhận mô phỏng</button></form>@endif</section>
<aside class="commerce-summary commerce-payment-details"><div class="commerce-panel-heading"><span class="commerce-step-number">2</span><div><span class="commerce-gold-label">ĐỐI CHIẾU THÔNG TIN</span><h2>Xác nhận đăng ký khóa học</h2></div></div><div class="commerce-amount-box"><span>TỔNG GIÁ TRỊ ĐƠN</span><strong>{{ number_format($order->total,0,',','.') }}đ</strong><small>Số tiền mô phỏng, không chuyển khoản thật</small></div><dl class="commerce-order-facts"><div><dt>Người học</dt><dd>{{ $order->buyer_name }}</dd></div><div><dt>Email liên hệ</dt><dd>{{ $order->buyer_email }}</dd></div><div><dt>Hình thức</dt><dd>QR mô phỏng</dd></div><div><dt>Số khóa học</dt><dd>{{ $order->items->count() }} khóa</dd></div><div><dt>Trạng thái</dt><dd><span class="commerce-status-pending">Chờ xác nhận</span></dd></div></dl><p class="commerce-order-tip"><x-study-icon name="check"/>Sau khi xác nhận mô phỏng, khóa học sẽ xuất hiện trong tài khoản của bạn. Không cần nhập mã bằng tay.</p></aside></div>@endif
<section class="commerce-white-panel order-course-receipt"><h2>Khóa học trong đơn</h2>@foreach($order->items as $item)<div class="order-line"><div><strong>{{ $item->course_name }}</strong><p>{{ $item->access_days }} ngày truy cập</p></div><b>{{ number_format($item->unit_price,0,',','.') }}đ</b></div>@endforeach<p>Thông tin đăng ký: {{ $order->buyer_name }} · {{ $order->buyer_email }}</p></section></section></div>
@if(!$adminView && $order->status!=='paid')<script src="{{ asset('js/vendor/qrcode.js') }}" defer></script><script src="{{ asset('js/commerce.js') }}" defer></script>@endif
@endif
@endsection


