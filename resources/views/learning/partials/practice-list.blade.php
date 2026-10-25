<section id="practice" class="practice-section section-space">
    <div class="shell">
        <div class="section-heading"><div><span class="eyebrow">GÓC LUYỆN TẬP</span><h2>Bắt đầu từ một bài luyện.</h2><p>Một chủ đề rõ ràng. Một chút tập trung. Một bước tiến mới.</p></div><span class="collection-label"><span class="tiny-dot"></span> Nội dung đang có</span></div>
        <form class="catalog-search" action="{{ route('practice.index') }}" method="GET" role="search">
            <div class="search-input"><x-study-icon name="search" /><label class="visually-hidden" for="practice-search">Tìm bài luyện</label><input id="practice-search" name="q" maxlength="120" value="{{ $keyword }}" placeholder="Bạn muốn luyện gì hôm nay?" type="search"></div>
            <label class="visually-hidden" for="practice-category">Chọn danh mục</label><select name="category" id="practice-category"><option value="">Tất cả danh mục</option>@foreach($categories as $item)<option value="{{ $item->slug }}" @selected($category === $item->slug)>{{ $item->name }}</option>@endforeach</select>
            <button class="button button-search" type="submit">Tìm bài luyện <x-study-icon name="arrow" /></button>
        </form>
        <div class="catalog-toolbar"><div class="quick-searches"><span>Gợi ý:</span><a href="{{ route('practice.index', ['q' => 'Đại từ']) }}#practice">Đại từ</a><a href="{{ route('practice.index', ['q' => 'Cấu tạo từ']) }}#practice">Cấu tạo từ</a><a href="{{ route('practice.index', ['q' => 'Từ vựng']) }}#practice">Từ vựng</a></div><span class="result-count">{{ $papers->total() }} bài luyện{{ $keyword !== '' ? ' phù hợp' : '' }}</span></div>
        @if($keyword !== '' || $category !== '')<div class="active-search"><span>Kết quả {{ $keyword !== '' ? 'cho “'.$keyword.'”' : 'theo danh mục' }}</span><a href="{{ route('practice.index') }}">Xóa bộ lọc ×</a></div>@endif
        <div class="practice-grid">
        @forelse($papers as $paper)
            <article class="practice-card accent-{{ ($loop->index % 3) + 1 }}">
                <div class="card-top"><span class="card-subject-icon"><x-study-icon name="{{ $loop->index % 2 === 0 ? 'book' : 'file' }}" /></span><span class="card-category">{{ $paper->learningCategory->name }}</span></div>
                <div class="card-body"><span class="card-eyebrow">BÀI LUYỆN {{ str_pad((string) ($papers->firstItem() + $loop->index), 2, '0', STR_PAD_LEFT) }}</span><h3><a href="{{ route('practice.show', $paper->slug) }}">{{ str_contains($paper->title, ' — ') ? \Illuminate\Support\Str::after($paper->title, ' — ') : $paper->title }}</a></h3><div class="card-meta"><span><x-study-icon name="clock" /> {{ (int) ceil($paper->duration_seconds / 60) }} phút</span><span><x-study-icon name="file" /> {{ $paper->questions_count }} câu hỏi</span></div><div class="card-bottom"><span>{{ $paper->sections_count }} phần luyện tập</span><a href="{{ route('practice.show', $paper->slug) }}" aria-label="Mở {{ $paper->title }}">Mở bài luyện <x-study-icon name="arrow" /></a></div></div>
            </article>
        @empty
            <div class="empty-state"><span class="empty-icon"><x-study-icon name="search" /></span><h3>{{ $keyword !== '' || $category !== '' ? 'Chưa tìm thấy bài luyện phù hợp' : 'Một hành trình mới đang bắt đầu' }}</h3><p>{{ $keyword !== '' || $category !== '' ? 'Thử một từ khóa ngắn hơn hoặc xem tất cả các chủ đề hiện có.' : 'Các bài luyện sẽ xuất hiện tại đây khi được đăng tải.' }}</p>@if($keyword !== '' || $category !== '')<a class="button button-outline" href="{{ route('practice.index') }}">Xem tất cả bài luyện</a>@endif</div>
        @endforelse
        </div>
        @if($papers->hasPages())<nav class="catalog-pagination" aria-label="Phân trang bài luyện">@if($papers->onFirstPage())<span>← Trang trước</span>@else<a href="{{ $papers->previousPageUrl() }}">← Trang trước</a>@endif<span>Trang {{ $papers->currentPage() }} / {{ $papers->lastPage() }}</span>@if($papers->hasMorePages())<a href="{{ $papers->nextPageUrl() }}">Trang sau →</a>@else<span>Trang sau →</span>@endif</nav>@endif
    </div>
</section>
