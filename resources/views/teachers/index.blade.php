@extends('layouts.admin')

@section('title', 'Quản lý Đội ngũ Giáo viên')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-person-badge-fill text-primary"></i> Quản lý Đội ngũ Giáo viên</h3>
        <p class="text-secondary mb-0 fs-7">Hệ thống đội ngũ giảng viên đào tạo lớp học và hiển thị trên website bán khóa học online.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('teachers.create') }}" class="btn btn-primary fw-semibold shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Thêm giáo viên mới
        </a>
    </div>
</div>

{{-- THẺ THỐNG KÊ NHANH --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 fs-3">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <span class="text-muted fs-8 text-uppercase fw-semibold">Tổng số giáo viên</span>
                    <h4 class="fw-bold mb-0 text-dark">{{ $teachers->total() }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 fs-3">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div>
                    <span class="text-muted fs-8 text-uppercase fw-semibold">Đang phụ trách lớp</span>
                    <h4 class="fw-bold mb-0 text-dark">
                        {{ $teachers->filter(fn($t) => $t->classRooms->count() > 0)->count() }}
                    </h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3 fs-3">
                    <i class="bi bi-stars"></i>
                </div>
                <div>
                    <span class="text-muted fs-8 text-uppercase fw-semibold">Hiển thị trên Banner</span>
                    <h4 class="fw-bold mb-0 text-dark">
                        {{ $teachers->filter(fn($t) => $t->publicTeacher && $t->publicTeacher->is_active)->count() }}
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- BẢNG DANH SÁCH GIÁO VIÊN DUY NHẤT (ĐÃ GỘP) --}}
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <strong class="text-dark fs-7"><i class="bi bi-list-check text-primary me-1"></i> Danh sách Đội ngũ Giảng viên</strong>
            <small class="text-muted d-block mt-0">Bao gồm hồ sơ đào tạo, chuyên môn bằng cấp và vị trí hiển thị trên Banner website.</small>
        </div>
        <span class="badge bg-light text-dark border px-3 py-2 fs-8 fw-semibold">
            Tổng cộng: {{ $teachers->total() }} giáo viên
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light fs-8 text-uppercase text-secondary">
                    <tr>
                        <th class="ps-3 text-center" style="width: 50px;">#</th>
                        <th style="width: 70px;">Ảnh</th>
                        <th>Họ và tên & Liên hệ</th>
                        <th>Chuyên môn & Bằng cấp</th>
                        <th>Lớp học phụ trách</th>
                        <th class="text-center" style="width: 170px;">Banner Website</th>
                        <th class="text-center pe-3" style="width: 140px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $index => $teacher)
                        @php
                            $pt = $teacher->publicTeacher;
                            $photoUrl = $pt && $pt->photo_path ? asset($pt->photo_path) : null;
                        @endphp
                        <tr>
                            <td class="ps-3 text-center text-muted fw-bold font-monospace">
                                {{ ($teachers->currentPage() - 1) * $teachers->perPage() + $loop->iteration }}
                            </td>
                            <td>
                                @if($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="{{ $teacher->user->name ?? 'GV' }}" 
                                         class="rounded-3 border shadow-xs" 
                                         style="width: 52px; height: 52px; object-fit: cover;">
                                @else
                                    <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold border" 
                                         style="width: 52px; height: 52px; font-size: 1.2rem;">
                                        {{ mb_substr($teacher->user->name ?? 'GV', 0, 1) }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <strong class="text-dark fs-7">{{ $teacher->user->name ?? 'Chưa cập nhật' }}</strong>
                                    <span class="badge bg-primary-subtle text-primary font-monospace fs-8">{{ $teacher->teacher_code }}</span>
                                </div>
                                <div class="text-muted fs-8 mt-1">
                                    <i class="bi bi-envelope me-1"></i>{{ $teacher->user->email ?? 'N/A' }}
                                    @if(!empty($teacher->user->phone))
                                        <span class="mx-1">·</span><i class="bi bi-telephone me-1"></i>{{ $teacher->user->phone }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div><span class="badge bg-info-subtle text-info-emphasis border fw-semibold">{{ $teacher->specialization }}</span></div>
                                <small class="text-secondary d-block mt-1">{{ $teacher->degree ?? 'Cử nhân Sư phạm / Thạc sĩ' }}</small>
                            </td>
                            <td>
                                @if($teacher->classRooms->count() > 0)
                                    <span class="badge bg-success-subtle text-success border">
                                        <i class="bi bi-calendar-check me-1"></i>{{ $teacher->classRooms->count() }} lớp đang dạy
                                    </span>
                                    <div class="fs-8 text-muted mt-1 text-truncate" style="max-width: 200px;">
                                        {{ $teacher->classRooms->pluck('code')->join(', ') }}
                                    </div>
                                @else
                                    <span class="text-muted fs-8">Chưa phân công lớp</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($pt && $pt->is_active)
                                    <span class="badge bg-success-subtle text-success border px-2 py-1 fs-8">
                                        <i class="bi bi-check-circle-fill me-1"></i>Hiện Banner (#{{ $pt->position }})
                                    </span>
                                    @if($pt->slug)
                                        <div class="mt-1">
                                            <a href="{{ route('public_teachers.show', $pt->slug) }}" target="_blank" class="fs-8 text-decoration-none text-primary">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Xem trên web
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <span class="badge bg-light text-secondary border px-2 py-1 fs-8">
                                        <i class="bi bi-dash-circle me-1"></i>Không hiển thị
                                    </span>
                                @endif
                            </td>
                            <td class="text-center pe-3">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-outline-warning" title="Chỉnh sửa hồ sơ">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-outline-primary ms-1" title="Xem chi tiết giảng dạy">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa giáo viên này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger ms-1" title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 text-secondary d-block mb-2"></i>
                                Chưa có giáo viên nào trong hệ thống.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($teachers->hasPages())
        <div class="card-footer bg-white border-top">
            {{ $teachers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
