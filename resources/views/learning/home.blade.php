@extends('layouts.learning')

@section('content')
<section class="hero">
    <div class="shell hero-grid">
        <div class="hero-copy">
            <span class="eyebrow hero-eyebrow"><span class="tiny-dot"></span> MỖI NGÀY MỘT BƯỚC TIẾN</span>
            <h1>Học tiếng Anh,<br><span>theo cách của bạn.</span></h1>
            <p class="hero-description">Không cần bắt đầu thật lớn. Tìm một bài luyện vừa sức, học thêm một điều mới và tiến gần hơn đến mục tiêu mỗi ngày.</p>
            <div class="hero-actions"><a class="button" href="#practice">Khám phá bài luyện <x-study-icon name="arrow" /></a><a class="text-link" href="#how-it-works">Bắt đầu như thế nào? <span>↗</span></a></div>
            <div class="hero-assurances"><span><x-study-icon name="check" /> Học theo nhịp của bạn</span><span><x-study-icon name="check" /> Tự kiểm tra với lời giải</span></div>
        </div>
        <div class="hero-art" aria-label="Hình ảnh học viên English Center" style="position: relative; display: flex; justify-content: center; align-items: center;">
            <div style="position: relative; width: 100%; max-width: 480px; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 35px -10px rgba(0,0,0,0.18), 0 10px 15px -5px rgba(0,0,0,0.1); border: 4px solid #ffffff;">
                <img src="{{ asset('images/banners/banner-home-hero.jpg') }}" alt="Học viên tiếng Anh tại English Center" style="width: 100%; height: 360px; object-fit: cover; display: block;">
                <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 60%, rgba(15,23,42,0.6) 100%);"></div>
            </div>
            
            <div class="art-note art-note-top" style="position: absolute; top: -10px; right: -15px; z-index: 5; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 10px;">
                <span class="note-icon" style="background: #eff6ff; color: #2563eb; width: 34px; height: 34px; border-radius: 8px; display: grid; place-items: center;"><x-study-icon name="spark" /></span>
                <div>
                    <strong style="font-size: 14px; color: #0f172a; display: block;">Học mỗi ngày</strong>
                    <span style="font-size: 12px; color: #64748b;">Tiến bộ vững vàng</span>
                </div>
            </div>

            <div class="art-note art-note-bottom" style="position: absolute; bottom: -15px; left: -15px; z-index: 5; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.08); display: flex; align-items: center; gap: 10px;">
                <span class="note-icon note-yellow" style="background: #ecfdf5; color: #059669; width: 34px; height: 34px; border-radius: 8px; display: grid; place-items: center;"><x-study-icon name="check" /></span>
                <div>
                    <strong style="font-size: 14px; color: #0f172a; display: block;">Chuẩn Study4</strong>
                    <span style="font-size: 12px; color: #64748b;">Chép chính tả & Luyện đề</span>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="quick-stats shell" aria-label="Nội dung học hiện có">
    <div><span class="stat-icon"><x-study-icon name="layers" /></span><p><strong>{{ number_format($stats['papers']) }} bộ bài luyện</strong><span>Khám phá theo chủ đề</span></p></div>
    <div><span class="stat-icon"><x-study-icon name="file" /></span><p><strong>{{ number_format($stats['questions']) }} câu hỏi</strong><span>Từng bước củng cố kiến thức</span></p></div>
    <div><span class="stat-icon"><x-study-icon name="leaf" /></span><p><strong>Học theo nhịp riêng</strong><span>Bắt đầu bất cứ khi nào bạn muốn</span></p></div>
