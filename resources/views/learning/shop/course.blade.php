@extends('layouts.learning')
@section('title',$course->name.' — English Center')
@section('content')
@php
    $lessonCount = $course->sections->flatMap->lessons->count();
    $videoPath = \App\Services\CourseVideo::local($course->intro_video_path);
    $videoEmbed = \App\Services\CourseVideo::embed($course->intro_video_url);
@endphp
<section class="detail-hero own-course-hero"><div class="shell"><div class="detail-hero-copy">
    <nav class="detail-breadcrumb"><a href="{{ route('catalog.index') }}">Chương trình học</a> / {{ $course->learningCategory?->name }}</nav>
    <h1>{{ $course->name }}</h1><span class="detail-provider">Học online · English Center</span><p>{{ $course->summary }}</p>
    <ul>@foreach(array_slice($course->learning_outcomes??[],0,3) as $outcome)<li><x-study-icon name="check"/>{{ $outcome }}</li>@endforeach</ul>
</div></div></section>
<nav class="course-section-nav" aria-label="Nội dung khóa học"><div class="shell"><a href="#goals">Mục tiêu khóa học</a><a href="#information">Thông tin khóa học</a><a href="#syllabus">Chương trình học</a><a href="#questions">Giải đáp</a></div></nav>
<div class="shell detail-grid"><div class="detail-main">
    <section class="detail-panel" id="goals"><span class="eyebrow">MỤC TIÊU HỌC TẬP</span><h2>Bạn sẽ học được những gì?</h2><ol class="course-outcomes">@foreach($course->learning_outcomes??[] as $item)<li>{{ $item }}</li>@endforeach</ol>
    @if($course->audience)<h3>Khóa học dành cho ai?</h3><p class="course-audience">{{ $course->audience }}</p>@endif</section>
    <section class="detail-panel" id="information"><h2>Thông tin khóa học</h2>
        @if($videoPath || $videoEmbed)<div class="course-intro-player">
        @if($videoPath)<video controls preload="metadata" playsinline @if($course->thumbnail_path)poster="{{ asset($course->thumbnail_path) }}"@endif aria-label="Video giới thiệu {{ $course->name }}"><source src="{{ asset($videoPath) }}" type="video/mp4">Trình duyệt chưa hỗ trợ phát video.</video>
        @else<iframe src="{{ $videoEmbed }}" title="Video giới thiệu {{ $course->name }}" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>@endif
        </div><p class="video-caption">Video giới thiệu chương trình</p>@endif
        <div class="lesson-text">{{ $course->description }}</div><div class="course-method-grid"><div><x-study-icon name="book"/><h3>Học theo chủ đề</h3><p>Xem nội dung, luyện tập và ôn lại từng phần trong lộ trình.</p></div><div><x-study-icon name="target"/><h3>Theo dõi tiến độ</h3><p>Đánh dấu bài đã hoàn thành trong khóa học của bạn.</p></div></div>
    </section>
    <section class="detail-panel" id="syllabus"><div class="syllabus-heading"><h2>Chương trình học</h2><span>{{ $lessonCount }} bài đã công bố</span></div>
    @if(count($course->curriculum_outline??[]))<h3>Các chủ đề trong lộ trình</h3><div class="syllabus-list">@foreach($course->curriculum_outline as $topic)<div><span>{{ str_pad((string)$loop->iteration,2,'0',STR_PAD_LEFT) }}</span><h3>{{ $topic }}</h3></div>@endforeach</div>@endif
    @forelse($course->sections as $section)<details class="course-section-accordion" open><summary>{{ $section->title }} <span>{{ $section->lessons->count() }} bài</span></summary><div class="syllabus-list">@foreach($section->lessons as $lesson)<div><span>{{ str_pad((string)$loop->iteration,2,'0',STR_PAD_LEFT) }}</span><h3>{{ $lesson->title }}</h3>@if($owned)<a class="text-link" href="{{ route('learn.lesson',[$course->slug,$lesson->id]) }}">Vào học →</a>@else<x-study-icon name="book"/>@endif</div>@endforeach</div></details>
    @empty<p class="course-content-status">Bài học đang được biên soạn. Chương trình sẽ mở đăng ký khi nội dung học được công bố.</p>@endforelse</section>
    <section class="detail-panel course-faq" id="questions"><h2>Giải đáp trước khi bắt đầu</h2><details open><summary>Mua xong tôi học ở đâu?</summary><p>Sau khi admin xác nhận đã nhận chuyển khoản, mở menu tài khoản → Khóa học của tôi → Vào học ngay. Bài học mở ngay trên English Center.</p></details><details><summary>Tôi có thể luyện đề trước khi đăng ký?</summary><p>Có. Bạn có thể làm các bài luyện và kiểm tra đáp án trong <a href="{{ route('practice.index') }}">thư viện đề thi miễn phí</a>.</p></details><details><summary>Có thu tiền thật không?</summary><p>Bạn thanh toán bằng mã QR ngân hàng. Admin xác nhận giao dịch đã vào tài khoản trước khi cấp quyền học.</p></details></section>
</div><aside class="detail-aside"><div class="detail-buy-card">
    @if($course->thumbnail_path)<img class="detail-buy-cover" src="{{ asset($course->thumbnail_path) }}" alt="{{ $course->name }}" width="800" height="300">@else<div class="shop-cover"><span class="shop-cover-brand">ENGLISH CENTER</span><strong>{{ $course->name }}</strong><span>HỌC ONLINE</span></div>@endif
    <div class="detail-buy-body"><p class="price-caption">Học phí chương trình</p><div class="detail-price"><strong>{{ number_format($shop->price($course),0,',','.') }}đ</strong>@if($course->sale_price!==null)<div><s>{{ number_format($course->price,0,',','.') }}đ</s><span>Tiết kiệm {{ number_format($course->price-$course->sale_price,0,',','.') }}đ</span></div>@endif</div>
    @if($owned)<a class="button buy-button" href="{{ route('learn.start',$course->slug) }}">Vào học ngay</a>@elseif($buyable)<form action="{{ route('cart.add',$course) }}" method="POST">@csrf<button class="button buy-button" type="submit">Thêm vào giỏ hàng</button></form><a class="button button-outline buy-button" href="{{ route('cart.index') }}">Xem giỏ hàng</a>@else<p class="course-coming-soon">Đang chuẩn bị nội dung</p><a class="button button-outline buy-button" href="{{ route('practice.index') }}">Luyện tập miễn phí</a>@endif
    @if($videoPath || $videoEmbed)<a class="course-watch-link" href="#information">▷ Xem video giới thiệu</a>@endif
    <div class="buy-facts"><p><x-study-icon name="book"/>{{ $lessonCount }} bài học đã công bố</p>@if($lessonCount)<p><x-study-icon name="clock"/>{{ $course->duration_hours }} giờ học gợi ý</p>@endif<p><x-study-icon name="target"/>Truy cập {{ $course->access_days }} ngày sau khi mua</p><p><x-study-icon name="file"/>Quản lý bài học trong tài khoản</p></div>
    @if($buyable)<p class="provider-caption">Thanh toán qua QR chuyển khoản ngân hàng. Khóa học được cấp sau khi admin xác nhận đã nhận tiền.</p>@endif</div>
</div></aside></div>
@endsection
