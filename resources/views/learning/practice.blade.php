@extends('layouts.learning')
@section('title', $paper->title.' — English Center')
@section('content')
<div class="shell practice-page">
    <nav class="breadcrumbs" aria-label="Đường dẫn"><a href="{{ route('home') }}">Trang chủ</a><span>/</span><a href="{{ route('practice.index') }}">Bài luyện</a><span>/</span><span>{{ $paper->learningCategory->name }}</span></nav>
    
    <div class="practice-page-heading">
        <div>
            <span class="eyebrow">{{ $paper->collection?->title ?? 'GÓC LUYỆN TẬP' }}</span>
            <h1>{{ $paper->title }}</h1>
            <p>Đọc kỹ hướng dẫn, trả lời các câu hỏi rồi tự kiểm tra khi bạn đã sẵn sàng.</p>
            <div class="card-meta">
                <span><x-study-icon name="clock" /> {{ (int) ceil($durationSeconds / 60) }} phút</span>
                <span><x-study-icon name="file" /> {{ $paper->questions_count }} câu hỏi</span>
                <span><x-study-icon name="book" /> {{ $paper->sections->count() }} phần</span>
            </div>
            @if($results === null)
                <div class="exam-mode-switcher" style="display: inline-flex; background: #e2e8f0; padding: 4px; border-radius: 8px; margin-top: 1.25rem;">
                    <a href="{{ route('practice.show', ['slug' => $paper->slug, 'mode' => 'practice']) }}" style="padding: 8px 18px; border-radius: 6px; font-weight: 600; text-decoration: none; font-size: 0.925rem; {{ $mode !== 'mock' ? 'background: #ffffff; color: #1e3a8a; box-shadow: 0 1px 3px rgba(0,0,0,0.1);' : 'color: #64748b;' }}">
                        📖 Luyện tập tự do
                    </a>
                    <a href="{{ route('practice.show', ['slug' => $paper->slug, 'mode' => 'mock']) }}" style="padding: 8px 18px; border-radius: 6px; font-weight: 600; text-decoration: none; font-size: 0.925rem; {{ $mode === 'mock' ? 'background: #2563eb; color: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.1);' : 'color: #64748b;' }}">
                        ⏱️ Thi thử tính giờ ({{ (int) ceil($durationSeconds / 60) }} phút)
                    </a>
                </div>
            @endif
        </div>
        <a class="text-link" href="{{ route('practice.index') }}">← Chọn bài khác</a>
    </div>

    @if($mockSummary !== null)
        <section class="mock-score-card" id="result" tabindex="-1" style="background: linear-gradient(135deg, #1e3a8a, #2563eb); color: white; border-radius: 12px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.4);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                <div style="max-width: 600px;">
                    <span style="background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.05em;">KẾT QUẢ THI THỬ TRỰC TUYẾN</span>
                    <h2 style="font-size: 1.85rem; font-weight: 800; margin: 0.5rem 0 0.5rem 0; color: #ffffff;">{{ $paper->title }}</h2>
                    <p style="opacity: 0.95; margin: 0; font-size: 1rem; line-height: 1.5;">
                        @if($mockSummary['percent'] >= 80)
                            🎉 <strong>Xuất sắc!</strong> Bạn đã nắm rất vững kiến thức và trả lời chính xác hầu hết câu hỏi.
                        @elseif($mockSummary['percent'] >= 65)
                            👏 <strong>Khá tốt!</strong> Bạn đã đạt chuẩn yêu cầu, hãy xem lại các câu sai bên dưới để hoàn thiện hơn nhé.
                        @elseif($mockSummary['percent'] >= 50)
                            💪 <strong>Đạt yêu cầu!</strong> Cần ôn luyện thêm các phần từ vựng và cấu trúc ngữ pháp trọng tâm.
                        @else
                            📚 <strong>Cần nỗ lực hơn!</strong> Hãy đọc kỹ đáp án và lời giải chi tiết bên dưới để rút kinh nghiệm nhé.
                        @endif
                    </p>
                </div>
                <div style="background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); border-radius: 12px; padding: 1.25rem 2rem; text-align: center; border: 1px solid rgba(255,255,255,0.3); min-width: 170px;">
                    <div style="font-size: 0.85rem; opacity: 0.9; text-transform: uppercase; font-weight: 600;">Điểm số đạt được</div>
                    <div style="font-size: 2.75rem; font-weight: 900; line-height: 1.1;">{{ $mockSummary['score10'] }}<span style="font-size: 1.25rem; font-weight: 500;">/10</span></div>
                    @if(str_contains(mb_strtolower($paper->learningCategory->name ?? ''), 'ielts'))
                        <div style="font-size: 0.95rem; font-weight: 700; background: #fbbf24; color: #78350f; border-radius: 6px; padding: 3px 10px; margin-top: 6px;">Band {{ number_format($mockSummary['scaledScore'], 1) }}</div>
                    @elseif(str_contains(mb_strtolower($paper->learningCategory->name ?? ''), 'toeic'))
                        <div style="font-size: 0.95rem; font-weight: 700; background: #34d399; color: #064e3b; border-radius: 6px; padding: 3px 10px; margin-top: 6px;">TOEIC {{ $mockSummary['scaledScore'] }}</div>
                    @endif
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.2);">
                <div style="background: rgba(0,0,0,0.18); padding: 0.75rem 1rem; border-radius: 8px;">
                    <div style="font-size: 0.8rem; opacity: 0.85;">Số câu đúng</div>
                    <div style="font-size: 1.35rem; font-weight: 800;">{{ $mockSummary['correct'] }} / {{ $mockSummary['total'] }} <span style="font-size: 0.85rem; font-weight: normal;">({{ $mockSummary['percent'] }}%)</span></div>
                </div>
                <div style="background: rgba(0,0,0,0.18); padding: 0.75rem 1rem; border-radius: 8px;">
                    <div style="font-size: 0.8rem; opacity: 0.85;">Số câu chưa đúng</div>
                    <div style="font-size: 1.35rem; font-weight: 800;">{{ $mockSummary['wrong'] }} câu</div>
                </div>
                <div style="background: rgba(0,0,0,0.18); padding: 0.75rem 1rem; border-radius: 8px;">
                    <div style="font-size: 0.8rem; opacity: 0.85;">Thời gian làm bài</div>
                    <div style="font-size: 1.35rem; font-weight: 800;">{{ $mockSummary['formattedTime'] }}</div>
                </div>
                <div style="background: rgba(0,0,0,0.18); padding: 0.75rem 1rem; border-radius: 8px;">
                    <div style="font-size: 0.8rem; opacity: 0.85;">Lịch sử thi</div>
                    <div style="font-size: 1rem; font-weight: 700; margin-top: 4px;">{{ auth()->check() ? 'Đã lưu kết quả ✓' : 'Làm bài tự do' }}</div>
                </div>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap;">
                <a href="{{ route('practice.show', ['slug' => $paper->slug, 'mode' => 'mock']) }}" class="button" style="background: #ffffff; color: #1e3a8a; font-weight: 700;">🔄 Thi lại bài này</a>
                <a href="{{ route('practice.index') }}" class="button button-outline" style="border-color: rgba(255,255,255,0.7); color: white;">← Danh sách đề thi</a>
            </div>
        </section>
    @elseif($results !== null)
        <section class="self-check-result" id="result" tabindex="-1" data-result><span class="result-symbol"><x-study-icon name="check" /></span><div><span class="eyebrow">KẾT QUẢ TỰ KIỂM TRA</span><h2>{{ $results['correct'] }} / {{ $results['total'] }} câu đúng</h2><p>{{ $results['points'] }} / {{ $results['max_points'] }} điểm cho phần có đáp án. Xem lời giải dưới từng câu để ôn lại.</p></div><a href="{{ route('practice.show', $paper->slug) }}" class="button button-outline">Làm lại bài này</a></section>
    @endif

    @if($mode === 'mock' && $results === null)
        <div id="sticky-timer-bar" style="position: sticky; top: 0; z-index: 999; background: #ffffff; border-bottom: 2px solid #2563eb; box-shadow: 0 4px 14px rgba(0,0,0,0.08); padding: 0.85rem 1.5rem; margin-bottom: 1.5rem; border-radius: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.85rem;">
                    <span style="font-size: 1.5rem;">⏱️</span>
                    <div>
                        <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 700;">Thời gian còn lại</div>
                        <div id="timer-display" style="font-size: 1.6rem; font-weight: 800; font-family: monospace; color: #1e293b; line-height: 1;">--:--</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 1.25rem;">
                    <div style="font-size: 0.95rem; color: #475569;">
                        Tiến độ: <strong id="progress-answered" style="color: #2563eb;">0</strong> / <strong>{{ $paper->questions_count }}</strong> câu
                    </div>
                    <button type="button" onclick="confirmSubmitMockExam()" class="button" style="background: #e11d48; color: white; padding: 0.55rem 1.35rem; font-weight: 700; border: none; border-radius: 6px; cursor: pointer;">Nộp bài thi</button>
                </div>
            </div>
            <div style="width: 100%; height: 5px; background: #e2e8f0; border-radius: 3px; margin-top: 0.65rem; overflow: hidden;">
                <div id="timer-progress-fill" style="width: 100%; height: 100%; background: #2563eb; transition: width 1s linear, background-color 0.5s;"></div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('practice.check', $paper->slug) }}#result" class="practice-form" id="practice-form">
        @csrf
        <input type="hidden" name="mode" value="{{ $mode }}">
        <input type="hidden" name="elapsed_seconds" id="elapsed_seconds" value="0">

        @php $number = 0; $hasObjective = false; @endphp
        @foreach($paper->sections as $section)
            <section class="exercise-section" aria-labelledby="section-{{ $section->id }}"><div class="exercise-section-title"><span>PHẦN {{ $loop->iteration }}</span><h2 id="section-{{ $section->id }}">{{ $section->title }}</h2></div>
                @foreach($section->groups as $group)
                    <div class="exercise-instructions"><x-study-icon name="book" /><div><h3>Trước khi bắt đầu</h3><p>{{ \Illuminate\Support\Str::before(\Illuminate\Support\Str::before($group->instructions ?? $section->instructions ?? '', 'Source:'), "\n\n") }}</p>@if($group->passage)<p class="study-notes">{{ $group->passage }}</p>@endif</div></div>
                    @foreach($group->questions as $question)
                        @php
                            $number++;
                            $selected = (array) old('answers.'.$question->id, $responses[$question->id] ?? []);
                            $itemResult = $results['items'][$question->id] ?? null;
                            $objective = in_array($question->type, ['short_answer', 'single_choice', 'multiple_choice', 'true_false'], true);
                            $hasObjective = $hasObjective || $objective;
                        @endphp
                        <fieldset class="question-box {{ $itemResult !== null ? ($itemResult['correct'] ? 'question-correct' : 'question-incorrect') : '' }}">
                            <legend><span class="question-number">{{ $number }}</span><span>{{ $question->prompt }}</span></legend>
                            @if($question->options->isNotEmpty())
                                <div class="answer-options">@foreach($question->options as $option)<label class="answer-option"><input type="{{ $question->type === 'multiple_choice' ? 'checkbox' : 'radio' }}" name="answers[{{ $question->id }}][]" value="{{ $option->label }}" @checked(in_array($option->label, $selected, true))><span class="option-letter">{{ $option->label }}</span><span>{{ $option->content }}</span></label>@endforeach</div>
                            @elseif($objective)
                                <label class="visually-hidden" for="answer-{{ $question->id }}">Đáp án câu {{ $number }}</label><input class="short-answer" type="text" name="answers[{{ $question->id }}][]" id="answer-{{ $question->id }}" maxlength="2000" value="{{ is_string($selected[0] ?? null) ? $selected[0] : '' }}" placeholder="Nhập câu trả lời của bạn" autocomplete="off">
                            @else
                                <p class="manual-note">Bài nói hoặc viết: thực hành theo yêu cầu và nhờ giảng viên nhận xét.</p>
                            @endif
                            @if($itemResult !== null)
                                <div class="answer-feedback"><strong>{{ $itemResult['correct'] ? '✓ Chính xác' : '✗ Cùng ôn lại nhé' }}</strong><p>Đáp án đúng: <b>@foreach($itemResult['answers'] as $answer){{ $answer }}@if($question->options->firstWhere('label', $answer)) — {{ $question->options->firstWhere('label', $answer)->content }}@endif{{ !$loop->last ? ' / ' : '' }}@endforeach</b></p><p>{{ \Illuminate\Support\Str::before($itemResult['explanation'] ?? '', ' Source:') }}</p></div>
                            @endif
                        </fieldset>
                    @endforeach
                    @if(data_get($group->rendering_data, 'source.attribution'))
                        <details class="source-credit"><summary>Nguồn học liệu và giấy phép</summary><p>{{ data_get($group->rendering_data, 'source.attribution') }}</p><p>{{ data_get($group->rendering_data, 'source.adaptation') }}</p>@if(\Illuminate\Support\Str::startsWith(data_get($group->rendering_data, 'source.url', ''), ['https://', 'http://']))<a href="{{ data_get($group->rendering_data, 'source.url') }}" target="_blank" rel="noopener noreferrer">Xem tài liệu gốc ↗</a>@endif @if(\Illuminate\Support\Str::startsWith(data_get($group->rendering_data, 'source.license_url', ''), ['https://', 'http://'])) · <a href="{{ data_get($group->rendering_data, 'source.license_url') }}" target="_blank" rel="noopener noreferrer">{{ data_get($group->rendering_data, 'source.license') }}</a>@endif</details>
                    @endif
                @endforeach
            </section>
        @endforeach
        
        @if($hasObjective)
            <div class="practice-submit">
                @if($mode === 'mock')
                    <div><strong>Hoàn thành bài thi thử?</strong><p>Đồng hồ đang đếm ngược. Bạn có thể nộp bài bất cứ lúc nào hoặc đợi hết giờ.</p></div>
                    <button class="button" type="button" onclick="confirmSubmitMockExam()" style="background: #e11d48; border-color: #e11d48;">Nộp bài thi thử <x-study-icon name="check" /></button>
                @else
                    <div><strong>Sẵn sàng xem kết quả?</strong><p>Bạn có thể kiểm tra cả khi chưa trả lời hết các câu.</p></div>
                    <button class="button" type="submit">Tự kiểm tra đáp án <x-study-icon name="check" /></button>
                @endif
            </div>
        @endif
    </form>
