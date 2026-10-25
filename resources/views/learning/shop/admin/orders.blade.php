@extends('layouts.admin')
@section('title', 'Quản lý Đơn hàng & Kích hoạt Khóa học')
@section('content')
<section class="shell admin-page">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 22px;">
        <div>
            <span class="eyebrow" style="color: #2563eb; font-weight: 700; font-size: 13px; text-transform: uppercase;">BÁN HÀNG & THANH TOÁN</span>
            <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 4px 0 0;">Quản lý Đơn hàng & Thanh toán</h1>
            <p style="color: #64748b; font-size: 14px; margin: 4px 0 0;">Kiểm tra tất cả đơn hàng khóa học Online & Lớp học trực tiếp, đối soát VietQR và kích hoạt khóa học cho học viên.</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a class="button button-outline" href="{{ route('admin.dashboard') }}" style="text-decoration: none; font-weight: 600;">
                📊 Báo cáo doanh thu
            </a>
            <a class="button button-outline" href="{{ route('shop_admin.bank.edit') }}" style="text-decoration: none; font-weight: 600;">
                ⚙️ Cài đặt VietQR Ngân hàng
            </a>
        </div>
    </div>

    {{-- Dải thống kê tổng quan --}}
    @if(isset($stats))
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 22px;">
            <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <span style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Tổng số đơn hàng</span>
                <strong style="font-size: 22px; color: #0f172a; display: block; margin-top: 4px;">{{ number_format($stats['all']) }}</strong>
            </div>
            <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <span style="font-size: 12px; color: #15803d; font-weight: 600; text-transform: uppercase;">Tổng doanh thu thực thu</span>
                <strong style="font-size: 22px; color: #166534; display: block; margin-top: 4px;">{{ number_format($stats['totalRevenue'], 0, ',', '.') }}đ</strong>
            </div>
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <span style="font-size: 12px; color: #1d4ed8; font-weight: 600; text-transform: uppercase;">Lớp học trực tiếp</span>
                <strong style="font-size: 22px; color: #1e40af; display: block; margin-top: 4px;">{{ number_format($stats['classroom']) }} đơn</strong>
            </div>
            <div style="background: #f5f3ff; border: 1px solid #ddd6fe; border-radius: 10px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <span style="font-size: 12px; color: #6d28d9; font-weight: 600; text-transform: uppercase;">Khóa học Online & Combo</span>
                <strong style="font-size: 22px; color: #5b21b6; display: block; margin-top: 4px;">{{ number_format($stats['online']) }} đơn</strong>
            </div>
            <div style="background: {{ $stats['pending'] > 0 ? '#fffbeb' : '#f8fafc' }}; border: 1px solid {{ $stats['pending'] > 0 ? '#fde68a' : '#e2e8f0' }}; border-radius: 10px; padding: 14px 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <span style="font-size: 12px; color: {{ $stats['pending'] > 0 ? '#b45309' : '#64748b' }}; font-weight: 600; text-transform: uppercase;">Chờ xác nhận</span>
                <strong style="font-size: 22px; color: {{ $stats['pending'] > 0 ? '#b45309' : '#475569' }}; display: block; margin-top: 4px;">{{ number_format($stats['pending']) }} đơn</strong>
            </div>
        </div>
    @endif

    {{-- Bộ lọc & Tìm kiếm --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
        <form method="GET" action="{{ route('shop_admin.orders.index') }}" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                <a href="{{ route('shop_admin.orders.index', array_merge(request()->except(['status', 'page']))) }}" 
                   style="padding: 7px 14px; border-radius: 7px; font-size: 13px; font-weight: 600; text-decoration: none; {{ !request('status') ? 'background: #2563eb; color: #ffffff;' : 'background: #f1f5f9; color: #475569;' }}">
                    Tất cả ({{ $stats['all'] ?? 0 }})
                </a>
                <a href="{{ route('shop_admin.orders.index', array_merge(request()->except(['page']), ['status' => 'pending'])) }}" 
                   style="padding: 7px 14px; border-radius: 7px; font-size: 13px; font-weight: 600; text-decoration: none; {{ request('status') === 'pending' ? 'background: #f59e0b; color: #ffffff;' : 'background: #f1f5f9; color: #475569;' }}">
                    ⏳ Chờ duyệt ({{ $stats['pending'] ?? 0 }})
                </a>
                <a href="{{ route('shop_admin.orders.index', array_merge(request()->except(['page']), ['status' => 'paid'])) }}" 
                   style="padding: 7px 14px; border-radius: 7px; font-size: 13px; font-weight: 600; text-decoration: none; {{ request('status') === 'paid' ? 'background: #059669; color: #ffffff;' : 'background: #f1f5f9; color: #475569;' }}">
                    ✓ Đã thanh toán ({{ $stats['paid'] ?? 0 }})
                </a>

                <span style="color: #cbd5e1; margin: 0 4px;">|</span>

                <a href="{{ route('shop_admin.orders.index', array_merge(request()->except(['type', 'page']))) }}" 
                   style="padding: 7px 12px; border-radius: 7px; font-size: 12.5px; font-weight: 600; text-decoration: none; {{ (!request('type') || request('type') === 'all') ? 'background: #334155; color: #ffffff;' : 'background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;' }}">
                    Tất cả nguồn
                </a>
                <a href="{{ route('shop_admin.orders.index', array_merge(request()->except(['page']), ['type' => 'classroom'])) }}" 
                   style="padding: 7px 12px; border-radius: 7px; font-size: 12.5px; font-weight: 600; text-decoration: none; {{ request('type') === 'classroom' ? 'background: #1d4ed8; color: #ffffff;' : 'background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;' }}">
                    🏫 Lớp trực tiếp ({{ $stats['classroom'] ?? 0 }})
                </a>
                <a href="{{ route('shop_admin.orders.index', array_merge(request()->except(['page']), ['type' => 'online'])) }}" 
                   style="padding: 7px 12px; border-radius: 7px; font-size: 12.5px; font-weight: 600; text-decoration: none; {{ request('type') === 'online' ? 'background: #6d28d9; color: #ffffff;' : 'background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0;' }}">
                    💻 Khóa Online ({{ $stats['online'] ?? 0 }})
                </a>
            </div>

            <div style="display: flex; gap: 8px; align-items: center; min-width: 280px; flex-grow: 1; max-width: 400px;">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('type'))
                    <input type="hidden" name="type" value="{{ request('type') }}">
                @endif
                <input type="text" name="q" value="{{ request('q', $keyword ?? '') }}" placeholder="Tìm mã đơn, học viên, email, khóa học..." style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 7px; font-size: 13.5px;">
                <button type="submit" class="button" style="padding: 8px 16px; font-size: 13px; font-weight: 600; white-space: nowrap;">
                    Tìm kiếm
                </button>
                @if(request('q'))
                    <a href="{{ route('shop_admin.orders.index', request()->except('q')) }}" style="color: #ef4444; font-size: 13px; text-decoration: none; white-space: nowrap;">
                        ✕ Xóa tìm
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="admin-table-wrap" style="box-shadow: 0 2px 8px rgba(0,0,0,0.03); border-radius: 10px;">
        <table class="admin-table admin-orders-table">
            <thead>
                <tr>
                    <th style="width: 250px; min-width: 230px; text-align: left;">Mã đơn hàng</th>
                    <th style="min-width: 200px; text-align: left;">Người mua & Liên hệ</th>
                    <th style="min-width: 220px; text-align: left;">Khóa học đặt mua</th>
                    <th style="width: 130px;">Tổng tiền</th>
                    <th style="width: 150px;">Hình thức & Nguồn</th>
                    <th style="width: 130px; text-align: center;">Trạng thái</th>
                    <th style="width: 160px; text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td style="text-align: left; vertical-align: middle;">
                            <a href="{{ route('shop_admin.orders.show', $order->number) }}" 
                               title="Xem chi tiết đơn hàng {{ $order->number }}"
                               style="color: #2563eb; font-size: 13px; font-weight: 700; font-family: 'JetBrains Mono', monospace; text-decoration: none; word-break: break-all; display: block; line-height: 1.4;">
                                {{ $order->number }}
                            </a>
                            <div style="display: flex; align-items: center; gap: 6px; margin-top: 4px; flex-wrap: wrap;">
                                <span style="font-size: 11.5px; color: #94a3b8; display: inline-flex; align-items: center; gap: 3px;">
                                    <i class="bi bi-clock"></i>
                                    {{ $order->created_at?->copy()->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                                </span>
                                @if($order->enrollment_id)
                                    <span style="font-size: 11px; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 4px; font-weight: 600;">
                                        {{ $order->payment?->reference ?? ('HD-' . $order->enrollment_id) }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td style="text-align: left; vertical-align: middle;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="width: 36px; height: 36px; border-radius: 50%; background: #eff6ff; color: #2563eb; display: grid; place-items: center; font-weight: 700; font-size: 13px; flex-shrink: 0; border: 1px solid #dbeafe;">
                                    {{ mb_strtoupper(mb_substr($order->buyer_name ?? 'U', 0, 1, 'UTF-8'), 'UTF-8') }}
                                </div>
                                <div style="min-width: 0;">
                                    <strong style="color: #0f172a; font-size: 13.5px; display: block; line-height: 1.4; font-weight: 600;">
                                        {{ $order->buyer_name }}
                                    </strong>
                                    <span style="font-size: 12px; color: #64748b; display: block; line-height: 1.4; word-break: break-all;">
                                        {{ $order->buyer_email }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: left; vertical-align: middle;">
                            @if($order->items && $order->items->isNotEmpty())
                                @foreach($order->items as $item)
                                    <div style="font-size: 13px; color: #1e293b; font-weight: 600; line-height: 1.4;">
                                        <i class="bi bi-journal-bookmark text-primary me-1"></i>
                                        {{ $item->course_name }}
                                    </div>
                                    @if($order->enrollment && $order->enrollment->classRoom)
                                        <div style="font-size: 11.5px; color: #64748b; margin-top: 2px;">
                                            🏫 Lớp: {{ $order->enrollment->classRoom->name }} ({{ $order->enrollment->classRoom->code }})
                                        </div>
                                    @endif
                                @endforeach
                            @else
                                <span style="color: #94a3b8; font-size: 12.5px;">Khóa học trung tâm</span>
                            @endif
                        </td>
                        <td style="vertical-align: middle;">
                            <strong style="color: #0f172a; font-size: 15px; font-weight: 700; font-family: monospace;">
                                {{ number_format($order->total, 0, ',', '.') }}đ
                            </strong>
                        </td>
                        <td style="vertical-align: middle;">
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span style="font-size: 12.5px; color: #334155; display: inline-flex; align-items: center; gap: 4px;">
                                    {{ $order->payment?->method === 'bank_transfer' ? '🏦 QR Ngân hàng / CK' : '💵 Tiền mặt' }}
                                </span>
                                @if($order->enrollment_id)
                                    <span style="font-size: 11px; background: #eff6ff; color: #1d4ed8; padding: 2px 7px; border-radius: 4px; display: inline-block; font-weight: 600; border: 1px solid #dbeafe;">
                                        Lớp trực tiếp
                                    </span>
                                @else
                                    <span style="font-size: 11px; background: #faf5ff; color: #7e22ce; padding: 2px 7px; border-radius: 4px; display: inline-block; font-weight: 600; border: 1px solid #f3e8ff;">
                                        Online E-Learning
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if($order->status === 'paid')
                                <span style="background: #dcfce7; color: #15803d; font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 6px; display: inline-block;">
                                    ✓ Đã thanh toán
                                </span>
                            @else
                                <span style="background: #fef3c7; color: #b45309; font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 6px; display: inline-block;">
                                    ⏳ Chờ xác nhận
                                </span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap; vertical-align: middle;">
                            @if($order->status !== 'paid')
                                <form method="POST" action="{{ route('shop_admin.orders.confirm', $order->number) }}" style="display: inline-block; margin-right: 6px;" onsubmit="return confirm('Xác nhận đã nhận đủ {{ number_format($order->total, 0, ',', '.') }}đ và DUYỆT ĐƠN NGAY cho học viên {{ $order->buyer_name }}? Khóa học sẽ được cấp ngay vào tài khoản.');">
                                    @csrf
                                    <input type="hidden" name="verified" value="1">
                                    <button type="submit" class="button button-small" style="background: #059669; border-color: #059669; color: #ffffff; padding: 5px 12px; font-size: 12.5px; font-weight: 700; cursor: pointer; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                        ✓ Duyệt
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('shop_admin.orders.show', $order->number) }}" class="button button-small button-outline" style="padding: 5px 12px; font-size: 12.5px; text-decoration: none;">
                                Chi tiết →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px; color: #94a3b8;">
                            Không tìm thấy đơn hàng nào phù hợp với điều kiện tìm kiếm.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('learning.shop.pager', ['records' => $orders])
</section>
@endsection
