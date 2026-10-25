<?php

namespace Tests\Feature;

use App\Models\ExamPaper;
use App\Models\Question;
use Database\Seeders\BcReadsPracticeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BcReadsPracticeTest extends TestCase
{
    use RefreshDatabase;

    public function test_five_source_attributed_sets_have_sixty_answered_questions_and_no_demo_accounts(): void
    {
        $this->seed(BcReadsPracticeSeeder::class);
        $this->assertDatabaseCount('exam_collections', 1);
        $this->assertDatabaseCount('exam_papers', 5);
        $this->assertDatabaseCount('questions', 60);
        $this->assertDatabaseCount('question_answer_keys', 60);
        $this->assertDatabaseCount('question_options', 63);
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('exam_attempts', 0);
        $this->assertSame(5, ExamPaper::published()->count());
        $expectedCounts = [13, 14, 8, 15, 10];
        foreach (ExamPaper::orderBy('slug')->with('sections.groups.questions.options', 'sections.groups.questions.answerKey')->get() as $index => $paper) {
            $this->assertStringContainsString('CC BY 4.0', $paper->description);
            $this->assertNull($paper->created_by);
            $section = $paper->sections->sole();
            $group = $section->groups->sole();
            $this->assertCount($expectedCounts[$index], $group->questions);
            $this->assertSame('Shantel Ivits', $group->rendering_data['source']['author']);
            $this->assertCount($expectedCounts[$index], $group->rendering_data['items']);
            foreach ($group->questions as $question) {
                $this->assertSame($section->id, $question->exam_section_id);
                $this->assertNotEmpty($question->answerKey->accepted_answers);
                $this->assertStringContainsString('Source:', $question->answerKey->explanation);
                if ($question->type === 'single_choice') {
                    $this->assertContains($question->answerKey->accepted_answers[0], $question->options->pluck('label')->all());
                }
                $this->assertArrayNotHasKey('answer_key', $question->toArray());
                $this->assertArrayNotHasKey('accepted_answers', $question->answerKey->toArray());
            }
            $this->assertStringNotContainsString('accepted_answers', json_encode($group->rendering_data));
        }
    }

    public function test_published_answer_key_sequences_match_the_source_and_spelling_variants_are_explicit(): void
    {
        $this->seed(BcReadsPracticeSeeder::class);
        $keys = fn (string $slug): array => Question::whereHas('examSection.examPaper', fn ($query) => $query->where('slug', $slug))
            ->orderBy('position')->with('answerKey')->get()->map(fn ($question) => $question->answerKey->accepted_answers)->all();
        $this->assertSame(['she', 'he', 'they', 'we', 'he', 'they', 'it', 'him', 'them', 'it', 'us', 'me', 'her'], array_column($keys('bc-reads-01-pronouns'), 0));
        $this->assertSame(['B', 'A', 'B', 'B', 'A', 'B', 'B', 'A', 'A', 'C', 'A', 'C', 'B', 'C'], array_column($keys('bc-reads-02-sentence-fragments'), 0));
        $this->assertSame(['inventor', 'viral', 'combination', 'extraordinary', 'participation', 'categorize', 'identification', 'dramatize'], array_column($keys('bc-reads-03-word-formation'), 0));
        $this->assertSame(['A', 'B', 'B', 'B', 'A', 'A', 'B', 'A', 'A', 'A', 'A', 'B', 'A', 'B', 'A'], array_column($keys('bc-reads-04-subject-verb-agreement'), 0));
        $this->assertSame(['honour', 'honor'], $keys('bc-reads-05-vocabulary')[9]);
    }

    public function test_reimport_preserves_edited_content_and_keys_without_duplicating_rows(): void
    {
        $this->seed(BcReadsPracticeSeeder::class);
        $paper = ExamPaper::first();
        $paper->update(['title' => 'Bộ đã biên tập']);
        $question = $paper->sections->first()->questions->first();
        $question->update(['prompt' => 'Nội dung đã biên tập']);
        $question->answerKey->update(['explanation' => 'Lời giải đã biên tập']);
        $this->seed(BcReadsPracticeSeeder::class);
        $this->assertDatabaseCount('exam_papers', 5);
        $this->assertDatabaseCount('questions', 60);
        $this->assertDatabaseCount('question_answer_keys', 60);
        $this->assertSame('Bộ đã biên tập', $paper->fresh()->title);
        $this->assertSame('Nội dung đã biên tập', $question->fresh()->prompt);
        $this->assertSame('Lời giải đã biên tập', $question->answerKey->fresh()->explanation);
    }
}
