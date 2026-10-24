@extends('layouts.learning')
@section('title',$course->name.' — English Center')
@section('content')
@php
    $lessonCount = $course->sections->flatMap->lessons->count();
    $videoPath = \App\Services\CourseVideo::local($course->intro_video_path);
    $videoEmbed = \App\Services\CourseVideo::embed($course->intro_video_url);
    $trials = $trialLessons ?? $course->sections->flatMap->lessons->where('is_preview', true);
@endphp
<section class="detail-hero own-course-hero"><div class="shell"><div class="detail-hero-copy">
    <nav class="detail-breadcrumb"><a href="{{ route('catalog.index') }}">Chương trình học</a> / {{ $course->learningCategory?->name }}</nav>
    @if($owned)
        <div style="margin-bottom: 8px;">
            <span class="badge-owned" style="background: #059669; color: #ffffff; padding: 4px 12px; border-radius: 6px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                <x-study-icon name="check"/> Đã mua
            </span>
        </div>
    @endif
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
    
    <section class="detail-panel" id="syllabus">
        <div class="syllabus-heading" style="margin-bottom: 20px;">
            <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0;">Chương trình học</h2>
            <span style="font-size: 15px; color: #64748b; font-weight: 600;">{{ $lessonCount }} bài đã công bố</span>
        </div>

        @if($owned && $lessonCount > 0)
            @php
                $allL = $course->sections->flatMap->lessons;
                $doneCount = $allL->filter(fn($l) => in_array($l->id, $done))->count();
                $progressP = (int)round(($doneCount / $lessonCount) * 100);
            @endphp
            <div class="user-course-progress-banner" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px 20px; margin-bottom: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 15px; font-weight: 700; color: #166534;">Tiến độ học tập của bạn:</span>
                    <strong style="font-size: 15px; font-weight: 700; color: #059669;">{{ $doneCount }}/{{ $lessonCount }} bài đã hoàn thành ({{ $progressP }}%)</strong>
                </div>
                <div style="background: #dcfce7; border-radius: 10px; height: 10px; overflow: hidden;">
                    <div style="background: linear-gradient(90deg, #10b981, #059669); height: 100%; width: {{ max(1, $progressP) }}%; border-radius: 10px; transition: width 0.3s ease;"></div>
                </div>
            </div>
        @endif

        @if(count($course->curriculum_outline??[]))
            <div class="course-curriculum-outline" style="margin-bottom: 24px;">
                <h3 style="font-size: 17px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">Các chủ đề trong lộ trình</h3>
                <div class="syllabus-list">
                    @foreach($course->curriculum_outline as $topic)
                        <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                            <span style="font-weight:700;color:#64748b;font-size:14px;">{{ str_pad((string)$loop->iteration,2,'0',STR_PAD_LEFT) }}</span>
                            <h4 style="font-size:15px;font-weight:600;color:#334155;margin:0;">{{ $topic }}</h4>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Khối "Học thử miễn phí" chuẩn giao diện Study4 --}}
        @if($trials->isNotEmpty())
            <div class="contentblock study4-trial-block" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:10px;padding:20px 24px;margin-bottom:28px;box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <h5 class="font-500" style="font-size:17px;font-weight:700;color:#1e293b;margin:0 0 16px 0;">Học thử miễn phí</h5>
                <table class="table table-sm table-borderless table-hover" style="width:100%;border-collapse:collapse;">
                    <tbody>
                        @foreach($trials as $trial)
                            <tr style="border-bottom:1px solid #f8fafc;">
                                <td style="width:32px;padding:11px 4px;font-weight:700;color:#334155;font-size:15px;">{{ $loop->iteration }}.</td>
                                <td style="padding:11px 10px;font-size:15px;color:#1e293b;">
                                    <a href="{{ route('learn.lesson',[$course->slug,$trial->id]) }}" style="color:#1e293b;text-decoration:none;font-weight:500;">
                                        {{ $trial->title }}
                                    </a>
                                </td>
                                <td style="width:90px;padding:11px 4px;text-align:right;">
                                    <a href="{{ route('learn.lesson',[$course->slug,$trial->id]) }}" class="learncourse-trial-badge" style="background:#2563eb;color:#ffffff;font-size:13px;font-weight:700;padding:5px 12px;border-radius:6px;text-decoration:none;display:inline-block;">
                                        Học thử
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- Danh sách Accordion từng chuyên đề (Units) chuẩn Study4 --}}
        <div class="study4-units-accordion">
            @forelse($course->sections as $section)
                <details class="learncourse-unit card mb-2" @if($loop->first) open @endif style="border:1px solid #e2e8f0;border-radius:10px;margin-bottom:12px;background:#ffffff;overflow:hidden;">
                    <summary class="card-header sky-bg" style="background:#f0f7ff;padding:14px 20px;cursor:pointer;user-select:none;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #e2e8f0;">
                        <h3 class="learncourse-heading" style="font-size:16px;font-weight:700;color:#0f172a;margin:0;display:flex;align-items:center;gap:10px;">
                            <span class="ph ph-caret-down" style="font-size:13px;color:#475569;">▾</span>
                            <span>{{ $section->title }}</span>
                        </h3>
                        <span class="unit-count-badge" style="font-size:14px;font-weight:600;color:#2563eb;background:#e0f2fe;padding:3px 12px;border-radius:12px;">
                            {{ $section->lessons->count() }} bài
                        </span>
                    </summary>
                    <div class="card-body" style="padding:6px 0;">
                        <table class="table table-borderless table-hover" style="width:100%;border-collapse:collapse;">
                            <tbody>
                                @foreach($section->lessons as $lesson)
                                    @php($isDone = in_array($lesson->id, $done))
                                    <tr style="border-bottom:1px solid #f8fafc;">
                                        <td style="width:40px;padding:12px 18px;font-weight:700;color:#64748b;font-size:14px;">{{ $loop->iteration }}.</td>
                                        <td style="padding:12px 10px;font-size:15px;color:#1e293b;">
                                            @if($lesson->is_preview)
                                                <a href="{{ route('learn.lesson',[$course->slug,$lesson->id]) }}" style="color:#1e293b;text-decoration:none;font-weight:500;">
                                                    {{ $lesson->title }}
                                                </a>
                                            @elseif($owned)
                                                <a href="{{ route('learn.lesson',[$course->slug,$lesson->id]) }}" style="color:#0f172a;text-decoration:none;font-weight:500;">
                                                    {{ $lesson->title }}
                                                </a>
                                            @else
                                                <span style="color:#475569;">{{ $lesson->title }}</span>
                                            @endif
                                        </td>
                                        <td style="width:115px;padding:12px 18px;text-align:right;">
                                            @if($lesson->is_preview)
                                                <a href="{{ route('learn.lesson',[$course->slug,$lesson->id]) }}" class="learncourse-trial-badge" style="background:#2563eb;color:#ffffff;font-size:12px;font-weight:700;padding:4px 10px;border-radius:5px;text-decoration:none;display:inline-block;">
                                                    Học thử
                                                </a>
                                            @elseif($owned)
                                                @if($isDone)
                                                    <a href="{{ route('learn.lesson',[$course->slug,$lesson->id]) }}" style="color:#059669;font-size:13px;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:3px;">
                                                        ✓ Đã học
                                                    </a>
                                                @else
                                                    <a href="{{ route('learn.lesson',[$course->slug,$lesson->id]) }}" class="text-link" style="color:#2563eb;font-size:13px;font-weight:700;text-decoration:none;">
                                                        Vào học →
                                                    </a>
                                                @endif
                                            @else
                                                <span class="ph-lock" title="Cần mua khóa học để mở toàn bộ" style="color:#94a3b8;font-size:15px;">🔒</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            @empty
                <p class="course-content-status" style="font-size: 15px; padding: 20px;">Bài học đang được biên soạn. Chương trình sẽ mở đăng ký khi nội dung học được công bố.</p>
            @endforelse
        </div>
    </section>

    @if($course->classRooms && $course->classRooms->isNotEmpty())
        <section class="detail-panel" id="classes" style="margin-top: 24px; border-left: 4px solid #2563eb;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; margin-bottom: 16px;">
                <div>
                    <span class="eyebrow" style="color: #2563eb; font-weight: 700;">ĐÀO TẠO TRỰC TIẾP TẠI TRUNG TÂM</span>
                    <h2 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 4px 0 0;">Lớp học đang tuyển sinh của khóa này</h2>
                </div>
                <a href="{{ route('public_classes.index') }}" class="text-link" style="font-size: 13.5px; font-weight: 600;">Xem tất cả lớp →</a>
            </div>

            <div style="display: grid; gap: 12px;">
                @foreach($course->classRooms as $class)
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <span style="background: #2563eb; color: #fff; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">{{ $class->code }}</span>
                                <strong style="font-size: 15px; color: #0f172a;">{{ $class->name }}</strong>
                            </div>
                            <div style="font-size: 13px; color: #64748b;">
                                <span>Giảng viên: <strong>{{ $class->teacher?->user?->name ?? 'Đang cập nhật' }}</strong></span> ·
                                <span>Lịch: <strong>{{ $class->schedule }}</strong></span> ·
                                <span>Khai giảng: <strong>{{ $class->start_date?->format('d/m/Y') }}</strong></span>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 14px;">
                            <div style="text-align: right;">
                                <div style="font-size: 15px; font-weight: 800; color: #059669;">{{ number_format($class->tuition_fee, 0, ',', '.') }}đ</div>
                                <small style="color: #64748b; font-size: 12px;">Sĩ số: {{ $class->enrollments()->count() }}/{{ $class->max_students }}</small>
                            </div>
                            <a href="{{ route('public_classes.show', $class) }}" class="button button-small" style="text-decoration: none; font-size: 13px; font-weight: 700;">
                                Xem &amp; Đăng ký →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="detail-panel course-faq" id="questions"><h2>Giải đáp trước khi bắt đầu</h2><details open><summary>Mua xong tôi học ở đâu?</summary><p>Sau khi admin xác nhận đã nhận chuyển khoản, mở menu tài khoản → Khóa học của tôi → Vào học ngay. Bài học mở ngay trên English Center.</p></details><details><summary>Tôi có thể luyện đề trước khi đăng ký?</summary><p>Có. Bạn có thể làm các bài luyện và kiểm tra đáp án trong <a href="{{ route('practice.index') }}">thư viện đề thi miễn phí</a>.</p></details><details><summary>Có thu tiền thật không?</summary><p>Bạn thanh toán bằng mã QR ngân hàng. Admin xác nhận giao dịch đã vào tài khoản trước khi cấp quyền học.</p></details></section>
