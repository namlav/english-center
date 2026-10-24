<section class="card">
    <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2 py-3 px-4">
        <div>
            <h2 class="h5 fw-bold mb-1">Danh sách hóa đơn</h2>
            <p class="text-secondary small mb-0">
                Hiển thị {{ $invoices->firstItem() ?? 0 }}–{{ $invoices->lastItem() ?? 0 }} trong {{ number_format($invoices->total()) }} hóa đơn.
            </p>
        </div>
        <span class="invoice-total-label"><i class="bi bi-receipt me-1"></i> Đơn vị: VNĐ</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Mã hóa đơn</th>
                    <th>Thời gian tạo</th>
                    <th>Học viên</th>
                    <th>Giáo viên phụ trách</th>
                    <th>Lớp học</th>
                    <th>Voucher</th>
                    <th class="text-end">Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($invoices as $invoice)
                @php($classRoom = $invoice->classRoom)
                <tr>
                    <td>
                        <a href="{{ route('invoices.show', $invoice) }}" class="invoice-code">{{ $invoice->invoice_code }}</a>
                    </td>
                    <td class="text-nowrap">
                        <div>{{ $invoice->created_at?->format('d/m/Y') ?? '—' }}</div>
                        <div class="small text-secondary">{{ $invoice->created_at?->format('H:i') ?? '' }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $invoice->student?->user?->name ?? '—' }}</div>
                        <div class="small text-secondary">{{ $invoice->student?->student_code }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $classRoom?->teacher?->user?->name ?? '—' }}</div>
                        <div class="small text-secondary">{{ $classRoom?->teacher?->teacher_code }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold">{{ $classRoom?->name ?? '—' }}</div>
                        <div class="small text-secondary">{{ $classRoom?->code }}@if($classRoom?->course) · {{ $classRoom->course->name }}@endif</div>
                    </td>
                    <td><span class="text-secondary small">Không có</span></td>
                    <td class="text-end fw-semibold text-nowrap">{{ number_format($invoice->invoice_total, 0, ',', '.') }} ₫</td>
                    <td>
                        @if($invoice->payment_status === 'paid')
                            <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i>Đã thanh toán</span>
                        @else
                            <span class="badge text-bg-warning"><i class="bi bi-clock me-1"></i>Chưa thanh toán</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        @if($invoice->payment_status === 'unpaid')
                            <form action="{{ route('invoices.mark_paid', $invoice) }}" method="POST" class="d-inline" onsubmit="return confirm('Xác nhận duyệt thu học phí cho học viên {{ $invoice->student?->user?->name }}?');">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success fw-bold text-nowrap me-1 shadow-sm">
                                    <i class="bi bi-check-circle me-1"></i>Duyệt đơn
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary text-nowrap">
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
