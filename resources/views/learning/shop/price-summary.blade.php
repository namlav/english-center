@php
    $cartTotal=$courses->sum(fn($c)=>$shop->price($c));
    $listTotal=$courses->sum(fn($c)=>max((int)$c->price,$shop->price($c)));
@endphp
<div class="commerce-summary-title"><span class="commerce-icon"><x-study-icon name="file"/></span><div><h2>{{ $summaryTitle??'Tóm tắt thanh toán' }}</h2><p>{{ $courses->count() }} khóa học trong đơn</p></div></div>
@if($showItems??false)<div class="checkout-mini-items">@foreach($courses as $course)<div>@if($course->thumbnail_path)<img src="{{ asset($course->thumbnail_path) }}" alt="" width="64" height="48">@endif<strong>{{ $course->name }}<span>{{ number_format($shop->price($course),0,',','.') }}đ</span></strong></div>@endforeach</div>@endif
<div class="commerce-summary-line"><span>Giá gốc</span><b>{{ number_format($listTotal,0,',','.') }}đ</b></div>@if($listTotal>$cartTotal)<div class="commerce-summary-line commerce-savings"><span>Ưu đãi khóa học</span><b>−{{ number_format($listTotal-$cartTotal,0,',','.') }}đ</b></div>@endif<div class="commerce-grand-total"><span>Tổng thanh toán</span><strong>{{ number_format($cartTotal,0,',','.') }}đ</strong></div>

