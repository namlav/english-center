@extends('layouts.admin')

@section('title', 'Quản lý Đội ngũ Giáo viên & Banner')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-person-badge-fill text-primary"></i> Quản lý Đội ngũ Giáo viên</h3>
        <p class="text-secondary mb-0 fs-7">Hợp nhất quản lý giảng viên đào tạo, lớp học và đội ngũ giáo viên hiển thị trên Banner website.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('teachers.create') }}" class="btn btn-primary fw-semibold shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Thêm giảng viên đào tạo
        </a>
        <a href="{{ route('website.teachers.create') }}" class="btn btn-outline-primary fw-semibold shadow-sm">
            <i class="bi bi-image-fill me-1"></i> Thêm giáo viên banner
        </a>
    </div>
</div>

{{-- NAV TABS CHUYỂN ĐỔI --}}
<ul class="nav nav-pills mb-3 bg-white p-2 rounded-3 border shadow-sm" id="teacherTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold px-4 py-2" id="training-tab" data-bs-toggle="tab" data-bs-target="#training-pane" type="button" role="tab" aria-controls="training-pane" aria-selected="true">
            <i class="bi bi-people-fill me-1"></i> 1. Giảng viên Đào tạo & Lớp học <span class="badge bg-primary ms-1">{{ $teachers->total() }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold px-4 py-2" id="banner-tab" data-bs-toggle="tab" data-bs-target="#banner-pane" type="button" role="tab" aria-controls="banner-pane" aria-selected="false">
            <i class="bi bi-stars text-warning me-1"></i> 2. Giáo viên Banner Website (Ảnh thật) <span class="badge bg-warning text-dark ms-1">{{ $publicTeachers->count() }}</span>
        </button>
    </li>
</ul>

<div class="tab-content" id="teacherTabContent">
    {{-- TAB 1: GIẢNG VIÊN ĐÀO TẠO & LỚP HỌC --}}
    <div class="tab-pane fade show active" id="training-pane" role="tabpanel" aria-labelledby="training-tab" tabindex="0">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark fs-7"><i class="bi bi-mortarboard text-primary me-1"></i> Danh sách giảng viên giảng dạy tại các cơ sở & lớp học</span>
                <span class="text-muted fs-8">Tổng cộng: {{ $teachers->total() }} giảng viên</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light fs-8 text-uppercase text-secondary">
                            <tr>
                                <th class="ps-3" style="width: 100px;">Mã GV</th>
                                <th>Họ và tên</th>
                                <th>Email tài khoản</th>
                                <th>Chuyên môn</th>
                                <th>Bằng cấp</th>
                                <th class="text-center pe-3" style="width: 140px;">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $teacher)
                                <tr>
                                    <td class="ps-3 fw-bold text-primary font-monospace">{{ $teacher->teacher_code }}</td>
                                    <td>
                                        <strong class="text-dark d-block">{{ $teacher->user->name ?? 'N/A' }}</strong>
                                        <small class="text-muted">{{ $teacher->user->phone ?? 'Chưa có SĐT' }}</small>
                                    </td>
                                    <td>{{ $teacher->user->email ?? 'N/A' }}</td>
                                    <td><span class="badge bg-info-subtle text-info-emphasis border">{{ $teacher->specialization }}</span></td>
                                    <td>{{ $teacher->degree ?? '—' }}</td>
                                    <td class="text-center pe-3">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-outline-primary" title="Xem chi tiết">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-outline-warning ms-1" title="Chỉnh sửa">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('teachers.destroy', $teacher) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa giảng viên này?');">
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
                                    <td colspan="6" class="text-center py-4 text-muted">Chưa có giảng viên đào tạo nào.</td>
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
    </div>

    {{-- TAB 2: GIÁO VIÊN BANNER WEBSITE (ẢNH THẬT NỔI BẬT) --}}
    <div class="tab-pane fade" id="banner-pane" role="tabpanel" aria-labelledby="banner-tab" tabindex="0">
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <strong class="text-dark fs-7 d-block"><i class="bi bi-stars text-warning me-1"></i> Đội ngũ Giáo viên Banner Trang chủ</strong>
                    <small class="text-muted">Các giáo viên này sẽ hiển thị trên băng chuyền xoay ảnh thật (Teacher Showcase) ngoài trang chủ.</small>
                </div>
                <a href="{{ route('website.teachers.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Thêm giáo viên banner mới
                </a>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    @forelse($publicTeachers as $pt)
                        <div class="col-md-6 col-xl-4">
                            <div class="card h-100 border shadow-sm rounded-3 overflow-hidden position-relative">
                                <div class="row g-0 h-100">
                                    <div class="col-4 bg-light d-flex align-items-center justify-content-center p-2 border-end">
                                        @if($pt->photo_path)
                                            <img src="{{ asset($pt->photo_path) }}" alt="{{ $pt->name }}" class="img-fluid rounded-2" style="max-height: 120px; object-fit: contain;">
                                        @else
                                            <div class="text-muted fs-8 text-center"><i class="bi bi-person-fill fs-1"></i><br>Chưa có ảnh</div>
                                        @endif
                                    </div>
                                    <div class="col-8 p-3 d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start gap-1">
                                                <strong class="text-dark fs-6 d-block">{{ $pt->name }}</strong>
                                                @if($pt->is_active)
                                                    <span class="badge bg-success-subtle text-success fs-8">Hiện banner</span>
                                                @else
                                                    <span class="badge bg-secondary fs-8">Ẩn</span>
                                                @endif
                                            </div>
                                            <div class="text-muted fs-8 mt-1"><i class="bi bi-building"></i> {{ $pt->provider }} · Vị trí: #{{ $pt->position }}</div>
                                            <p class="text-secondary fs-8 mt-2 mb-0 text-truncate" style="max-width: 220px;" title="{{ $pt->bio }}">
                                                {{ $pt->bio }}
                                            </p>
                                        </div>
                                        <div class="mt-3 pt-2 border-top d-flex justify-content-between align-items-center">
                                            <a href="{{ route('public_teachers.show', $pt->slug) }}" target="_blank" class="btn btn-sm btn-light border fs-8">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> Xem web
                                            </a>
                                            <a href="{{ route('website.teachers.edit', $pt) }}" class="btn btn-sm btn-outline-primary fs-8">
                                                <i class="bi bi-pencil-square me-1"></i> Sửa hồ sơ
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-4 text-muted">
                            Chưa có giáo viên nào được cấu hình cho banner website.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
