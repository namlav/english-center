@extends('layouts.learning')
@section('title','English Center — Học tiếng Anh mỗi ngày')
@section('content')

{{-- 1. TOP HERO SECTION (STUDY4 & IIG E-LEARNING STYLE) --}}
<section class="study4-hero" id="intro">
    <div class="shell">
        <div class="study4-hero-grid">
            <div class="study4-hero-copy">
                <span class="study4-hero-badge">
                    <span class="badge-dot"></span> NỀN TẢNG HỌC &amp; LUYỆN THI CHỨNG CHỈ QUỐC TẾ
                </span>
                <h1 class="study4-hero-title">
                    {{ $home['title'] }}
                </h1>
                <p class="study4-hero-desc">
                    {{ $home['description'] }}
                </p>
                <div class="study4-hero-checklist">
                    <div class="study4-hero-checklist-item">
                        <span class="icon-check"><x-study-icon name="check" /></span>
                        <span>Phương pháp <strong>Dictation (Chép chính tả)</strong> bứt phá kỹ năng nghe hiểu</span>
                    </div>
                    <div class="study4-hero-checklist-item">
                        <span class="icon-check"><x-study-icon name="check" /></span>
                        <span>Phòng thi máy tính mô phỏng 100% format <strong>IDP, British Council &amp; ETS</strong></span>
                    </div>
                    <div class="study4-hero-checklist-item">
                        <span class="icon-check"><x-study-icon name="check" /></span>
                        <span>Lộ trình cá nhân hóa từ mất gốc đến <strong>IELTS 7.5+ &amp; TOEIC 850+</strong></span>
                    </div>
                </div>
                <div class="study4-hero-actions">
                    <a class="btn-study4-primary" href="{{ route('catalog.index') }}">
                        Khám phá khóa học <x-study-icon name="arrow" />
                    </a>
                    <a class="btn-study4-secondary" href="{{ route('practice.index') }}">
                        Luyện đề online miễn phí
                    </a>
                </div>
            </div>
            
            <div class="study4-hero-mockup">
                <div class="study4-hero-img-wrap">
                    <img src="{{ asset('images/banners/banner-home-hero.jpg') }}" alt="Học viên luyện thi tiếng Anh tại English Center" width="800" height="480" fetchpriority="high">
                </div>
                <div class="study4-floating-badge study4-floating-top">
                    <span style="font-size: 20px;">⭐️</span>
                    <div>
                        <strong style="font-size: 14px; color: #0f172a; display: block;">4.9/5 Đánh giá</strong>
                        <span style="font-size: 12px; color: #64748b;">Hơn 12.000+ học viên</span>
                    </div>
                </div>
                <div class="study4-floating-badge study4-floating-bottom">
                    <span style="font-size: 20px;">🎯</span>
                    <div>
                        <strong style="font-size: 14px; color: #0f172a; display: block;">Computer-Based Test</strong>
                        <span style="font-size: 12px; color: #64748b;">Mô phỏng phòng thi thật</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- QUICK STATS BAR --}}
        <div class="study4-stats-bar">
            <div class="study4-stat-card">
                <div class="study4-stat-icon" style="background: #eff6ff; color: #2563eb;">
                    <x-study-icon name="layers" />
                </div>
                <div class="study4-stat-info">
                    <strong>{{ $catalogTotal }} khóa học trực tuyến</strong>
                    <span>IELTS, TOEIC &amp; Nền tảng</span>
                </div>
            </div>
            <div class="study4-stat-card">
                <div class="study4-stat-icon" style="background: #ecfdf5; color: #059669;">
                    <x-study-icon name="book" />
                </div>
                <div class="study4-stat-info">
                    <strong>{{ $stats['papers'] }} bộ bài luyện</strong>
                    <span>Đề thi thử có đáp án chi tiết</span>
                </div>
            </div>
            <div class="study4-stat-card">
                <div class="study4-stat-icon" style="background: #fffbeb; color: #d97706;">
                    <x-study-icon name="file" />
                </div>
                <div class="study4-stat-info">
                    <strong>{{ $stats['questions'] }} câu hỏi</strong>
                    <span>Luyện tập thực hành miễn phí</span>
                </div>
            </div>
            <div class="study4-stat-card">
                <div class="study4-stat-icon" style="background: #faf5ff; color: #9333ea;">
                    <x-study-icon name="spark" />
                </div>
                <div class="study4-stat-info">
                    <strong>100% Phương pháp Dictation</strong>
                    <span>Tự học hiệu quả gấp 3 lần</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 2. EXCLUSIVE STUDY METHODOLOGY (STUDY4 METHOD) --}}
