@extends('layouts.learning')

@section('title', 'Lịch học của tôi — English Center')

@section('content')
<div class="shell account-layout">
    @include('learning.account.nav')

    <section class="account-content">
        <span class="eyebrow">SẮP XẾP VIỆC HỌC</span>
        <h1>Lịch học của tôi</h1>
        <p>Lịch các lớp bạn đã đăng ký hoặc được phân công giảng dạy.</p>

        @forelse($classes as $class)
            @php
                $today = now()->startOfDay();
                $startDate = $class->start_date ? \Carbon\Carbon::parse($class->start_date)->startOfDay() : null;
                $endDate = $class->expected_end_date ? \Carbon\Carbon::parse($class->expected_end_date)->endOfDay() : null;

                $isUpcoming = $startDate && $today->lt($startDate);
                $isEnded = $endDate && $today->gt($endDate);
                $isOngoing = ($startDate && $today->gte($startDate)) && (! $endDate || $today->lte($endDate));

                // Kiểm tra xem hôm nay có phải là ngày học theo lịch
                $dayIso = now()->dayOfWeekIso; // 1 (Thứ 2) -> 7 (Chủ nhật)
                $dayMap = [
                    1 => 'Thứ 2',
                    2 => 'Thứ 3',
                    3 => 'Thứ 4',
                    4 => 'Thứ 5',
                    5 => 'Thứ 6',
                    6 => 'Thứ 7',
                    7 => 'Chủ nhật',
                ];
                $todayName = $dayMap[$dayIso] ?? '';
                $hasLessonToday = $isOngoing && !empty($todayName) && str_contains($class->schedule, $todayName);
                $daysLeft = $isUpcoming && $startDate ? $today->diffInDays($startDate) : 0;
            @endphp

            <article class="schedule-card" style="position: relative;">
                <span class="status-pill">{{ \App\Models\ClassRoom::STATUSES[$class->status] ?? $class->status }}</span>
                <h2>{{ $class->name }}</h2>
                <p>{{ $class->course->name }}</p>
                <strong>{{ $class->schedule }}</strong>
                <p>{{ $class->start_date->format('d/m/Y') }} – {{ $class->expected_end_date?->format('d/m/Y') ?? 'Đang cập nhật' }}</p>
                <p>Giáo viên: {{ $class->teacher?->user?->name ?? 'Đang cập nhật' }}</p>

                {{-- Thông báo lịch học theo ngày (thay thế nút xem chi tiết) --}}
                <div class="schedule-timing-notice" style="margin-top: 14px;">
                    @if($hasLessonToday)
                        <div style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 20px;">🔔</span>
                            <div>
                                <strong style="color: #047857; display: block; font-size: 14px;">HÔM NAY CÓ BUỔI HỌC: {{ $class->schedule }}</strong>
                                <span style="font-size: 12.5px; color: #065f46;">Vui lòng có mặt đúng giờ tại phòng học / cơ sở chính của trung tâm.</span>
                            </div>
                        </div>
                    @elseif($isUpcoming)
                        <div style="background: #eff6ff; border: 1px solid #93c5fd; color: #1e40af; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 20px;">⏳</span>
                            <div>
                                <strong style="color: #1d4ed8; display: block; font-size: 14px;">Lớp sắp khai giảng: {{ $class->start_date->format('d/m/Y') }} (Còn {{ $daysLeft }} ngày)</strong>
                                <span style="font-size: 12.5px; color: #3b82f6;">Hệ thống sẽ gửi thông báo và nhắc giờ học khi đến ngày khai giảng.</span>
                            </div>
                        </div>
                    @elseif($isOngoing)
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 10px;">
                            <span style="font-size: 20px;">🟢</span>
                            <div>
                                <strong style="color: #166534; display: block; font-size: 14px;">Lớp đang học định kỳ ({{ $class->schedule }})</strong>
                                <span style="font-size: 12.5px; color: #15803d;">Hôm nay không có ca học của lớp này. Lịch học tiếp theo diễn ra theo khung giờ trên.</span>
                            </div>
                        </div>
                    @else
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b; padding: 10px 14px; border-radius: 8px; font-size: 13px;">
                            ✓ Lớp đã hoàn thành lộ trình đào tạo ({{ $class->start_date->format('d/m/Y') }} – {{ $class->expected_end_date?->format('d/m/Y') ?? '' }}).
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="empty-state">
                <x-study-icon name="clock"/>
                <h2>Chưa có lịch học</h2>
                <p>Lịch sẽ xuất hiện khi bạn được đăng ký hoặc phân công vào lớp.</p>
                <a class="button button-outline" href="{{ route('practice.index') }}">Luyện tập trong lúc chờ</a>
            </div>
        @endforelse

        @if($classes->hasPages())
            <nav class="catalog-pagination">
                @if($classes->previousPageUrl())
                    <a href="{{ $classes->previousPageUrl() }}">← Trang trước</a>
                @endif
                <span>Trang {{ $classes->currentPage() }}/{{ $classes->lastPage() }}</span>
                @if($classes->nextPageUrl())
                    <a href="{{ $classes->nextPageUrl() }}">Trang sau →</a>
                @endif
            </nav>
        @endif
    </section>
</div>
@endsection
