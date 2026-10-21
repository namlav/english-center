@extends('layouts.app')

@section('title', 'Quản lý Giảng viên')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold mb-0"><i class="bi bi-person-badge"></i> Quản lý Giảng viên</h3>
    <a href="{{ route('teachers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm giảng viên
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Mã GV</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Chuyên môn</th>
                        <th>Bằng cấp</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teachers as $teacher)
                        <tr>
                            <td class="fw-bold">{{ $teacher->teacher_code }}</td>
                            <td>{{ $teacher->user->name ?? 'N/A' }}</td>
                            <td>{{ $teacher->user->email ?? 'N/A' }}</td>
                            <td><span class="badge bg-info text-dark">{{ $teacher->specialization }}</span></td>
                            <td>{{ $teacher->degree ?? '—' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('teachers.show', $teacher) }}" class="btn btn-outline-primary" title="Xem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('teachers.edit', $teacher) }}" class="btn btn-outline-warning ms-1" title="Sửa">
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
                            <td colspan="6" class="text-center py-4 text-muted">Chưa có giảng viên nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($teachers->hasPages())
        <div class="card-footer bg-white">
            {{ $teachers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
