<?php

namespace App\Http\Controllers;

use App\Models\ExamPaper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PracticeController extends Controller
{
    private const OBJECTIVE_TYPES = ['single_choice', 'multiple_choice', 'short_answer', 'true_false'];

    public function show(string $slug): View
    {
        $paper = $this->paper($slug);

        return view('learning.practice', ['paper' => $paper, 'results' => null, 'responses' => []]);
    }

    public function check(Request $request, string $slug): View
    {
        $paper = $this->paper($slug);
        $data = $request->validate([
            'answers' => ['nullable', 'array', 'max:500'],
            'answers.*' => ['array', 'max:50'],
            'answers.*.*' => ['nullable', 'string', 'max:2000'],
        ]);
        $responses = $data['answers'] ?? [];
        $questions = new Collection($paper->sections->flatMap(fn ($section) => $section->groups->flatMap(fn ($group) => $group->questions))->all());
        if (array_diff(array_map('strval', array_keys($responses)), $questions->modelKeys()) !== []) {
            throw ValidationException::withMessages(['answers' => 'Câu trả lời không thuộc bộ bài luyện này.']);
        }
        // Keys are loaded only after the learner explicitly requests feedback.
        $questions->load('answerKey');
        $results = ['correct' => 0, 'total' => 0, 'points' => 0, 'max_points' => 0, 'items' => []];
        foreach ($questions as $question) {
            if (! in_array($question->type, self::OBJECTIVE_TYPES, true) || empty($question->answerKey?->accepted_answers)) {
                continue;
            }
            $response = array_values(array_filter($responses[$question->id] ?? [], fn ($value) => $value !== null && trim($value) !== ''));
            $multiple = $question->type === 'multiple_choice';
            if ((! $multiple && count($response) > 1) || ($question->options->isNotEmpty() && array_diff($response, $question->options->pluck('label')->all()) !== [])) {
                throw ValidationException::withMessages(['answers.'.$question->id => 'Vui lòng chọn đáp án hợp lệ cho câu hỏi.']);
            }
            $key = $question->answerKey;
            $normal = static function ($value) use ($key): string {
                $text = preg_replace('/\s+/u', ' ', trim((string) $value));

                return $key->case_sensitive ? $text : mb_strtolower($text, 'UTF-8');
            };
            $expected = array_map($normal, $key->accepted_answers);
            $actual = array_values(array_unique(array_map($normal, $response)));
            if ($multiple) {
                sort($expected); sort($actual);
                $correct = $actual === $expected;
            } else {
                $correct = count($actual) === 1 && in_array($actual[0], $expected, true);
            }
            $results['total']++;
            $results['correct'] += (int) $correct;
            $results['max_points'] += (float) $question->max_points;
            $results['points'] += $correct ? (float) $question->max_points : 0;
            $results['items'][$question->id] = ['correct' => $correct, 'answers' => $key->accepted_answers, 'explanation' => $key->explanation];
        }

        return view('learning.practice', compact('paper', 'results', 'responses'));
    }

    private function paper(string $slug): ExamPaper
    {
        return ExamPaper::published()->where('slug', $slug)->whereHas('questions')
            ->with(['collection', 'learningCategory', 'sections.groups.questions.options'])
            ->withCount('questions')->firstOrFail();
    }
}