<section class="study4-section" style="background: #ffffff; border-bottom: 1px solid var(--study4-border);">
    <div class="shell">
        <div class="study4-section-header" style="text-align: center; justify-content: center; margin-bottom: 36px;">
            <div style="max-width: 720px;">
                <span class="study4-eyebrow">PHƯƠNG PHÁP HỌC ĐỘC QUYỀN</span>
                <h2 class="study4-section-h2">Bứt phá điểm số cùng công nghệ học tập thông minh</h2>
                <p class="study4-section-subtitle">
                    Hệ thống tích hợp các phương pháp khoa học đã được kiểm chứng, giúp người học ghi nhớ sâu và phản xạ nhanh như người bản xứ.
                </p>
            </div>
        </div>

        <div class="study4-methods-grid">
            <div class="study4-method-card">
                <div class="study4-method-icon" style="background: #eff6ff; color: #2563eb;">
                    <span style="font-size: 24px;">🎧</span>
                </div>
                <h3>Phương pháp Dictation (Chép chính tả)</h3>
                <p>Nghe từng câu ngắn, gõ lại nội dung để nhận diện nối âm, nuốt âm, trọng âm câu và bứt phá kỹ năng Listening.</p>
            </div>

            <div class="study4-method-card">
                <div class="study4-method-icon" style="background: #ecfdf5; color: #059669;">
                    <span style="font-size: 24px;">💻</span>
                </div>
                <h3>Mô phỏng thi máy tính 100%</h3>
                <p>Giao diện chia đôi màn hình, công cụ highlight, take note và đồng hồ đếm ngược y hệt phần mềm thi IELTS &amp; TOEIC thật.</p>
            </div>

            <div class="study4-method-card">
                <div class="study4-method-icon" style="background: #fffbeb; color: #d97706;">
                    <span style="font-size: 24px;">🧠</span>
                </div>
                <h3>Spaced Repetition &amp; Flashcard</h3>
                <p>Thuật toán lặp lại ngắt quãng khoa học, tự động nhắc lại từ vựng vào thời điểm vàng để khắc sâu vào trí nhớ dài hạn.</p>
            </div>

            <div class="study4-method-card">
                <div class="study4-method-icon" style="background: #faf5ff; color: #9333ea;">
                    <span style="font-size: 24px;">📊</span>
                </div>
                <h3>Chấm chữa &amp; Lời giải chi tiết</h3>
                <p>Tự động phân tích điểm số, dịch nghĩa câu hỏi, chỉ rõ bẫy đề thi và đo lường tiến độ học tập từng ngày.</p>
            </div>
        </div>
    </div>
</section>

