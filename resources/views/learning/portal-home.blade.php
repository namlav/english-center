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
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <a class="btn-study4-primary" href="{{ route('website.index') }}">
                            ⚙️ Trang Quản Trị Website <x-study-icon name="arrow" />
                        </a>
                        <a class="btn-study4-secondary" href="{{ route('shop_admin.index') }}">
                            📚 Quản lý Khóa học &amp; Video
                        </a>
                    @else
                        <a class="btn-study4-primary" href="{{ route('catalog.index') }}">
                            Khám phá khóa học <x-study-icon name="arrow" />
                        </a>
                        <a class="btn-study4-secondary" href="{{ route('practice.index') }}">
                            Luyện đề online miễn phí
                        </a>
                    @endif
                </div>
            </div>
            
            <div class="study4-hero-mockup">
                <div class="study4-hero-slider" id="heroRealBannerSlider" style="position: relative; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px -15px rgba(0,0,0,0.22); border: 4px solid #ffffff;">
                    @php
                        $heroBanners = [
                            ['img' => $home['image'] ?? 'images/banners/banner-home-hero.jpg', 'title' => 'Luyện thi chứng chỉ quốc tế', 'tag' => 'Chuẩn Quốc Tế'],
                            ['img' => 'images/banners/banner-ielts-real.jpg', 'title' => 'IELTS Intensive 7.5+ bứt phá', 'tag' => 'IELTS 7.5+'],
                            ['img' => 'images/banners/banner-toeic-real.jpg', 'title' => 'TOEIC 850+ & Giao tiếp phản xạ', 'tag' => 'TOEIC 850+'],
                            ['img' => 'images/banners/banner-online-real.jpg', 'title' => 'Học E-Learning 24/7 linh hoạt', 'tag' => 'Online 24/7'],
                            ['img' => 'images/banners/banner-speaking-real.jpg', 'title' => 'Luyện Phát Âm & Nói Bản Xứ 1-on-1', 'tag' => 'Speaking 1-on-1'],
                        ];
                    @endphp
                    
                    <div class="hero-slides-wrapper" style="position: relative; width: 100%; height: 380px;">
                        @foreach($heroBanners as $idx => $b)
                            <div class="hero-slide-item {{ $idx === 0 ? 'active' : '' }}" data-slide-index="{{ $idx }}" style="position: absolute; inset: 0; opacity: {{ $idx === 0 ? '1' : '0' }}; transition: opacity 0.6s ease-in-out; pointer-events: {{ $idx === 0 ? 'auto' : 'none' }};">
                                <img src="{{ asset($b['img']) }}" alt="{{ $b['title'] }}" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                                <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 45%, rgba(15,23,42,0.85) 100%); display: flex; align-items: flex-end; padding: 20px 24px;">
                                    <div>
                                        <span style="background: #2563eb; color: #fff; font-size: 11px; font-weight: 800; padding: 3px 10px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-block; margin-bottom: 6px;">{{ $b['tag'] }}</span>
                                        <h4 style="color: #ffffff; font-size: 17px; font-weight: 700; margin: 0; text-shadow: 0 1px 3px rgba(0,0,0,0.5);">{{ $b['title'] }}</h4>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Slider Controls --}}
                    <button type="button" class="hero-slide-prev" onclick="prevHeroBanner()" aria-label="Banner trước" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,0.9); border: none; color: #0f172a; font-size: 20px; font-weight: 700; line-height: 1; display: grid; place-items: center; cursor: pointer; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">‹</button>
                    <button type="button" class="hero-slide-next" onclick="nextHeroBanner()" aria-label="Banner kế tiếp" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,0.9); border: none; color: #0f172a; font-size: 20px; font-weight: 700; line-height: 1; display: grid; place-items: center; cursor: pointer; z-index: 10; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">›</button>

                    {{-- Slider Dots --}}
                    <div class="hero-slide-dots" style="position: absolute; bottom: 12px; right: 20px; display: flex; gap: 6px; z-index: 10;">
                        @foreach($heroBanners as $idx => $b)
                            <button type="button" class="hero-dot {{ $idx === 0 ? 'active' : '' }}" onclick="goToHeroBanner({{ $idx }})" aria-label="Đến slide {{ $idx + 1 }}" style="width: {{ $idx === 0 ? '20px' : '8px' }}; height: 8px; border-radius: 4px; border: none; background: {{ $idx === 0 ? '#38bdf8' : 'rgba(255,255,255,0.5)' }}; cursor: pointer; transition: all 0.25s; padding: 0;"></button>
                        @endforeach
                    </div>
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
                @if(auth()->check() && auth()->user()->isAdmin())
                    <a class="btn-study4-primary" href="{{ route('website.index') }}" style="background: #ffffff; color: #1e3a8a !important; border-color: #ffffff; font-weight: 800;">
                        ⚙️ Bảng Quản Trị Trung Tâm <x-study-icon name="arrow" />
                    </a>
                    <a class="btn-study4-secondary" href="{{ route('shop_admin.orders.index') }}" style="background: transparent; color: #ffffff !important; border-color: rgba(255,255,255,0.4);">
                        📦 Quản lý đơn hàng
                    </a>
                @else
                    <a class="btn-study4-primary" href="{{ route('catalog.index') }}" style="background: #ffffff; color: #1e3a8a !important; border-color: #ffffff; font-weight: 800;">
                        Khám phá khóa học ngay <x-study-icon name="arrow" />
                    </a>
                    <a class="btn-study4-secondary" href="{{ route('practice.index') }}" style="background: transparent; color: #ffffff !important; border-color: rgba(255,255,255,0.4);">
                        Luyện đề online miễn phí
                    </a>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- 8. REAL CAMPUS & LEARNING SPACES (ẢNH THẬT TRUNG TÂM) --}}
