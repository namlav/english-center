@php
 $adminView=$adminView??false;
 $requests=\App\Models\PaymentQrRequest::where('payment_id',$order->payment->id)->latest('id')->get();
 $qr=$requests->first();$bank=$qr?->bank_snapshot??[];
@endphp
<div class="commerce-surface"><section class="shell commerce-page">
<span class="eyebrow">{{ $adminView?'QUẢN LÝ ĐƠN HÀNG':'ĐƠN HÀNG CỦA BẠN' }}</span>
<h1>{{ $order->status==='paid'?'Đăng ký học thành công':'Thanh toán bằng QR ngân hàng' }}</h1>
<p class="order-number">Mã đơn: {{ $order->number }}</p>
@if($order->status==='paid')
 <section class="commerce-paid-panel"><h2>Đã xác nhận thanh toán</h2><p>Khóa học đã được cấp vào tài khoản người mua.</p><a class="commerce-button" href="{{ route('account.courses') }}">Đến khóa học của tôi →</a></section>
@else
 <div class="commerce-payment-grid">
 <section class="commerce-qr-panel">
 <div class="commerce-panel-heading"><span class="commerce-step-number">1</span><div><h2>Quét mã bằng ứng dụng ngân hàng</h2><p>QR đã điền sẵn số tiền và nội dung chuyển khoản.</p></div></div>
 @if($qr)
 <div class="order-qr-frame"><img class="bank-qr-image" src="{{ $qr->imageUrl() }}" width="320" height="320" alt="VietQR chuyển khoản đơn hàng" referrerpolicy="no-referrer" data-bank-qr-image><p class="bank-qr-error" hidden>Không tải được QR từ VietQR. Bạn có thể chuyển khoản theo thông tin bên cạnh hoặc tải lại trang.</p></div>
 <div class="qr-controls"><a class="button button-outline" href="{{ $qr->imageUrl() }}" target="_blank" rel="noopener noreferrer">Mở / lưu mã QR</a><button type="button" data-copy-value="{{ $qr->reference }}">Chép nội dung</button></div>
 <p class="qr-local-note">Kiểm tra {{ $bank['account_name'] }} và số tiền trên ứng dụng ngân hàng trước khi xác nhận chuyển khoản. Chỉ chuyển một lần cho đơn này.</p>
 @if(!$adminView)
 <form method="POST" action="{{ route('orders.report-transfer',$order->number) }}">@csrf<input type="hidden" name="reference" value="{{ $qr->reference }}"><button class="commerce-button" type="submit">Tôi đã chuyển khoản — báo admin kiểm tra</button></form>
 <form class="bank-refresh-form" method="POST" action="{{ route('orders.bank-qr',$order->number) }}">@csrf<button class="button button-outline" type="submit">Tạo QR mới với nội dung khác</button></form>
 @endif
 @endif
 <div class="qr-status" data-order-status-url="{{ $adminView?'':route('orders.status',$order->number) }}"><span class="qr-status-dot"></span>{{ $requests->whereNotNull('reported_at')->isNotEmpty()?'Đã báo chuyển khoản · Chờ admin đối soát':'Chờ chuyển khoản và admin đối soát' }}<p>Tạo hoặc quét QR chưa xác nhận tiền đã vào tài khoản. Các nội dung QR cũ vẫn được lưu để đối chiếu.</p></div>
 @if($adminView || auth()->user()?->isAdmin())
 <div class="admin-verification-card" style="margin-top:16px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;padding:14px;box-shadow:0 1px 2px rgba(0,0,0,0.05);">
  <strong style="color:#166534;font-size:14px;display:block;margin-bottom:6px;">Quyền Quản trị viên (Admin):</strong>
  <p style="color:#15803d;font-size:13px;margin:0 0 10px 0;">Bạn có thể đối soát và kích hoạt đơn hàng ngay lập tức mà không cần chuyển trang.</p>
  <form method="POST" action="{{ route('shop_admin.orders.confirm',$order->number) }}">
   @csrf
   <label class="bank-verified" style="display:flex;align-items:center;gap:8px;color:#166534;font-size:13px;margin-bottom:10px;cursor:pointer;">
    <input type="checkbox" name="verified" value="1" required checked> Tôi xác nhận đã nhận đủ tiền và cấp quyền học ngay.
   </label>
   <button class="commerce-button" type="submit" style="background:#059669;border-color:#059669;font-weight:700;">
    Xác nhận đã nhận tiền & Kích hoạt học ngay
   </button>
  </form>
 </div>
 @endif
 </section>
 <aside class="commerce-summary commerce-payment-details"><span class="commerce-gold-label">ĐỐI CHIẾU THÔNG TIN</span><h2>Thông tin chuyển khoản</h2>
 <div class="commerce-amount-box"><span>SỐ TIỀN CẦN THANH TOÁN</span><strong>{{ number_format($order->total,0,',','.') }}đ</strong></div>
 <dl class="commerce-order-facts">
 <div><dt>Ngân hàng</dt><dd>{{ $bank['bank_name']??'Chưa phát hành QR' }}</dd></div>
 <div><dt>Số tài khoản</dt><dd>{{ $bank['account_number']??'' }}</dd></div>
 <div><dt>Chủ tài khoản</dt><dd>{{ $bank['account_name']??'' }}</dd></div>
 <div><dt>Nội dung chuyển khoản</dt><dd class="bank-transfer-reference">{{ $qr?->reference }}</dd></div>
 <div><dt>Người học</dt><dd>{{ $order->buyer_name }}</dd></div><div><dt>Trạng thái</dt><dd>Chờ xác nhận</dd></div>
 </dl><p class="commerce-order-tip">Admin xác nhận đã nhận tiền xong, khóa học sẽ xuất hiện trong “Khóa học của tôi”.</p></aside>
 </div>
@endif
<section class="commerce-white-panel order-course-receipt"><h2>Khóa học trong đơn</h2>@foreach($order->items as $item)<div class="order-line"><strong>{{ $item->course_name }}</strong><b>{{ number_format($item->unit_price,0,',','.') }}đ</b></div>@endforeach</section>
@if($adminView)<section class="commerce-white-panel"><h2>Lịch sử nội dung QR</h2><p>Đối chiếu được cả QR cũ sau khi người học tạo QR mới.</p>@foreach($requests as $request)<div class="order-line"><div><strong>{{ $request->reference }}</strong><p>{{ $request->created_at->copy()->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i:s') }} · {{ $request->bank_snapshot['bank_name'] }} · {{ $request->bank_snapshot['account_number'] }} · {{ $request->bank_snapshot['account_name'] }}</p><p>{{ $request->reported_at?'Người học đã báo chuyển khoản':'Chưa báo chuyển khoản' }}</p></div><b>{{ number_format($request->amount,0,',','.') }}đ</b></div>@endforeach</section>@endif
</section></div>
<script src="{{ asset('js/commerce.js') }}" defer></script>
