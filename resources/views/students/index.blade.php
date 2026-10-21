@extends('layouts.app')

@section('title', 'Quản lý Học viên')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="fw-bold mb-0"><i class="bi bi-mortarboard"></i> Quản lý Học viên</h3>
    <a href="{{ route('students.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Thêm học viên
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Mã HV</th>
                        <th>Họ và tên</th>
                        <th>Email</th>
                        <th>Ngày sinh</th>
                        <th>Giới tính</th>
                        <th>Địa chỉ</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td class="fw-bold">{{ $student->student_code }}</td>
                            <td>{{ $student->user->name ?? 'N/A' }}</td>
                            <td>{{ $student->user->email ?? 'N/A' }}</td>
                            <td>{{ $student->date_of_birth ?? '—' }}</td>
                            <td>
                                @switch($student->gender)
                                    @case('male')
                                        <span>Nam</span>
                                        @break
                                    @case('female')
                                        <span>Nữ</span>
                                        @break
                                    @default
                                        <span>Khác</span>
                                @endswitch
                            </td>
                            <td>{{ $student->address ?? '—' }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('students.show', $student) }}" class="btn btn-outline-primary" title="Xem">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('students.edit', $student) }}" class="btn btn-outline-warning ms-1" title="Sửa">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('students.destroy', $student) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa học viên này?');">
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
                            <td colspan="7" class="text-center py-4 text-muted">Không có học viên nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($students->hasPages())
        <div class="card-footer bg-white">
            {{ $students->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
