@extends('layouts.learning')
@section('title', 'Quản lý Khóa học & Giáo trình Video')
@section('content')
<section class="shell admin-page">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 22px;">
        <div>
            <span class="eyebrow" style="color: #2563eb; font-weight: 700; font-size: 13px; text-transform: uppercase;">E-LEARNING LMS</span>
            <h1 style="font-size: 28px; font-weight: 800; color: #0f172a; margin: 4px 0 0;">Quản lý Khóa học & Giáo trình Video</h1>
            <p style="color: #64748b; font-size: 14px; margin: 4px 0 0;">Hệ thống bài giảng, video YouTube, học phí và đề luyện trắc nghiệm kèm theo.</p>
        </div>
        <a class="button" href="{{ route('shop_admin.courses.create') }}" style="font-weight: 700; padding: 10px 20px; text-decoration: none;">+ Thêm khóa học mới</a>
    </div>

    @include('learning.admin.nav')

    {{-- Thống kê tổng quan --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <span style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Tổng số khóa học</span>
            <div style="font-size: 26px; font-weight: 800; color: #2563eb; margin-top: 4px;">{{ $courses->count() }} khóa</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <span style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Tổng số bài học</span>
            <div style="font-size: 26px; font-weight: 800; color: #059669; margin-top: 4px;">{{ number_format($courses->sum('lessons_count'), 0, ',', '.') }} bài</div>
        </div>
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <span style="font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase;">Đang mở bán</span>
            <div style="font-size: 26px; font-weight: 800; color: #0f172a; margin-top: 4px;">{{ $courses->where('is_for_sale', true)->count() }} khóa</div>
        </div>
    </div>

    {{-- Ô tìm kiếm nhanh --}}
    <div style="margin-bottom: 18px; display: flex; gap: 12px; align-items: center;">
        <input type="text" id="courseSearchInput" onkeyup="filterCourseTable()" placeholder="🔍 Tìm khóa học theo tên hoặc mã khóa..." style="padding: 10px 16px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; width: 100%; max-width: 450px;">
        <span style="font-size: 13px; color: #64748b;" id="courseCountNotice">Hiển thị {{ $courses->count() }} khóa học</span>
    </div>

    {{-- Bảng danh sách khóa học --}}
    <div class="admin-table-wrap" style="box-shadow: 0 2px 8px rgba(0,0,0,0.03); border-radius: 10px;">
        <table class="admin-table" id="coursesAdminTable">
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th style="width: 38%;">Khóa học & Giáo trình</th>
                    <th>Danh mục</th>
                    <th style="text-align: center;">Bài học</th>
                    <th>Học phí</th>
                    <th style="text-align: center;">Trạng thái</th>
                    <th style="text-align: right;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $i => $course)
                    <tr class="course-row">
                        <td style="color: #94a3b8; font-weight: 600;">{{ $i + 1 }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                @if($course->thumbnail_path)
                                    <img src="{{ asset($course->thumbnail_path) }}" alt="{{ $course->name }}" style="width: 54px; height: 38px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; flex-shrink: 0;">
                                @else
                                    <div style="width: 54px; height: 38px; background: #eff6ff; color: #2563eb; border-radius: 6px; display: grid; place-items: center; font-weight: 700; font-size: 11px; flex-shrink: 0;">
                                        {{ $course->code }}
                                    </div>
                                @endif
                                <div>
                                    <strong style="color: #0f172a; font-size: 14px; display: block;" class="course-name-text">{{ $course->name }}</strong>
                                    <span style="font-size: 12px; color: #64748b;" class="course-code-text">Mã: {{ $course->code }} · {{ $course->duration_hours }}h học</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="background: #f1f5f9; color: #475569; font-size: 12px; font-weight: 600; padding: 3px 8px; border-radius: 4px;">
                                {{ $course->learningCategory?->name ?? 'Tổng quát' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span style="background: #ecfdf5; color: #047857; font-size: 12.5px; font-weight: 700; padding: 3px 9px; border-radius: 20px;">
                                {{ $course->lessons_count }} bài
                            </span>
                        </td>
                        <td>
                            <strong style="color: #0f172a; font-size: 14px;">{{ number_format($course->sale_price ?? $course->price, 0, ',', '.') }}đ</strong>
                            @if($course->sale_price && $course->sale_price < $course->price)
                                <span style="display: block; font-size: 11px; color: #94a3b8; text-decoration: line-through;">
                                    {{ number_format($course->price, 0, ',', '.') }}đ
                                </span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($course->is_for_sale)
                                <span style="background: #dcfce7; color: #15803d; font-size: 11.5px; font-weight: 700; padding: 4px 10px; border-radius: 6px; display: inline-block;">
                                    Đang mở bán
                                </span>
                            @else
                                <span style="background: #f1f5f9; color: #64748b; font-size: 11.5px; font-weight: 600; padding: 4px 10px; border-radius: 6px; display: inline-block;">
                                    Chưa mở bán
                                </span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <a href="{{ route('shop.show', $course->slug) }}" target="_blank" class="button button-outline button-small" style="padding: 5px 10px; font-size: 12px; margin-right: 6px; text-decoration: none;" title="Xem trang bán khóa học">
                                👁️ Xem
                            </a>
                            <a href="{{ route('shop_admin.courses.edit', $course) }}" class="button button-small" style="padding: 5px 12px; font-size: 12px; text-decoration: none;">
                                ✏️ Sửa & Bài học
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>

<script>
function filterCourseTable() {
    const input = document.getElementById('courseSearchInput');
    const filter = input.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#coursesAdminTable tbody tr.course-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const name = row.querySelector('.course-name-text')?.innerText.toLowerCase() || '';
        const code = row.querySelector('.course-code-text')?.innerText.toLowerCase() || '';
        if (name.includes(filter) || code.includes(filter)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const notice = document.getElementById('courseCountNotice');
    if (notice) {
        notice.innerText = 'Hiển thị ' + visibleCount + ' khóa học';
    }
}
</script>
@endsection
