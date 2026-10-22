<?php

namespace Tests\Feature;

use App\Models\ExamPaper;
use App\Models\Question;
use Database\Seeders\BcReadsPracticeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_home_uses_actual_catalog_counts_and_search_filters(): void
    {
        $this->seed(BcReadsPracticeSeeder::class);
        $this->get('/')->assertOk()->assertSee('5 bộ bài luyện')->assertSee('60 câu hỏi')->assertSee('Đại từ chủ ngữ và tân ngữ');
        $this->get('/tests?q='.urlencode('Cấu tạo từ'))->assertOk()->assertSee('Cấu tạo từ với tiền tố và hậu tố')->assertDontSee('Hòa hợp chủ ngữ và động từ');
        $this->get('/tests?category=missing')->assertOk()->assertSee('Chưa tìm thấy bài luyện phù hợp');
    }

    public function test_empty_catalog_is_a_real_empty_state_and_search_is_escaped(): void
    {
        $this->get('/')->assertOk()->assertSee('0 bộ bài luyện')->assertSee('Một hành trình mới đang bắt đầu');
        $this->get('/tests?q='.urlencode('<script>alert(1)</script>'))->assertOk()->assertDontSee('<script>alert(1)</script>', false)->assertSee('<script>alert(1)</script>');
        $this->get('/tests?q[]=invalid')->assertRedirect();
    }

    public function test_draft_future_inactive_categories_and_inactive_collections_are_not_exposed(): void
    {
        $this->seed(BcReadsPracticeSeeder::class);
        $papers = ExamPaper::orderBy('id')->get();
        $papers[0]->update(['status' => 'draft']);
        $papers[1]->update(['published_at' => now()->addDay()]);
        $this->get('/')->assertOk()->assertSee('3 bộ bài luyện');
        $this->get(route('practice.show', $papers[0]->slug))->assertNotFound();
        $this->post(route('practice.check', $papers[1]->slug), [])->assertNotFound();
        $papers[2]->collection->update(['is_active' => false]);
        $this->get('/')->assertOk()->assertSee('0 bộ bài luyện');
        $papers[2]->collection->update(['is_active' => true]);
        $papers[2]->learningCategory->update(['is_active' => false]);
        $this->get(route('practice.show', $papers[2]->slug))->assertNotFound();
    }

    public function test_question_page_does_not_send_private_keys_before_self_check(): void
    {
        $this->seed(BcReadsPracticeSeeder::class);
        $question = Question::with('answerKey', 'examSection.examPaper')->first();
        $this->get(route('practice.show', $question->examSection->examPaper->slug))
            ->assertOk()->assertSee($question->prompt)->assertDontSee($question->answerKey->explanation)
            ->assertDontSee('accepted_answers')->assertDontSee('KẾT QUẢ TỰ KIỂM TRA');
    }

    public function test_all_five_papers_can_be_self_checked_with_correct_answers_without_creating_fake_history(): void
    {
        $this->seed(BcReadsPracticeSeeder::class);
        foreach (ExamPaper::with('questions.answerKey')->get() as $paper) {
            $answers = [];
            foreach ($paper->questions as $question) {
                $answer = $question->answerKey->accepted_answers[0];
                $answers[$question->id] = [$question->type === 'short_answer' ? '  '.mb_strtoupper($answer).'  ' : $answer];
            }
            $this->post(route('practice.check', $paper->slug), compact('answers'))->assertOk()
                ->assertViewHas('results', fn ($results) => $results['correct'] === count($answers) && $results['total'] === count($answers))
                ->assertSee('KẾT QUẢ TỰ KIỂM TRA')->assertSee('Chính xác');
        }
        $this->assertDatabaseCount('exam_attempts', 0);
        $this->assertDatabaseCount('attempt_answers', 0);
    }

    public function test_cross_paper_answers_and_invalid_options_are_rejected(): void
    {
        $this->seed(BcReadsPracticeSeeder::class);
        $paper = ExamPaper::where('slug', 'bc-reads-02-sentence-fragments')->first();
        $otherQuestion = Question::whereNotIn('exam_section_id', $paper->sections()->pluck('id'))->first();
        $this->postJson(route('practice.check', $paper->slug), ['answers' => [$otherQuestion->id => ['A']]])->assertUnprocessable()->assertJsonValidationErrors('answers');
        $question = $paper->questions()->first();
        $this->postJson(route('practice.check', $paper->slug), ['answers' => [$question->id => ['Z']]])->assertUnprocessable()->assertJsonValidationErrors('answers.'.$question->id);
        $this->postJson(route('practice.check', $paper->slug), ['answers' => [$question->id => ['A', 'B']]])->assertUnprocessable();
    }

    public function test_blank_answers_score_zero_and_spelling_variants_are_accepted(): void
    {
        $this->seed(BcReadsPracticeSeeder::class);
        $paper = ExamPaper::where('slug', 'bc-reads-05-vocabulary')->first();
        $this->post(route('practice.check', $paper->slug), [])->assertOk()->assertViewHas('results', fn ($results) => $results['correct'] === 0 && $results['total'] === 10);
        $question = $paper->questions()->where('questions.position', 10)->first();
        $this->post(route('practice.check', $paper->slug), ['answers' => [$question->id => ['Honor']]])
            ->assertOk()->assertViewHas('results', fn ($results) => $results['correct'] === 1 && $results['total'] === 10);
    }
}
