<p class="small text-secondary" data-result-summary>
    Hiển thị {{ $classRooms->firstItem() ?? 0 }}–{{ $classRooms->lastItem() ?? 0 }}
    trong {{ $classRooms->total() }} lớp học.
</p>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>Mã lớp</th>
                        <th>Tên lớp học</th>
                        <th>Khóa học</th>
                        <th>Giáo viên</th>
                        <th>Lịch học</th>
                        <th>Học phí</th>
                        <th>Sĩ số</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classRooms as $class)
                        <tr>
                            <td class="fw-bold">{{ $class->code }}</td>
                            <td>{{ $class->name }}</td>
                            <td><span class="badge bg-info text-dark">{{ $class->course->name ?? 'N/A' }}</span></td>
                            <td>{{ $class->teacher->user->name ?? 'N/A' }}</td>
                            <td><small>{{ $class->schedule }}</small></td>
                            <td class="text-success fw-bold">{{ number_format($class->tuition_fee, 0, ',', '.') }} đ</td>
                            <td>
                                <span class="badge {{ $class->enrollments_count >= $class->max_students ? 'bg-danger' : 'bg-success' }}">
                                    {{ $class->enrollments_count }} / {{ $class->max_students }}
                                </span>
                            </td>
                            <td>
                                @switch($class->status)
                                    @case('ongoing')
                                        <span class="badge bg-primary">Đang học</span>
                                        @break
                                    @case('upcoming')
                                        <span class="badge bg-warning text-dark">Sắp mở</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-secondary">Kết thúc</span>
                                        @break
                                    @default
                                        <span class="badge bg-dark">{{ $statuses[$class->status] ?? $class->status }}</span>
                                @endswitch
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('class_rooms.show', $class) }}" class="btn btn-outline-primary" title="Chi tiết">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if(auth()->check() && auth()->user()->isStudent())
                                        <form action="{{ route('enrollments.enroll', $class) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn đăng ký lớp học này?');">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm ms-1" title="Đăng ký học">
                                                <i class="bi bi-pencil-square"></i> Đăng ký
                                            </button>
                                        </form>
                                    @endif

                                    @if(auth()->check() && (auth()->user()->isAdmin() || (auth()->user()->isTeacher() && auth()->user()->teacher && auth()->user()->teacher->id === $class->teacher_id)))
                                        <a href="{{ route('enrollments.class_students', $class) }}" class="btn btn-outline-info ms-1" title="Danh sách học viên">
                                            <i class="bi bi-people"></i>
                                        </a>
                                        <a href="{{ route('attendances.index', $class) }}" class="btn btn-outline-secondary ms-1" title="Điểm danh">
                                            <i class="bi bi-check2-square"></i>
                                        </a>
                                        <a href="{{ route('grades.index', $class) }}" class="btn btn-outline-warning ms-1" title="Nhập điểm">
                                            <i class="bi bi-award"></i>
                                        </a>
                                    @endif

                                    @if(auth()->check() && auth()->user()->isAdmin())
                                        <a href="{{ route('class_rooms.edit', $class) }}" class="btn btn-outline-warning ms-1" title="Sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('class_rooms.destroy', $class) }}" class="d-inline" onsubmit="return confirm('Xác nhận xóa lớp học này?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger ms-1" title="Xóa lớp"><i class="bi bi-trash"></i><span class="visually-hidden">Xóa lớp {{ $class->code }}</span></button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block"></i> Không tìm thấy lớp học nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($classRooms->hasPages())
        <div class="card-footer bg-white">
            {{ $classRooms->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
