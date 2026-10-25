@extends('layouts.learning')
@section('title','Chương trình học — English Center')
@section('content')

{{-- Real Photo Hero Banner --}}
<section class="shell" style="padding-top: 24px;">
    <div class="catalog-hero-showcase" style="position: relative; border-radius: 16px; overflow: hidden; min-height: 320px; display: flex; align-items: center; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.06); background: #0f172a; margin-bottom: 32px;">
        <img src="{{ asset('images/banners/banner-home-hero.jpg') }}" alt="English Center — Học tiếng Anh trực tuyến" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; opacity: 0.38; filter: brightness(0.85);">
        <div style="position: relative; z-index: 2; padding: 44px 48px; max-width: 780px;">
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(37, 99, 235, 0.9); color: #ffffff; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; letter-spacing: 0.04em; text-transform: uppercase; margin-bottom: 16px;">
                <span>✦</span> LỘ TRÌNH TIẾNG ANH TOÀN DIỆN · CHUẨN STUDY4
            </div>
            <h1 style="font-size: 34px; font-weight: 800; color: #ffffff; line-height: 1.3; margin: 0 0 14px 0; letter-spacing: -0.02em;">
                Học thông minh hơn, vững vàng bứt phá mục tiêu
            </h1>
            <p style="font-size: 16px; line-height: 1.65; color: #f1f5f9; margin: 0 0 24px 0;">
                Hơn 6.380 bài học chuyên sâu với phương pháp nghe chép chính tả (Dictation), lộ trình IELTS, TOEIC và tiếng Anh thực hành từ cơ bản đến nâng cao. Học thử miễn phí ngay hôm nay!
            </p>
            <div style="display: flex; gap: 14px; flex-wrap: wrap; align-items: center;">
                <a href="#catalog-courses" class="button" style="background: #2563eb; border-color: #2563eb; color: #ffffff; font-size: 15px; font-weight: 700; padding: 12px 26px; border-radius: 8px; text-decoration: none;">
                    Khám phá lộ trình học ↓
                </a>
                <a href="{{ route('practice.index') }}" class="button button-outline" style="background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.45); color: #ffffff; font-size: 15px; font-weight: 600; padding: 12px 24px; border-radius: 8px; text-decoration: none;">
                    Thi thử online miễn phí ↗
                </a>
            </div>
        </div>
    </div>
</section>

{{-- 3-Banner Real Photo Highlights --}}
<section class="shell" style="margin-bottom: 36px;">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        {{-- Banner IELTS --}}
        <div style="position: relative; border-radius: 12px; overflow: hidden; min-height: 170px; display: flex; align-items: flex-end; padding: 22px 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07); background: #1e293b;">
            <img src="{{ asset('images/banners/banner-ielts-real.jpg') }}" alt="Luyện thi IELTS" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.42;">
            <div style="position: relative; z-index: 2; color: #ffffff;">
                <span style="font-size: 12px; font-weight: 700; color: #60a5fa; text-transform: uppercase; letter-spacing: 0.05em;">Lộ trình chuyên sâu</span>
                <h3 style="font-size: 20px; font-weight: 700; margin: 4px 0 6px 0; color: #ffffff;">Luyện thi IELTS 4 Kỹ năng</h3>
                <p style="font-size: 14px; margin: 0; color: #e2e8f0; line-height: 1.5;">Chiến lược làm bài, Dictation & chữa đề Cam chi tiết.</p>
            </div>
        </div>

        {{-- Banner TOEIC --}}
        <div style="position: relative; border-radius: 12px; overflow: hidden; min-height: 170px; display: flex; align-items: flex-end; padding: 22px 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07); background: #1e293b;">
            <img src="{{ asset('images/banners/banner-toeic-real.jpg') }}" alt="Luyện thi TOEIC" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.42;">
            <div style="position: relative; z-index: 2; color: #ffffff;">
                <span style="font-size: 12px; font-weight: 700; color: #34d399; text-transform: uppercase; letter-spacing: 0.05em;">Bứt phá điểm số</span>
                <h3 style="font-size: 20px; font-weight: 700; margin: 4px 0 6px 0; color: #ffffff;">Complete TOEIC & Speaking/Writing</h3>
                <p style="font-size: 14px; margin: 0; color: #e2e8f0; line-height: 1.5;">Nắm vững bẫy đề thi, từ vựng và ngữ pháp trọng tâm.</p>
            </div>
        </div>

        {{-- Banner Practical English --}}
        <div style="position: relative; border-radius: 12px; overflow: hidden; min-height: 170px; display: flex; align-items: flex-end; padding: 22px 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07); background: #1e293b;">
            <img src="{{ asset('images/banners/banner-speaking-real.jpg') }}" alt="Giao tiếp & Thực hành" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.42;">
            <div style="position: relative; z-index: 2; color: #ffffff;">
                <span style="font-size: 12px; font-weight: 700; color: #fbbf24; text-transform: uppercase; letter-spacing: 0.05em;">Thực hành thực tế</span>
                <h3 style="font-size: 20px; font-weight: 700; margin: 4px 0 6px 0; color: #ffffff;">Tiếng Anh giao tiếp & TED Talks</h3>
                <p style="font-size: 14px; margin: 0; color: #e2e8f0; line-height: 1.5;">Luyện phát âm chuẩn, 3600 từ vựng và đàm thoại tự tin.</p>
            </div>
        </div>
    </div>
