@extends('layouts.learning')
@section('title','Quản lý website — English Center')
@section('content')
<section class="shell admin-page"><span class="eyebrow">DÀNH CHO QUẢN TRỊ VIÊN</span><h1>Quản lý website</h1><p>Chỉnh sửa nội dung trong database và áp dụng trực tiếp lên website.</p>@include('learning.admin.nav')
<div class="import-panel"><div><strong>Khóa học, hình ảnh và video giới thiệu</strong><p>Sửa thông tin, ảnh bìa, clip giới thiệu, giá và nội dung học ở cùng một nơi.</p></div><a class="button" href="{{ route('shop_admin.index') }}">Quản lý khóa học & video</a></div>
<details class="detail-panel"><summary>Nạp dữ liệu ban đầu</summary><p>Nạp nội dung còn thiếu từ các bộ dữ liệu đi kèm dự án. Giữ nguyên thông tin đã chỉnh sửa.</p><form action="{{ route('website.import') }}" method="POST">@csrf<button class="button">Nạp nội dung</button></form></details><div class="page-section-heading"><h2>Giáo viên trên banner <span>({{ $teachers->count() }})</span></h2><a class="button button-small" href="{{ route('website.teachers.create') }}">Thêm giáo viên</a></div><div class="admin-teacher-grid">@foreach($teachers as $teacher)<article><img src="{{ asset($teacher->photo_path) }}" alt="{{ $teacher->name }}" width="100" height="130"><div><strong>{{ $teacher->name }}</strong><p>{{ $teacher->provider }} · {{ $teacher->is_active ? 'Đang hiện' : 'Đang ẩn' }} · Thứ tự {{ $teacher->position }}</p><a class="text-link" href="{{ route('website.teachers.edit',$teacher) }}">Chỉnh sửa hồ sơ →</a></div></article>@endforeach</div></section>
@endsection

