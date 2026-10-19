@extends('layouts.app')

@section('title', 'Quản lý Khóa học')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold mb-0"><i class="bi bi-book-half"></i> Quản lý Khóa học</h3>
    @if(auth()->check() && auth()->user()->isAdmin())
        <a href="{{ route('courses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm khóa học
        </a>
    @endif
</div>

<form method="GET" action="{{ route('courses.index') }}" class="card shadow-sm mb-4">
    <div class="card-body row g-3 align-items-end">
        <div class="col-md-7"><label for="keyword" class="form-label">Tìm mã hoặc tên khóa học</label><input id="keyword" name="keyword" type="search" maxlength="100" class="form-control" value="{{ $keyword }}"></div>
        <div class="col-md-3"><label for="status" class="form-label">Trạng thái</label><select id="status" name="status" class="form-select"><option value="">Tất cả</option><option value="1" @selected($status === '1')>Hoạt động</option><option value="0" @selected($status === '0')>Ngừng hoạt động</option></select></div>
        <div class="col-md-2 d-flex gap-2"><button class="btn btn-primary" type="submit">Lọc</button><a class="btn btn-outline-secondary" href="{{ route('courses.index') }}">Đặt lại</a></div>
    </div>
</form>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Mã khóa</th>
                        <th>Tên khóa học</th>
                        <th>Cấp độ</th>
                        <th>Thời lượng (giờ)</th>
                        <th>Số lớp</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td class="fw-bold">{{ $course->code }}</td>
                            <td>{{ $course->name }}</td>
                            <td><span class="badge bg-info text-dark">{{ $course->level ?? '—' }}</span></td>
                            <td>{{ $course->duration_hours }} giờ</td>
                            <td>
                                <span class="badge bg-secondary">{{ $course->class_rooms_count }} lớp</span>
                            </td>
                            <td>
                                @if($course->is_active)
                                    <span class="badge bg-success">Đang mở</span>
                                @else
                                    <span class="badge bg-danger">Tạm đóng</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-primary" title="Xem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(auth()->check() && auth()->user()->isAdmin())
                                        <a href="{{ route('courses.edit', $course) }}" class="btn btn-outline-warning ms-1" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('courses.destroy', $course) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa khóa học này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger ms-1" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Không có khóa học nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($courses->hasPages())
        <div class="card-footer bg-white">
            {{ $courses->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
