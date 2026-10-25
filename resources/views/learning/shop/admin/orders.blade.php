@extends('layouts.admin')
@section('title', 'Quản lý Đơn hàng & Kích hoạt Khóa học')
@section('content')
<section class="shell admin-page">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 22px;">
        <div>
            <span class="eyebrow" style="color: #2563eb; font-weight: 700; font-size: 13px; text-transform: uppercase;">BÁN HÀNG & THANH TOÁN</span>
            <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 4px 0 0;">Quản lý Đơn hàng & Thanh toán</h1>
            <p style="color: #64748b; font-size: 14px; margin: 4px 0 0;">Kiểm tra giao dịch chuyển khoản VietQR, đối soát hóa đơn và kích hoạt khóa học cho học viên.</p>
        </div>
        <a class="button button-outline" href="{{ route('shop_admin.bank.edit') }}" style="text-decoration: none; font-weight: 600;">
            ⚙️ Cài đặt VietQR Ngân hàng
        </a>
    </div>

    {{-- Bộ lọc trạng thái --}}
    <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">
        <a href="{{ route('shop_admin.orders.index') }}" 
           style="padding: 8px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 600; text-decoration: none; {{ !request('status') ? 'background: #2563eb; color: #ffffff;' : 'background: #f1f5f9; color: #475569;' }}">
            Tất cả đơn hàng
        </a>
        <a href="{{ route('shop_admin.orders.index', ['status' => 'pending']) }}" 
           style="padding: 8px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 600; text-decoration: none; {{ request('status') === 'pending' ? 'background: #f59e0b; color: #ffffff;' : 'background: #f1f5f9; color: #475569;' }}">
            ⏳ Chờ thanh toán / xác nhận
        </a>
        <a href="{{ route('shop_admin.orders.index', ['status' => 'paid']) }}" 
           style="padding: 8px 16px; border-radius: 8px; font-size: 13.5px; font-weight: 600; text-decoration: none; {{ request('status') === 'paid' ? 'background: #059669; color: #ffffff;' : 'background: #f1f5f9; color: #475569;' }}">
            ✓ Đã thanh toán / kích hoạt
        </a>
    </div>

    <div class="admin-table-wrap" style="box-shadow: 0 2px 8px rgba(0,0,0,0.03); border-radius: 10px;">
        <table class="admin-table admin-orders-table">
            <thead>
                <tr>
                    <th style="width: 250px; min-width: 230px; text-align: left;">Mã đơn hàng</th>
                    <th style="min-width: 210px; text-align: left;">Người mua & Liên hệ</th>
                    <th style="width: 110px; text-align: center;">Số khóa</th>
                    <th style="width: 130px;">Tổng tiền</th>
                    <th style="width: 140px;">Hình thức</th>
                    <th style="width: 130px; text-align: center;">Trạng thái</th>
                    <th style="width: 170px; text-align: right;">Thao tác</th>
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
                            <span style="font-size: 11.5px; color: #94a3b8; display: inline-flex; align-items: center; gap: 4px; margin-top: 4px;">
                                <i class="bi bi-clock"></i>
                                {{ $order->created_at?->copy()->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                            </span>
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
                        <td style="text-align: center; vertical-align: middle;">
                            <span style="background: #f1f5f9; color: #475569; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px; display: inline-block;">
                                {{ $order->items_count }} khóa học
                            </span>
                        </td>
                        <td style="vertical-align: middle;">
                            <strong style="color: #0f172a; font-size: 15px; font-weight: 700;">{{ number_format($order->total, 0, ',', '.') }}đ</strong>
                        </td>
                        <td style="vertical-align: middle;">
                            <span style="font-size: 12.5px; color: #475569; display: inline-flex; align-items: center; gap: 6px;">
                                {{ $order->payment?->method === 'bank_transfer' ? '🏦 QR Ngân hàng' : 'Mô phỏng cũ' }}
                            </span>
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if($order->status === 'paid')
                                <span style="background: #dcfce7; color: #15803d; font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 6px; display: inline-block;">
                                    ✓ Đã kích hoạt
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
                                        ✓ Duyệt đơn ngay
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
                            Không có đơn hàng nào trong mục này.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('learning.shop.pager', ['records' => $orders])
</section>
@endsection
