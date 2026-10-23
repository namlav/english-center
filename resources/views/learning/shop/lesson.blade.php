@extends('layouts.learning')
@section('title',$lesson->title.' — '.$course->name)
@section('content')
@php
    $userShop = app(\App\Services\CourseShop::class);
    $isOwned = auth()->check() && $userShop->owned(auth()->user(), $course->id);
    
    $allCourseLessons = $course->sections->flatMap->lessons;
    $totalCourseLessons = $allCourseLessons->count();
    $completedCourseLessons = $allCourseLessons->filter(fn($l) => in_array($l->id, $done))->count();
    $progressPercent = $totalCourseLessons > 0 ? (int)round(($completedCourseLessons / $totalCourseLessons) * 100) : 0;
    $isThisCompleted = in_array($lesson->id, $done);
@endphp

<div class="shell lesson-layout" style="display: grid; grid-template-columns: 320px minmax(0, 1fr); gap: 32px; align-items: start; padding: 32px 0 64px;">
    {{-- Thanh bên danh sách bài học --}}
    <aside class="lesson-sidebar" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 22px 18px; position: sticky; top: 90px; max-height: calc(100vh - 110px); overflow-y: auto;">
        <a class="text-link" href="{{ $isOwned ? route('account.courses') : route('shop.show', $course->slug) }}" style="font-size: 14px; font-weight: 600; color: #2563eb; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 14px; text-decoration: none;">
            {{ $isOwned ? '← Khóa học của tôi' : '← Về thông tin khóa học' }}
        </a>
        
        <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 0 0 16px 0; line-height: 1.4;">{{ $course->name }}</h2>

        {{-- Thanh tiến độ khóa học trực quan --}}
        @if($isOwned)
            <div class="course-learning-progress" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 13px; font-weight: 600; color: #475569;">Tiến độ học tập:</span>
                    <strong style="font-size: 14px; font-weight: 700; color: #059669;">{{ $completedCourseLessons }}/{{ $totalCourseLessons }} bài ({{ $progressPercent }}%)</strong>
                </div>
                <div style="background: #e2e8f0; border-radius: 10px; height: 8px; overflow: hidden;">
                    <div style="background: linear-gradient(90deg, #10b981, #059669); height: 100%; width: {{ max(2, $progressPercent) }}%; border-radius: 10px; transition: width 0.3s ease;"></div>
                </div>
            </div>
        @endif

        <div class="lesson-sections-list">
            @foreach($course->sections as $section)
                <div style="margin-bottom: 16px;">
                    <h3 style="font-size: 14px; font-weight: 700; color: #334155; text-transform: uppercase; letter-spacing: 0.04em; margin: 0 0 8px 0; padding-bottom: 4px; border-bottom: 1px solid #f1f5f9;">
                        {{ $section->title }}
                    </h3>
                    <nav aria-label="Bài học" style="display: flex; flex-direction: column; gap: 4px;">
                        @foreach($section->lessons as $item)
                            @php
                                $itemDone = in_array($item->id, $done);
                                $isCurrent = $item->id === $lesson->id;
                            @endphp
                            @if($isOwned || $item->is_preview)
                                <a href="{{ route('learn.lesson',[$course->slug,$item->id]) }}" 
                                   style="display: flex; align-items: center; gap: 10px; padding: 9px 12px; border-radius: 6px; font-size: 14px; line-height: 1.45; text-decoration: none; {{ $isCurrent ? 'background: #eff6ff; color: #1d4ed8; font-weight: 600; border-left: 3px solid #2563eb;' : 'color: #334155;' }}"
                                   @if($isCurrent) aria-current="page" @endif>
                                    <span style="font-weight: 700; font-size: 14px; width: 16px; text-align: center; color: {{ $itemDone ? '#059669' : ($isCurrent ? '#2563eb' : '#94a3b8') }};">
                                        {{ $itemDone ? '✓' : ($item->is_preview ? '★' : '○') }}
                                    </span>
                                    <span style="flex: 1;">{{ $item->title }}</span>
                                    @if($item->is_preview && !$isOwned)
                                        <span style="background: #2563eb; color: #fff; font-size: 11px; font-weight: 600; padding: 2px 6px; border-radius: 4px;">Học thử</span>
                                    @endif
                                </a>
                            @else
                                <span style="display: flex; align-items: center; gap: 10px; padding: 8px 12px; color: #94a3b8; font-size: 14px;">
                                    <span>🔒</span> {{ $item->title }}
                                </span>
                            @endif
                        @endforeach
                    </nav>
                </div>
            @endforeach
        </div>
    </aside>

    {{-- Khu vực nội dung bài học chính --}}
    <article class="lesson-content" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 36px 40px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        @if($lesson->is_preview && !$isOwned)
            <div class="trial-preview-alert" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 14px 20px; border-radius: 8px; margin-bottom: 28px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <span style="background: #2563eb; color: #ffffff; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700;">HỌC THỬ MIỄN PHÍ</span>
                    <span style="font-size: 15px;">Bạn đang trải nghiệm bài học thử của khóa học <strong>{{ $course->name }}</strong>.</span>
                </div>
                <a href="{{ route('shop.show', $course->slug) }}" class="button button-small" style="background: #2563eb; border-color: #2563eb; color: #ffffff; text-decoration: none; font-weight: 700; font-size: 14px; padding: 7px 16px;">
                    Đăng ký khóa học ngay →
                </a>
            </div>
        @endif

        {{-- Tiến độ hiển thị ngay đầu bài học --}}
        @if($isOwned)
            <div class="lesson-top-progress" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 18px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 14px; font-weight: 600; color: #475569;">Tiến độ của bạn:</span>
                    <span style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; border-radius: 6px; padding: 3px 10px; font-size: 13px; font-weight: 700;">
                        {{ $completedCourseLessons }} / {{ $totalCourseLessons }} bài học ({{ $progressPercent }}%)
                    </span>
                </div>
                @if($isThisCompleted)
                    <span style="color: #059669; font-size: 14px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                        ✓ Bài học đã hoàn thành
                    </span>
                @endif
            </div>
        @endif

        <span class="eyebrow" style="font-size: 13px; font-weight: 700; color: #3b82f6; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 8px;">
            BÀI HỌC · ENGLISH CENTER
        </span>
        <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0; line-height: 1.35;">
            {{ $lesson->title }}
        </h1>
        
        <p class="small-source" style="font-size: 14px; color: #64748b; margin-bottom: 28px;">
            Thời lượng gợi ý: {{ (int)ceil($lesson->duration_seconds/60) }} phút 
            @if($lesson->is_preview) · <strong style="color: #2563eb;">Bài học thử miễn phí</strong>@endif
        </p>
        
        <div class="lesson-text" style="font-size: 16px; line-height: 1.85; color: #1e293b; margin-bottom: 36px; white-space: pre-line;">{{ $lesson->content }}</div>

        @if($lesson->examPapers->isNotEmpty())
            <section class="lesson-exams" style="margin: 32px 0; padding: 22px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 0 0 14px 0;">Đề luyện kèm bài học</h2>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach($lesson->examPapers as $paper)
                        <a href="{{ route('practice.show',$paper->slug) }}" style="display: flex; align-items: center; justify-content: space-between; padding: 12px 18px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 8px; text-decoration: none; color: #1e293b;">
                            <span style="display: flex; align-items: center; gap: 10px; font-size: 15px; font-weight: 600;">
                                <x-study-icon name="file"/>
                                <span>{{ $paper->title }}</span>
                            </span>
                            <strong style="color: #2563eb; font-size: 14px;">Làm bài →</strong>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Khối tương tác hoàn thành & điều hướng bài học --}}
        <div class="lesson-action-footer" style="margin-top: 40px; padding-top: 24px; border-top: 1px solid #e2e8f0;">
            @if($isOwned)
                <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px;">
                    <form method="POST" action="{{ route('learn.complete',[$course->slug,$lesson->id]) }}" style="margin: 0;">
                        @csrf
                        <button class="button" type="submit" style="font-size: 15px; font-weight: 700; padding: 12px 24px; border-radius: 8px; {{ $isThisCompleted ? 'background: #059669; border-color: #059669; color: #ffffff;' : 'background: #2563eb; border-color: #2563eb; color: #ffffff;' }}">
                            {{ $isThisCompleted ? '✓ Đã hoàn thành (Bấm để hủy)' : 'Đánh dấu đã hoàn thành bài học' }}
                        </button>
                    </form>

                    <div style="display: flex; align-items: center; gap: 10px;">
                        @if($prevLesson)
                            <a href="{{ route('learn.lesson', [$course->slug, $prevLesson->id]) }}" class="button button-outline" style="font-size: 14px; font-weight: 600; padding: 10px 18px; border-radius: 8px;">
                                ← Bài trước
                            </a>
                        @endif

                        @if($nextLesson)
                            <a href="{{ route('learn.lesson', [$course->slug, $nextLesson->id]) }}" class="button" style="background: #2563eb; border-color: #2563eb; color: #ffffff; font-size: 14px; font-weight: 700; padding: 10px 20px; border-radius: 8px;">
                                Bài tiếp theo →
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div style="padding: 22px; background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                    <span style="font-size: 15px; color: #334155;">Bạn cảm thấy bài học thử này hữu ích? Hãy đăng ký khóa học để tiếp tục học lộ trình đầy đủ nhé!</span>
                    <a href="{{ route('shop.show', $course->slug) }}" class="button" style="background: #2563eb; border-color: #2563eb; color: #ffffff; font-weight: 700; font-size: 15px; padding: 10px 22px;">Mua khóa học này →</a>
                </div>
            @endif
        </div>
    </article>
</div>
@endsection