<section class="study4-section" style="background: #f8fafc; border-top: 1px solid var(--study4-border); padding-block: 55px; margin-top: 40px;">
    <div class="shell">
        <div class="study4-section-header text-center" style="max-width: 650px; margin: 0 auto 36px; text-align: center;">
            <span class="study4-eyebrow" style="display: block; margin-bottom: 6px;">KHÔNG GIAN HỌC TẬP THỰC TẾ</span>
            <h2 class="study4-section-h2" style="font-size: 30px; font-weight: 800; color: #0f172a;">Môi trường học tập & Đào tạo chuyên nghiệp</h2>
            <p class="study4-section-subtitle" style="color: #64748b; font-size: 14.5px;">
                Cơ sở vật chất hiện đại, phòng thi máy tính chuẩn IDP/ETS và không gian học tập truyền cảm hứng.
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); position: relative; height: 260px;">
                <img src="{{ asset('images/catalog/study4/study-banner.jpg') }}" alt="Phòng máy luyện thi IELTS" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 40%, rgba(15,23,42,0.85) 100%); display: flex; align-items: flex-end; padding: 18px;">
                    <div>
                        <span style="background: #2563eb; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">Cơ sở vật chất</span>
                        <h4 style="color: #ffffff; font-weight: 700; font-size: 15px; margin: 4px 0 0;">Phòng máy luyện thi máy tính chuẩn ETS & IDP</h4>
                    </div>
                </div>
            </div>
            <div style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); position: relative; height: 260px;">
                <img src="{{ asset('images/banners/banner-toeic-real.jpg') }}" alt="Lớp học tương tác" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 40%, rgba(15,23,42,0.85) 100%); display: flex; align-items: flex-end; padding: 18px;">
                    <div>
                        <span style="background: #059669; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">Lớp học thực tế</span>
                        <h4 style="color: #ffffff; font-weight: 700; font-size: 15px; margin: 4px 0 0;">Lớp học sĩ số vàng & Giảng viên kèm cặp</h4>
                    </div>
                </div>
            </div>
            <div style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06); position: relative; height: 260px;">
                <img src="{{ asset('images/banners/banner-speaking-real.jpg') }}" alt="Luyện Speaking 1-on-1" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 40%, rgba(15,23,42,0.85) 100%); display: flex; align-items: flex-end; padding: 18px;">
                    <div>
                        <span style="background: #d97706; color: #fff; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px; text-transform: uppercase;">Phản xạ bản ngữ</span>
                        <h4 style="color: #ffffff; font-weight: 700; font-size: 15px; margin: 4px 0 0;">Luyện phát âm & Speaking tương tác 1 kèm 1</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Hero Real Banner Slider Script
    (function() {
        let currentIdx = 0;
        const slides = document.querySelectorAll('.hero-slide-item');
        const dots = document.querySelectorAll('.hero-dot');
        if (!slides.length) return;
        
        let timer = setInterval(nextSlide, 4500);

        function showSlide(n) {
            currentIdx = (n + slides.length) % slides.length;
            slides.forEach((s, i) => {
                s.style.opacity = i === currentIdx ? '1' : '0';
                s.style.pointerEvents = i === currentIdx ? 'auto' : 'none';
            });
            dots.forEach((d, i) => {
                d.style.width = i === currentIdx ? '20px' : '8px';
                d.style.background = i === currentIdx ? '#38bdf8' : 'rgba(255,255,255,0.5)';
            });
        }
        function nextSlide() { showSlide(currentIdx + 1); }
        function prevSlide() { showSlide(currentIdx - 1); }

        window.nextHeroBanner = function() {
            clearInterval(timer);
            nextSlide();
            timer = setInterval(nextSlide, 4500);
        };
        window.prevHeroBanner = function() {
            clearInterval(timer);
            prevSlide();
            timer = setInterval(nextSlide, 4500);
        };
        window.goToHeroBanner = function(i) {
            clearInterval(timer);
            showSlide(i);
            timer = setInterval(nextSlide, 4500);
        };
    })();
</script>
@endsection
