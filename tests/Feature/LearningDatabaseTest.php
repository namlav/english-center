<?php

namespace Tests\Feature;

use App\Models\AttemptAnswer;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptSection;
use App\Models\ExamPaper;
use App\Models\FlashcardReview;
use App\Models\LearningCategory;
use App\Models\LessonProgress;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\LearningPlatformSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LearningDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_is_repeatable_and_online_learning_does_not_require_a_class_profile(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $question = Question::first();
        $question->update(['prompt' => 'Edited by the author']);
        $this->seed(LearningPlatformSeeder::class);

        $this->assertDatabaseCount('learning_categories', 3);
        $this->assertDatabaseCount('courses', 2);
        $this->assertDatabaseCount('lessons', 8);
        $this->assertDatabaseCount('exam_papers', 2);
        $this->assertDatabaseCount('questions', 6);
        $this->assertDatabaseCount('exam_attempts', 1);
        $this->assertDatabaseCount('attempt_answers', 4);
        $this->assertDatabaseCount('flashcards', 3);
        $this->assertDatabaseCount('flashcard_reviews', 3);
        $this->assertDatabaseCount('students', 0);
        $this->assertSame('Edited by the author', $question->fresh()->prompt);
        $this->assertCount(4, Course::first()->lessons);
        $this->assertTrue(CourseEnrollment::first()->isCurrent());
        $attempt = ExamAttempt::first();
        $this->assertSame((float) $attempt->max_points, (float) $attempt->answers->sum('max_points'));
        $this->assertSame((float) $attempt->earned_points, (float) $attempt->answers->sum('awarded_points'));
        $this->assertNull($attempt->scaled_score);
    }

    public function test_old_training_data_and_models_survive_the_online_extension(): void
    {
        $this->seed(DatabaseSeeder::class);
        $oldCourse = Course::where('code', 'KH-IELTS')->firstOrFail();
        $classIds = $oldCourse->classRooms->modelKeys();
        $this->seed(LearningPlatformSeeder::class);

        $this->assertSame('classroom', $oldCourse->fresh()->delivery_mode);
        $this->assertSame($classIds, $oldCourse->fresh()->classRooms->modelKeys());
        $this->assertDatabaseCount('class_rooms', 6);
        $this->assertDatabaseCount('enrollments', 40);
        $this->assertSame(2, Course::publishedOnline()->count());
    }

    public function test_a_user_cannot_have_duplicate_access_to_a_course(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $copy = CourseEnrollment::first()->replicate();
        $this->expectException(QueryException::class);
        $copy->save();
    }

    public function test_lesson_progress_has_one_row_per_user_and_lesson(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $copy = LessonProgress::first()->replicate();
        $this->expectException(QueryException::class);
        $copy->save();
    }

    public function test_expired_revoked_and_future_course_access_is_not_current(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $access = CourseEnrollment::first();
        $access->update(['expires_at' => now()->subHour()]);
        $this->assertFalse($access->isCurrent());
        $access->update(['expires_at' => null, 'status' => 'revoked']);
        $this->assertFalse($access->isCurrent());
        $access->update(['status' => 'active', 'started_at' => now()->addDay()]);
        $this->assertFalse($access->isCurrent());
    }

    public function test_an_attempt_cannot_select_a_section_from_another_paper(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $attempt = ExamAttempt::first();
        $other = ExamPaper::where('id', '!=', $attempt->exam_paper_id)->first()->sections()->first();
        $this->expectException(QueryException::class);
        ExamAttemptSection::create([
            'exam_attempt_id' => $attempt->id, 'exam_paper_id' => $attempt->exam_paper_id,
            'exam_section_id' => $other->id, 'title_snapshot' => $other->title, 'position' => 3,
        ]);
    }

    public function test_question_group_and_question_must_belong_to_the_same_section(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $question = Question::first()->replicate();
        $question->position = 20;
        $question->exam_section_id = QuestionGroup::where('exam_section_id', '!=', $question->exam_section_id)->first()->exam_section_id;
        $this->expectException(QueryException::class);
        $question->save();
    }

    public function test_an_answer_cannot_reference_an_unselected_section(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $existing = ExamAttempt::first();
        $practice = ExamAttempt::create([
            'user_id' => $existing->user_id, 'exam_paper_id' => $existing->exam_paper_id,
            'mode' => 'practice', 'title_snapshot' => $existing->title_snapshot, 'started_at' => now(),
        ]);
        $copy = AttemptAnswer::first()->replicate();
        $copy->exam_attempt_id = $practice->id;
        $this->expectException(QueryException::class);
        $copy->save();
    }

    public function test_an_answer_cannot_mislabel_the_section_of_its_question(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $answer = AttemptAnswer::first();
        $other = ExamAttemptSection::where('exam_attempt_id', $answer->exam_attempt_id)
            ->where('exam_section_id', '!=', $answer->exam_section_id)->first();
        $this->expectException(QueryException::class);
        $answer->update(['exam_section_id' => $other->exam_section_id]);
    }

    public function test_attempt_snapshots_survive_edits_to_live_exam_content(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $answer = AttemptAnswer::first();
        $snapshot = $answer->only(['prompt_snapshot', 'group_snapshot', 'options_snapshot', 'grading_snapshot']);
        $question = $answer->question;
        $question->update(['prompt' => 'A revised prompt']);
        $question->questionGroup->update(['passage' => 'A revised passage']);
        $question->options()->first()->update(['content' => 'A revised option']);
        $question->answerKey->update(['accepted_answers' => ['C']]);
        $this->assertSame($snapshot, $answer->fresh()->only(array_keys($snapshot)));
    }

    public function test_private_grading_data_is_excluded_from_default_serialization(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $question = Question::with('answerKey')->first();
        $this->assertArrayNotHasKey('answer_key', $question->toArray());
        $this->assertArrayNotHasKey('accepted_answers', $question->answerKey->toArray());
        $this->assertArrayNotHasKey('grading_snapshot', AttemptAnswer::first()->toArray());
        $this->assertArrayNotHasKey('transcript', QuestionGroup::first()->toArray());
        $this->assertNotEmpty($question->answerKey->accepted_answers);
    }

    public function test_public_catalog_scopes_hide_future_inactive_and_archived_content(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $paper = ExamPaper::first();
        $paper->update(['published_at' => now()->addDay()]);
        $this->assertFalse(ExamPaper::published()->whereKey($paper->id)->exists());
        $paper->update(['published_at' => now()->subDay(), 'status' => 'archived']);
        $this->assertFalse(ExamPaper::published()->whereKey($paper->id)->exists());
        $course = Course::first();
        $course->update(['published_at' => now()->addDay()]);
        $this->assertFalse(Course::publishedOnline()->whereKey($course->id)->exists());
        LearningCategory::query()->update(['is_active' => false]);
        $this->assertSame(0, ExamPaper::published()->count());
        $this->assertSame(0, Course::publishedOnline()->count());
    }

    public function test_flashcard_due_query_is_scoped_to_one_user(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $learner = User::where('email', 'learner.demo@example.test')->first();
        $other = User::factory()->create();
        $review = FlashcardReview::first();
        $otherReview = $review->replicate();
        $otherReview->user_id = $other->id;
        $otherReview->save();
        $review->update(['due_at' => now()->addDay()]);
        $this->assertSame(2, FlashcardReview::dueFor($learner)->count());
        $this->assertSame([$other->id], FlashcardReview::dueFor($other)->pluck('user_id')->unique()->all());
    }

    public function test_content_with_attempt_history_cannot_be_deleted(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $this->expectException(QueryException::class);
        ExamPaper::first()->delete();
    }

    public function test_legacy_course_delete_reports_online_dependencies_without_an_error_page(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $course = Course::first();
        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('courses.destroy', $course))
            ->assertRedirect(route('courses.index'))->assertSessionHas('error');
        $this->assertModelExists($course);
    }

    public function test_legacy_user_delete_preserves_online_learning_history(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $learner = User::where('email', 'learner.demo@example.test')->first();
        $this->actingAs(User::factory()->admin()->create())
            ->delete(route('users.destroy', $learner))
            ->assertRedirect(route('users.index'))->assertSessionHas('error');
        $this->assertModelExists($learner);
        $this->assertDatabaseCount('exam_attempts', 1);
    }

    public static function invalidValues(): array
    {
        return [
            'negative price' => ['courses', ['price' => -1]],
            'sale above price' => ['courses', ['sale_price' => 99999999]],
            'zero access period' => ['courses', ['access_days' => 0]],
            'online without slug' => ['courses', ['slug' => null]],
            'expiry before start' => ['course_enrollments', ['expires_at' => '2000-01-01 00:00:00']],
            'negative playback' => ['lesson_progress', ['position_seconds' => -1]],
            'zero duration' => ['exam_papers', ['duration_seconds' => 0]],
            'published without date' => ['exam_papers', ['published_at' => null]],
            'negative question score' => ['questions', ['max_points' => -1]],
            'invalid question order' => ['questions', ['position' => 0]],
            'score above maximum' => ['attempt_answers', ['awarded_points' => 100]],
            'total above maximum' => ['exam_attempts', ['earned_points' => 100]],
            'full test without clock' => ['exam_attempts', ['time_limit_seconds' => null]],
            'graded without submission' => ['exam_attempts', ['submitted_at' => null]],
            'invalid review rating' => ['course_reviews', ['rating' => 6]],
            'invalid memory rating' => ['flashcard_reviews', ['last_rating' => 6]],
            'invalid memory ease' => ['flashcard_reviews', ['ease_factor' => 1]],
            'course deck without course' => ['flashcard_decks', ['visibility' => 'course', 'course_id' => null]],
            'private deck without owner' => ['flashcard_decks', ['visibility' => 'private', 'user_id' => null]],
        ];
    }

    #[DataProvider('invalidValues')]
    public function test_database_rejects_invalid_values_without_model_validation(string $table, array $values): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $id = DB::table($table)->value('id');
        $this->expectException(QueryException::class);
        DB::table($table)->where('id', $id)->update($values);
    }

    public function test_new_migrations_can_roll_back_without_removing_legacy_courses(): void
    {
        $this->seed(DatabaseSeeder::class);
        $ids = Course::orderBy('id')->pluck('id')->all();
        $onlineMigrations = count(glob(database_path('migrations/2026_10_2[12]_*.php')));
        $this->assertSame(0, Artisan::call('migrate:rollback', ['--step' => $onlineMigrations, '--force' => true]));
        $this->assertFalse(Schema::hasTable('exam_papers'));
        $this->assertFalse(Schema::hasColumn('courses', 'delivery_mode'));
        $this->assertSame($ids, Course::orderBy('id')->pluck('id')->all());
        $this->assertDatabaseCount('enrollments', 40);
        $this->assertSame(0, Artisan::call('migrate', ['--force' => true]));
        $this->assertTrue(Schema::hasTable('exam_papers'));
        $this->assertSame(2, (int) DB::table('sqlite_master')->where('type', 'trigger')->whereIn('name', ['courses_training_insert', 'courses_training_update'])->count());
        $this->assertEmpty(DB::select('PRAGMA foreign_key_check'));
    }

    public function test_demo_seeder_refuses_production(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('only available in local/testing');
        (new LearningPlatformSeeder)->run();
    }
}
