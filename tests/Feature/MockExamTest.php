<?php

namespace Tests\Feature;

use App\Models\ExamAttempt;
use App\Models\ExamPaper;
use App\Models\User;
use Database\Seeders\BcReadsPracticeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MockExamTest extends TestCase
{
    use RefreshDatabase;

    private ExamPaper $paper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(BcReadsPracticeSeeder::class);
        $this->paper = ExamPaper::published()->whereHas('questions')->firstOrFail();
    }

    public function test_user_can_view_mock_exam_mode_with_timer(): void
    {
        $response = $this->get(route('practice.show', [
            'slug' => $this->paper->slug,
            'mode' => 'mock',
        ]));

        $response->assertOk();
        $response->assertSee('Thi thử tính giờ');
        $response->assertSee('Thời gian còn lại');
        $response->assertSee('timer-display', false);
        $response->assertSee('Nộp bài thi');
    }

    public function test_guest_can_submit_mock_exam_and_get_graded(): void
    {
        $questions = $this->paper->questions()->with('answerKey')->get();
        $answers = [];

        // Fill answers correctly for the first 2 questions
        foreach ($questions as $index => $q) {
            if ($index < 2 && !empty($q->answerKey->accepted_answers)) {
                $answers[$q->id] = [$q->answerKey->accepted_answers[0]];
            } else {
                $answers[$q->id] = ['wrong_answer_xyz'];
            }
        }

        $response = $this->post(route('practice.check', $this->paper->slug), [
            'answers' => $answers,
            'mode' => 'mock',
            'elapsed_seconds' => 300,
        ]);

        $response->assertOk();
        $response->assertSee('KẾT QUẢ THI THỬ TRỰC TUYẾN');
        $response->assertSee('Số câu đúng');
        $response->assertSee('05:00'); // 300 seconds formatted as 05:00
        $response->assertSee('Thi lại bài này');
    }

    public function test_authenticated_user_has_attempt_recorded_in_database(): void
    {
        $user = User::factory()->create(['role' => 'student']);
        $questions = $this->paper->questions()->with('answerKey')->get();
        $answers = [];

        foreach ($questions as $index => $q) {
            if ($index === 0 && !empty($q->answerKey->accepted_answers)) {
                $answers[$q->id] = [$q->answerKey->accepted_answers[0]];
            }
        }

        $response = $this->actingAs($user)->post(route('practice.check', $this->paper->slug), [
            'answers' => $answers,
            'mode' => 'mock',
            'elapsed_seconds' => 150,
        ]);

        $response->assertOk();
        $response->assertSee('Đã lưu kết quả ✓');

        $this->assertDatabaseHas('exam_attempts', [
            'user_id' => $user->id,
            'exam_paper_id' => $this->paper->id,
            'mode' => 'full',
            'status' => 'graded',
            'elapsed_seconds' => 150,
        ]);

        $attempt = ExamAttempt::where('user_id', $user->id)->first();
        $this->assertNotNull($attempt);
        $this->assertGreaterThan(0, $attempt->answers()->count());
    }

    public function test_practice_mode_still_works_as_self_check(): void
    {
        $response = $this->get(route('practice.show', [
            'slug' => $this->paper->slug,
            'mode' => 'practice',
        ]));

        $response->assertOk();
        $response->assertSee('Luyện tập tự do');
        $response->assertDontSee('timer-display', false);
    }
}
