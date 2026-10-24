@extends('layouts.learning')
@section('title', $classRoom->name . ' — English Center')

@section('content')
@php
    $currentStudent = auth()->check() ? auth()->user()->student : null;
    $isEnrolled = $currentStudent && $classRoom->enrollments->contains('student_id', $currentStudent->id);
    $isFull = $classRoom->enrollments_count >= $classRoom->max_students;
    $canEnroll = in_array($classRoom->status, ['upcoming', 'ongoing'], true);
@endphp

<section class="shell" style="padding-top: 24px; margin-bottom: 48px;">
    {{-- Breadcrumb --}}
    <nav style="font-size: 13.5px; color: #64748b; margin-bottom: 20px;">
        <a href="{{ route('home') }}" style="color: #64748b; text-decoration: none;">Trang chủ</a> /
        <a href="{{ route('public_classes.index') }}" style="color: #64748b; text-decoration: none;">Lớp học &amp; Khai giảng</a> /
        <span style="color: #0f172a; font-weight: 600;">{{ $classRoom->code }}</span>
    </nav>

    <div style="display: grid; grid-template-columns: 1fr 360px; gap: 32px; align-items: start;">
        {{-- CỘT TRÁI: THÔNG TIN CHI TIẾT LỚP HỌC --}}
        <div>
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 32px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04); margin-bottom: 28px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px;">
                    <span style="background: #eff6ff; color: #1d4ed8; font-weight: 800; font-size: 13px; font-family: monospace; padding: 4px 12px; border-radius: 6px;">
                        {{ $classRoom->code }}
                    </span>
                    <span style="background: #ecfdf5; color: #059669; font-weight: 700; font-size: 12.5px; padding: 4px 12px; border-radius: 6px;">
                        {{ \App\Models\ClassRoom::STATUSES[$classRoom->status] ?? $classRoom->status }}
                    </span>
                </div>

                <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 0 0 12px 0; line-height: 1.3;">
                    {{ $classRoom->name }}
                </h1>

                <p style="font-size: 15px; color: #64748b; margin: 0 0 24px 0;">
                    Chương trình đào tạo: <strong style="color: #2563eb;">{{ $classRoom->course->name ?? 'Chuẩn trung tâm' }}</strong>
                </p>

                <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0 0 16px 0; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                    Thông tin lớp học &amp; Lịch đào tạo
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 28px;">
                    <div style="background: #f8fafc; border-radius: 10px; padding: 14px 18px;">
                        <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 4px;">📅 Lịch học trong tuần</span>
                        <strong style="font-size: 15px; color: #0f172a;">{{ $classRoom->schedule }}</strong>
                    </div>

                    <div style="background: #f8fafc; border-radius: 10px; padding: 14px 18px;">
                        <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 4px;">🚀 Ngày khai giảng</span>
                        <strong style="font-size: 15px; color: #0f172a;">{{ $classRoom->start_date?->format('d/m/Y') }}</strong>
                    </div>

                    <div style="background: #f8fafc; border-radius: 10px; padding: 14px 18px;">
                        <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 4px;">🏁 Dự kiến kết thúc</span>
                        <strong style="font-size: 15px; color: #0f172a;">{{ $classRoom->expected_end_date?->format('d/m/Y') ?? 'Đang cập nhật' }}</strong>
                    </div>

                    <div style="background: #f8fafc; border-radius: 10px; padding: 14px 18px;">
                        <span style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 4px;">🏢 Địa điểm / Phòng học</span>
                        <strong style="font-size: 15px; color: #0f172a;">Cơ sở chính English Center</strong>
                    </div>
                </div>

                {{-- Giảng viên phụ trách --}}
                <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0 0 16px 0; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                    Giảng viên giảng dạy
                </h3>

                <div style="display: flex; align-items: center; gap: 16px; background: #f8fafc; border-radius: 12px; padding: 16px 20px;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: #2563eb; color: #ffffff; display: grid; place-items: center; font-weight: 800; font-size: 20px;">
                        {{ mb_strtoupper(mb_substr($classRoom->teacher?->user?->name ?? 'G', 0, 1)) }}
                    </div>
                    <div>
                        <strong style="font-size: 16px; color: #0f172a; display: block;">{{ $classRoom->teacher?->user?->name ?? 'Đang phân công giảng viên' }}</strong>
                        <span style="font-size: 13px; color: #64748b;">Mã GV: {{ $classRoom->teacher?->teacher_code ?? 'GV' }} · Giảng viên chuyên ngữ tại English Center</span>
                    </div>
                </div>
            </div>

            {{-- Các lớp cùng khóa học --}}
            @if(isset($relatedClasses) && $relatedClasses->isNotEmpty())
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 24px; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);">
                    <h3 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 0 0 16px 0;">
                        Các lớp học khác cùng chương trình
                    </h3>
                    <div style="display: grid; gap: 12px;">
                        @foreach($relatedClasses as $rel)
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: #f8fafc; border-radius: 8px;">
                                <div>
                                    <strong style="font-size: 14.5px; color: #0f172a;">{{ $rel->name }}</strong>
                                    <div style="font-size: 12.5px; color: #64748b;">{{ $rel->schedule }} · Khai giảng {{ $rel->start_date?->format('d/m/Y') }}</div>
                                </div>
                                <a href="{{ route('public_classes.show', $rel) }}" class="button button-small button-outline" style="font-size: 12.5px; text-decoration: none;">Xem lớp →</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- CỘT PHẢI: KHỐI ĐĂNG KÝ HỌC PHÍ --}}
        <div>
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 24px; box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06); position: sticky; top: 20px;">
                <span style="font-size: 12px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 6px;">
                    HỌC PHÍ TOÀN KHÓA
                </span>
                <div style="font-size: 30px; font-weight: 900; color: #059669; line-height: 1.2; margin-bottom: 16px;">
                    {{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫
                </div>

                {{-- Tình trạng chỗ trống --}}
                <div style="margin-bottom: 20px; padding: 12px 14px; background: #f8fafc; border-radius: 8px; font-size: 13.5px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                        <span style="color: #64748b;">Sĩ số hiện tại:</span>
                        <strong style="color: #0f172a;">{{ $classRoom->enrollments_count }}/{{ $classRoom->max_students }} học viên</strong>
                    </div>
                    @php
                        $pct = min(100, round(($classRoom->enrollments_count / max(1, $classRoom->max_students)) * 100));
                    @endphp
                    <div style="height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                        <div style="width: {{ $pct }}%; height: 100%; background: {{ $pct >= 100 ? '#dc2626' : ($pct >= 80 ? '#f59e0b' : '#2563eb') }};"></div>
                    </div>
                    <span style="font-size: 12px; color: {{ $isFull ? '#dc2626' : '#059669' }}; font-weight: 600; display: block; margin-top: 6px;">
                        @if($isFull)
                            ⚠️ Lớp đã đủ sĩ số, vui lòng chọn lớp khác.
                        @else
                            ✓ Còn trống {{ max(0, $classRoom->max_students - $classRoom->enrollments_count) }} chỗ
                        @endif
                    </span>
                </div>

                {{-- NÚT HÀNH ĐỘNG ĐĂNG KÝ --}}
                @if($isEnrolled)
                    <div style="background: #ecfdf5; border: 1px solid #10b981; border-radius: 8px; padding: 14px; text-align: center; margin-bottom: 14px;">
                        <strong style="color: #065f46; font-size: 14.5px; display: block; margin-bottom: 4px;">✓ Bạn đã đăng ký lớp học này</strong>
                        <span style="color: #047857; font-size: 12.5px;">Theo dõi lịch học và kết quả trong trang cá nhân.</span>
                    </div>
                    <a href="{{ route('enrollments.my_classes') }}" class="button" style="width: 100%; text-align: center; font-weight: 700; text-decoration: none; display: block; background: #059669; border-color: #059669;">
                        Đến lớp học của tôi →
                    </a>
                @elseif(auth()->check() && auth()->user()->isStudent())
                    @if($isFull)
                        <button type="button" class="button button-outline" style="width: 100%; opacity: 0.6; cursor: not-allowed;" disabled>
                            Lớp đã đủ học viên
                        </button>
                    @elseif(!$canEnroll)
                        <button type="button" class="button button-outline" style="width: 100%; opacity: 0.6; cursor: not-allowed;" disabled>
                            Lớp không nhận đăng ký
                        </button>
                    @else
                        <form action="{{ route('enrollments.enroll', $classRoom) }}" method="POST" onsubmit="return confirm('Xác nhận đăng ký tham gia lớp học {{ $classRoom->name }} (Học phí: {{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫)?');">
                            @csrf
                            <button type="submit" class="button" style="width: 100%; font-size: 16px; font-weight: 800; padding: 13px; text-align: center; background: #2563eb; border-color: #2563eb; cursor: pointer; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                                Đăng ký lớp học ngay ➔
                            </button>
                        </form>
                    @endif
                @elseif(!auth()->check())
                    <a href="{{ route('login') }}" class="button" style="width: 100%; text-align: center; font-size: 15px; font-weight: 800; padding: 12px; text-decoration: none; display: block; background: #2563eb; border-color: #2563eb;">
                        Đăng nhập để đăng ký học ➔
                    </a>
                    <p style="font-size: 12.5px; color: #64748b; text-align: center; margin-top: 8px;">
                        Chưa có tài khoản? <a href="{{ route('register') }}" style="color: #2563eb; font-weight: 600;">Đăng ký ngay</a>
                    </p>
                @endif

                @if(auth()->check() && auth()->user()->isAdmin())
                    <div style="margin-top: 14px; padding-top: 12px; border-top: 1px dashed #e2e8f0;">
                        <a href="{{ route('class_rooms.show', $classRoom) }}" class="button button-small button-outline" style="width: 100%; text-align: center; font-size: 12px; text-decoration: none; display: block;">
                            ⚙️ Quản lý lớp này trong Admin →
                        </a>
                    </div>
                @endif

                <ul style="margin: 20px 0 0 0; padding-left: 18px; font-size: 13px; color: #64748b; line-height: 1.6;">
                    <li>Cam kết đầu ra chuẩn quốc tế.</li>
                    <li>Giảng viên theo sát tiến độ từng buổi.</li>
                    <li>Phòng học trang bị đầy đủ máy lạnh, máy chiếu.</li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection
