@extends('layouts.learning')
@section('title','Khóa học của tôi — English Center')
@section('content')
<div class="shell account-layout">
@include('learning.account.nav')
<section class="account-content">
<span class="eyebrow">GÓC HỌC TẬP CÁ NHÂN</span>
<h1>Khóa học của tôi</h1>
<p>Các khóa học đã được cấp quyền trên English Center.</p>

@if(auth()->check() && auth()->user()->isAdmin())
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 18px 22px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap;">
        <div>
            <strong style="color: #1e40af; font-size: 15px; display: block; margin-bottom: 4px;">👑 Quyền Quản trị viên (Toàn quyền học & quản lý)</strong>
            <span style="font-size: 13px; color: #3b82f6;">Bạn có toàn quyền vào học, kiểm tra đề thi và quản lý tất cả các khóa học trên hệ thống.</span>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('shop_admin.index') }}" class="button button-small" style="background: #2563eb; border-color: #2563eb; color: #fff; font-size: 13px; font-weight: 700; text-decoration: none;">⚙️ Quản lý Khóa học</a>
            <a href="{{ route('shop_admin.orders.index') }}" class="button button-outline button-small" style="font-size: 13px; font-weight: 600; text-decoration: none;">📦 Quản lý Đơn hàng</a>
            <a href="{{ route('class_rooms.index') }}" class="button button-outline button-small" style="font-size: 13px; font-weight: 600; text-decoration: none;">🏫 Quản lý Lớp học</a>
        </div>
    </div>
@endif

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
    @php($percent = $lessons->count() > 0 ? (int)round(($done / $lessons->count()) * 100) : 0)
    <article class="enrolled-course" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;padding:26px 28px;margin-bottom:24px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <div class="enrolled-course-heading" style="display:flex;align-items:center;gap:16px;margin-bottom:12px;">
            <span class="card-subject-icon" style="background:#eff6ff;color:#2563eb;width:44px;height:44px;border-radius:10px;display:grid;place-items:center;"><x-study-icon name="book"/></span>
            <div style="flex:1;">
                <h2 style="font-size:20px;font-weight:700;color:#0f172a;margin:0 0 4px 0;line-height:1.4;">{{ $course->name }}</h2>
                <span class="status-pill" style="font-size:12px;font-weight:700;padding:3px 10px;border-radius:12px;{{ $enrollment->isCurrent() ? 'background:#ecfdf5;color:#059669;' : 'background:#f1f5f9;color:#64748b;' }}">
                    {{ $enrollment->isCurrent() ? '✓ Đang có quyền học' : ($enrollment->status==='completed' ? 'Đã hoàn thành' : 'Hết hạn truy cập') }}
                </span>
            </div>
        </div>
        <p style="font-size:15px;line-height:1.6;color:#475569;margin:0 0 14px 0;">{{ $course->summary }}</p>
        <p class="enrollment-date" style="font-size:13px;color:#64748b;margin:0 0 16px 0;">
            Bắt đầu: {{ $enrollment->started_at->format('d/m/Y') }} · {{ $enrollment->expires_at ? 'Hết hạn: '.$enrollment->expires_at->format('d/m/Y') : 'Không giới hạn thời gian' }}
        </p>

        {{-- Thanh tiến độ thiết kế hiện đại --}}
        <div class="course-progress-box" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 18px;margin-bottom:18px;">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                <span style="font-size:14px;font-weight:600;color:#334155;">Tiến độ học tập:</span>
                <strong style="font-size:15px;font-weight:700;color:#059669;">{{ $done }} / {{ $lessons->count() }} bài học ({{ $percent }}%)</strong>
            </div>
            <div style="background:#e2e8f0;border-radius:10px;height:10px;overflow:hidden;">
                <div style="background:linear-gradient(90deg, #10b981, #059669);height:100%;width:{{ max(1, $percent) }}%;border-radius:10px;transition:width 0.3s ease;"></div>
            </div>
        </div>

        @if($enrollment->isCurrent() && $course->slug)
            <a class="button" href="{{ route('learn.start',$course->slug) }}" style="font-size:15px;font-weight:700;padding:10px 22px;border-radius:8px;text-decoration:none;display:inline-block;margin-right:12px;">
                Vào học ngay →
            </a>
        @endif
        
        <details class="enrolled-syllabus" style="margin-top:16px;">
            <summary style="font-size:14px;font-weight:600;color:#2563eb;cursor:pointer;">Xem danh sách chuyên đề & bài học ({{ $lessons->count() }} bài)</summary>
            <div style="margin-top:12px;padding-left:10px;">
                @foreach($course->sections as $section)
                    <h3 style="font-size:15px;font-weight:700;color:#1e293b;margin:14px 0 6px 0;">{{ $section->title }}</h3>
                    <ul style="margin:0;padding-left:20px;font-size:14px;color:#475569;line-height:1.7;">
                        @foreach($section->lessons as $lesson)
                            <li>
                                @if($completedIds->has($lesson->id))
                                    <strong style="color:#059669;">✓ {{ $lesson->title }}</strong>
                                @else
                                    <span>○ {{ $lesson->title }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
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