</section>
<section id="practice" class="practice-section section-space">
    <div class="shell">
        <div class="section-heading"><div><span class="eyebrow">GÓC LUYỆN TẬP</span><h2>Bắt đầu từ một bài luyện.</h2><p>Một chủ đề rõ ràng. Một chút tập trung. Một bước tiến mới.</p></div><span class="collection-label"><span class="tiny-dot"></span> Nội dung đang có</span></div>
        <form class="catalog-search" action="{{ route('home') }}#practice" method="GET" role="search">
            <div class="search-input"><x-study-icon name="search" /><label class="visually-hidden" for="practice-search">Tìm bài luyện</label><input id="practice-search" name="q" maxlength="120" value="{{ $keyword }}" placeholder="Bạn muốn luyện gì hôm nay?" type="search"></div>
            <label class="visually-hidden" for="practice-category">Chọn danh mục</label><select name="category" id="practice-category"><option value="">Tất cả danh mục</option>@foreach($categories as $item)<option value="{{ $item->slug }}" @selected($category === $item->slug)>{{ $item->name }}</option>@endforeach</select>
            <button class="button button-search" type="submit">Tìm bài luyện <x-study-icon name="arrow" /></button>
        </form>
        <div class="catalog-toolbar"><div class="quick-searches"><span>Gợi ý:</span><a href="{{ route('home', ['q' => 'Đại từ']) }}#practice">Đại từ</a><a href="{{ route('home', ['q' => 'Cấu tạo từ']) }}#practice">Cấu tạo từ</a><a href="{{ route('home', ['q' => 'Từ vựng']) }}#practice">Từ vựng</a></div><span class="result-count">{{ $papers->total() }} bài luyện{{ $keyword !== '' ? ' phù hợp' : '' }}</span></div>
        @if($keyword !== '' || $category !== '')<div class="active-search"><span>Kết quả {{ $keyword !== '' ? 'cho “'.$keyword.'”' : 'theo danh mục' }}</span><a href="{{ route('home') }}#practice">Xóa bộ lọc ×</a></div>@endif
        <div class="practice-grid">
        @forelse($papers as $paper)
            <article class="practice-card accent-{{ ($loop->index % 3) + 1 }}">
                <div class="card-top"><span class="card-subject-icon"><x-study-icon name="{{ $loop->index % 2 === 0 ? 'book' : 'file' }}" /></span><span class="card-category">{{ $paper->learningCategory->name }}</span></div>
                <div class="card-body"><span class="card-eyebrow">BÀI LUYỆN {{ str_pad((string) ($papers->firstItem() + $loop->index), 2, '0', STR_PAD_LEFT) }}</span><h3><a href="{{ route('practice.show', $paper->slug) }}">{{ str_contains($paper->title, ' — ') ? \Illuminate\Support\Str::after($paper->title, ' — ') : $paper->title }}</a></h3><div class="card-meta"><span><x-study-icon name="clock" /> {{ (int) ceil($paper->duration_seconds / 60) }} phút</span><span><x-study-icon name="file" /> {{ $paper->questions_count }} câu hỏi</span></div><div class="card-bottom"><span>{{ $paper->sections_count }} phần luyện tập</span><a href="{{ route('practice.show', $paper->slug) }}" aria-label="Mở {{ $paper->title }}">Mở bài luyện <x-study-icon name="arrow" /></a></div></div>
            </article>
        @empty
            <div class="empty-state"><span class="empty-icon"><x-study-icon name="search" /></span><h3>{{ $keyword !== '' || $category !== '' ? 'Chưa tìm thấy bài luyện phù hợp' : 'Một hành trình mới đang bắt đầu' }}</h3><p>{{ $keyword !== '' || $category !== '' ? 'Thử một từ khóa ngắn hơn hoặc xem tất cả các chủ đề hiện có.' : 'Các bài luyện sẽ xuất hiện tại đây khi được đăng tải.' }}</p>@if($keyword !== '' || $category !== '')<a class="button button-outline" href="{{ route('home') }}#practice">Xem tất cả bài luyện</a>@endif</div>
        @endforelse
        </div>
        @if($papers->hasPages())<nav class="catalog-pagination" aria-label="Phân trang bài luyện">@if($papers->onFirstPage())<span>← Trang trước</span>@else<a href="{{ $papers->previousPageUrl() }}">← Trang trước</a>@endif<span>Trang {{ $papers->currentPage() }} / {{ $papers->lastPage() }}</span>@if($papers->hasMorePages())<a href="{{ $papers->nextPageUrl() }}">Trang sau →</a>@else<span>Trang sau →</span>@endif</nav>@endif
    </div>
</section>
<section id="learning-paths" class="paths-section section-space">
    <div class="shell"><div class="section-heading"><div><span class="eyebrow">MỖI NGƯỜI MỘT ĐIỂM BẮT ĐẦU</span><h2>Hôm nay, bạn muốn học gì?</h2></div><p>Chọn điều bạn muốn làm tốt hơn.<br>Chúng mình cùng bắt đầu từ đó.</p></div>
        <div class="path-grid">
            <a class="path-card" href="{{ route('home', ['q' => 'Đại từ']) }}#practice"><span class="path-number">01 / NGỮ PHÁP</span><h3>Vững những<br>điều căn bản.</h3><p>Bắt đầu với đại từ, cấu trúc câu và cách dùng từ trong câu.</p><span class="path-cta">Khám phá bài ngữ pháp <x-study-icon name="arrow" /></span><span class="path-decoration" aria-hidden="true">Aa</span></a>
            <a class="path-card path-yellow" href="{{ route('home', ['q' => 'Từ vựng']) }}#practice"><span class="path-number">02 / TỪ VỰNG</span><h3>Mỗi từ mới,<br>một góc nhìn.</h3><p>Hiểu ý nghĩa và nhận diện từ qua những định nghĩa gần gũi.</p><span class="path-cta">Khám phá bài từ vựng <x-study-icon name="arrow" /></span><span class="path-decoration" aria-hidden="true">abc</span></a>
            <a class="path-card path-lilac" href="{{ route('home') }}#practice"><span class="path-number">03 / THÓI QUEN</span><h3>Học một chút.<br>Đều đặn hơn.</h3><p>Chọn một bài ngắn và dành một khoảng thời gian cho tiếng Anh.</p><span class="path-cta">Tìm bài luyện hôm nay <x-study-icon name="arrow" /></span><span class="path-decoration" aria-hidden="true">↗</span></a>
        </div>
    </div>
</section>
<section id="how-it-works" class="how-section section-space"><div class="shell how-grid"><div><span class="eyebrow">ĐƠN GIẢN ĐỂ BẮT ĐẦU</span><h2>Biến thời gian rảnh<br>thành một bước tiến.</h2><p>Bạn không cần một kế hoạch hoàn hảo.<br>Chỉ cần bắt đầu và quay lại vào ngày mai.</p><a class="text-link" href="#practice">Tìm bài luyện của bạn <x-study-icon name="arrow" /></a></div><ol class="learning-steps"><li><span>01</span><div><h3>Chọn một chủ đề</h3><p>Bắt đầu với phần kiến thức bạn muốn củng cố.</p></div></li><li><span>02</span><div><h3>Luyện tập và tự kiểm tra</h3><p>Trả lời câu hỏi, đối chiếu đáp án và đọc lời giải.</p></div></li><li><span>03</span><div><h3>Giữ một nhịp học đều đặn</h3><p>Ôn lại những câu chưa đúng và thử thêm một bài mới.</p></div></li></ol></div></section>
<section class="shell bottom-cta"><div><span class="eyebrow">SẴN SÀNG CHO HÔM NAY?</span><h2>Một bài luyện nhỏ.<br>Một khởi đầu thật tốt.</h2></div><a class="button button-light" href="#practice">Bắt đầu luyện tập <x-study-icon name="arrow" /></a><span class="cta-decoration" aria-hidden="true">✦</span></section>
@endsection