</div><aside class="detail-aside"><div class="detail-buy-card">
    @if($course->thumbnail_path)<img class="detail-buy-cover" src="{{ asset($course->thumbnail_path) }}" alt="{{ $course->name }}" width="800" height="300">@else<div class="shop-cover"><span class="shop-cover-brand">ENGLISH CENTER</span><strong>{{ $course->name }}</strong><span>HỌC ONLINE</span></div>@endif
    <div class="detail-buy-body"><p class="price-caption">Học phí chương trình</p><div class="detail-price"><strong>{{ number_format($shop->price($course),0,',','.') }}đ</strong>@if($course->sale_price!==null)<div><s>{{ number_format($course->price,0,',','.') }}đ</s><span>Tiết kiệm {{ number_format($course->price-$course->sale_price,0,',','.') }}đ</span></div>@endif</div>
    @if($owned)
        <div class="owned-notification" style="background: #ecfdf5; border: 1px solid #10b981; border-radius: 8px; padding: 12px 14px; margin-bottom: 14px; display: flex; align-items: center; gap: 10px; color: #065f46;">
            <span style="display:inline-flex; align-items:center; justify-content:center; width:24px; height:24px; background:#059669; color:#fff; border-radius:50%; font-size:13px; font-weight:bold; flex-shrink: 0;">✓</span>
            <div>
                <strong style="display: block; font-size: 14px; color:#065f46;">
                    @if(auth()->check() && auth()->user()->isAdmin())
                        Quyền Quản trị viên
                    @else
                        Khóa học đã mua
                    @endif
                </strong>
                <span style="font-size: 12px; color: #047857;">
                    @if(auth()->check() && auth()->user()->isAdmin())
                        Bạn có toàn quyền truy cập toàn bộ bài học và quản lý nội dung khóa học.
                    @else
                        Tài khoản của bạn đã sở hữu khóa học này.
                    @endif
                </span>
            </div>
        </div>
        <a class="button buy-button already-owned" href="{{ route('learn.start',$course->slug) }}" style="background:#059669;border-color:#059669;color:#ffffff;font-weight:700;"><x-study-icon name="check"/> Vào học ngay</a>
        @if(auth()->check() && auth()->user()->isAdmin())
            <a class="button button-outline buy-button" href="{{ route('shop_admin.courses.edit', $course) }}" style="margin-top: 8px; font-size: 13px;">⚙️ Quản lý khóa học trong Admin</a>
        @endif
    @elseif($buyable)
        <form action="{{ route('cart.add',$course) }}" method="POST">@csrf<button class="button buy-button" type="submit">Thêm vào giỏ hàng</button></form>
        <a class="button button-outline buy-button" href="{{ route('cart.index') }}">Xem giỏ hàng</a>
    @else
        <p class="course-coming-soon">Đang chuẩn bị nội dung</p>
        <a class="button button-outline buy-button" href="{{ route('practice.index') }}">Luyện tập miễn phí</a>
    @endif
    @if($videoPath || $videoEmbed)<a class="course-watch-link" href="#information">▷ Xem video giới thiệu</a>@endif
    <div class="buy-facts"><p><x-study-icon name="book"/>{{ $lessonCount }} bài học đã công bố</p>@if($lessonCount)<p><x-study-icon name="clock"/>{{ $course->duration_hours }} giờ học gợi ý</p>@endif<p><x-study-icon name="target"/>Truy cập {{ $course->access_days }} ngày sau khi mua</p><p><x-study-icon name="file"/>Quản lý bài học trong tài khoản</p></div>
    @if($buyable)<p class="provider-caption">Thanh toán qua QR chuyển khoản ngân hàng. Khóa học được cấp sau khi admin xác nhận đã nhận tiền.</p>@endif</div>
</div></aside></div>
@endsection
