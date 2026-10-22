@extends('layouts.learning')
@section('title', $paper->title.' — English Center')
@section('content')
<div class="shell practice-page">
    <nav class="breadcrumbs" aria-label="Đường dẫn"><a href="{{ route('home') }}">Trang chủ</a><span>/</span><a href="{{ route('practice.index') }}">Bài luyện</a><span>/</span><span>{{ $paper->learningCategory->name }}</span></nav>
    <div class="practice-page-heading"><div><span class="eyebrow">{{ $paper->collection?->title ?? 'GÓC LUYỆN TẬP' }}</span><h1>{{ $paper->title }}</h1><p>Đọc kỹ hướng dẫn, trả lời các câu hỏi rồi tự kiểm tra khi bạn đã sẵn sàng.</p><div class="card-meta"><span><x-study-icon name="clock" /> {{ (int) ceil($paper->duration_seconds / 60) }} phút gợi ý</span><span><x-study-icon name="file" /> {{ $paper->questions_count }} câu hỏi</span><span><x-study-icon name="book" /> {{ $paper->sections->count() }} phần</span></div></div><a class="text-link" href="{{ route('practice.index') }}">← Chọn bài khác</a></div>
    @if($results !== null)
        <section class="self-check-result" id="result" tabindex="-1" data-result><span class="result-symbol"><x-study-icon name="check" /></span><div><span class="eyebrow">KẾT QUẢ TỰ KIỂM TRA</span><h2>{{ $results['correct'] }} / {{ $results['total'] }} câu đúng</h2><p>{{ $results['points'] }} / {{ $results['max_points'] }} điểm cho phần có đáp án. Xem lời giải dưới từng câu để ôn lại.</p></div><a href="{{ route('practice.show', $paper->slug) }}" class="button button-outline">Làm lại bài này</a></section>
    @endif
    <form method="POST" action="{{ route('practice.check', $paper->slug) }}#result" class="practice-form">
        @csrf
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
                                <div class="answer-feedback"><strong>{{ $itemResult['correct'] ? 'Chính xác' : 'Cùng ôn lại nhé' }}</strong><p>Đáp án: <b>@foreach($itemResult['answers'] as $answer){{ $answer }}@if($question->options->firstWhere('label', $answer)) — {{ $question->options->firstWhere('label', $answer)->content }}@endif{{ !$loop->last ? ' / ' : '' }}@endforeach</b></p><p>{{ \Illuminate\Support\Str::before($itemResult['explanation'] ?? '', ' Source:') }}</p></div>
                            @endif
                        </fieldset>
                    @endforeach
                    @if(data_get($group->rendering_data, 'source.attribution'))
                        <details class="source-credit"><summary>Nguồn học liệu và giấy phép</summary><p>{{ data_get($group->rendering_data, 'source.attribution') }}</p><p>{{ data_get($group->rendering_data, 'source.adaptation') }}</p>@if(\Illuminate\Support\Str::startsWith(data_get($group->rendering_data, 'source.url', ''), ['https://', 'http://']))<a href="{{ data_get($group->rendering_data, 'source.url') }}" target="_blank" rel="noopener noreferrer">Xem tài liệu gốc ↗</a>@endif @if(\Illuminate\Support\Str::startsWith(data_get($group->rendering_data, 'source.license_url', ''), ['https://', 'http://'])) · <a href="{{ data_get($group->rendering_data, 'source.license_url') }}" target="_blank" rel="noopener noreferrer">{{ data_get($group->rendering_data, 'source.license') }}</a>@endif</details>
                    @endif
                @endforeach
            </section>
        @endforeach
        @if($hasObjective)<div class="practice-submit"><div><strong>Sẵn sàng xem kết quả?</strong><p>Bạn có thể kiểm tra cả khi chưa trả lời hết các câu.</p></div><button class="button" type="submit">Tự kiểm tra đáp án <x-study-icon name="check" /></button></div>@endif
    </form>
</div>
@endsection
