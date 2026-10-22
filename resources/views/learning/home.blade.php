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
        <div class="hero-art" aria-label="Minh họa sách tiếng Anh và góc học tập">
            <div class="art-orbit orbit-one"></div><div class="art-orbit orbit-two"></div><span class="art-spark spark-one">✦</span><span class="art-spark spark-two">✦</span>
            <div class="art-note art-note-top"><span class="note-icon"><x-study-icon name="spark" /></span><div><strong>Một chút mỗi ngày</strong><span>Tạo nên thay đổi lớn</span></div></div>
            <svg class="study-illustration" viewBox="0 0 520 420" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <ellipse cx="265" cy="365" rx="192" ry="24" fill="#CCDCD3" fill-opacity=".65"/>
                <path d="M84 293 293 250 438 294 235 347Z" fill="#155E50"/><path d="m84 293 151 54v23L84 316Z" fill="#0F4D42"/><path d="m235 347 203-53v23l-203 53Z" fill="#F8F2DF"/><path d="m247 355 174-45m-174 52 174-45" stroke="#D9D1BA" stroke-width="2"/>
                <path d="m112 262 195-33 110 38-194 43Z" fill="#EAB94E"/><path d="m112 262 111 48v19l-111-45Z" fill="#D4A33C"/><path d="m223 310 194-43v20l-194 42Z" fill="#FFF9E9"/>
                <path d="m107 139 145 34 163-38-8 124-156 38-152-38Z" fill="#106F5B"/>
                <path d="m111 128 140 35 156-37-6 121-149 38-144-36Z" fill="#FFFDF4"/>
                <path d="M251 163v122M111 128l140 35 156-37" stroke="#DED9C8" stroke-width="2"/>
                <path d="M111 128c51-14 92-5 140 16v19l-140-35Z" fill="#F1EDD9"/><path d="M251 144c60-27 106-32 156-18l-156 37v-19Z" fill="#FFFDF4"/>
                <path d="m132 165 92 23m-92-8 92 23m-92-8 92 23m-92-8 63 16" stroke="#D0D5C5" stroke-width="4" stroke-linecap="round"/>
                <path d="m280 180 94-23m-94 40 94-23m-94 40 94-23m-94 40 65-16" stroke="#D0D5C5" stroke-width="4" stroke-linecap="round"/>
                <path d="m329 145 22-5-1 69-11-7-10 12v-69Z" fill="#E7B348"/>
                <path d="M76 308c-28-18-40-45-25-65 25 4 43 31 25 65Z" fill="#77A989"/><path d="M76 309c-6-30 0-55 22-62 16 25 6 51-22 62Z" fill="#397C61"/><path d="M76 292v41" stroke="#155E50" stroke-width="4"/><path d="M53 316h49l-7 43H61Z" fill="#F8F2E2"/><path d="M53 316h49v9H53Z" fill="#E4DDCB"/>
                <path d="m411 186 20 5-29 117-13-3Z" fill="#E3AF44"/><path d="m389 305 13 3-10 17Z" fill="#F2D7AA"/><path d="m392 325-3-8 6 2Z" fill="#183F36"/>
                <path d="M169 95c2-29 22-48 46-48s44 19 46 48" stroke="#174E43" stroke-width="10"/><rect x="159" y="85" width="19" height="37" rx="9" fill="#174E43"/><rect x="251" y="85" width="19" height="37" rx="9" fill="#174E43"/>
            </svg>
            <div class="art-note art-note-bottom"><span class="note-icon note-yellow"><x-study-icon name="book" /></span><div><strong>Thêm kiến thức mới</strong><span>Ngữ pháp · Từ vựng · Bài luyện</span></div><span class="note-check"><x-study-icon name="check" /></span></div>
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
