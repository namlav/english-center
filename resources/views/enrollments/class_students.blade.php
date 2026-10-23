@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Danh sách học viên — ' . $classRoom->code)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <a href="{{ route(auth()->user()->isAdmin() ? 'class_rooms.show' : 'teacher.classes', $classRoom) }}" class="small text-decoration-none">
            <i class="bi bi-arrow-left me-1"></i> Quay lại chi tiết lớp
        </a>
        <h1 class="h3 fw-bold mt-2 mb-1 text-dark">{{ $classRoom->name }}</h1>
        <div class="text-secondary fs-7">
            <span class="badge bg-primary-subtle text-primary font-monospace">{{ $classRoom->code }}</span>
            <span class="mx-1">·</span>{{ $classRoom->course->name }}
            <span class="mx-1">·</span>Giảng viên: <strong class="text-dark">{{ $classRoom->teacher->user->name ?? 'N/A' }}</strong>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('attendances.index', $classRoom) }}">
            <i class="bi bi-calendar-check me-1"></i> Điểm danh
        </a>
        <a class="btn btn-outline-warning" href="{{ route('grades.index', $classRoom) }}">
            <i class="bi bi-award me-1"></i> Nhập điểm
        </a>
    </div>
</div>

{{-- NẾU LÀ ADMIN VÀ LỚP CHƯA ĐẦY: FORM ĐĂNG KÝ HỌC VIÊN NHANH --}}
@if(auth()->check() && auth()->user()->isAdmin() && isset($availableStudents) && $availableStudents->isNotEmpty() && $classRoom->enrollments->count() < $classRoom->max_students)
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <strong class="text-dark"><i class="bi bi-person-plus-fill text-success me-1"></i> Đăng ký Học viên vào Lớp học này</strong>
            <span class="badge bg-success-subtle text-success border">
                Còn trống {{ max(0, $classRoom->max_students - $classRoom->enrollments->count()) }} chỗ
            </span>
        </div>
        <div class="card-body p-3">
            <form action="{{ route('enrollments.admin_enroll', $classRoom) }}" method="POST" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-6 col-lg-7">
                    <label for="student_id" class="form-label fs-8 fw-semibold text-secondary">Chọn học viên ghi danh</label>
                    <select name="student_id" id="student_id" class="form-select form-select-sm" required>
                        <option value="">-- Chọn học viên từ hệ thống --</option>
                        @foreach($availableStudents as $st)
                            <option value="{{ $st->id }}">
                                [{{ $st->student_code }}] {{ $st->user->name ?? 'N/A' }} — {{ $st->user->email ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-3">
                    <label for="paid_tuition" class="form-label fs-8 fw-semibold text-secondary">Học phí thu (₫)</label>
                    <input type="number" step="1000" min="0" name="paid_tuition" id="paid_tuition" class="form-control form-select-sm" value="{{ (int)$classRoom->tuition_fee }}">
                </div>
                <div class="col-md-3 col-lg-2">
                    <button type="submit" class="btn btn-sm btn-success w-100 fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> Đăng ký
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <strong class="text-dark fs-7"><i class="bi bi-people-fill text-primary me-1"></i> Danh sách học viên lớp</strong>
            <span class="badge bg-light text-dark border ms-2">{{ $classRoom->enrollments->count() }}/{{ $classRoom->max_students }} học viên</span>
        </div>
        <small class="text-muted">Lịch: {{ $classRoom->schedule }}</small>
    </div>
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle mb-0">
            <thead class="table-light fs-8 text-uppercase text-secondary">
                <tr>
                    <th class="ps-3 text-center" style="width: 50px;">#</th>
                    <th>Học viên</th>
                    <th style="width: 120px;">Mã học viên</th>
                    <th>Email</th>
                    <th>Ngày đăng ký</th>
                    <th>Điểm danh</th>
                    <th>Điểm số</th>
                    <th class="text-center" style="width: 110px;">Kết quả</th>
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <th class="text-center pe-3" style="width: 80px;">Thao tác</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($classRoom->enrollments as $enrollment)
                    @php
                        $student = $enrollment->student;
                        $counts = $attendanceCounts->get($student->id);
                    @endphp
                    <tr>
                        <td class="ps-3 text-center text-muted fw-bold font-monospace">{{ $loop->iteration }}</td>
                        <td>
                            <strong class="text-dark">{{ $student->user->name ?? 'N/A' }}</strong>
                            @if(!empty($student->user->phone))
                                <div class="text-muted fs-8">{{ $student->user->phone }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $student->student_code ?? 'N/A' }}</span>
                        </td>
                        <td>{{ $student->user->email ?? '—' }}</td>
                        <td>{{ $enrollment->enrollment_date ? \Carbon\Carbon::parse($enrollment->enrollment_date)->format('d/m/Y') : '—' }}</td>
                        <td>
                            <span class="text-success fw-semibold">{{ (int) ($counts->present_count ?? 0) }} có mặt</span>
                            <div class="small text-muted">{{ (int) ($counts->absent_count ?? 0) }} vắng · {{ (int) ($counts->late_count ?? 0) }} trễ</div>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $enrollment->midterm_score ?? '—' }}</span> / 
                            <span class="fw-semibold">{{ $enrollment->final_score ?? '—' }}</span>
                        </td>
                        <td class="text-center">
                            @php
                                $resultMap = [
                                    'studying' => ['Đang học', 'bg-info text-dark'],
                                    'passed' => ['Đạt', 'bg-success'],
                                    'failed' => ['Chưa đạt', 'bg-danger'],
                                ];
                                [$resLabel, $resBadge] = $resultMap[$enrollment->result_status] ?? ['Đang học', 'bg-secondary'];
                            @endphp
                            <span class="badge {{ $resBadge }}">{{ $resLabel }}</span>
                        </td>
                        @if(auth()->check() && auth()->user()->isAdmin())
                            <td class="text-center pe-3">
                                <form action="{{ route('enrollments.admin_remove', [$classRoom, $enrollment]) }}" method="POST"
                                      onsubmit="return confirm('Bạn có chắc chắn muốn hủy ghi danh của học viên {{ $student->user->name ?? $student->student_code }} khỏi lớp này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hủy ghi danh khỏi lớp">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 9 : 8 }}" class="text-center py-5 text-secondary">
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                            Lớp chưa có học viên đăng ký.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
