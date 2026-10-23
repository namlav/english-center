@extends('layouts.admin')
@section('title', ($question->exists ? 'Sửa câu hỏi' : 'Thêm câu hỏi mới') . ' — ' . $paper->title)
@section('content')
<section class="shell admin-page">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
        <div>
            <span class="eyebrow" style="color: #2563eb; font-weight: 700; font-size: 13px; text-transform: uppercase;">ĐỀ THI: {{ $paper->title }}</span>
            <h1 style="font-size: 26px; font-weight: 800; color: #0f172a; margin: 4px 0 0;">{{ $question->exists ? 'Chỉnh sửa Câu ' . $question->position : 'Thêm câu hỏi mới' }}</h1>
        </div>
        <a class="button button-outline" href="{{ route('exam_admin.edit', $paper) }}" style="text-decoration: none;">← Quay lại đề thi</a>
    </div>

    @include('learning.admin.nav')

    <form class="portal-form admin-form" method="POST" action="{{ $question->exists ? route('exam_admin.questions.update', [$paper, $question]) : route('exam_admin.questions.store', $paper) }}">
        @csrf
        @if($question->exists) @method('PATCH') @endif

        {{-- Khối 1: Nội dung câu hỏi --}}
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 22px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px;">1. Nội dung câu hỏi</h3>

            <label for="prompt" style="font-weight: 600;">Nội dung câu hỏi / Đề bài <span style="color: #dc2626;">*</span></label>
            <textarea id="prompt" name="prompt" rows="5" required placeholder="Nhập câu hỏi hoặc đoạn văn cần điền từ..." style="font-size: 14.5px; line-height: 1.6;">{{ old('prompt', $question->prompt) }}</textarea>

            <div class="form-columns" style="margin-top: 14px;">
                <div>
                    <label for="type" style="font-weight: 600;">Dạng câu hỏi <span style="color: #dc2626;">*</span></label>
                    <select id="type" name="type" style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 10px; font-size: 14px; width: 100%;">
                        @foreach(['single_choice'=>'Trắc nghiệm chọn một đáp án','multiple_choice'=>'Trắc nghiệm chọn nhiều đáp án','true_false'=>'Đúng / Sai (True / False)','short_answer'=>'Điền câu trả lời ngắn'] as $key => $label)
                            <option value="{{ $key }}" @selected(old('type', $question->type) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="position" style="font-weight: 600;">Thứ tự câu <span style="color: #dc2626;">*</span></label>
                    <input id="position" name="position" type="number" min="1" value="{{ old('position', $question->position) }}" required>
                </div>
            </div>

            <div style="margin-top: 12px; max-width: 250px;">
                <label for="max_points" style="font-weight: 600;">Điểm số câu này <span style="color: #dc2626;">*</span></label>
                <input id="max_points" name="max_points" type="number" step="0.1" min="0.1" value="{{ old('max_points', $question->max_points ?? 1) }}" required>
            </div>
        </div>

        {{-- Khối 2: Các phương án lựa chọn A, B, C, D --}}
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 22px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 10px;">2. Các phương án lựa chọn</h3>
            <p style="font-size: 13px; color: #64748b; margin: 0 0 16px;">Với dạng trắc nghiệm, nhập ít nhất 2 lựa chọn (A và B). Câu đúng/sai tự động gán TRUE/FALSE; câu điền từ có thể bỏ trống phần này.</p>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach(['A' => '#2563eb', 'B' => '#059669', 'C' => '#d97706', 'D' => '#dc2626'] as $label => $color)
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="background: {{ $color }}; color: #ffffff; width: 32px; height: 32px; border-radius: 6px; display: grid; place-items: center; font-weight: 800; font-size: 14px; flex-shrink: 0;">
                            {{ $label }}
                        </span>
                        <input id="option-{{ $label }}" name="options[{{ $label }}]" value="{{ old('options.'.$label, $question->options->firstWhere('label', $label)?->content) }}" placeholder="Nhập nội dung phương án {{ $label }}..." style="margin: 0; width: 100%;">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Khối 3: Đáp án đúng & Lời giải chi tiết --}}
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 24px;">
            <h3 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 14px;">3. Đáp án đúng & Lời giải chi tiết</h3>

            <label for="answers" style="font-weight: 600;">Đáp án đúng <span style="color: #dc2626;">*</span></label>
            <input id="answers" name="answers" value="{{ old('answers', implode("\n", $question->answerKey?->accepted_answers ?? [])) }}" placeholder="Ví dụ: A (hoặc B, C, D, TRUE, FALSE)" required>
            <span style="font-size: 12px; color: #64748b; margin-top: 4px; display: block;">Nhập nhãn đáp án đúng (A, B, C hoặc D). Với nhiều đáp án đúng, mỗi đáp án xuống dòng một hàng.</span>

            <div style="margin-top: 16px;">
                <label for="explanation" style="font-weight: 600;">Lời giải thích chi tiết <span style="color: #dc2626;">*</span></label>
                <textarea id="explanation" name="explanation" rows="5" required placeholder="Giải thích vì sao đáp án này đúng, phân tích ngữ pháp, dịch nghĩa câu..." style="font-size: 14px; line-height: 1.6;">{{ old('explanation', $question->answerKey?->explanation) }}</textarea>
                <span style="font-size: 12px; color: #64748b;">Lời giải sẽ được hiển thị cho thí sinh sau khi nộp bài thi để ôn luyện.</span>
            </div>

            <label class="checkbox-row" style="margin-top: 14px; font-size: 13px; color: #475569;">
                <input name="case_sensitive" type="checkbox" value="1" @checked(old('case_sensitive', $question->answerKey?->case_sensitive))>
                Phân biệt chữ hoa / chữ thường khi chấm bài điền từ
            </label>
        </div>

        <div class="form-actions" style="display: flex; gap: 12px; align-items: center;">
            <button class="button" style="font-weight: 700; padding: 10px 24px;">Lưu câu hỏi & đáp án</button>
            <a class="button button-outline" href="{{ route('exam_admin.edit', $paper) }}">Hủy bỏ</a>
        </div>
    </form>
</section>
@endsection
