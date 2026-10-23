@php
    $cardShop=$shop??app(\App\Services\CourseShop::class);
    $publishedLessons=$course->lessons_count??$course->lessons()->where('is_published',true)->count();
    $canAdd=$course->is_for_sale && $publishedLessons>0;
    $alreadyOwned=auth()->check() && $cardShop->owned(auth()->user(),$course->id);
    $alreadyInCart=$canAdd && auth()->check() && \Illuminate\Support\Facades\Schema::hasTable('cart_items') && \Illuminate\Support\Facades\DB::table('cart_items')->where('user_id',auth()->id())->where('course_id',$course->id)->exists();
@endphp
<article class="course-tile shop-course-card" style="position: relative;">
    <a class="course-cover shop-cover" href="{{ route('shop.show',$course->slug) }}" style="position: relative; display: block;">
        @if($alreadyOwned)
            <span class="badge-owned" style="position: absolute; top: 10px; right: 10px; z-index: 5; background: #059669; color: #ffffff; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; box-shadow: 0 2px 6px rgba(0,0,0,0.25); display: inline-flex; align-items: center; gap: 4px;">
                <x-study-icon name="check"/> Đã mua
            </span>
        @endif
        @if($course->thumbnail_path)
            <img src="{{ asset($course->thumbnail_path) }}" alt="{{ $course->name }}" width="800" height="300" loading="lazy">
        @else
            <span class="shop-cover-brand">ENGLISH CENTER</span>
            <strong>{{ $course->name }}</strong>
            <span>HỌC · THỰC HÀNH · TIẾN BỘ</span>
        @endif
    </a>
    <div class="course-tile-body">
        <h3><a href="{{ route('shop.show',$course->slug) }}">{{ $course->name }}</a></h3>
        <p class="shop-summary">{{ $course->summary }}</p>
        <p class="source-learners">
            @if($alreadyOwned)
                <span style="color: #059669; font-weight: 700;"><x-study-icon name="check"/> Đã mua</span> · {{ $publishedLessons }} bài học · {{ $course->access_days }} ngày truy cập
            @elseif($canAdd)
                {{ $publishedLessons }} bài học · {{ $course->access_days }} ngày truy cập
            @else
                Chương trình sắp mở
            @endif
        </p>
        <div class="course-tile-price">
            <strong>{{ number_format($cardShop->price($course),0,',','.') }}đ</strong>
            @if($course->sale_price!==null)<s>{{ number_format($course->price,0,',','.') }}đ</s>@endif
        </div>
        <div class="course-purchase-actions">
            <a class="course-details-link" href="{{ route('shop.show',$course->slug) }}">Thông tin khóa học <x-study-icon name="arrow"/></a>
            @if($alreadyOwned)
                <a class="card-cart-button already-owned" href="{{ route('learn.start',$course->slug) }}" style="background: #059669; border-color: #059669; color: #ffffff; font-weight: 700;">
                    <x-study-icon name="check"/> Đã mua
                </a>
            @elseif($canAdd)
                @if($alreadyInCart)
                    <a class="card-cart-button in-cart" href="{{ route('cart.index') }}"><x-study-icon name="check"/>Đã trong giỏ hàng</a>
                @else
                    @auth
                        <form method="POST" action="{{ route('cart.add',$course) }}">
                            @csrf
                            <button class="card-cart-button" type="submit" aria-label="Thêm vào giỏ hàng: {{ $course->name }}"><x-study-icon name="cart"/>Thêm vào giỏ hàng</button>
                        </form>
                    @else
                        <a class="card-cart-button" href="{{ route('cart.select',$course) }}"><x-study-icon name="cart"/>Thêm vào giỏ hàng</a>
                    @endauth
                @endif
            @endif
        </div>
    </div>
</article>
