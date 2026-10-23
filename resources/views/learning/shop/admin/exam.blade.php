@extends('layouts.admin')
@section('title', ($paper->exists ? 'Soạn đề: ' . $paper->title : 'Tạo đề thi mới'))
@section('content')
<section class="shell admin-page">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
        <div>
            <span class="eyebrow" style="color: #2563eb; font-weight: 700; font-size: 13px; text-transform: uppercase;">KHẢO THÍ & ĐỀ THI TRỰC TUYẾN</span>
            <h1 style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 4px 0 0;">{{ $paper->exists ? 'Soạn đề thi: ' . $paper->title : 'Tạo bộ đề thi mới' }}</h1>
        </div>
        <div style="display: flex; gap: 8px;">
            @if($paper->exists)
                <a class="button button-outline" href="{{ route('practice.show', $paper->slug) }}" target="_blank" style="text-decoration: none;">👁️ Làm thử đề</a>
            @endif
            <a class="button button-outline" href="{{ route('exam_admin.index') }}" style="text-decoration: none;">← Về ngân hàng đề</a>
        </div>
    </div>

    @include('learning.admin.nav')

    <form class="portal-form admin-form" action="{{ $paper->exists ? route('exam_admin.update', $paper) : route('exam_admin.store') }}" method="POST">
        @csrf
        @if($paper->exists) @method('PATCH') @endif

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 22px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px;">1. Thông tin đề thi</h3>

            <label for="title" style="font-weight: 600;">Tên đề thi <span style="color: #dc2626;">*</span></label>
            <input id="title" name="title" value="{{ old('title', $paper->title) }}" placeholder="Ví dụ: TOEIC Mini Test 01 — Reading & Listening" required>

            <div class="form-columns" style="margin-top: 12px;">
                <div>
                    <label for="slug" style="font-weight: 600;">Đường dẫn Slug <span style="color: #dc2626;">*</span></label>
                    <input id="slug" name="slug" value="{{ old('slug', $paper->slug) }}" placeholder="toeic-mini-test-01" required>
                </div>
                <div>
                    <label for="learning_category_id" style="font-weight: 600;">Danh mục bài thi <span style="color: #dc2626;">*</span></label>
                    <select id="learning_category_id" name="learning_category_id" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; font-size: 14px; width: 100%;">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('learning_category_id', $paper->learning_category_id) == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="margin-top: 14px;">
                <label for="description" style="font-weight: 600;">Giới thiệu / Hướng dẫn làm bài</label>
                <textarea id="description" name="description" rows="3" placeholder="Hướng dẫn thí sinh cách làm bài, số lượng câu...">{{ old('description', $paper->description) }}</textarea>
            </div>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 22px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px;">2. Kỹ năng & Thời lượng</h3>

            <div class="form-columns">
                <div>
                    <label for="skill" style="font-weight: 600;">Kỹ năng kiểm tra <span style="color: #dc2626;">*</span></label>
                    <select id="skill" name="skill" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; font-size: 14px; width: 100%;">
                        @foreach(['reading'=>'Đọc hiểu (Reading)','listening'=>'Nghe hiểu (Listening)','writing'=>'Viết (Writing)','speaking'=>'Nói (Speaking)','mixed'=>'Tổng hợp nhiều kỹ năng'] as $key => $label)
                            <option value="{{ $key }}" @selected(old('skill', $paper->skill) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="duration_seconds" style="font-weight: 600;">Thời gian làm bài (giây) <span style="color: #dc2626;">*</span></label>
                    <input id="duration_seconds" name="duration_seconds" type="number" min="60" max="18000" value="{{ old('duration_seconds', $paper->duration_seconds ?? 900) }}" required>
                    <span style="font-size: 12px; color: #64748b;">Ví dụ: 900s = 15 phút; 2700s = 45 phút; 7200s = 120 phút.</span>
                </div>
            </div>

            <div style="margin-top: 14px;">
                <label for="status" style="font-weight: 600;">Trạng thái công bố <span style="color: #dc2626;">*</span></label>
                <select id="status" name="status" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; font-size: 14px; width: 100%;">
                    @foreach(['draft'=>'Bản nháp (Chưa hiện ra ngoài)','published'=>'Công bố (Thí sinh có thể làm bài)','archived'=>'Lưu trữ'] as $key => $label)
                        <option value="{{ $key }}" @selected(old('status', $paper->status) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                <span style="font-size: 12px; color: #64748b;">Lưu ý: Đề thi cần có ít nhất một câu hỏi có đáp án mới có thể Công bố.</span>
            </div>
        </div>

        <div class="form-actions" style="display: flex; gap: 12px; align-items: center; margin-bottom: 36px;">
            <button class="button" style="font-weight: 700; padding: 10px 24px;">Lưu đề thi</button>
            <a class="button button-outline" href="{{ route('exam_admin.index') }}">Hủy</a>
        </div>
    </form>

    {{-- Ngân hàng câu hỏi của đề --}}
    @if($paper->exists)
        <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; box-shadow: 0 1px 4px rgba(0,0,0,0.03);">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 16px;">
                <div>
                    <h2 style="font-size: 20px; font-weight: 800; color: #0f172a; margin: 0;">
                        Câu hỏi & Đáp án của đề thi
                        <span style="font-size: 14px; font-weight: 600; color: #2563eb; background: #eff6ff; padding: 2px 10px; border-radius: 20px;">
                            {{ $paper->questions->count() }} câu hỏi
                        </span>
                    </h2>
                    <p style="font-size: 13px; color: #64748b; margin: 4px 0 0;">Mỗi câu hỏi có 4 phương án A-B-C-D, đáp án đúng và lời giải chi tiết theo chuẩn Study4 / IIG.</p>
                </div>
                <a class="button" href="{{ route('exam_admin.questions.create', $paper) }}" style="font-weight: 700;">+ Thêm câu hỏi</a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 14px;">
                @forelse($paper->questions as $question)
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 16px 20px; display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                                <span style="background: #2563eb; color: #ffffff; font-size: 12px; font-weight: 800; padding: 2px 8px; border-radius: 4px;">
                                    Câu {{ $question->position }}
                                </span>
                                <span style="font-size: 12px; color: #64748b;">
                                    Dạng: {{ $question->type === 'single_choice' ? 'Trắc nghiệm đơn' : ($question->type === 'true_false' ? 'Đúng / Sai' : 'Điền từ') }} · {{ $question->max_points }} điểm
                                </span>
                                @if($question->answerKey)
                                    <span style="background: #ecfdf5; color: #065f46; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px;">
                                        Đáp án: {{ implode(', ', $question->answerKey->accepted_answers ?? []) }}
                                    </span>
                                @endif
                            </div>
                            <p style="font-size: 14.5px; font-weight: 600; color: #1e293b; margin: 0 0 6px 0;">
                                {{ $question->prompt }}
                            </p>
                            @if($question->options->isNotEmpty())
                                <div style="display: flex; flex-wrap: wrap; gap: 8px 18px; font-size: 13px; color: #475569;">
                                    @foreach($question->options as $opt)
                                        <span><strong>{{ $opt->label }}.</strong> {{ $opt->content }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <a class="button button-small" href="{{ route('exam_admin.questions.edit', [$paper, $question]) }}" style="padding: 6px 14px; font-size: 12.5px; text-decoration: none; flex-shrink: 0;">
                            Sửa câu →
                        </a>
                    </div>
                @empty
                    <div style="text-align: center; padding: 32px; color: #94a3b8; background: #f8fafc; border-radius: 8px;">
                        Chưa có câu hỏi nào trong đề thi này. Hãy bấm "+ Thêm câu hỏi" để bắt đầu soạn đề.
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</section>
@endsection
