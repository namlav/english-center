@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Chi tiết lớp học — ' . $classRoom->code)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-primary-subtle text-primary font-monospace fs-7 px-2 py-1">{{ $classRoom->code }}</span>
            <span class="badge {{ $classRoom->status === 'ongoing' ? 'bg-success' : ($classRoom->status === 'upcoming' ? 'bg-info text-dark' : 'bg-secondary') }}">
                {{ \App\Models\ClassRoom::STATUSES[$classRoom->status] ?? $classRoom->status }}
            </span>
        </div>
        <h2 class="fw-bold mb-0 text-dark">{{ $classRoom->name }}</h2>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('class_rooms.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Về danh sách lớp
        </a>
        <a href="{{ route('enrollments.class_students', $classRoom) }}" class="btn btn-outline-primary">
            <i class="bi bi-people me-1"></i> Sổ điểm danh & Kết quả
        </a>
        @if(auth()->check() && auth()->user()->isAdmin())
            <a href="{{ route('class_rooms.edit', $classRoom) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i> Sửa lớp học
            </a>
        @endif
    </div>
</div>

{{-- 4 THẺ CHỈ SỐ LỚP HỌC --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body">
                <div class="text-secondary fs-8 text-uppercase fw-semibold mb-1">Học phí niêm yết</div>
                <div class="fs-4 fw-bold text-primary">{{ number_format($classRoom->tuition_fee, 0, ',', '.') }} ₫</div>
                <small class="text-muted fs-8">Mức thu tiêu chuẩn cho mỗi học viên</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-secondary fs-8 text-uppercase fw-semibold">Sĩ số lớp học</span>
                    <span class="badge {{ $classRoom->enrollments_count >= $classRoom->max_students ? 'bg-danger' : 'bg-primary' }}">
                        {{ $classRoom->enrollments_count }}/{{ $classRoom->max_students }}
                    </span>
                </div>
                <div class="fs-4 fw-bold text-dark">
                    {{ $classRoom->enrollments_count }} <span class="fs-7 fw-normal text-muted">/ {{ $classRoom->max_students }} học viên</span>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    @php
                        $percent = min(100, round(($classRoom->enrollments_count / max(1, $classRoom->max_students)) * 100));
                    @endphp
                    <div class="progress-bar {{ $percent >= 100 ? 'bg-danger' : ($percent >= 80 ? 'bg-warning' : 'bg-primary') }}" 
                         role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body">
                <div class="text-secondary fs-8 text-uppercase fw-semibold mb-1">Ngày khai giảng</div>
                <div class="fs-4 fw-bold text-dark">{{ $classRoom->start_date->format('d/m/Y') }}</div>
                <small class="text-muted fs-8">Lịch khai giảng chính thức</small>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body">
                <div class="text-secondary fs-8 text-uppercase fw-semibold mb-1">Dự kiến kết thúc</div>
                <div class="fs-4 fw-bold text-dark">{{ $classRoom->expected_end_date?->format('d/m/Y') ?? 'Chưa cập nhật' }}</div>
                <small class="text-muted fs-8">Thời lượng chương trình đào tạo</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- THÔNG TIN ĐÀO TẠO & LỊCH HỌC --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <strong class="text-dark"><i class="bi bi-info-circle text-primary me-1"></i> Thông tin Đào tạo & Giảng viên</strong>
            </div>
            <div class="card-body p-4">
                <dl class="row mb-0">
                    <dt class="col-sm-4 text-secondary fw-normal">Khóa học:</dt>
                    <dd class="col-sm-8 fw-semibold">
                        @if(auth()->check() && auth()->user()->isAdmin())
                            <a href="{{ route('courses.show', $classRoom->course) }}" class="text-decoration-none">
                                {{ $classRoom->course->name }} <i class="bi bi-box-arrow-up-right fs-8"></i>
                            </a>
                        @else
                            {{ $classRoom->course->name }}
                        @endif
                    </dd>

                    <dt class="col-sm-4 text-secondary fw-normal">Giảng viên:</dt>
                    <dd class="col-sm-8">
                        @if($classRoom->teacher)
                            <strong class="text-dark">{{ $classRoom->teacher->user->name ?? 'N/A' }}</strong>
                            <span class="badge bg-light text-secondary border font-monospace ms-1">{{ $classRoom->teacher->teacher_code }}</span>
                            <div class="text-muted fs-8 mt-1">{{ $classRoom->teacher->specialization }}</div>
                        @else
                            <span class="text-muted fst-italic">Chưa phân công giảng viên</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4 text-secondary fw-normal">Thời lượng:</dt>
                    <dd class="col-sm-8">{{ $classRoom->course->duration_hours ?? 0 }} giờ học</dd>

                    <dt class="col-sm-4 text-secondary fw-normal">Lịch học chi tiết:</dt>
                    <dd class="col-sm-8 mb-0">
                        <div class="p-2 bg-light rounded-2 border fs-7 font-monospace" style="white-space: pre-wrap;">{{ $classRoom->schedule }}</div>
                    </dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- KHỐI ĐĂNG KÝ HỌC VIÊN MỚI VÀO LỚP (DÀNH CHO ADMIN) --}}
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <strong class="text-dark"><i class="bi bi-person-plus-fill text-success me-1"></i> Đăng ký Học viên vào Lớp này</strong>
                <span class="badge bg-light text-dark border fs-8">
                    Chỗ trống còn lại: {{ max(0, $classRoom->max_students - $classRoom->enrollments_count) }}
                </span>
            </div>
            <div class="card-body p-4">
                @if(auth()->check() && auth()->user()->isAdmin())
                    @if($classRoom->enrollments_count >= $classRoom->max_students)
                        <div class="alert alert-warning d-flex align-items-center gap-2 mb-0">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                            <div>
                                <strong>Lớp đã đạt sĩ số tối đa!</strong>
                                <div class="fs-8">Hiện lớp đã đủ {{ $classRoom->max_students }} học viên. Hãy tăng sĩ số tối đa nếu muốn nhận thêm.</div>
                            </div>
                        </div>
                    @else
                        <p class="text-secondary fs-8 mb-3">
                            Chọn học viên từ danh sách học viên của trung tâm để ghi danh trực tiếp vào lớp học này.
                        </p>
                        <form action="{{ route('enrollments.admin_enroll', $classRoom) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="student_id" class="form-label fw-semibold fs-7">Chọn học viên cần đăng ký <span class="text-danger">*</span></label>
                                <select name="student_id" id="student_id" class="form-select" required>
                                    <option value="">-- Tìm và chọn học viên --</option>
                                    @foreach($availableStudents as $st)
                                        <option value="{{ $st->id }}">
                                            [{{ $st->student_code }}] {{ $st->user->name ?? 'N/A' }} — {{ $st->user->email ?? 'Chưa có email' }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($availableStudents->isEmpty())
                                    <small class="text-muted d-block mt-1">Tất cả học viên trong hệ thống đã được ghi danh vào lớp này.</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="paid_tuition" class="form-label fw-semibold fs-7">Học phí thu (₫)</label>
                                <input type="number" step="1000" min="0" name="paid_tuition" id="paid_tuition" class="form-control" value="{{ (int)$classRoom->tuition_fee }}">
                                <small class="text-muted fs-8">Mặc định tính theo mức học phí của lớp (có thể điều chỉnh miễn giảm nếu có).</small>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success fw-semibold" {{ $availableStudents->isEmpty() ? 'disabled' : '' }}>
                                    <i class="bi bi-check2-circle me-1"></i> Xác nhận ghi danh vào lớp
                                </button>
                            </div>
                        </form>
                    @endif
                @else
                    <p class="text-muted mb-0">Chức năng ghi danh học viên dành riêng cho Quản trị viên.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- BẢNG DANH SÁCH HỌC VIÊN HIỆN CÓ TRONG LỚP --}}
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <strong class="text-dark fs-7"><i class="bi bi-people-fill text-primary me-1"></i> Danh sách Học viên đang theo học ({{ $classRoom->enrollments->count() }} học viên)</strong>
            <small class="text-muted d-block">Danh sách các học viên đã được ghi danh vào lớp này.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('attendances.index', $classRoom) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-calendar2-check me-1"></i> Điểm danh lớp
            </a>
            <a href="{{ route('grades.index', $classRoom) }}" class="btn btn-sm btn-outline-warning">
                <i class="bi bi-award me-1"></i> Nhập điểm số
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light fs-8 text-uppercase text-secondary">
                    <tr>
                        <th class="ps-3 text-center" style="width: 50px;">#</th>
                        <th style="width: 110px;">Mã HV</th>
                        <th>Họ và tên học viên</th>
                        <th>Email & SĐT</th>
                        <th>Ngày ghi danh</th>
                        <th>Học phí đã nộp</th>
                        <th class="text-center" style="width: 130px;">Trạng thái</th>
                        @if(auth()->check() && auth()->user()->isAdmin())
                            <th class="text-center pe-3" style="width: 100px;">Thao tác</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($classRoom->enrollments as $enrollment)
                        @php
                            $student = $enrollment->student;
                        @endphp
                        <tr>
                            <td class="ps-3 text-center text-muted fw-bold font-monospace">{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $student->student_code ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ $student->user->name ?? 'Chưa cập nhật' }}</strong>
                            </td>
                            <td>
                                <div class="text-muted fs-8">
                                    <i class="bi bi-envelope me-1"></i>{{ $student->user->email ?? '—' }}
                                    @if(!empty($student->user->phone))
                                        <div class="mt-1"><i class="bi bi-telephone me-1"></i>{{ $student->user->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="fs-8 text-secondary">{{ \Carbon\Carbon::parse($enrollment->enrollment_date)->format('d/m/Y') }}</span>
                            </td>
                            <td>
                                <span class="fw-semibold text-success">{{ number_format($enrollment->paid_tuition, 0, ',', '.') }} ₫</span>
                                <span class="badge bg-success-subtle text-success fs-8 ms-1">Đã thu</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $resultLabel = match($enrollment->result_status) {
                                        'passed' => 'Đạt',
                                        'failed' => 'Chưa đạt',
                                        default => 'Đang học',
                                    };
                                    $badgeClass = match($enrollment->result_status) {
                                        'passed' => 'bg-success',
                                        'failed' => 'bg-danger',
                                        default => 'bg-info text-dark',
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $resultLabel }}</span>
                            </td>
                            @if(auth()->check() && auth()->user()->isAdmin())
                                <td class="text-center pe-3">
                                    <form action="{{ route('enrollments.admin_remove', [$classRoom, $enrollment]) }}" method="POST" 
                                          onsubmit="return confirm('Bạn có chắc chắn muốn hủy ghi danh học viên {{ $student->user->name ?? $student->student_code }} khỏi lớp này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hủy ghi danh khỏi lớp">
                                            <i class="bi bi-person-x"></i>
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->check() && auth()->user()->isAdmin() ? 8 : 7 }}" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 text-secondary d-block mb-2"></i>
                                Lớp này hiện chưa có học viên nào. Hãy dùng form phía trên để đăng ký học viên vào lớp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
