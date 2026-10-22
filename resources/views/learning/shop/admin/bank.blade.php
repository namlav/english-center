@extends('layouts.learning')
@section('title','Cài đặt QR ngân hàng')
@section('content')
<section class="shell admin-page"><h1>Cài đặt QR ngân hàng</h1>@include('learning.admin.nav')
<p>Thông tin từ QR được cung cấp: MB 0389279885. Tên chủ tài khoản do chủ website cung cấp. Mã QR không xác thực tên chủ tài khoản với ngân hàng.</p>
<form class="commerce-white-panel bank-settings-form" method="POST" action="{{ route('shop_admin.bank.save') }}">@csrf
@foreach(['bank_bin'=>'Mã BIN ngân hàng (6 chữ số)','bank_name'=>'Tên ngân hàng','account_number'=>'Số tài khoản','account_name'=>'Tên chủ tài khoản (in hoa, không dấu)'] as $key=>$label)
<label for="{{ $key }}">{{ $label }}</label><input id="{{ $key }}" name="{{ $key }}" value="{{ old($key,$bank[$key]??'') }}" required maxlength="{{ $key==='account_number'?19:100 }}">
@endforeach
<p>Thay đổi chỉ áp dụng cho đơn mới. Tài khoản của các QR đã phát hành được lưu riêng theo đơn.</p><button class="commerce-button" type="submit">Lưu thông tin ngân hàng</button></form></section>
@endsection