</div>

@if($mode === 'mock' && $results === null)
<script>
document.addEventListener('DOMContentLoaded', function() {
    let totalSeconds = {{ $durationSeconds }};
    let remainingSeconds = totalSeconds;
    const timerDisplay = document.getElementById('timer-display');
    const progressFill = document.getElementById('timer-progress-fill');
    const elapsedInput = document.getElementById('elapsed_seconds');
    const form = document.getElementById('practice-form');

    function updateTimer() {
        if (remainingSeconds <= 0) {
            clearInterval(timerInterval);
            timerDisplay.textContent = "00:00";
            progressFill.style.width = "0%";
            elapsedInput.value = totalSeconds;
            alert("Đã hết thời gian làm bài! Hệ thống đang tự động nộp bài thi của bạn.");
            form.submit();
            return;
        }

        const mins = Math.floor(remainingSeconds / 60);
        const secs = remainingSeconds % 60;
        timerDisplay.textContent = String(mins).padStart(2, '0') + ":" + String(secs).padStart(2, '0');

        const pct = (remainingSeconds / totalSeconds) * 100;
        progressFill.style.width = pct + "%";

        if (remainingSeconds <= 60) {
            timerDisplay.style.color = "#dc2626";
            progressFill.style.backgroundColor = "#dc2626";
        } else if (remainingSeconds <= 300) {
            timerDisplay.style.color = "#d97706";
            progressFill.style.backgroundColor = "#d97706";
        }

        elapsedInput.value = totalSeconds - remainingSeconds;
        remainingSeconds--;
    }

    updateTimer();
    const timerInterval = setInterval(updateTimer, 1000);

    function countAnswered() {
        const formInputs = form.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked, input.short-answer');
        const answeredNames = new Set();
        formInputs.forEach(input => {
            if (input.type === 'radio' || input.type === 'checkbox') {
                answeredNames.add(input.name);
            } else if (input.value.trim() !== '') {
                answeredNames.add(input.name);
            }
        });
        const countEl = document.getElementById('progress-answered');
        if (countEl) countEl.textContent = answeredNames.size;
    }

    form.addEventListener('change', countAnswered);
    form.addEventListener('input', countAnswered);
    countAnswered();

    window.confirmSubmitMockExam = function() {
        if (confirm("Bạn có chắc chắn muốn nộp bài thi ngay bây giờ?")) {
            form.submit();
        }
    };
});
</script>
@endif
@endsection
