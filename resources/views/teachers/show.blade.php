@extends(auth()->check() && auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.app')

@section('title', 'Chi tiết giảng viên')

@section('content')

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <span class="badge bg-primary-subtle text-primary mb-2">{{ $teacher->teacher_code }}</span>
        <h2 class="fw-bold mb-1">{{ $teacher->user?->name ?? 'N/A' }}</h2>
        <span class="badge bg-info text-dark">{{ $teacher->specialization }}</span>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">Về danh sách</a>
        <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-primary">Sửa giảng viên</a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Email</p><strong class="text-break">{{ $teacher->user?->email ?? '—' }}</strong></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Số điện thoại</p><strong>{{ $teacher->user?->phone ?? '—' }}</strong></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Bằng cấp</p><strong>{{ $teacher->degree ?? '—' }}</strong></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Trạng thái</p><strong>{{ $teacher->user?->is_active ? 'Hoạt động' : 'Bị khóa' }}</strong></div></div></div>
</div>

<div class="card border-0 shadow-sm mb-4"><div class="card-body p-4">
    <h3 class="h5 mb-3">Giới thiệu</h3>
    <p class="mb-0" style="white-space:pre-wrap">{{ $teacher->bio ?? 'Chưa cập nhật' }}</p>
</div></div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><h3 class="h5 mb-0">Lớp phụ trách</h3></div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-primary">
                <tr>
                    <th>Mã lớp</th>
                    <th>Tên lớp</th>
                    <th>Khóa học</th>
                    <th>Lịch học</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teacher->classRooms as $classRoom)
                    <tr>
                        <td class="fw-bold">{{ $classRoom->code }}</td>
                        <td><a href="{{ route('class_rooms.show', $classRoom) }}">{{ $classRoom->name }}</a></td>
                        <td>{{ $classRoom->course?->name ?? '—' }}</td>
                        <td>{{ $classRoom->schedule }}</td>
                        <td><span class="badge bg-secondary">{{ \App\Models\ClassRoom::STATUSES[$classRoom->status] ?? $classRoom->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-secondary py-4">Giảng viên chưa phụ trách lớp nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
