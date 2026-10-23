@extends('layouts.admin')
@section('title', ($course->exists ? 'Sửa khóa học: ' . $course->name : 'Thêm khóa học mới'))
@section('content')
<section class="shell admin-page">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
        <div>
            <span class="eyebrow" style="color: #2563eb; font-weight: 700; font-size: 13px; text-transform: uppercase;">E-LEARNING LMS</span>
            <h1 style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 4px 0 0;">{{ $course->exists ? 'Chỉnh sửa khóa học: ' . $course->name : 'Tạo khóa học mới' }}</h1>
        </div>
        <div style="display: flex; gap: 8px;">
            @if($course->exists)
                <a class="button button-outline" href="{{ route('shop.show', $course->slug) }}" target="_blank" style="text-decoration: none;">👁️ Xem trên web</a>
            @endif
            <a class="button button-outline" href="{{ route('shop_admin.index') }}" style="text-decoration: none;">← Về danh sách khóa</a>
        </div>
    </div>

    @include('learning.admin.nav')

    <form class="portal-form admin-form" method="POST" enctype="multipart/form-data" action="{{ $course->exists ? route('shop_admin.courses.update', $course) : route('shop_admin.courses.store') }}">
        @csrf
        @if($course->exists) @method('PATCH') @endif

        {{-- Khối 1: Thông tin cơ bản --}}
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 22px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px;">1. Thông tin chung</h3>
            
            <label for="name" style="font-weight: 600;">Tên khóa học <span style="color: #dc2626;">*</span></label>
            <input id="name" name="name" value="{{ old('name', $course->name) }}" placeholder="Ví dụ: Khóa học IELTS Intensive 6.5+" required>

            <div class="form-columns" style="margin-top: 12px;">
                <div>
                    <label for="code" style="font-weight: 600;">Mã khóa học <span style="color: #dc2626;">*</span></label>
                    <input id="code" name="code" value="{{ old('code', $course->code) }}" placeholder="IELTS-INTENSIVE" required>
                </div>
                <div>
                    <label for="slug" style="font-weight: 600;">Đường dẫn Slug <span style="color: #dc2626;">*</span></label>
                    <input id="slug" name="slug" value="{{ old('slug', $course->slug) }}" placeholder="khoa-hoc-ielts-intensive" required>
                </div>
            </div>

            <div style="margin-top: 12px;">
                <label for="learning_category_id" style="font-weight: 600;">Danh mục đào tạo <span style="color: #dc2626;">*</span></label>
                <select id="learning_category_id" name="learning_category_id" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; font-size: 14px; width: 100%;">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('learning_category_id', $course->learning_category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Khối 2: Học phí & Thời hạn --}}
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 22px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px;">2. Học phí & Thời hạn học</h3>
            
            <div class="form-columns">
                <div>
                    <label for="price" style="font-weight: 600;">Giá gốc (đ) <span style="color: #dc2626;">*</span></label>
                    <input id="price" name="price" type="number" min="1" value="{{ old('price', (int)$course->price) }}" required>
                </div>
                <div>
                    <label for="sale_price" style="font-weight: 600;">Giá bán ưu đãi (đ, để trống nếu không giảm)</label>
                    <input id="sale_price" name="sale_price" type="number" min="1" value="{{ old('sale_price', $course->sale_price ? (int)$course->sale_price : null) }}">
                </div>
            </div>

            <div class="form-columns" style="margin-top: 12px;">
                <div>
                    <label for="access_days" style="font-weight: 600;">Thời hạn truy cập (ngày) <span style="color: #dc2626;">*</span></label>
                    <input id="access_days" name="access_days" type="number" min="1" value="{{ old('access_days', $course->access_days ?? 365) }}" required>
                    <span style="font-size: 12px; color: #64748b;">Mặc định 365 ngày (1 năm).</span>
                </div>
                <div>
                    <label for="duration_hours" style="font-weight: 600;">Số giờ học gợi ý <span style="color: #dc2626;">*</span></label>
                    <input id="duration_hours" name="duration_hours" type="number" min="1" value="{{ old('duration_hours', $course->duration_hours ?? 40) }}" required>
                </div>
            </div>
        </div>

        {{-- Khối 3: Ảnh & Video giới thiệu --}}
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 22px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px;">3. Hình ảnh & Video giới thiệu</h3>

            <div style="margin-bottom: 16px;">
                <label for="image" style="font-weight: 600;">Ảnh bìa khóa học (JPG, PNG, WebP tối đa 4 MB)</label>
                @if($course->thumbnail_path)
                    <div style="margin: 8px 0;">
                        <img src="{{ asset($course->thumbnail_path) }}" alt="Ảnh bìa hiện tại" style="height: 120px; border-radius: 8px; border: 1px solid #cbd5e1; object-fit: cover;">
                        <span style="font-size: 12px; color: #64748b; display: block;">Ảnh bìa hiện tại</span>
                    </div>
                @endif
                <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">
            </div>

            <div style="margin-top: 16px;">
                <label for="intro_video_url" style="font-weight: 600;">Video giới thiệu YouTube (Link xem thử)</label>
                <input id="intro_video_url" name="intro_video_url" type="url" value="{{ old('intro_video_url', $course->intro_video_url) }}" placeholder="https://www.youtube.com/watch?v=...">
                <span style="font-size: 12px; color: #64748b;">Video giới thiệu sẽ được phát ngay trên trang chi tiết khóa học.</span>
            </div>

            @if($course->intro_video_url && $embed = \App\Services\CourseVideo::embed($course->intro_video_url))
                <div style="margin-top: 12px; max-width: 480px;">
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px;">
                        <iframe src="{{ $embed }}" style="position: absolute; top:0; left:0; width:100%; height:100%; border:0;" allowfullscreen></iframe>
                    </div>
                </div>
            @endif

            @if($course->intro_video_url || $course->intro_video_path)
                <label class="checkbox-row" style="margin-top: 12px; color: #dc2626;">
                    <input type="checkbox" name="remove_video" value="1"> Gỡ video giới thiệu hiện tại
                </label>
            @endif
        </div>

        {{-- Khối 4: Nội dung & Mục tiêu --}}
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 22px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px;">4. Giới thiệu & Mục tiêu đào tạo</h3>

            <label for="summary" style="font-weight: 600;">Mô tả tóm tắt <span style="color: #dc2626;">*</span></label>
            <textarea id="summary" name="summary" rows="3" required placeholder="Tóm tắt ngắn 1-2 câu về khóa học...">{{ old('summary', $course->summary) }}</textarea>

            <div style="margin-top: 14px;">
                <label for="description" style="font-weight: 600;">Giới thiệu chi tiết</label>
                <textarea id="description" name="description" rows="5" placeholder="Thông tin chi tiết về khóa học...">{{ old('description', $course->description) }}</textarea>
            </div>

            <div style="margin-top: 14px;">
                <label for="outcomes" style="font-weight: 600;">Mục tiêu đạt được sau khóa học (Mỗi dòng một mục tiêu)</label>
                <textarea id="outcomes" name="outcomes" rows="4" placeholder="Nắm vững 1000 từ vựng cốt lõi&#10;Đạt điểm tối thiểu 6.5&#10;Tự tin giao tiếp...">{{ old('outcomes', implode("\n", $course->learning_outcomes ?? [])) }}</textarea>
            </div>

            <div style="margin-top: 14px;">
                <label for="audience" style="font-weight: 600;">Đối tượng phù hợp</label>
                <textarea id="audience" name="audience" rows="2" placeholder="Học sinh, sinh viên, người đi làm cần chứng chỉ...">{{ old('audience', $course->audience) }}</textarea>
            </div>
        </div>

        {{-- Khối 5: Trạng thái --}}
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; margin-bottom: 24px;">
            <label class="checkbox-row" style="font-weight: 600; color: #1e293b;">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $course->is_active ?? true))>
                Khóa học đang hoạt động
            </label>
            <label class="checkbox-row" style="font-weight: 600; color: #1e293b; margin-top: 10px;">
                <input type="checkbox" name="published" value="1" @checked(old('published', (bool)$course->published_at))>
                Công bố trang khóa học lên danh mục công khai
            </label>
            <label class="checkbox-row" style="font-weight: 600; color: #15803d; margin-top: 10px;">
                <input type="checkbox" name="is_for_sale" value="1" @checked(old('is_for_sale', $course->is_for_sale))>
                🟢 Mở bán trực tuyến (Học viên có thể thêm vào giỏ hàng và thanh toán QR)
            </label>
        </div>

        <div class="form-actions" style="display: flex; gap: 12px; align-items: center; margin-bottom: 36px;">
            <button class="button" style="font-weight: 700; padding: 10px 24px;">Lưu khóa học</button>
            <a class="button button-outline" href="{{ route('shop_admin.index') }}">Hủy</a>
        </div>
    </form>

    {{-- Danh sách bài học của khóa --}}
    @if($course->exists)
        @php
            $allLessons = $course->sections->flatMap->lessons;
        @endphp
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 16px;">
                <div>
                    <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0;">
                        Danh sách Bài học của khóa 
                        <span style="font-size: 14px; font-weight: 600; color: #2563eb; background: #eff6ff; padding: 2px 10px; border-radius: 20px;">
                            {{ $allLessons->count() }} bài
                        </span>
                    </h2>
                    <p style="font-size: 13px; color: #64748b; margin: 4px 0 0;">Quản lý nội dung chi tiết, video YouTube bài giảng và đề luyện kèm theo.</p>
                </div>
                <a class="button" href="{{ route('shop_admin.lessons.create', $course) }}" style="font-weight: 700;">+ Thêm bài học mới</a>
            </div>

            {{-- Ô tìm kiếm bài học nhanh --}}
            <div style="margin-bottom: 14px;">
                <input type="text" id="lessonSearchInput" onkeyup="filterLessonList()" placeholder="🔍 Lọc bài học theo tên..." style="padding: 8px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; width: 100%; max-width: 360px;">
            </div>

            <div style="max-height: 520px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px;">
                <table class="admin-table" id="lessonsTable" style="margin: 0;">
                    <thead>
                        <tr>
                            <th style="width: 50px;">Thứ tự</th>
                            <th>Tên bài học</th>
                            <th style="text-align: center; width: 90px;">Video</th>
                            <th style="text-align: center; width: 100px;">Học thử</th>
                            <th style="text-align: center; width: 110px;">Trạng thái</th>
                            <th style="text-align: right; width: 120px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allLessons as $lesson)
                            <tr class="lesson-row">
                                <td style="font-weight: 700; color: #64748b; text-align: center;">{{ $lesson->position }}</td>
                                <td>
                                    <strong style="color: #0f172a; font-size: 13.5px;" class="lesson-title-text">{{ $lesson->title }}</strong>
                                    <span style="font-size: 11.5px; color: #94a3b8; display: block;">{{ (int)ceil($lesson->duration_seconds/60) }} phút · Slug: {{ $lesson->slug }}</span>
                                </td>
                                <td style="text-align: center;">
                                    @if($lesson->media_path)
                                        <span title="Có video bài giảng" style="color: #dc2626; font-size: 16px;">🎬</span>
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($lesson->is_preview)
                                        <span style="background: #dbeafe; color: #1e40af; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Học thử</span>
                                    @else
                                        <span style="color: #94a3b8; font-size: 12px;">Khóa</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($lesson->is_published)
                                        <span style="background: #dcfce7; color: #15803d; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;">Đã công bố</span>
                                    @else
                                        <span style="background: #f1f5f9; color: #64748b; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px;">Bản nháp</span>
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    <a class="button button-small" href="{{ route('shop_admin.lessons.edit', [$course, $lesson]) }}" style="padding: 4px 10px; font-size: 12px; text-decoration: none;">
                                        Sửa bài →
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 24px; color: #94a3b8;">Chưa có bài học nào trong khóa học này. Hãy bấm "+ Thêm bài học mới" ở trên.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</section>

<script>
function filterLessonList() {
    const input = document.getElementById('lessonSearchInput');
    const filter = input.value.toLowerCase().trim();
    const rows = document.querySelectorAll('#lessonsTable tbody tr.lesson-row');
    rows.forEach(row => {
        const title = row.querySelector('.lesson-title-text')?.innerText.toLowerCase() || '';
        row.style.display = title.includes(filter) ? '' : 'none';
    });
}
</script>
@endsection