</section>

{{-- Thanh tìm kiếm & Bộ lọc danh mục --}}
<section class="shell page-heading" id="catalog-courses" style="padding-top: 10px;">
    <div style="display: flex; flex-direction: column; gap: 10px; margin-bottom: 24px;">
        <span class="eyebrow" style="font-size: 14px; font-weight: 700; color: #2563eb; letter-spacing: 0.05em;">DANH MỤC KHÓA HỌC</span>
        <h2 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 0;">Lựa chọn chương trình phù hợp</h2>
        <p style="font-size: 16px; color: #475569; margin: 0;">Tìm kiếm khóa học theo kỹ năng hoặc chọn danh mục chuyên đề bên dưới.</p>
    </div>

    <form class="course-finder" action="{{ route('catalog.index') }}" method="GET" style="margin-bottom: 24px;">
        <x-study-icon name="search"/>
        <label class="visually-hidden" for="shop-search">Tìm khóa học</label>
        <input id="shop-search" name="q" type="search" maxlength="120" value="{{ $keyword }}" placeholder="Tìm khóa học IELTS, TOEIC, Ngữ pháp, Từ vựng..." style="font-size: 15px;">
        <input type="hidden" name="category" value="{{ $category }}">
        <button class="button" style="font-size: 15px; font-weight: 700; padding: 11px 24px;">Tìm kiếm</button>
    </form>

    <nav class="course-category-tabs" aria-label="Danh mục chương trình" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 32px;">
        <a href="{{ route('catalog.index',['q'=>$keyword]) }}" @if(!$category)aria-current="page"@endif style="font-size: 15px; font-weight: 600; padding: 10px 20px; border-radius: 8px;">
            Tất cả
        </a>
        @foreach($categories as $item)
            <a href="{{ route('catalog.index',['category'=>$item->slug,'q'=>$keyword]) }}" @if($categoryName===$item->name)aria-current="page"@endif style="font-size: 15px; font-weight: 600; padding: 10px 20px; border-radius: 8px;">
                {{ $item->name }}
            </a>
        @endforeach
    </nav>
</section>

{{-- Danh sách các nhóm khóa học --}}
<section class="shell home-feature course-catalog-sections">
@forelse($courses->getCollection()->groupBy(fn($c)=>$c->learningCategory?->name??'Chương trình khác')->sortBy(fn($group,$label)=>array_search($label,['Combo đặc biệt','TOEIC','IELTS Academic','IELTS General','Tiếng Anh cơ bản','Tiếng Anh nền tảng'],true)===false?99:array_search($label,['Combo đặc biệt','TOEIC','IELTS Academic','IELTS General','Tiếng Anh cơ bản','Tiếng Anh nền tảng'],true)) as $label=>$group)
    <section class="course-catalog-group" style="margin-bottom: 52px;">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e2e8f0;">
            <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0;">{{ $label }}</h2>
            <span style="font-size: 14px; color: #64748b; font-weight: 600;">{{ $group->count() }} khóa học</span>
        </div>
        <div class="course-grid">
            @foreach($group as $course)
                @include('learning.shop.card')
            @endforeach
        </div>
    </section>
@empty
    <div class="empty-state" style="padding: 48px 24px; text-align: center;">
        <x-study-icon name="search"/>
        <h2 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 16px 0 8px 0;">Chưa tìm thấy khóa học phù hợp</h2>
        <p style="font-size: 15px; color: #64748b; margin-bottom: 20px;">Hãy thử tìm kiếm với từ khóa khác hoặc bấm vào "Tất cả" để xem toàn bộ danh mục.</p>
        <a href="{{ route('catalog.index') }}" class="button" style="font-size: 15px; font-weight: 600; padding: 10px 22px;">Xem tất cả khóa học</a>
    </div>
@endforelse

@if($courses->hasPages())
    <nav class="catalog-pagination" style="margin-top: 36px;">
        @if($courses->previousPageUrl())
            <a href="{{ $courses->previousPageUrl() }}" style="font-size: 14px; font-weight: 600;">← Trang trước</a>
        @endif
        <span style="font-size: 14px; font-weight: 600;">Trang {{ $courses->currentPage() }}/{{ $courses->lastPage() }}</span>
        @if($courses->nextPageUrl())
            <a href="{{ $courses->nextPageUrl() }}" style="font-size: 14px; font-weight: 600;">Trang sau →</a>
        @endif
    </nav>
@endif
</section>
@endsection
