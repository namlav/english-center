@extends('layouts.app')

@section('title', 'Lớp học phụ trách')

@section('content')
<div class="mb-4"><p class="text-primary fw-semibold mb-1">{{ auth()->user()->isAdmin() ? 'QUẢN TRỊ' : 'KHU VỰC GIÁO VIÊN' }}</p><h1 class="h3 fw-bold mb-0">{{ auth()->user()->isAdmin() ? 'Danh sách lớp học' : 'Lớp tôi phụ trách' }}</h1></div>
<div class="row g-3">
    @forelse($classRooms as $classRoom)
        <div class="col-md-6 col-xl-4"><div class="card h-100 border-0 shadow-sm"><div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start gap-3"><div><div class="text-secondary small">{{ $classRoom->code }}</div><h2 class="h5 fw-bold mt-1">{{ $classRoom->name }}</h2></div><span class="badge text-bg-{{ $classRoom->status === 'ongoing' ? 'primary' : ($classRoom->status === 'upcoming' ? 'warning' : 'secondary') }}">{{ \App\Models\ClassRoom::STATUSES[$classRoom->status] ?? $classRoom->status }}</span></div>
            <p class="mb-2"><i class="bi bi-book me-2 text-primary"></i>{{ $classRoom->course->name }}</p>
            <p class="mb-2"><i class="bi bi-person me-2 text-primary"></i>{{ $classRoom->teacher->user->name }}</p>
            <p class="mb-2"><i class="bi bi-calendar3 me-2 text-primary"></i>{{ $classRoom->schedule }}</p>
            <p class="mb-0"><i class="bi bi-people me-2 text-primary"></i>{{ $classRoom->enrollments_count }} / {{ $classRoom->max_students }} học viên</p>
        </div><div class="card-footer bg-white border-0 px-4 pb-4 pt-0 d-flex flex-wrap gap-2">
            <a href="{{ route('enrollments.class_students', $classRoom) }}" class="btn btn-sm btn-outline-primary">Danh sách học viên</a>
            <a href="{{ route('attendances.index', $classRoom) }}" class="btn btn-sm btn-outline-secondary">Điểm danh</a>
            <a href="{{ route('grades.index', $classRoom) }}" class="btn btn-sm btn-outline-warning">Nhập điểm</a>
        </div></div></div>
    @empty
        <div class="col-12"><div class="card border-0 shadow-sm"><div class="card-body text-center py-5 text-secondary">Chưa có lớp học nào được phân công.</div></div></div>
    @endforelse
</div>
@if($classRooms->hasPages())<div class="mt-4">{{ $classRooms->links('pagination::bootstrap-5') }}</div>@endif
@endsection
