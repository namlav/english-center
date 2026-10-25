@extends('layouts.learning')

@section('title', 'Khóa học của tôi — English Center')

@section('content')
<div class="shell account-layout">
    @include('learning.account.nav')

    <section class="account-content">
        <span class="eyebrow" style="color: #2563eb; font-weight: 700; font-size: 13px; text-transform: uppercase;">GÓC HỌC TẬP CÁ NHÂN</span>
        <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 4px 0 8px;">Khóa học của tôi</h1>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 24px;">
            Tổng hợp các khóa học theo lớp trực tiếp tại trung tâm và khóa học trực tuyến bạn đã được kích hoạt quyền học.
        </p>

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

        @php
            $hasClasses = isset($classroomEnrollments) && $classroomEnrollments->isNotEmpty();
            $hasOnline = isset($onlineEnrollments) && $onlineEnrollments->isNotEmpty();
            $totalCount = ($hasClasses ? $classroomEnrollments->count() : 0) + ($hasOnline ? $onlineEnrollments->count() : 0);
        @endphp

        {{-- Thanh tóm tắt trạng thái khóa học của học viên --}}
        @if($totalCount > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 28px;">
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px;">
                    <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Tổng khóa học</span>
                    <strong style="font-size: 20px; color: #0f172a; display: block; margin-top: 4px;">{{ $totalCount }} khóa đang học</strong>
                </div>
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 16px;">
                    <span style="font-size: 12px; color: #1d4ed8; font-weight: 600; text-transform: uppercase;">Lớp học trực tiếp</span>
                    <strong style="font-size: 20px; color: #1e40af; display: block; margin-top: 4px;">{{ $hasClasses ? $classroomEnrollments->count() : 0 }} lớp</strong>
                </div>
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px;">
                    <span style="font-size: 12px; color: #15803d; font-weight: 600; text-transform: uppercase;">Khóa học Online & Combo</span>
                    <strong style="font-size: 20px; color: #166534; display: block; margin-top: 4px;">{{ $hasOnline ? $onlineEnrollments->count() : 0 }} khóa</strong>
                </div>
            </div>
        @endif

        {{-- PHẦN 1: KHÓA HỌC THEO LỚP HỌC TRỰC TIẾP (OFFLINE CLASSROOM COURSES) --}}
        @if($hasClasses)
            <div style="margin-bottom: 36px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                    <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <span>🏫</span> Khóa học theo Lớp học trực tiếp ({{ $classroomEnrollments->count() }})
                    </h2>
                    <a href="{{ route('account.schedule') }}" style="color: #2563eb; font-size: 13.5px; font-weight: 600; text-decoration: none;">
                        📅 Xem lịch học chi tiết →
                    </a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 20px;">
                    @foreach($classroomEnrollments as $enr)
                        @php
                            $classRoom = $enr->classRoom;
                            $course = $classRoom?->course;
                            $teacher = $classRoom?->teacher;
                            $teacherUser = $teacher?->user;

                            // Kiểm tra lịch học hôm nay
                            $dayIso = now()->dayOfWeekIso;
                            $dayMap = [1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7', 7 => 'Chủ nhật'];
                            $todayName = $dayMap[$dayIso] ?? '';
                            $hasLessonToday = !empty($todayName) && str_contains($classRoom?->schedule ?? '', $todayName);
                        @endphp

                        <article class="enrolled-course" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;padding:24px 26px;box-shadow:0 1px 4px rgba(0,0,0,0.04);">
                            <div class="enrolled-course-heading" style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:14px;flex-wrap:wrap;">
                                <div style="display:flex;align-items:center;gap:14px;">
                                    <span class="card-subject-icon" style="background:#eff6ff;color:#2563eb;width:48px;height:48px;border-radius:12px;display:grid;place-items:center;font-size:22px;flex-shrink:0;">
                                        <x-study-icon name="academic"/>
                                    </span>
                                    <div>
                                        <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                                            <span style="font-size:11.5px;font-weight:700;color:#1d4ed8;background:#eff6ff;padding:2px 8px;border-radius:4px;border:1px solid #bfdbfe;">
                                                🏫 KHÓA HỌC LỚP TRỰC TIẾP
                                            </span>
                                            <span style="font-size:11.5px;font-weight:600;color:#64748b;background:#f1f5f9;padding:2px 8px;border-radius:4px;">
                                                Mã lớp: {{ $classRoom?->code }}
                                            </span>
                                        </div>
                                        <h2 style="font-size:20px;font-weight:800;color:#0f172a;margin:0;line-height:1.4;">
                                            {{ $course?->name ?? $classRoom?->name }}
                                        </h2>
                                        <div style="font-size:13.5px;color:#475569;margin-top:2px;">
                                            Lớp: <strong>{{ $classRoom?->name }}</strong>
                                        </div>
                                    </div>
                                </div>

                                <div style="text-align:right;">
                                    <span class="status-pill" style="font-size:12px;font-weight:700;padding:4px 12px;border-radius:20px;background:#ecfdf5;color:#059669;border:1px solid #a7f3d0;display:inline-block;">
                                        ✓ Đã kích hoạt nhập học
                                    </span>
                                </div>
                            </div>

                            {{-- Khung thông tin lịch học & Giảng viên (Đồng bộ 100% với Lịch học của tôi) --}}
                            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px;margin-bottom:16px;">
                                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(210px, 1fr));gap:14px;font-size:13.5px;">
                                    <div>
                                        <span style="color:#64748b;display:block;font-size:12px;margin-bottom:2px;">📅 Lịch học hàng tuần:</span>
                                        <strong style="color:#2563eb;font-size:14.5px;">{{ $classRoom?->schedule }}</strong>
                                    </div>
                                    <div>
                                        <span style="color:#64748b;display:block;font-size:12px;margin-bottom:2px;">👨‍🏫 Giảng viên phụ trách:</span>
                                        <strong style="color:#0f172a;">{{ $teacherUser?->name ?? 'Đang xếp' }}</strong>
                                        <span style="color:#64748b;font-size:12px;display:block;">{{ $teacher?->specialization ?: 'Giảng viên chuyên môn' }}</span>
                                    </div>
                                    <div>
                                        <span style="color:#64748b;display:block;font-size:12px;margin-bottom:2px;">⏱ Thời gian khóa học:</span>
                                        <strong style="color:#0f172a;">{{ $classRoom?->start_date?->format('d/m/Y') }} → {{ $classRoom?->expected_end_date?->format('d/m/Y') ?? 'Chưa chốt' }}</strong>
                                    </div>
                                    <div>
                                        <span style="color:#64748b;display:block;font-size:12px;margin-bottom:2px;">📊 Chuyên cần & Điểm số:</span>
                                        <strong style="color:#059669;">{{ $enr->present_count }} có mặt</strong> <span style="color:#dc2626;">({{ $enr->absent_count }} vắng)</span>
                                        <span style="color:#64748b;font-size:12px;display:block;">(GK: {{ $enr->midterm_score ?? '—' }} · CK: {{ $enr->final_score ?? '—' }})</span>
                                    </div>
                                </div>

                                {{-- Thông báo ca học hôm nay nếu có --}}
                                @if($hasLessonToday)
                                    <div style="background:#ecfdf5;border:1px solid #10b981;color:#047857;padding:8px 12px;border-radius:6px;font-weight:700;font-size:13px;margin-top:12px;display:flex;align-items:center;gap:6px;">
                                        <span>🔔</span> Hôm nay có buổi học trực tiếp: {{ $classRoom?->schedule }}
                                    </div>
                                @endif
                            </div>

                            {{-- Footer: Thao tác & liên kết nhanh --}}
                            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;border-top:1px solid #f1f5f9;padding-top:14px;">
                                <div style="font-size:13px;color:#64748b;">
                                    Mã phiếu học phí: <a href="{{ route('orders.index') }}" style="color:#2563eb;font-family:monospace;font-weight:700;text-decoration:none;">{{ $enr->invoice_code }}</a> 
                                    (Đã thanh toán {{ number_format((float) ($enr->paid_tuition > 0 ? $enr->paid_tuition : ($classRoom?->tuition_fee ?? 0)), 0, ',', '.') }} ₫)
                                </div>
                                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                                    <a href="{{ route('account.schedule') }}" class="button" style="font-size:13px;font-weight:700;padding:8px 16px;border-radius:6px;background:#2563eb;color:#fff;text-decoration:none;">
                                        📅 Xem lịch học chi tiết →
                                    </a>
                                    <a href="{{ route('enrollments.my_classes') }}" class="button button-outline" style="font-size:13px;font-weight:600;padding:8px 16px;border-radius:6px;text-decoration:none;">
                                        📋 Xem lớp học & Điểm danh
                                    </a>
                                    <a href="{{ route('practice.index') }}" class="button button-outline" style="font-size:13px;font-weight:600;padding:8px 16px;border-radius:6px;text-decoration:none;">
                                        📝 Đề thi thử
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- PHẦN 2: KHÓA HỌC VIDEO TRỰC TUYẾN & GÓI COMBO (ONLINE / E-LEARNING COURSES) --}}
        @if($hasOnline)
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                    <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <span>💻</span> Khóa học Video Trực tuyến & Gói Combo ({{ $onlineEnrollments->count() }})
                    </h2>
                    <a href="{{ route('catalog.index') }}" style="color: #2563eb; font-size: 13.5px; font-weight: 600; text-decoration: none;">
                        + Khám phá thêm khóa Online
                    </a>
                </div>

                <div style="display: flex; flex-direction: column; gap: 20px;">
                    @foreach($onlineEnrollments as $enrollment)
                        @php
                            $course = $enrollment->course;
                            $lessons = $course ? $course->sections->flatMap->lessons : collect();
                            $done = $lessons->filter(fn($lesson)=>$completedIds->has($lesson->id))->count();
                            $percent = $lessons->count() > 0 ? (int)round(($done / $lessons->count()) * 100) : 0;

                            $childKeys = ($course && isset(\App\Services\CourseShop::COMBO_MAP[$course->catalog_key])) ? \App\Services\CourseShop::COMBO_MAP[$course->catalog_key] : null;
                            $parentIds = ($course && isset($shop)) ? $shop->parentComboCourseIds($course->id) : [];
                            $parentCourse = !empty($parentIds) ? \App\Models\Course::whereIn('id', $parentIds)->first() : null;
                            $hasParentCombo = $parentCourse && $onlineEnrollments->contains('course_id', $parentCourse->id);
                            
                            $order = null;
                            if (isset($paidOrders) && $course) {
                                if ($hasParentCombo) {
                                    $order = $paidOrders->first(fn($o) => $o->items->contains('course_id', $parentCourse->id));
                                } else {
                                    $order = $paidOrders->first(fn($o) => $o->items->contains('course_id', $course->id));
                                }
                            }
                        @endphp

                        <article class="enrolled-course" style="background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;padding:26px 28px;box-shadow:0 1px 3px rgba(0,0,0,0.05);">
                            <div class="enrolled-course-heading" style="display:flex;align-items:center;gap:16px;margin-bottom:12px;">
                                <span class="card-subject-icon" style="background:#eff6ff;color:#2563eb;width:44px;height:44px;border-radius:10px;display:grid;place-items:center;"><x-study-icon name="book"/></span>
                                <div style="flex:1;">
                                    <h2 style="font-size:20px;font-weight:700;color:#0f172a;margin:0 0 4px 0;line-height:1.4;">{{ $course->name }}</h2>
                                    <span class="status-pill" style="font-size:12px;font-weight:700;padding:3px 10px;border-radius:12px;{{ $enrollment->isCurrent() ? 'background:#ecfdf5;color:#059669;' : 'background:#f1f5f9;color:#64748b;' }}">
                                        {{ $enrollment->isCurrent() ? '✓ Đang có quyền học Online' : ($enrollment->status==='completed' ? 'Đã hoàn thành' : 'Hết hạn truy cập') }}
                                    </span>
                                </div>
                            </div>

                            @if($childKeys)
                                @php
                                    $includedSubCourses = \App\Models\Course::whereIn('catalog_key', $childKeys)->pluck('name')->all();
                                @endphp
                                <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px; font-size: 13px; color: #3730a3;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                        <div>
                                            <strong>📦 GÓI COMBO TRỌN GÓI</strong> — Bao gồm <strong>{{ count($includedSubCourses) }} khóa học thành phần</strong>: <em>{{ implode(', ', $includedSubCourses) }}</em>.
                                        </div>
                                        @if($order)
                                            <span style="font-size: 12px; color: #4f46e5;">
                                                Đơn hàng: <a href="{{ route('orders.show', $order->number) }}" style="color: #4338ca; font-weight: 700; text-decoration: underline;">#{{ substr($order->number, 0, 8) }}...</a> (Đã thanh toán {{ number_format($order->total, 0, ',', '.') }}đ)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @elseif($hasParentCombo)
                                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px; margin-bottom: 14px; font-size: 13px; color: #166534;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                                        <div>
                                            <strong>🎁 KHÓA HỌC THÀNH PHẦN</strong> — Được cấp quyền tự động theo gói <strong>{{ $parentCourse->name }}</strong> <em>(Học viên không cần thanh toán thêm)</em>.
                                        </div>
                                        @if($order)
                                            <span style="font-size: 12px; color: #15803d;">
                                                Theo Đơn hàng: <a href="{{ route('orders.show', $order->number) }}" style="color: #166534; font-weight: 700; text-decoration: underline;">#{{ substr($order->number, 0, 8) }}...</a>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @elseif($order)
                                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px 14px; margin-bottom: 14px; font-size: 12.5px; color: #475569;">
                                    Kích hoạt theo Đơn hàng: <a href="{{ route('orders.show', $order->number) }}" style="color: #2563eb; font-weight: 600; text-decoration: underline;">#{{ substr($order->number, 0, 8) }}...</a> (Đã thanh toán {{ number_format($order->total, 0, ',', '.') }}đ)
                                </div>
                            @endif

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
                                <a class="button" href="{{ route('learn.start',$course->slug) }}" style="font-size:15px;font-weight:700;padding:10px 22px;border-radius:8px;text-decoration:none;display:inline-block;margin-right:12px;background:#2563eb;color:#fff;">
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
                    @endforeach
                </div>
            </div>
        @endif

        {{-- TRƯỜNG HỢP HỌC VIÊN CHƯA CÓ KHÓA NÀO (CẢ OFFLINE LẪN ONLINE ĐỀU TRỐNG) --}}
        @if(!$hasClasses && !$hasOnline)
            <div class="empty-state" style="background:#fff;border:1px dashed #cbd5e1;border-radius:14px;padding:50px 20px;text-align:center;">
                <span style="font-size:44px;display:block;margin-bottom:12px;">📚</span>
                <h3 style="font-size:18px;font-weight:700;color:#1e293b;margin:0 0 8px;">Bạn chưa có khóa học nào đã kích hoạt</h3>
                <p style="color:#64748b;font-size:14px;max-width:500px;margin:0 auto 20px;">
                    Các khóa học theo lớp trực tiếp hoặc khóa học trực tuyến sau khi kích hoạt thành công sẽ xuất hiện tại đây. Bạn cũng có thể bắt đầu với các bài luyện tập miễn phí.
                </p>
                <div class="empty-actions" style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                    <a class="button" href="{{ route('public_classes.index') }}" style="font-size:14px;font-weight:700;padding:10px 20px;border-radius:8px;background:#2563eb;color:#fff;text-decoration:none;">
                        Đăng ký lớp học mới
                    </a>
                    <a class="button button-outline" href="{{ route('catalog.index') }}" style="font-size:14px;font-weight:600;padding:10px 20px;border-radius:8px;text-decoration:none;">
                        Khám phá khóa học Online
                    </a>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection
