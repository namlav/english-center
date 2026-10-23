@extends('layouts.admin')
@section('title','Quản lý website — English Center')
@section('content')
<section class="shell admin-page">
    <span class="eyebrow">DÀNH CHO QUẢN TRỊ VIÊN</span>
    <h1>Quản lý Website & Đội ngũ</h1>
    <p>Chỉnh sửa nội dung trang chủ, danh sách giáo viên hiển thị trên banner và liên kết khóa học.</p>
    @include('learning.admin.nav')

    <div class="import-panel">
        <div>
            <strong>Khóa học & bài học trực tuyến</strong>
            <p>Quản lý giáo trình 21 khóa học, video YouTube, học phí và đề thi trắc nghiệm đi kèm.</p>
        </div>
        <a class="button" href="{{ route('shop_admin.index') }}">Quản lý khóa học & video</a>
    </div>

    <div class="page-section-heading">
        <h2>Giáo viên hiển thị trên Banner <span>({{ $teachers->count() }})</span></h2>
        <a class="button button-small" href="{{ route('website.teachers.create') }}">Thêm giáo viên mới</a>
    </div>

    <div class="admin-teacher-grid">
        @foreach($teachers as $teacher)
            <article>
                <img src="{{ asset($teacher->photo_path) }}" alt="{{ $teacher->name }}" width="100" height="130">
                <div>
                    <strong>{{ $teacher->name }}</strong>
                    <p>{{ $teacher->provider }} · {{ $teacher->is_active ? 'Đang hiện' : 'Đang ẩn' }} · Thứ tự {{ $teacher->position }}</p>
                    <a class="text-link" href="{{ route('website.teachers.edit',$teacher) }}">Chỉnh sửa hồ sơ →</a>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endsection


