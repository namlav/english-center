<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['paid', 'unpaid'])],
            'day' => ['nullable', 'integer', 'between:1,31'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'total_desc', 'total_asc'])],
        ]);
        $keyword = trim($filters['keyword'] ?? '');
        $status = $filters['status'] ?? '';
        $sort = $filters['sort'] ?? 'newest';

        $query = Enrollment::with(['student.user', 'classRoom.course', 'classRoom.teacher.user'])
            ->when($keyword !== '', function (Builder $query) use ($keyword): void {
                $query->where(function (Builder $search) use ($keyword): void {
                    $search->whereHas('student.user', fn (Builder $user): Builder => $user->where('name', 'like', '%'.$keyword.'%'))
                        ->orWhereHas('student', fn (Builder $student): Builder => $student->where('student_code', 'like', '%'.$keyword.'%'))
                        ->orWhereHas('classRoom', fn (Builder $classRoom): Builder => $classRoom
                            ->where('code', 'like', '%'.$keyword.'%')
                            ->orWhere('name', 'like', '%'.$keyword.'%'))
                        ->orWhereHas('classRoom.teacher.user', fn (Builder $teacher): Builder => $teacher->where('name', 'like', '%'.$keyword.'%'));

                    if (preg_match('/^HD[-\s]?(?:\d{4}[-\s]?)?0*(\d+)$/i', $keyword, $matches)) {
                        $search->orWhereKey((int) $matches[1]);
                    }
                });
            })
            ->when($status !== '', fn (Builder $query): Builder => $query->where('payment_status', $status))
            ->when(! empty($filters['day']), fn (Builder $query): Builder => $query->whereDay('created_at', (int) $filters['day']))
            ->when(! empty($filters['month']), fn (Builder $query): Builder => $query->whereMonth('created_at', (int) $filters['month']))
            ->when(! empty($filters['year']), fn (Builder $query): Builder => $query->whereYear('created_at', (int) $filters['year']));

        match ($sort) {
            'oldest' => $query->oldest('created_at')->oldest('id'),
            'total_desc' => $query->orderByDesc('paid_tuition')->latest('id'),
            'total_asc' => $query->orderBy('paid_tuition')->latest('id'),
            default => $query->latest('created_at')->latest('id'),
        };

        $invoices = $query->paginate(15)->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'html' => view('invoices._results', compact('invoices'))->render(),
                'total' => $invoices->total(),
            ]);
        }

        $summary = [
            'total' => Enrollment::count(),
            'paid' => Enrollment::where('payment_status', 'paid')->count(),
            'unpaid' => Enrollment::where('payment_status', 'unpaid')->count(),
        ];

        $firstInvoice = Enrollment::min('created_at');
        $lastInvoice = Enrollment::max('created_at');
        $years = $firstInvoice && $lastInvoice
            ? range((int) date('Y', strtotime($lastInvoice)), (int) date('Y', strtotime($firstInvoice)))
            : [];

        return view('invoices.index', compact('invoices', 'summary', 'years', 'keyword', 'status', 'sort'));
    }

    public function show(Enrollment $invoice): View
    {
        $invoice->load(['student.user', 'classRoom.course', 'classRoom.teacher.user']);

        return view('invoices.show', compact('invoice'));
    }

    public function markPaid(Request $request, Enrollment $invoice): \Illuminate\Http\RedirectResponse
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        $classRoom = $invoice->classRoom;
        $tuition = (float) ($classRoom?->tuition_fee ?? $invoice->paid_tuition);

        $invoice->update([
            'payment_status' => 'paid',
            'paid_tuition' => $tuition > 0 ? $tuition : $invoice->paid_tuition,
            'result_status' => 'studying',
        ]);

        return back()->with('success', 'Đã duyệt thanh toán thành công cho hồ sơ '.$invoice->invoice_code.' ('.number_format($tuition, 0, ',', '.').' ₫). Học viên đã được kích hoạt nhập học chính thức.');
    }
}
