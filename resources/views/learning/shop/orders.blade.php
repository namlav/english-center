@extends('layouts.learning')

@section('title', 'Đơn hàng & Hóa đơn của tôi — English Center')

@section('content')
<div class="shell account-layout">
    @include('learning.account.nav')

    <section class="account-content">
        <span class="eyebrow" style="color: #2563eb; font-weight: 700; font-size: 13px; text-transform: uppercase;">TÀI CHÍNH & LỊCH SỬ THANH TOÁN</span>
        <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 4px 0 8px;">Đơn hàng & Hóa đơn của tôi</h1>
        <p style="color: #64748b; font-size: 14px; margin-bottom: 24px;">
            Theo dõi tất cả đơn hàng khóa học Online và hóa đơn học phí lớp học trực tiếp đã ghi danh của bạn tại English Center.
        </p>

        @php
            $paidOrdersTotal = $orders->getCollection()->where('status', 'paid')->sum('total');
            $paidClassesTotal = $classEnrollments->where('payment_status', 'paid')->sum('paid_tuition');
            $totalPaid = $paidOrdersTotal + $paidClassesTotal;
            $totalCount = $orders->total() + $classEnrollments->count();
        @endphp

        {{-- Thanh tóm tắt tài chính học viên --}}
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 28px;">
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px;">
                <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Tổng giao dịch</span>
                <strong style="font-size: 20px; color: #0f172a; display: block; margin-top: 4px;">{{ $totalCount }} bản ghi</strong>
            </div>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 16px;">
                <span style="font-size: 12px; color: #15803d; font-weight: 600; text-transform: uppercase;">Tổng tiền đã thanh toán</span>
                <strong style="font-size: 20px; color: #166534; display: block; margin-top: 4px;">{{ number_format($totalPaid, 0, ',', '.') }} ₫</strong>
            </div>
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 16px;">
                <span style="font-size: 12px; color: #1d4ed8; font-weight: 600; text-transform: uppercase;">Đơn hàng Online</span>
                <strong style="font-size: 20px; color: #1e40af; display: block; margin-top: 4px;">{{ $orders->total() }} đơn</strong>
            </div>
            <div style="background: #fefce8; border: 1px solid #fef08a; border-radius: 10px; padding: 16px;">
                <span style="font-size: 12px; color: #a16207; font-weight: 600; text-transform: uppercase;">Lớp học trực tiếp</span>
                <strong style="font-size: 20px; color: #854d0e; display: block; margin-top: 4px;">{{ $classEnrollments->count() }} lớp</strong>
            </div>
        </div>

        {{-- PHẦN 1: ĐƠN HÀNG KHÓA HỌC TRỰC TUYẾN --}}
        <div style="margin-bottom: 36px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <span>💻</span> Đơn hàng Khóa học Online & Combo ({{ $orders->total() }})
                </h2>
                <a href="{{ route('catalog.index') }}" style="color: #2563eb; font-size: 13.5px; font-weight: 600; text-decoration: none;">
                    + Xem thêm khóa học Online
                </a>
            </div>

            @if($orders->isEmpty())
                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 30px; text-align: center; color: #64748b;">
                    Bạn chưa có đơn hàng khóa học Online nào.
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($orders as $order)
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 14px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                                <div>
                                    <span style="font-size: 12px; color: #64748b;">Mã đơn hàng:</span>
                                    <strong style="color: #2563eb; font-size: 14px; font-family: monospace; display: block;">
                                        {{ $order->number }}
                                    </strong>
                                    <span style="font-size: 12px; color: #94a3b8; margin-top: 2px; display: block;">
                                        🕒 Đặt lúc {{ $order->created_at?->copy()->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <div style="text-align: right;">
                                    @if($order->status === 'paid')
                                        <span style="background: #ecfdf5; color: #059669; font-weight: 700; font-size: 12.5px; padding: 4px 12px; border-radius: 20px; display: inline-block;">
                                            ✓ Đã thanh toán / Đã kích hoạt
                                        </span>
                                    @else
                                        <span style="background: #fffbeb; color: #b45309; font-weight: 700; font-size: 12.5px; padding: 4px 12px; border-radius: 20px; display: inline-block;">
                                            ⏳ Chờ xác nhận thanh toán
                                        </span>
                                    @endif
                                    <strong style="font-size: 17px; color: #0f172a; display: block; margin-top: 6px;">
                                        {{ number_format($order->total, 0, ',', '.') }} ₫
                                    </strong>
                                </div>
                            </div>

                            {{-- Danh sách khóa học trong đơn --}}
                            <div style="margin-bottom: 14px;">
                                <span style="font-size: 12.5px; font-weight: 600; color: #475569; display: block; margin-bottom: 6px;">Khóa học trong đơn:</span>
                                @foreach($order->items as $item)
                                    @php($childKeys = $item->course ? (\App\Services\CourseShop::COMBO_MAP[$item->course->catalog_key] ?? null) : null)
                                    <div style="background: #f8fafc; border-radius: 8px; padding: 10px 14px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                                        <div>
                                            <strong style="font-size: 14px; color: #1e293b;">{{ $item->course_name }}</strong>
                                            @if($childKeys)
                                                @php($childCourses = \App\Models\Course::whereIn('catalog_key', $childKeys)->pluck('name')->all())
                                                <div style="font-size: 12px; color: #4338ca; margin-top: 3px;">
                                                    📦 <em>Gói Combo bao gồm {{ count($childCourses) }} khóa học thành phần: {{ implode(', ', $childCourses) }}</em>
                                                </div>
                                            @endif
                                        </div>
                                        <div style="font-size: 13.5px; font-weight: 700; color: #0f172a;">
                                            {{ number_format($item->unit_price, 0, ',', '.') }} ₫
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                <span style="font-size: 12.5px; color: #64748b;">
                                    Phương thức: <strong>{{ $order->payment?->method === 'bank_transfer' ? '🏦 QR Ngân hàng VietQR' : 'Mô phỏng thanh toán' }}</strong>
                                </span>
                                <div style="display: flex; gap: 10px;">
                                    <a href="{{ route('orders.show', $order->number) }}" class="button button-outline" style="font-size: 13px; font-weight: 600; padding: 6px 14px; text-decoration: none; border-radius: 6px;">
                                        Xem hóa đơn & mã đơn →
                                    </a>
                                    @if($order->status === 'paid')
                                        <a href="{{ route('account.courses') }}" class="button" style="font-size: 13px; font-weight: 700; padding: 6px 14px; text-decoration: none; border-radius: 6px; background: #2563eb; color: #fff;">
                                            Vào học ngay →
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div style="margin-top: 16px;">
                    @include('learning.shop.pager', ['records' => $orders])
                </div>
            @endif
        </div>

        {{-- PHẦN 2: HÓA ĐƠN HỌC PHÍ LỚP HỌC TRỰC TIẾP --}}
        <div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                <h2 style="font-size: 18px; font-weight: 700; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                    <span>🏫</span> Hóa đơn Học phí Lớp học Trực tiếp ({{ $classEnrollments->count() }})
                </h2>
                <a href="{{ route('enrollments.my_classes') }}" style="color: #2563eb; font-size: 13.5px; font-weight: 600; text-decoration: none;">
                    Xem chi tiết lớp của tôi →
                </a>
            </div>

            @if($classEnrollments->isEmpty())
                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 10px; padding: 30px; text-align: center; color: #64748b;">
                    Bạn chưa có hồ sơ đăng ký lớp học trực tiếp nào.
                </div>
            @else
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    @foreach($classEnrollments as $enr)
                        @php($classRoom = $enr->classRoom)
                        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 12px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                                <div>
                                    <span style="font-size: 12px; color: #64748b;">Mã hóa đơn học phí:</span>
                                    <strong style="color: #2563eb; font-size: 14.5px; font-family: monospace; display: block;">
                                        {{ $enr->invoice_code }}
                                    </strong>
                                    <span style="font-size: 12px; color: #94a3b8; margin-top: 2px; display: block;">
                                        🕒 Ngày đăng ký: {{ $enr->created_at?->copy()->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                <div style="text-align: right;">
                                    @if($enr->payment_status === 'paid')
                                        <span style="background: #ecfdf5; color: #059669; font-weight: 700; font-size: 12.5px; padding: 4px 12px; border-radius: 20px; display: inline-block;">
                                            ✓ Đã thanh toán học phí
                                        </span>
                                    @else
                                        <span style="background: #fffbeb; color: #b45309; font-weight: 700; font-size: 12.5px; padding: 4px 12px; border-radius: 20px; display: inline-block;">
                                            ⏳ Chờ trung tâm duyệt học phí
                                        </span>
                                    @endif
                                    <strong style="font-size: 17px; color: #047857; display: block; margin-top: 6px;">
                                        {{ number_format((float) ($enr->paid_tuition > 0 ? $enr->paid_tuition : ($classRoom?->tuition_fee ?? 0)), 0, ',', '.') }} ₫
                                    </strong>
                                </div>
                            </div>

                            <div style="background: #f8fafc; border-radius: 8px; padding: 12px 16px; margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                <div>
                                    <strong style="font-size: 14.5px; color: #0f172a;">{{ $classRoom?->name ?? 'Lớp học' }}</strong>
                                    <span style="font-size: 12.5px; color: #64748b; display: block; margin-top: 2px;">
                                        Mã lớp: <strong>{{ $classRoom?->code }}</strong> · Giảng viên: <strong>{{ $classRoom?->teacher?->user?->name ?? 'Đang xếp' }}</strong>
                                    </span>
                                </div>
                                <div style="font-size: 13px; color: #475569;">
                                    Lịch học: <strong>{{ $classRoom?->schedule ?? 'Chưa cập nhật' }}</strong>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                <span style="font-size: 12px; color: #64748b;">
                                    Tình trạng học tập: <strong style="color: #0f172a;">{{ ['studying'=>'Đang học','passed'=>'Đạt chuẩn','failed'=>'Chưa đạt'][$enr->result_status] ?? 'Đang học' }}</strong>
                                </span>
                                <a href="{{ route('enrollments.my_classes') }}" class="button button-outline" style="font-size: 13px; font-weight: 600; padding: 6px 14px; text-decoration: none; border-radius: 6px;">
                                    Xem lớp học & Điểm danh →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
