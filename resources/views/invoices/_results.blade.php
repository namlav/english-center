<section class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-4">
        <div>
            <h2 class="h5 fw-bold mb-1">Danh sách hóa đơn</h2>
            <p class="text-secondary small mb-0">
                Hiển thị {{ $invoices->firstItem() ?? 0 }}–{{ $invoices->lastItem() ?? 0 }} trong tổng số {{ number_format($invoices->total()) }} hóa đơn.
            </p>
        </div>
        <span class="invoice-total-label"><i class="bi bi-receipt me-1"></i> Đơn vị: VNĐ</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="fs-8 text-uppercase text-secondary">
                    <th style="min-width: 170px;">Mã hóa đơn</th>
                    <th style="min-width: 110px;">Thời gian tạo</th>
                    <th style="min-width: 170px;">Học viên</th>
                    <th style="min-width: 160px;">Phụ trách / Nguồn</th>
                    <th style="min-width: 220px;">Lớp học / Khóa học</th>
                    <th>Voucher</th>
                    <th class="text-end" style="min-width: 130px;">Tổng tiền</th>
                    <th style="min-width: 140px;">Thanh toán</th>
                    <th class="text-end pe-3" style="min-width: 110px;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            @forelse($invoices as $invoice)
                <tr>
                    <td>
                        <a href="{{ $invoice->detail_url }}" class="invoice-code d-block">
                            {{ $invoice->invoice_code }}
                        </a>
                        <div class="mt-1">
                            <span class="badge {{ $invoice->type_badge_class }}" style="font-size: 10.5px; font-weight: 600;">
                                {{ $invoice->type_label }}
                            </span>
                        </div>
                    </td>
                    <td class="text-nowrap">
                        <div class="fw-semibold">{{ $invoice->created_at?->format('d/m/Y') ?? '—' }}</div>
                        <div class="small text-secondary">{{ $invoice->created_at?->format('H:i') ?? '' }}</div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark">{{ $invoice->customer_name }}</div>
                        <div class="small text-secondary">{{ $invoice->customer_sub }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $invoice->teacher_name }}</div>
                        <div class="small text-secondary">{{ $invoice->teacher_sub }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $invoice->target_name }}</div>
                        <div class="small text-secondary">{{ $invoice->target_sub }}</div>
                    </td>
                    <td><span class="text-secondary small">Không có</span></td>
                    <td class="text-end fw-bold text-nowrap font-monospace text-dark fs-7">
                        {{ number_format($invoice->total, 0, ',', '.') }} ₫
                    </td>
                    <td>
                        @if($invoice->payment_status === 'paid')
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                <i class="bi bi-check-circle-fill me-1"></i>Đã thanh toán
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill px-2 py-1">
                                <i class="bi bi-clock-fill me-1"></i>Chờ xác nhận
                            </span>
                        @endif
                        <div class="small text-muted mt-1" style="font-size: 11.5px;">{{ $invoice->payment_method }}</div>
                    </td>
                    <td class="text-end text-nowrap pe-3">
                        @if($invoice->can_mark_paid)
                            <form action="{{ $invoice->mark_paid_url }}" method="POST" class="d-inline" onsubmit="return confirm('Xác nhận duyệt thanh toán cho {{ $invoice->customer_name }}?');">
                                @csrf
                                @if($invoice->type === 'online_course')
                                    <input type="hidden" name="verified" value="1">
                                @endif
                                <button type="submit" class="btn btn-sm btn-success fw-bold text-nowrap me-1 shadow-sm">
                                    <i class="bi bi-check-circle me-1"></i>Duyệt
                                </button>
                            </form>
                        @endif
                        <a href="{{ $invoice->detail_url }}" class="btn btn-sm btn-outline-primary text-nowrap">
                            <i class="bi bi-eye me-1"></i>Chi tiết
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="bi bi-receipt-cutoff fs-2 text-secondary d-block mb-2"></i>
                        <span class="text-secondary">Không tìm thấy hóa đơn phù hợp.</span>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    @if($invoices->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $invoices->links('pagination::bootstrap-5') }}
        </div>
    @endif
</section>
