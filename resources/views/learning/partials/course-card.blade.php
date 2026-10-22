<article class="course-tile">
    <a class="course-cover" href="{{ route('course_catalog.show', $course['source_key']) }}"><img src="{{ asset($course['images'][0]['local_path']) }}" alt="{{ $course['title'] }}" loading="lazy" width="800" height="300"></a>
    <div class="course-tile-body"><h3><a href="{{ route('course_catalog.show', $course['source_key']) }}">{{ $course['title'] }}</a></h3>
        <div class="course-rating-row">@if($course['metadata']['source_statistics']['rating'] !== null)<span class="rating-stars" aria-label="{{ $course['metadata']['source_statistics']['rating'] }} trên 5 sao">★★★★★</span><b>{{ number_format($course['metadata']['source_statistics']['rating'], 1) }}</b>@endif<span>({{ number_format($course['metadata']['source_statistics']['review_count'] ?? 0) }} đánh giá)</span></div>
        <p class="source-learners">{{ number_format($course['metadata']['source_statistics']['learner_count'] ?? 0) }} học viên tại STUDY4</p><span class="online-tag">Khóa học online · STUDY4</span>
        <div class="course-tile-price"><strong>{{ number_format($course['metadata']['price'], 0, ',', '.') }}đ</strong>@if($course['metadata']['list_price'] > $course['metadata']['price'])<s>{{ number_format($course['metadata']['list_price'], 0, ',', '.') }}đ</s><span class="discount-label">-{{ (int) floor(100 * (1 - $course['metadata']['price'] / $course['metadata']['list_price'])) }}%</span>@endif</div><a class="course-details-link" href="{{ route('course_catalog.show', $course['source_key']) }}">Thông tin khóa học <x-study-icon name="arrow" /></a>
    </div>
</article>
