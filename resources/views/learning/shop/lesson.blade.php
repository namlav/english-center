@extends('layouts.learning')
@section('title',$lesson->title.' — '.$course->name)
@section('content')
@php
    $userShop = app(\App\Services\CourseShop::class);
    $isOwned = auth()->check() && $userShop->owned(auth()->user(), $course->id);
@endphp
<div class="shell lesson-layout">
    <aside class="lesson-sidebar">
        <a class="text-link" href="{{ $isOwned ? route('account.courses') : route('shop.show', $course->slug) }}">
            {{ $isOwned ? '← Khóa học của tôi' : '← Về trang thông tin khóa học' }}
        </a>
        <h2>{{ $course->name }}</h2>
        @foreach($course->sections as $section)
            <h3>{{ $section->title }}</h3>
            <nav aria-label="Bài học">
                @foreach($section->lessons as $item)
                    @if($isOwned || $item->is_preview)
                        <a href="{{ route('learn.lesson',[$course->slug,$item->id]) }}" @if($item->id===$lesson->id) aria-current="page" @endif>
                            <span>{{ in_array($item->id,$done)?'✓':($item->is_preview ? '★' : '○') }}</span>
                            {{ $item->title }}
                            @if($item->is_preview && !$isOwned)
                                <span style="background: #2563eb; color: #fff; font-size: 10px; padding: 1px 6px; border-radius: 4px; margin-left: auto;">Học thử</span>
                            @endif
                        </a>
                    @else
                        <span style="display: flex; align-items: center; gap: 8px; padding: 7px 12px; color: #94a3b8; font-size: 13px;">
                            <span>🔒</span> {{ $item->title }}
                        </span>
                    @endif
                @endforeach
            </nav>
        @endforeach
    </aside>

    <article class="lesson-content">
        @if($lesson->is_preview && !$isOwned)
            <div class="trial-preview-alert" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 12px 18px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="background: #2563eb; color: #ffffff; padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 700;">HỌC THỬ MIỄN PHÍ</span>
                    <span>Bạn đang trải nghiệm bài học thử của khóa học <strong>{{ $course->name }}</strong>.</span>
                </div>
                <a href="{{ route('shop.show', $course->slug) }}" class="button button-small" style="background: #2563eb; border-color: #2563eb; color: #ffffff; text-decoration: none; font-weight: 700;">
                    Đăng ký khóa học ngay →
                </a>
            </div>
        @endif

        <span class="eyebrow">BÀI HỌC · ENGLISH CENTER</span>
        <h1>{{ $lesson->title }}</h1>
        <p class="small-source">Thời lượng gợi ý: {{ (int)ceil($lesson->duration_seconds/60) }} phút @if($lesson->is_preview) · <strong style="color: #2563eb;">Bài học thử miễn phí</strong>@endif</p>
        
        <div class="lesson-text">{{ $lesson->content }}</div>

        @if($lesson->examPapers->isNotEmpty())
            <section class="lesson-exams">
                <h2>Đề luyện kèm bài học</h2>
                @foreach($lesson->examPapers as $paper)
                    <a href="{{ route('practice.show',$paper->slug) }}">
                        <x-study-icon name="file"/>
                        <span>{{ $paper->title }}</span>
                        <strong>Làm bài →</strong>
                    </a>
                @endforeach
            </section>
        @endif

        @if($isOwned)
            <form method="POST" action="{{ route('learn.complete',[$course->slug,$lesson->id]) }}">
                @csrf
                <button class="button" type="submit">{{ in_array($lesson->id,$done)?'✓ Đã hoàn thành bài học':'Đánh dấu đã hoàn thành' }}</button>
            </form>
        @else
            <div style="margin-top: 30px; padding: 18px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                <span>Bạn cảm thấy bài học thử này hữu ích? Đăng ký khóa học để học toàn bộ lộ trình và làm bài tập nhé!</span>
                <a href="{{ route('shop.show', $course->slug) }}" class="button" style="background: #2563eb; border-color: #2563eb; color: #ffffff; font-weight: 700;">Mua khóa học này →</a>
            </div>
        @endif
    </article>
</div>
@endsection