{{-- 3. FEATURED COURSES (STUDY4 HOME COURSES) --}}
<section class="study4-section home-feature" style="background: #f8fafc; border-bottom: 1px solid var(--study4-border);">
    <div class="shell">
        <div class="study4-section-header">
            <div class="study4-section-header-title">
                <span class="study4-eyebrow">CHƯƠNG TRÌNH ĐÀO TẠO TRỰC TUYẾN</span>
                <h2 class="study4-section-h2">Khóa học online nổi bật</h2>
                <p class="study4-section-subtitle">
                    Hệ thống khóa học toàn diện theo lộ trình cá nhân hóa, từ xây gốc đến luyện thi chuyên sâu.
                </p>
            </div>
            <a class="study4-view-all-link" href="{{ route('catalog.index') }}">
                Xem tất cả khóa học <x-study-icon name="arrow" />
            </a>
        </div>

        <div class="study4-category-pills">
            <a class="study4-category-pill active" href="{{ route('catalog.index') }}">Tất cả khóa học</a>
            <a class="study4-category-pill" href="{{ route('catalog.index', ['category' => 'ielts']) }}">IELTS Intensive</a>
            <a class="study4-category-pill" href="{{ route('catalog.index', ['category' => 'toeic']) }}">Luyện thi TOEIC</a>
            <a class="study4-category-pill" href="{{ route('catalog.index', ['category' => 'english-foundation']) }}">Tiếng Anh nền tảng</a>
        </div>

        <div class="course-grid">
            @forelse($featuredCourses as $course)
                @include('learning.shop.card')
            @empty
                <div class="empty-state">
                    <h3>Khóa học đang được cập nhật</h3>
                    <p>Quay lại sau để khám phá các chương trình học.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- 4. PRACTICE ZONE (STUDY4 HOME TESTS) --}}
<section class="study4-section practice-section" style="background: #ffffff; border-bottom: 1px solid var(--study4-border);">
    <div class="shell">
        <div class="study4-section-header">
            <div class="study4-section-header-title">
                <span class="study4-eyebrow">THỰC HÀNH MỖI NGÀY</span>
                <h2 class="study4-section-h2">Đề thi mới nhất &amp; Thư viện bài luyện</h2>
                <p class="study4-section-subtitle">
                    Hàng chục bộ đề luyện thi trực tuyến kèm đồng hồ bấm giờ, đáp án và giải thích chi tiết.
                </p>
            </div>
            <a class="study4-view-all-link" href="{{ route('practice.index') }}">
                Đến thư viện đề thi <x-study-icon name="arrow" />
            </a>
        </div>

        <div class="practice-grid">
            @forelse($featuredPapers as $paper)
                @include('learning.partials.paper-card',['paperNumber'=>$loop->iteration])
            @empty
                <div class="empty-state">
                    <h3>Một hành trình mới đang bắt đầu</h3>
                    <p>Các bài luyện sẽ xuất hiện khi được đăng tải.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

