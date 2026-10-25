@extends('layouts.learning')
@section('title', 'Lịch khai giảng & Lớp học trực tiếp — English Center')

@section('content')
<section class="shell" style="padding-top: 24px;">
    {{-- Banner Lớp học Khách hàng --}}
    <div style="position: relative; border-radius: 16px; overflow: hidden; min-height: 280px; display: flex; align-items: center; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); background: #0f172a; margin-bottom: 32px;">
        <img src="{{ asset('images/catalog/study4/study-banner.jpg') }}" alt="Lớp học trực tiếp English Center" style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.35;">
        <div style="position: relative; z-index: 2; padding: 40px 48px; max-width: 800px;">
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(37, 99, 235, 0.9); color: #ffffff; padding: 6px 16px; border-radius: 20px; font-size: 13px; font-weight: 700; text-transform: uppercase; margin-bottom: 14px;">
                <span>🏫</span> ĐÀO TẠO TRỰC TIẾP TẠI TRUNG TÂM
            </div>
            <h1 style="font-size: 32px; font-weight: 800; color: #ffffff; line-height: 1.3; margin: 0 0 12px 0;">
                Lịch khai giảng &amp; Danh sách Lớp học
            </h1>
            <p style="font-size: 16px; line-height: 1.6; color: #f1f5f9; margin: 0 0 20px 0;">
                Môi trường học tập tương tác cao cùng đội ngũ giảng viên chuyên môn giỏi. Lớp học giới hạn sĩ số để đảm bảo từng học viên đều được kèm cặp sát sao.
            </p>
            @if(auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('class_rooms.index') }}" class="button button-small" style="background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.5); color: #ffffff; text-decoration: none; font-weight: 600;">
                    ⚙️ Chuyển sang Trang Quản lý Lớp học Admin →
                </a>
            @endif
        </div>
    </div>

    {{-- Bộ lọc tìm kiếm nhanh cho khách hàng --}}
    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); margin-bottom: 32px;">
        <form method="GET" action="{{ route('public_classes.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)) auto; gap: 16px; align-items: flex-end;">
            <div>
                <label for="filter-kw" style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Tìm kiếm lớp học</label>
                <input id="filter-kw" type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Nhập mã lớp hoặc tên lớp..." style="width: 100%; padding: 9px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
            </div>

            <div>
                <label for="filter-course" style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Chương trình đào tạo</label>
                <select id="filter-course" name="course_id" style="width: 100%; padding: 9px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; background: #fff;">
                    <option value="">-- Tất cả khóa học --</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" @selected(request('course_id') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="filter-teacher" style="display: block; font-size: 13px; font-weight: 700; color: #475569; margin-bottom: 6px;">Giảng viên</label>
                <select id="filter-teacher" name="teacher_id" style="width: 100%; padding: 9px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; background: #fff;">
                    <option value="">-- Tất cả giảng viên --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" @selected(request('teacher_id') == $t->id)>{{ $t->user?->name ?? $t->teacher_code }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="button" style="padding: 9px 20px; font-size: 14px; font-weight: 700;">Tìm lớp</button>
                @if(request()->hasAny(['keyword', 'course_id', 'teacher_id']))
                    <a href="{{ route('public_classes.index') }}" class="button button-outline" style="padding: 9px 16px; font-size: 14px; text-decoration: none;">Đặt lại</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Danh sách Thẻ Lớp học --}}
    <div style="margin-bottom: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 0;">
                Các lớp học đang nhận học viên ({{ $classRooms->total() }} lớp)
            </h2>
            <span style="font-size: 14px; color: #64748b;">Khai giảng hàng tháng</span>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 24px;">
            @forelse($classRooms as $class)
                @php
                    $isFull = $class->enrollments_count >= $class->max_students;
                    $statusLabel = match($class->status) {
                        'ongoing' => 'Đang học',
                        'upcoming' => 'Sắp khai giảng',
                        'completed' => 'Đã kết thúc',
                        default => $class->status
                    };
                    $statusColor = match($class->status) {
                        'ongoing' => '#2563eb',
                        'upcoming' => '#059669',
                        default => '#64748b'
                    };
                @endphp
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 24px; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04); transition: transform 0.2s, box-shadow 0.2s;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 12px;">
                            <span style="background: #eff6ff; color: #1d4ed8; font-weight: 800; font-size: 12px; font-family: monospace; padding: 4px 10px; border-radius: 6px;">
                                {{ $class->code }}
                            </span>
                            <span style="background: {{ $statusColor }}15; color: {{ $statusColor }}; font-weight: 700; font-size: 12px; padding: 4px 10px; border-radius: 6px;">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <h3 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 0 0 8px 0; line-height: 1.35;">
                            {{ $class->name }}
                        </h3>

                        <div style="color: #64748b; font-size: 13.5px; margin-bottom: 16px;">
                            Thuộc khóa: <strong style="color: #334155;">{{ $class->course->name ?? 'Chương trình chuẩn' }}</strong>
                        </div>

                        <div style="background: #f8fafc; border-radius: 10px; padding: 14px 16px; margin-bottom: 18px; display: grid; gap: 8px; font-size: 13.5px;">
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">👨‍🏫 Giảng viên:</span>
                                <strong style="color: #0f172a;">{{ $class->teacher?->user?->name ?? 'Đang phân công' }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">📅 Lịch học:</span>
                                <strong style="color: #0f172a;">{{ $class->schedule }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">🚀 Khai giảng:</span>
                                <strong style="color: #0f172a;">{{ $class->start_date?->format('d/m/Y') }}</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b;">👥 Tình trạng:</span>
                                <strong>
                                    @if($isFull)
                                        <span style="color: #dc2626;">Đã đủ {{ $class->max_students }} học viên</span>
                                    @else
                                        <span style="color: #059669;">Còn trống {{ max(0, $class->max_students - $class->enrollments_count) }} chỗ</span>
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 14px; padding-top: 12px; border-top: 1px dashed #e2e8f0;">
                            <span style="font-size: 13px; color: #64748b;">Học phí niêm yết:</span>
                            <strong style="font-size: 20px; font-weight: 800; color: #059669;">
                                {{ number_format($class->tuition_fee, 0, ',', '.') }} ₫
                            </strong>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr auto; gap: 10px;">
                            <a href="{{ route('public_classes.show', $class) }}" class="button" style="text-align: center; font-weight: 700; font-size: 14px; text-decoration: none;">
                                Chi tiết &amp; Đăng ký →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #ffffff; border-radius: 12px; border: 1px dashed #cbd5e1;">
                    <div style="font-size: 40px; margin-bottom: 12px;">🏫</div>
                    <h3 style="font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">Không tìm thấy lớp học phù hợp</h3>
                    <p style="color: #64748b; font-size: 14px; margin-bottom: 16px;">Vui lòng thử tìm kiếm với từ khóa khác hoặc xóa bộ lọc.</p>
                    <a href="{{ route('public_classes.index') }}" class="button button-outline" style="font-size: 13.5px; text-decoration: none;">Xem tất cả lớp học</a>
                </div>
            @endforelse
        </div>

        <div style="margin-top: 32px;">
            {{ $classRooms->links() }}
        </div>
    </div>
</section>
@endsection
