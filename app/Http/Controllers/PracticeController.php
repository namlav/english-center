<?php

namespace App\Http\Controllers;

use App\Models\AttemptAnswer;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptSection;
use App\Models\ExamPaper;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PracticeController extends Controller
{
    private const OBJECTIVE_TYPES = ['single_choice', 'multiple_choice', 'short_answer', 'true_false'];

    public function show(Request $request, string $slug): View
    {
        $paper = $this->paper($slug);
        $mode = $request->query('mode', 'practice');
        $durationSeconds = $paper->duration_seconds > 0 ? (int) $paper->duration_seconds : 2700;

        return view('learning.practice', [
            'paper' => $paper,
            'results' => null,
            'responses' => [],
            'mode' => $mode,
            'durationSeconds' => $durationSeconds,
            'mockSummary' => null,
        ]);
    }

    public function check(Request $request, string $slug): View
    {
        $paper = $this->paper($slug);
        $mode = $request->input('mode', 'practice');
        $durationSeconds = $paper->duration_seconds > 0 ? (int) $paper->duration_seconds : 2700;
        $elapsedSeconds = min((int) $request->input('elapsed_seconds', 0), $durationSeconds);

        $data = $request->validate([
            'answers' => ['nullable', 'array', 'max:500'],
            'answers.*' => ['array', 'max:50'],
            'answers.*.*' => ['nullable', 'string', 'max:2000'],
            'mode' => ['nullable', 'in:practice,mock'],
            'elapsed_seconds' => ['nullable', 'integer', 'min:0'],
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

        $mockSummary = null;
        if ($mode === 'mock') {
            $score10 = $results['max_points'] > 0 ? round(($results['points'] / $results['max_points']) * 10, 1) : 0;
            $percent = $results['total'] > 0 ? round(($results['correct'] / $results['total']) * 100) : 0;

            $scaledScore = $score10;
            $categoryName = mb_strtolower($paper->learningCategory->name ?? '');
            if (str_contains($categoryName, 'ielts')) {
                $scaledScore = match (true) {
                    $percent >= 90 => 8.5,
                    $percent >= 80 => 7.5,
                    $percent >= 70 => 6.5,
                    $percent >= 60 => 5.5,
                    $percent >= 50 => 5.0,
                    $percent >= 40 => 4.0,
                    default => 3.0,
                };
            } elseif (str_contains($categoryName, 'toeic')) {
                $scaledScore = round(($percent / 100) * 990 / 5) * 5;
            }

            $mockSummary = [
                'score10' => $score10,
                'percent' => $percent,
                'scaledScore' => $scaledScore,
                'elapsedSeconds' => $elapsedSeconds,
                'formattedTime' => sprintf('%02d:%02d', floor($elapsedSeconds / 60), $elapsedSeconds % 60),
                'correct' => $results['correct'],
                'wrong' => $results['total'] - $results['correct'],
                'total' => $results['total'],
            ];

            if (Auth::check()) {
                DB::transaction(function () use ($paper, $results, $responses, $elapsedSeconds, $durationSeconds, $scaledScore) {
                    $user = Auth::user();
                    $attempt = ExamAttempt::create([
                        'user_id' => $user->id,
                        'exam_paper_id' => $paper->id,
                        'mode' => 'full',
                        'status' => 'graded',
                        'title_snapshot' => $paper->title,
                        'started_at' => now()->subSeconds($elapsedSeconds),
                        'submitted_at' => now(),
                        'graded_at' => now(),
                        'time_limit_seconds' => $durationSeconds,
                        'elapsed_seconds' => $elapsedSeconds,
                        'earned_points' => $results['points'],
                        'max_points' => $results['max_points'],
                        'scaled_score' => $scaledScore,
                    ]);

                    $pos = 0;
                    foreach ($paper->sections as $section) {
                        ExamAttemptSection::create([
                            'exam_attempt_id' => $attempt->id,
                            'exam_paper_id' => $paper->id,
                            'exam_section_id' => $section->id,
                            'title_snapshot' => $section->title,
                            'instructions_snapshot' => $section->instructions,
                            'position' => $section->position ?? 1,
                        ]);

                        foreach ($section->groups as $group) {
                            foreach ($group->questions as $question) {
                                $pos++;
                                $item = $results['items'][$question->id] ?? null;
                                $isCorrect = $item ? $item['correct'] : false;
                                $qResponse = $responses[$question->id] ?? [];

                                AttemptAnswer::create([
                                    'exam_attempt_id' => $attempt->id,
                                    'exam_section_id' => $section->id,
                                    'question_id' => $question->id,
                                    'position' => $pos,
                                    'type_snapshot' => in_array($question->type, ['single_choice', 'multiple_choice', 'short_answer', 'true_false', 'essay', 'speaking']) ? $question->type : 'single_choice',
                                    'prompt_snapshot' => $question->prompt,
                                    'group_snapshot' => $group->only(['title', 'instructions', 'passage']),
                                    'options_snapshot' => $question->options->map(fn (QuestionOption $o) => $o->only(['label', 'content']))->all(),
                                    'grading_snapshot' => [
                                        'accepted_answers' => $question->answerKey?->accepted_answers ?? [],
                                        'explanation' => $question->answerKey?->explanation,
                                    ],
                                    'response' => $qResponse,
                                    'is_correct' => $isCorrect,
                                    'awarded_points' => $isCorrect ? (float) $question->max_points : 0,
                                    'max_points' => (float) $question->max_points,
                                    'answered_at' => now(),
                                ]);
                            }
                        }
                    }
                });
            }
        }

        return view('learning.practice', compact('paper', 'results', 'responses', 'mode', 'durationSeconds', 'mockSummary'));
    }

    private function paper(string $slug): ExamPaper
    {
        return ExamPaper::published()->where('slug', $slug)->whereHas('questions')
            ->with(['collection', 'learningCategory', 'sections.groups.questions.options'])
            ->withCount('questions')->firstOrFail();
    }
}
