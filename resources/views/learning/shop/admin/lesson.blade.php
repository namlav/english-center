@extends('layouts.learning')
@section('title', ($lesson->exists ? 'Sửa bài học' : 'Thêm bài học') . ' — ' . $course->name)
@section('content')
<section class="shell admin-page">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
        <div>
            <span class="eyebrow" style="color: #2563eb; font-weight: 700; font-size: 13px; text-transform: uppercase;">KHÓA HỌC: {{ $course->name }}</span>
            <h1 style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 4px 0 0;">{{ $lesson->exists ? 'Chỉnh sửa bài học: ' . $lesson->title : 'Thêm bài học mới' }}</h1>
        </div>
        <a class="button button-outline" href="{{ route('shop_admin.courses.edit', $course) }}" style="text-decoration: none;">← Quay lại khóa học</a>
    </div>

    @include('learning.admin.nav')

    <form class="portal-form admin-form" method="POST" action="{{ $lesson->exists ? route('shop_admin.lessons.update', [$course, $lesson]) : route('shop_admin.lessons.store', $course) }}">
        @csrf
        @if($lesson->exists) @method('PATCH') @endif

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; margin-bottom: 22px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px;">1. Thông tin cơ bản</h3>
            
            <label for="title" style="font-weight: 600;">Tên bài học <span style="color: #dc2626;">*</span></label>
            <input id="title" name="title" value="{{ old('title', $lesson->title) }}" placeholder="Ví dụ: Unit 1: Introduction to IELTS Listening" required>

            <div class="form-columns" style="margin-top: 12px;">
                <div>
                    <label for="slug" style="font-weight: 600;">Đường dẫn Slug <span style="color: #dc2626;">*</span></label>
                    <input id="slug" name="slug" value="{{ old('slug', $lesson->slug) }}" placeholder="unit-1-listening-intro" required>
                </div>
                <div>
                    <label for="position" style="font-weight: 600;">Thứ tự bài học <span style="color: #dc2626;">*</span></label>
                    <input id="position" name="position" type="number" min="1" value="{{ old('position', $lesson->position) }}" required>
                </div>
            </div>
            
            <div style="margin-top: 12px;">
                <label for="duration_seconds" style="font-weight: 600;">Thời lượng bài học (giây) <span style="color: #dc2626;">*</span></label>
                <input id="duration_seconds" name="duration_seconds" type="number" min="0" value="{{ old('duration_seconds', $lesson->duration_seconds ?? 600) }}" required>
                <span style="font-size: 12px; color: #64748b;">Ví dụ: 600 giây = 10 phút; 1800 giây = 30 phút.</span>
            </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; margin-bottom: 22px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px;">2. Video bài giảng & Đề luyện</h3>
            
            <label for="media_path" style="font-weight: 600;">Link Video bài học YouTube</label>
            <input id="media_path" name="media_path" type="url" value="{{ old('media_path', $lesson->media_path) }}" placeholder="https://www.youtube.com/watch?v=... hoặc https://youtu.be/...">
            <span style="font-size: 12px; color: #64748b; margin-top: 4px; display: block;">Hệ thống tự động nhúng video chuẩn YouTube bảo mật (No-cookie embed) ngay trong trang học bài của học viên.</span>

            @if($lesson->media_path && $embed = \App\Services\CourseVideo::embed($lesson->media_path))
                <div style="margin-top: 12px; max-width: 480px;">
                    <span style="font-size: 12px; font-weight: 600; color: #059669;">✓ Video đang phát thử:</span>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; margin-top: 6px;">
                        <iframe src="{{ $embed }}" style="position: absolute; top:0; left:0; width:100%; height:100%; border:0;" allowfullscreen></iframe>
                    </div>
                </div>
            @endif

            <div style="margin-top: 18px;">
                <label for="exam_ids" style="font-weight: 600;">Gắn Đề luyện kèm theo (Giữ phím Ctrl để chọn nhiều đề)</label>
                <select id="exam_ids" name="exam_ids[]" multiple size="6" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 8px; font-size: 13px;">
                    @foreach($papers as $paper)
                        <option value="{{ $paper->id }}" @selected(in_array($paper->id, old('exam_ids', $selected)))>
                            {{ $paper->title }} ({{ $paper->questions_count ?? $paper->questions()->count() }} câu hỏi)
                        </option>
                    @endforeach
                </select>
                <span style="font-size: 12px; color: #64748b;">Học viên học xong bài có thể làm ngay các đề trắc nghiệm này để củng cố kiến thức.</span>
            </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; margin-bottom: 22px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px;">3. Nội dung bài giảng</h3>
            
            <label for="content" style="font-weight: 600;">Nội dung bài học (Văn bản / Hướng dẫn / Từ vựng) <span style="color: #dc2626;">*</span></label>
            <textarea id="content" name="content" rows="16" required placeholder="Nhập tóm tắt bài học, từ vựng, tài liệu ghi chú..." style="font-family: inherit; font-size: 14px; line-height: 1.6;">{{ old('content', $lesson->content) }}</textarea>
        </div>

        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px; margin-bottom: 24px;">
            <label class="checkbox-row" style="font-weight: 600; color: #1e293b;">
                <input name="is_preview" type="checkbox" value="1" @checked(old('is_preview', $lesson->is_preview))>
                ⭐ Cho phép học thử miễn phí (Học viên chưa mua khóa học vẫn xem được bài này)
            </label>
            
            <label class="checkbox-row" style="font-weight: 600; color: #1e293b; margin-top: 10px;">
                <input name="is_published" type="checkbox" value="1" @checked(old('is_published', $lesson->is_published ?? true))>
                ✅ Công bố bài học ngay lên giáo trình
            </label>
        </div>

        <div class="form-actions" style="display: flex; gap: 12px; align-items: center;">
            <button class="button" style="font-weight: 700; padding: 10px 24px;">Lưu bài học</button>
            <a class="button button-outline" href="{{ route('shop_admin.courses.edit', $course) }}">Hủy bỏ</a>
        </div>
    </form>
</section>
@endsection
