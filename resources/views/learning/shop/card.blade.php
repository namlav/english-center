@php
    $cardShop=$shop??app(\App\Services\CourseShop::class);
    $publishedLessons=$course->lessons_count??$course->lessons()->where('is_published',true)->count();
    $canAdd=$course->is_for_sale && $publishedLessons>0;
    $alreadyOwned=auth()->check() && (auth()->user()->isAdmin() || $cardShop->owned(auth()->user(),$course->id));
    $alreadyInCart=$canAdd && auth()->check() && !auth()->user()->isAdmin() && \Illuminate\Support\Facades\Schema::hasTable('cart_items') && \Illuminate\Support\Facades\DB::table('cart_items')->where('user_id',auth()->id())->where('course_id',$course->id)->exists();
    $currentPrice = $cardShop->price($course);
    $discountPct = ($course->sale_price !== null && $course->price > 0 && $course->price > $currentPrice)
        ? round((($course->price - $currentPrice) / $course->price) * 100)
        : 0;
@endphp
<article class="course-tile shop-course-card" style="position: relative; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; display: flex; flex-direction: column; box-shadow: 0 2px 6px rgba(0,0,0,0.04); transition: transform 0.2s ease, box-shadow 0.2s ease;">
    <a class="course-cover shop-cover" href="{{ route('shop.show',$course->slug) }}" style="position: relative; display: block; overflow: hidden; aspect-ratio: 16/9; background: #0f172a;">
        @if($alreadyOwned)
            <span class="badge-owned" style="position: absolute; top: 12px; right: 12px; z-index: 5; background: #059669; color: #ffffff; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; box-shadow: 0 2px 6px rgba(0,0,0,0.25); display: inline-flex; align-items: center; gap: 5px;">
                <x-study-icon name="check"/> Đã mua
            </span>
        @endif
        @if($course->thumbnail_path)
            <img src="{{ asset($course->thumbnail_path) }}" alt="{{ $course->name }}" width="800" height="450" loading="lazy" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.25s ease;">
        @else
            <div style="width: 100%; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff; padding: 20px; text-align: center;">
                <span style="font-size: 11px; letter-spacing: 0.1em; opacity: 0.85; margin-bottom: 6px;">ENGLISH CENTER</span>
                <strong style="font-size: 17px; line-height: 1.3;">{{ $course->name }}</strong>
            </div>
        @endif
    </a>
    <div class="course-tile-body" style="padding: 20px; display: flex; flex-direction: column; flex: 1;">
        <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px; margin-bottom: 8px;">
            <span class="study4-card-tag">Phần mềm online</span>
            <span style="font-size: 12px; color: #64748b; display: inline-flex; align-items: center; gap: 4px;">
                <x-study-icon name="book"/> {{ $publishedLessons }} bài
            </span>
        </div>

        <h3 style="font-size: 17px; font-weight: 700; line-height: 1.45; margin: 0 0 8px 0; min-height: 48px; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden;">
            <a href="{{ route('shop.show',$course->slug) }}" style="color: #0f172a; text-decoration: none;">
                {{ $course->name }}
            </a>
        </h3>

        <div class="study4-rating-line">
            <span class="study4-rating-stars">★★★★★</span>
            <strong style="color: #0f172a; font-weight: 700;">4.9</strong>
            <span>(260)</span>
            <span>·</span>
            <span>12.450 học viên</span>
        </div>
        
        <p class="shop-summary" style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 14px 0; display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; overflow: hidden; min-height: 44px;">
            {{ $course->summary }}
        </p>
        
        <div class="source-learners" style="font-size: 13px; color: #64748b; margin-bottom: 14px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
            @if($alreadyOwned)
                <span style="color: #059669; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                    <x-study-icon name="check"/> Đã có trong tài khoản
                </span>
            @elseif($canAdd)
                <span>Truy cập {{ $course->access_days }} ngày</span>
                <span>·</span>
                <span style="color: #2563eb; font-weight: 600;">Luyện đề & Dictation</span>
            @else
                <span style="color: #94a3b8;">Nội dung sắp mở bán</span>
            @endif
        </div>

        <div class="study4-price-row">
            <span class="study4-price-current">
                {{ number_format($currentPrice,0,',','.') }}đ
            </span>
            @if($course->sale_price!==null)
                <span class="study4-price-old">{{ number_format($course->price,0,',','.') }}đ</span>
                @if($discountPct > 0)
                    <span class="study4-price-badge">-{{ $discountPct }}%</span>
                @endif
            @endif
        </div>

        <div class="course-purchase-actions" style="display: flex; align-items: center; gap: 10px; justify-content: space-between; margin-top: 10px;">
            <a class="course-details-link" href="{{ route('shop.show',$course->slug) }}" style="font-size: 14px; font-weight: 600; color: #2563eb; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                Chi tiết <x-study-icon name="arrow"/>
            </a>
            @if($alreadyOwned)
                <a class="card-cart-button already-owned" href="{{ route('learn.start',$course->slug) }}" style="background: #059669; border: 1px solid #059669; color: #ffffff; font-size: 14px; font-weight: 700; padding: 8px 16px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                    <x-study-icon name="check"/> Vào học ngay
                </a>
            @elseif($canAdd)
                @if($alreadyInCart)
                    <a class="card-cart-button in-cart" href="{{ route('cart.index') }}" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; font-size: 13px; font-weight: 700; padding: 8px 14px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                        <x-study-icon name="check"/> Đã trong giỏ hàng
                    </a>
                @else
                    @auth
                        <form method="POST" action="{{ route('cart.add',$course) }}" style="margin: 0;">
                            @csrf
                            <button class="card-cart-button" type="submit" aria-label="Thêm vào giỏ hàng: {{ $course->name }}" style="background: #2563eb; border: 1px solid #2563eb; color: #ffffff; font-size: 14px; font-weight: 700; padding: 8px 16px; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                                <x-study-icon name="cart"/> Thêm vào giỏ hàng
                            </button>
                        </form>
                    @else
                        <a class="card-cart-button" href="{{ route('cart.select',$course) }}" style="background: #2563eb; border: 1px solid #2563eb; color: #ffffff; font-size: 14px; font-weight: 700; padding: 8px 16px; border-radius: 6px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">
                            <x-study-icon name="cart"/> Thêm vào giỏ hàng
                        </a>
                    @endauth
                @endif
            @endif
        </div>
    </div>
</article>
