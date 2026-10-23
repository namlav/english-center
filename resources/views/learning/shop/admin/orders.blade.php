@extends('layouts.learning')
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

    @include('learning.admin.nav')

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
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 140px;">Mã đơn hàng</th>
                    <th>Người mua & Liên hệ</th>
                    <th>Số khóa</th>
                    <th>Tổng tiền</th>
                    <th>Hình thức</th>
                    <th style="text-align: center;">Trạng thái</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong style="color: #2563eb; font-size: 13.5px; font-family: monospace;">{{ $order->number }}</strong>
                            <span style="font-size: 11.5px; color: #94a3b8; display: block;">
                                {{ $order->created_at?->copy()->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}
                            </span>
                        </td>
                        <td>
                            <strong style="color: #0f172a; font-size: 14px; display: block;">{{ $order->buyer_name }}</strong>
                            <span style="font-size: 12px; color: #64748b;">{{ $order->buyer_email }}</span>
                        </td>
                        <td>
                            <span style="background: #f1f5f9; color: #475569; font-size: 12px; font-weight: 600; padding: 3px 8px; border-radius: 4px;">
                                {{ $order->items_count }} khóa học
                            </span>
                        </td>
                        <td>
                            <strong style="color: #0f172a; font-size: 15px;">{{ number_format($order->total, 0, ',', '.') }}đ</strong>
                        </td>
                        <td>
                            <span style="font-size: 12.5px; color: #475569; display: flex; align-items: center; gap: 4px;">
                                {{ $order->payment?->method === 'bank_transfer' ? '🏦 QR Ngân hàng' : 'Mô phỏng cũ' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
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
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('shop_admin.orders.show', $order->number) }}" class="button button-small" style="padding: 5px 12px; font-size: 12.5px; text-decoration: none;">
                                Xem & Duyệt đơn →
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
