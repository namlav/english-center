@extends('layouts.learning')
@section('title','Khóa học của tôi — English Center')
@section('content')
<div class="shell account-layout">
@include('learning.account.nav')
<section class="account-content">
<span class="eyebrow">GÓC HỌC TẬP CÁ NHÂN</span>
<h1>Khóa học của tôi</h1>
<p>Các khóa học đã được cấp quyền trên English Center.</p>

@if(isset($pendingOrders) && $pendingOrders->isNotEmpty())
    <div class="pending-orders-notice" style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:16px 20px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.04);">
        <h3 style="margin:0 0 8px 0;color:#92400e;font-size:16px;font-weight:700;display:flex;align-items:center;gap:8px;">
            <span>⏳</span> Đơn hàng đang chờ kích hoạt ({{ $pendingOrders->count() }})
        </h3>
        <p style="margin:0 0 14px 0;font-size:13px;color:#78350f;">
            Sau khi chuyển khoản ngân hàng, hệ thống sẽ đối soát và mở khóa học. Nếu bạn là Quản trị viên, bạn có thể bấm kích hoạt ngay bên dưới.
        </p>
        @foreach($pendingOrders as $pOrder)
            <div style="background:#ffffff;border:1px solid #fef3c7;border-radius:8px;padding:12px 16px;margin-bottom:10px;display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;">
                <div>
                    <strong style="color:#1e293b;font-size:14px;">Mã đơn: #{{ substr($pOrder->number, 0, 8) }}...</strong> — 
                    <span style="color:#b45309;font-weight:700;">{{ number_format($pOrder->total, 0, ',', '.') }}đ</span>
                    <div style="margin-top:4px;font-size:13px;color:#475569;">
                        Khóa học: <strong>{{ $pOrder->items->pluck('course_name')->implode(', ') }}</strong>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <a href="{{ route('orders.show', $pOrder->number) }}" class="button button-outline button-small" style="font-size:13px;padding:6px 14px;">
                        Xem mã QR & Đơn
                    </a>
                    @if(auth()->user()->isAdmin())
                        <form method="POST" action="{{ route('shop_admin.orders.confirm', $pOrder->number) }}" style="display:inline;margin:0;">
                            @csrf
                            <input type="hidden" name="verified" value="1">
                            <button type="submit" class="button button-small" style="background:#059669;border-color:#059669;color:#ffffff;font-size:13px;font-weight:700;padding:6px 14px;">
                                Admin kích hoạt ngay ✓
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

@forelse($enrollments as $enrollment)
    @php($course=$enrollment->course)
    @php($lessons=$course->sections->flatMap->lessons)
    @php($done=$lessons->filter(fn($lesson)=>$completedIds->has($lesson->id))->count())
    <article class="enrolled-course">
        <div class="enrolled-course-heading">
            <span class="card-subject-icon"><x-study-icon name="book"/></span>
            <div>
                <h2>{{ $course->name }}</h2>
                <span class="status-pill">{{ $enrollment->isCurrent() ? 'Đang có quyền học' : ($enrollment->status==='completed' ? 'Đã hoàn thành' : 'Quyền học không còn hiệu lực') }}</span>
            </div>
        </div>
        <p>{{ $course->summary }}</p>
        <p class="enrollment-date">Bắt đầu: {{ $enrollment->started_at->format('d/m/Y') }} · {{ $enrollment->expires_at ? 'Hết hạn: '.$enrollment->expires_at->format('d/m/Y') : 'Không giới hạn thời gian' }}</p>
        <label>Tiến độ: {{ $done }}/{{ $lessons->count() }} bài học</label>
        <progress max="{{ max(1,$lessons->count()) }}" value="{{ $done }}">{{ $done }}/{{ $lessons->count() }}</progress>
        @if($enrollment->isCurrent() && $course->slug)
            <a class="button" href="{{ route('learn.start',$course->slug) }}" style="margin-top:10px;">Vào học ngay →</a>
        @endif
        <details class="enrolled-syllabus" style="margin-top:14px;">
            <summary>Xem chương trình học</summary>
            @foreach($course->sections as $section)
                <h3>{{ $section->title }}</h3>
                <ul>
                    @foreach($section->lessons as $lesson)
                        <li>{{ $completedIds->has($lesson->id) ? '✓ ' : '' }}{{ $lesson->title }}</li>
                    @endforeach
                </ul>
            @endforeach
            @if($lessons->isEmpty())
                <p>Nội dung khóa học đang được cập nhật.</p>
            @endif
        </details>
    </article>
@empty
    <div class="empty-state">
        <x-study-icon name="book"/>
        <h2>Bạn chưa có khóa học nào đã kích hoạt</h2>
        <p>Các khóa học sau khi kích hoạt thành công sẽ xuất hiện tại đây. Bạn cũng có thể bắt đầu với các bài luyện tập miễn phí.</p>
        <div class="empty-actions">
            <a class="button" href="{{ route('catalog.index') }}">Khám phá chương trình học</a>
            <a class="button button-outline" href="{{ route('practice.index') }}">Luyện tập miễn phí</a>
        </div>
    </div>
@endforelse

@if($enrollments->hasPages())
    <nav class="catalog-pagination">
        @if($enrollments->previousPageUrl())
            <a href="{{ $enrollments->previousPageUrl() }}">← Trang trước</a>
        @endif
        <span>Trang {{ $enrollments->currentPage() }}/{{ $enrollments->lastPage() }}</span>
        @if($enrollments->nextPageUrl())
            <a href="{{ $enrollments->nextPageUrl() }}">Trang sau →</a>
        @endif
    </nav>
@endif
</section>
</div>
@endsection
