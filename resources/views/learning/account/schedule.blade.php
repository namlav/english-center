@extends('layouts.learning')

@section('title', 'Lịch học của tôi — English Center')

@section('content')
<div class="shell account-layout">
    @include('learning.account.nav')

    <section class="account-content">
        <h1 style="font-size: 22px; font-weight: 800; color: #0f172a; margin: 0 0 16px;">Lịch học của tôi</h1>

        @forelse($classes as $class)
            @php
                $today = now()->startOfDay();
                $startDate = $class->start_date ? \Carbon\Carbon::parse($class->start_date)->startOfDay() : null;
                $endDate = $class->expected_end_date ? \Carbon\Carbon::parse($class->expected_end_date)->endOfDay() : null;

                $isUpcoming = $startDate && $today->lt($startDate);
                $isEnded = $endDate && $today->gt($endDate);
                $isOngoing = ($startDate && $today->gte($startDate)) && (! $endDate || $today->lte($endDate));

                // Kiểm tra hôm nay có ca học không
                $dayIso = now()->dayOfWeekIso;
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

            <article class="schedule-card" style="margin-bottom: 16px; padding: 20px;">
                <span class="status-pill">{{ \App\Models\ClassRoom::STATUSES[$class->status] ?? $class->status }}</span>
                <h2 style="font-size: 18px; margin: 8px 0 6px;">{{ $class->name }}</h2>
                <strong style="color: #2563eb; font-size: 14.5px;">{{ $class->schedule }}</strong>
                <p style="margin: 6px 0 4px; color: #64748b; font-size: 13.5px;">
                    {{ $class->start_date->format('d/m/Y') }} – {{ $class->expected_end_date?->format('d/m/Y') ?? 'Đang cập nhật' }} · GV: {{ $class->teacher?->user?->name ?? 'Đang cập nhật' }}
                </p>

                {{-- Thông báo lịch học trực tiếp --}}
                <div style="margin-top: 10px;">
                    @if($hasLessonToday)
                        <div style="background: #ecfdf5; border: 1px solid #10b981; color: #047857; padding: 8px 12px; border-radius: 6px; font-weight: 700; font-size: 13px;">
                            🔔 Hôm nay có buổi học: {{ $class->schedule }}
                        </div>
                    @elseif($isUpcoming)
                        <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 13px;">
                            ⏳ Khai giảng: {{ $class->start_date->format('d/m/Y') }} (Còn {{ $daysLeft }} ngày)
                        </div>
                    @elseif($isOngoing)
                        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 13px;">
                            🟢 Đang diễn ra · Lịch học: {{ $class->schedule }}
                        </div>
                    @else
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; color: #64748b; padding: 8px 12px; border-radius: 6px; font-size: 12.5px;">
                            ✓ Đã kết thúc
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="empty-state">
                <x-study-icon name="clock"/>
                <h2>Chưa có lịch học</h2>
                <a class="button button-outline" href="{{ route('practice.index') }}" style="margin-top: 10px;">Luyện tập</a>
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