{{-- 5. TEACHER SHOWCASE (PRESERVES data-teacher-carousel FOR TESTS) --}}
<section class="teacher-showcase" id="teachers" aria-labelledby="teacher-heading">
    <div class="shell teacher-intro">
        <div>
            <span class="eyebrow">GƯƠNG MẶT GIẢNG DẠY</span>
            <h2 id="teacher-heading">{{ $home['teacher_title'] }}</h2>
        </div>
        <div>
            <p>{{ $home['teacher_description'] }}</p>
            <span class="teacher-source-label">Thông tin chuyên môn theo hồ sơ công khai của PREP</span>
        </div>
    </div>

    @if($teachers->isNotEmpty())
        <div class="teacher-carousel" data-teacher-carousel role="region" aria-roledescription="băng chuyền" aria-label="Giáo viên được giới thiệu">
            <div class="teacher-stage">
                @foreach($teachers as $teacher)
                    <article class="teacher-slide {{ $loop->first ? 'is-current' : ($loop->index === 1 ? 'is-next' : 'is-previous') }}" data-teacher-slide aria-label="{{ $loop->iteration }} / {{ $teachers->count() }}">
                        <a class="teacher-portrait" href="{{ route('public_teachers.show',$teacher->slug) }}" tabindex="{{ $loop->first ? '0' : '-1' }}">
                            <img src="{{ asset($teacher->photo_path) }}" alt="{{ $teacher->name }}" width="384" height="498">
                            <span class="portrait-detail">Xem chi tiết ↗</span>
                        </a>
                        <div class="teacher-caption">
                            <h3>{{ $teacher->name }}</h3>
                            <span>{{ $teacher->provider }}</span>
                            <p>{{ $teacher->qualifications[0] ?? $teacher->bio }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="teacher-controls">
                <button type="button" class="carousel-arrow" data-teacher-prev aria-label="Giáo viên trước">←</button>
                <div class="carousel-dots">
                    @foreach($teachers as $teacher)
                        <button type="button" data-teacher-dot="{{ $loop->index }}" aria-label="Xem {{ $teacher->name }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"></button>
                    @endforeach
                </div>
                <button type="button" class="carousel-arrow" data-teacher-next aria-label="Giáo viên tiếp theo">→</button>
                <button class="carousel-pause" type="button" data-teacher-pause aria-label="Tạm dừng chuyển giáo viên" aria-pressed="false">Ⅱ Tạm dừng</button>
            </div>
            <p class="visually-hidden" data-teacher-status aria-live="polite"></p>
        </div>
    @else
        <div class="shell empty-state">
            <h3>Hồ sơ giáo viên đang được cập nhật</h3>
            <p>Các giáo viên sẽ xuất hiện tại đây sau khi được giới thiệu.</p>
            @auth
                @if(auth()->user()->isAdmin())
                    <a class="button" href="{{ route('website.index') }}">Quản lý nội dung website</a>
                @endif
            @endauth
        </div>
    @endif
</section>

{{-- 6. HOW IT WORKS (STUDY GUIDE) --}}
<section class="study-guide shell" id="how-it-works" style="padding: 56px 0;">
    <div class="study4-section-header" style="text-align: center; justify-content: center; margin-bottom: 36px;">
        <div>
            <span class="study4-eyebrow">QUY TRÌNH HỌC TẬP</span>
            <h2 class="study4-section-h2">Bắt đầu học như thế nào?</h2>
            <p class="study4-section-subtitle">Chỉ 3 bước đơn giản để thiết lập lộ trình và bứt phá mục tiêu của bạn.</p>
        </div>
    </div>
    <div class="guide-columns">
        <article>
            <span>01</span>
            <h3>Khám phá chương trình</h3>
            <p>Chọn mục tiêu, xem nội dung chi tiết và chọn khóa học phù hợp với trình độ hiện tại.</p>
        </article>
        <article>
            <span>02</span>
            <h3>Luyện tập từng ngày</h3>
            <p>Luyện nghe chép chính tả Dictation, làm đề thi thử và đọc giải thích chi tiết từng câu.</p>
        </article>
        <article>
            <span>03</span>
            <h3>Theo dõi việc học</h3>
            <p>Hệ thống tự động lưu tiến độ, xem lại các bài đã hoàn thành và sẵn sàng cho kỳ thi thật.</p>
        </article>
    </div>
</section>

{{-- 7. BOTTOM CTA BANNER (IIG & STUDY4 STYLE) --}}
<section class="shell">
    <div class="study4-bottom-banner">
        <div class="study4-bottom-banner-content">
            <span style="display: inline-block; font-size: 12px; font-weight: 800; letter-spacing: 0.1em; color: #93c5fd; text-transform: uppercase; margin-bottom: 12px;">
                ✦ BẮT ĐẦU NGAY HÔM NAY
            </span>
            <h2>Sẵn sàng bứt phá mục tiêu tiếng Anh của bạn?</h2>
            <p>
                Tham gia cùng hàng nghìn học viên đang tự tin luyện thi IELTS, TOEIC và tiếng Anh thực hành mỗi ngày trên nền tảng English Center.
            </p>
            <div class="study4-bottom-banner-actions">
                <a class="btn-study4-primary" href="{{ route('catalog.index') }}" style="background: #ffffff; color: #1e3a8a !important; border-color: #ffffff; font-weight: 800;">
                    Khám phá khóa học ngay <x-study-icon name="arrow" />
                </a>
                <a class="btn-study4-secondary" href="{{ route('practice.index') }}" style="background: transparent; color: #ffffff !important; border-color: rgba(255,255,255,0.4);">
                    Luyện đề online miễn phí
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
