<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLearningGoal;
use App\Models\CourseLearningProfile;
use App\Models\ExamCollection;
use App\Models\ExamPaper;
use App\Models\LearningScoreScale;
use App\Models\User;
use Database\Seeders\LearningPlatformSeeder;
use Database\Seeders\LearningReferenceScreensSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LearningReferenceDatabaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_reference_demo_is_repeatable_and_preserves_user_edits(): void
    {
        $this->seed(LearningReferenceScreensSeeder::class);
        $profile = CourseLearningProfile::first();
        $profile->update(['learning_reason' => 'My own learning reason']);
        $before = [];
        foreach (['courses', 'learner_profiles', 'course_learning_profiles', 'course_learning_goals', 'exam_collections', 'exam_tags', 'lesson_exam_papers', 'course_instructors', 'course_skills', 'exam_section_tags'] as $table) {
            $before[$table] = DB::table($table)->count();
        }
        $this->seed(LearningReferenceScreensSeeder::class);
        foreach ($before as $table => $count) {
            $this->assertDatabaseCount($table, $count);
        }
        $this->assertSame('My own learning reason', $profile->fresh()->learning_reason);
        $this->assertDatabaseCount('courses', 3);
        $this->assertDatabaseCount('lessons', 10);
        $this->assertDatabaseCount('exam_papers', 4);
        $this->assertDatabaseCount('questions', 8);
        $this->assertDatabaseCount('course_learning_goals', 3);
        $this->assertDatabaseCount('learning_score_scales', 4);
    }

    public function test_course_profile_does_not_grant_access_or_opt_a_learner_into_consultation(): void
    {
        $this->seed(LearningReferenceScreensSeeder::class);
        $course = Course::where('code', 'DEMO-ONLINE-TOEIC-SW')->firstOrFail();
        $profile = $course->learningProfiles()->firstOrFail();
        $this->assertFalse($profile->consultation_opt_in);
        $this->assertNull($profile->consented_at);
        $this->assertNull($profile->profile_completed_at);
        $this->assertFalse(CourseEnrollment::where('user_id', $profile->user_id)->where('course_id', $course->id)->exists());
        $this->assertSame('student', $profile->user->learnerProfile->occupation);
        $this->assertCount(2, $profile->goals);
    }

    public function test_catalog_combines_category_skill_collection_and_tag_filters(): void
    {
        $this->seed(LearningReferenceScreensSeeder::class);
        $papers = ExamPaper::published()->catalogFilter([
            'keyword' => 'Đọc đoạn văn', 'category' => 'toeic', 'skill' => 'reading',
            'collection' => 'demo-original-practice-2026', 'tag' => 'short-reading',
        ])->get();
        $this->assertSame(['demo-toeic-reading-01'], $papers->pluck('slug')->all());
        $this->assertSame(['demo-toeic-writing-01'], ExamPaper::published()->catalogFilter(['tag' => 'email-writing'])->pluck('slug')->all());
        $this->assertSame(0, ExamPaper::published()->catalogFilter(['keyword' => 'no-such-phrase'])->count());
        $this->assertSame(4, ExamPaper::published()->catalogFilter(['keyword' => 'Bộ bài luyện tự biên soạn'])->count());
        ExamCollection::query()->update(['is_active' => false]);
        $this->assertSame(0, ExamPaper::published()->count());
    }

    public function test_course_can_have_multiple_skills_and_instructors_and_ordered_practice_links(): void
    {
        $this->seed(LearningReferenceScreensSeeder::class);
        $course = Course::forSkill('writing')->sole();
        $this->assertSame('DEMO-ONLINE-TOEIC-SW', $course->code);
        $this->assertSame(['speaking', 'writing'], $course->skillNames());
        $this->assertCount(2, $course->learning_outcomes);
        $this->assertCount(1, $course->instructors);
        $this->assertCount(3, $course->instructors->first()->taughtOnlineCourses);
        $lesson = $course->sections()->first()->lessons()->first();
        $paper = $lesson->examPapers()->sole();
        $this->assertSame('speaking', $paper->skill);
        $this->assertSame($lesson->id, $paper->lessons()->sole()->id);
        $this->assertSame('speaking', $paper->sections->first()->groups->first()->questions->first()->type);
        $this->assertSame(30, $paper->sections->first()->groups->first()->rendering_data['preparation_seconds']);
    }

    public static function scoreCases(): array
    {
        return [
            'LR total valid' => ['toeic_lr', 450, 650, true],
            'IELTS half band valid' => ['ielts_band', 5.5, 6.5, true],
            'Speaking valid' => ['toeic_speaking', 0, 160, true],
            'Writing valid' => ['toeic_writing', null, 150, true],
            'unknown current score valid' => ['toeic_lr', null, 650, true],
            'LR above maximum' => ['toeic_lr', 450, 1000, false],
            'LR invalid step' => ['toeic_lr', 451, 650, false],
            'LR zero is not an official score' => ['toeic_lr', 0, 650, false],
            'IELTS above band 9' => ['ielts_band', 6, 9.5, false],
            'IELTS quarter band invalid' => ['ielts_band', 5, 6.25, false],
            'Speaking above 200' => ['toeic_speaking', 120, 210, false],
            'Writing invalid step' => ['toeic_writing', 100, 155, false],
            'no score or goal' => ['toeic_lr', null, null, false],
        ];
    }

    #[DataProvider('scoreCases')]
    public function test_score_bounds_and_steps_are_enforced_by_the_database(string $code, ?float $current, ?float $target, bool $valid): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $profile = CourseLearningProfile::create(['user_id' => User::where('role', 'student')->first()->id, 'course_id' => Course::first()->id]);
        $attributes = [...LearningScoreScale::findOrFail($code)->goalBounds(),
            'course_learning_profile_id' => $profile->id, 'current_score' => $current, 'target_score' => $target];
        if (! $valid) {
            $this->expectException(QueryException::class);
        }
        DB::table('course_learning_goals')->insert($attributes);
        if ($valid) {
            $goal = $profile->goals()->sole();
            $this->assertSame($code, $goal->scoreScale->code);
            $this->assertSame((float) $target, (float) $goal->target_score);
        }
    }

    public function test_a_goal_cannot_spoof_its_scale_bounds(): void
    {
        $this->seed(LearningReferenceScreensSeeder::class);
        $goal = CourseLearningGoal::first();
        $this->expectException(QueryException::class);
        DB::table('course_learning_goals')->where('id', $goal->id)->update(['max_score' => 9999]);
    }

    public function test_scale_bounds_cannot_change_beneath_existing_goals(): void
    {
        $this->seed(LearningReferenceScreensSeeder::class);
        $this->expectException(QueryException::class);
        LearningScoreScale::findOrFail('toeic_lr')->update(['max_score' => 1000]);
    }

    public function test_a_learner_has_only_one_profile_for_each_course(): void
    {
        $this->seed(LearningReferenceScreensSeeder::class);
        $copy = CourseLearningProfile::first()->replicate();
        $this->expectException(QueryException::class);
        $copy->save();
    }

    public function test_consultation_opt_in_requires_a_recorded_time(): void
    {
        $this->seed(LearningReferenceScreensSeeder::class);
        $this->expectException(QueryException::class);
        CourseLearningProfile::first()->update(['consultation_opt_in' => true]);
    }

    public function test_consultation_preference_can_be_recorded_and_withdrawn(): void
    {
        $this->seed(LearningReferenceScreensSeeder::class);
        $profile = CourseLearningProfile::first();
        $profile->update(['consultation_opt_in' => true, 'consented_at' => now()]);
        $this->assertTrue($profile->fresh()->consultation_opt_in);
        $profile->update(['consultation_opt_in' => false, 'consented_at' => null]);
        $this->assertFalse($profile->fresh()->consultation_opt_in);
    }

    public function test_a_lesson_cannot_link_the_same_paper_twice(): void
    {
        $this->seed(LearningReferenceScreensSeeder::class);
        $row = (array) DB::table('lesson_exam_papers')->first();
        $row['position'] = 99;
        $this->expectException(QueryException::class);
        DB::table('lesson_exam_papers')->insert($row);
    }

    public function test_only_the_reference_extensions_can_roll_back_and_reapply_without_altering_history(): void
    {
        $this->seed(LearningPlatformSeeder::class);
        $before = DB::table('attempt_answers')->orderBy('id')->get()->toJson();
        $referenceMigrations = count(glob(database_path('migrations/2026_10_22_*.php')));
        $this->assertSame(0, Artisan::call('migrate:rollback', ['--step' => $referenceMigrations, '--force' => true]));
        $this->assertFalse(Schema::hasTable('course_learning_profiles'));
        $this->assertFalse(Schema::hasColumn('exam_papers', 'exam_collection_id'));
        $this->assertSame($before, DB::table('attempt_answers')->orderBy('id')->get()->toJson());
        $this->assertSame(0, Artisan::call('migrate', ['--force' => true]));
        $this->assertTrue(Schema::hasTable('course_learning_profiles'));
        $this->assertSame($before, DB::table('attempt_answers')->orderBy('id')->get()->toJson());
        $this->assertEmpty(DB::select('PRAGMA foreign_key_check'));
        $this->assertSame(2, DB::table('sqlite_master')->where('type', 'trigger')->whereIn('name', ['exam_papers_learning_insert', 'exam_papers_learning_update'])->count());
    }
}
