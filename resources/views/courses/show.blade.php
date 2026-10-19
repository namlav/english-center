@extends('layouts.app')

@section('title', 'Chi tiết khóa học')

@section('content')

<div class="d-flex flex-wrap justify-content-between gap-3 align-items-center mb-4">
    <div><span class="badge bg-primary-subtle text-primary mb-2">{{ $course->code }}</span><h2 class="fw-bold mb-1">{{ $course->name }}</h2><span class="text-secondary">Chi tiết chương trình đào tạo</span></div>
    <div class="d-flex flex-wrap gap-2"><a class="btn btn-outline-secondary" href="{{ route('courses.index') }}">Về danh sách</a><a class="btn btn-primary" href="{{ route('courses.edit', $course) }}">Sửa khóa học</a></div>
</div>
<div class="row g-4 mb-4">
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Thời lượng</p><strong class="fs-3">{{ $course->duration_hours }} giờ</strong></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Trình độ</p><strong class="fs-3">{{ $course->level ?: 'Chưa cập nhật' }}</strong></div></div></div>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><p class="text-secondary mb-2">Trạng thái</p><strong class="fs-4 {{ $course->is_active ? 'text-success' : 'text-secondary' }}">{{ $course->is_active ? 'Hoạt động' : 'Ngừng hoạt động' }}</strong></div></div></div>
</div>
<div class="card border-0 shadow-sm mb-4"><div class="card-body p-4"><h3 class="h5 mb-3">Mô tả chương trình</h3><p class="mb-0 text-secondary" style="white-space:pre-wrap">{{ $course->description ?: 'Chưa có mô tả.' }}</p></div></div>
<div class="card border-0 shadow-sm"><div class="card-header bg-white py-3"><h3 class="h5 mb-0">Các lớp thuộc khóa học ({{ $course->class_rooms_count }})</h3></div>
    <div class="table-responsive"><table class="table mb-0 align-middle"><thead><tr><th>Mã lớp</th><th>Tên lớp</th><th>Giảng viên</th><th>Học phí</th></tr></thead><tbody>
        @forelse($classRooms as $classRoom)
            <tr><td><a href="{{ route('class_rooms.show', $classRoom) }}">{{ $classRoom->code }}</a></td><td>{{ $classRoom->name }}</td><td>{{ $classRoom->teacher?->user?->name ?? 'Chưa cập nhật' }}</td><td>{{ number_format($classRoom->tuition_fee, 0, ',', '.') }} đ</td></tr>
        @empty <tr><td colspan="4" class="text-center text-secondary py-4">Khóa học chưa có lớp.</td></tr> @endforelse
    </tbody></table></div>
    <div class="card-footer bg-white">{{ $classRooms->links('pagination::bootstrap-5') }}</div>
</div>

@endsection
