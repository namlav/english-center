<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseLearningGoal;
use App\Models\CourseLearningProfile;
use App\Models\CourseSection;
use App\Models\ExamCollection;
use App\Models\ExamPaper;
use App\Models\ExamSection;
use App\Models\ExamTag;
use App\Models\LearnerProfile;
use App\Models\LearningScoreScale;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionAnswerKey;
use App\Models\QuestionGroup;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LearningReferenceScreensSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('LearningReferenceScreensSeeder is only available in local/testing environments.');
        }
        $this->call(LearningPlatformSeeder::class);

        DB::transaction(function (): void {
            $author = User::where('email', 'author.demo@example.test')->firstOrFail();
            $learner = User::where('email', 'learner.demo@example.test')->firstOrFail();
            LearnerProfile::firstOrCreate(['user_id' => $learner->id], ['occupation' => 'student']);
            $collection = ExamCollection::firstOrCreate(['slug' => 'demo-original-practice-2026'], [
                'title' => 'Bộ bài luyện tự biên soạn 2026 — Demo',
                'publication_year' => 2026, 'description' => 'Dữ liệu minh họa thư viện đề; không phải đề thi chuẩn.',
            ]);

            foreach (Course::whereIn('code', ['DEMO-ONLINE-IELTS', 'DEMO-ONLINE-TOEIC'])->get() as $course) {
                $this->pivot('course_instructors', ['course_id' => $course->id, 'user_id' => $author->id], ['position' => 1]);
                $this->pivot('course_skills', ['course_id' => $course->id, 'skill' => 'reading'], ['position' => 1]);
            }
            foreach (ExamPaper::whereIn('slug', ['demo-ielts-reading-01', 'demo-toeic-reading-01'])->get() as $paper) {
                if ($paper->exam_collection_id === null) {
                    $paper->update(['exam_collection_id' => $collection->id]);
                }
                $tag = ExamTag::firstOrCreate(['slug' => 'short-reading'], ['name' => 'Đọc đoạn văn ngắn', 'kind' => 'question_type']);
                foreach ($paper->sections as $section) {
                    $this->pivot('exam_section_tags', ['exam_section_id' => $section->id, 'exam_tag_id' => $tag->id]);
                }
            }

            $lrCourse = Course::where('code', 'DEMO-ONLINE-TOEIC')->firstOrFail();
            $lrProfile = $this->profile($learner, $lrCourse);
            $this->goal($lrProfile, 'toeic_lr', 450, 650);
            $readingPaper = ExamPaper::where('slug', 'demo-toeic-reading-01')->firstOrFail();
            $this->pivot('lesson_exam_papers', ['lesson_id' => $lrCourse->lessons()->first()->id, 'exam_paper_id' => $readingPaper->id], ['position' => 1, 'is_required' => false]);

            $swCourse = Course::firstOrCreate(['code' => 'DEMO-ONLINE-TOEIC-SW'], [
                'learning_category_id' => $lrCourse->learning_category_id,
                'name' => 'TOEIC Speaking & Writing — Demo', 'slug' => 'toeic-speaking-writing-demo',
                'delivery_mode' => 'online', 'level' => 'Cơ bản', 'duration_hours' => 12,
                'summary' => 'Minh họa dữ liệu khóa học hai kỹ năng nói và viết.',
                'description' => 'Nội dung tự biên soạn phục vụ phát triển, không phải khóa học của STUDY4.',
                'learning_outcomes' => ['Thực hành trình bày một ý kiến ngắn', 'Luyện viết email công sở'],
                'price' => 490000, 'access_days' => 365, 'published_at' => now()->subDay(),
            ]);
            $this->pivot('course_instructors', ['course_id' => $swCourse->id, 'user_id' => $author->id], ['position' => 1]);
            foreach (['speaking', 'writing'] as $index => $skill) {
                $this->pivot('course_skills', ['course_id' => $swCourse->id, 'skill' => $skill], ['position' => $index + 1]);
                $section = CourseSection::firstOrCreate(['course_id' => $swCourse->id, 'position' => $index + 1], ['title' => ucfirst($skill).' — Demo']);
                $lesson = Lesson::firstOrCreate(['course_section_id' => $section->id, 'position' => 1], [
                    'title' => $skill === 'speaking' ? 'Trình bày ý kiến ngắn' : 'Viết email trả lời',
                    'slug' => 'demo-'.$skill, 'type' => 'text', 'is_preview' => true, 'is_published' => true,
                    'content' => 'Bài học mẫu tự biên soạn. Đọc yêu cầu và thực hành bằng bài luyện liên kết.',
                ]);
                $paper = $this->skillPaper($swCourse, $collection, $author, $skill);
                $this->pivot('lesson_exam_papers', ['lesson_id' => $lesson->id, 'exam_paper_id' => $paper->id], ['position' => 1, 'is_required' => false]);
            }
            $swProfile = $this->profile($learner, $swCourse);
            $this->goal($swProfile, 'toeic_speaking', 120, 160);
            $this->goal($swProfile, 'toeic_writing', 100, 150);
        });
    }

    private function profile(User $learner, Course $course): CourseLearningProfile
    {
        return CourseLearningProfile::firstOrCreate(['user_id' => $learner->id, 'course_id' => $course->id], [
            'learning_reason' => 'Mục tiêu minh họa do dự án tạo, không phải thông tin từ ảnh tham khảo.',
            'consultation_opt_in' => false,
        ]);
    }

    private function goal(CourseLearningProfile $profile, string $scaleCode, float $current, float $target): void
    {
        $scale = LearningScoreScale::findOrFail($scaleCode);
        CourseLearningGoal::firstOrCreate([
            'course_learning_profile_id' => $profile->id, 'score_scale_code' => $scaleCode,
        ], [...$scale->goalBounds(), 'current_score' => $current, 'target_score' => $target]);
    }

    private function skillPaper(Course $course, ExamCollection $collection, User $author, string $skill): ExamPaper
    {
        $paper = ExamPaper::firstOrCreate(['slug' => 'demo-toeic-'.$skill.'-01'], [
            'learning_category_id' => $course->learning_category_id, 'created_by' => $author->id,
            'exam_collection_id' => $collection->id, 'title' => 'Bài luyện '.ucfirst($skill).' — Demo tự biên soạn',
            'description' => 'Một câu minh họa lưu trữ câu trả lời và rubric. Không phải đề TOEIC chuẩn; chưa có chấm AI.',
            'skill' => $skill, 'duration_seconds' => $skill === 'speaking' ? 120 : 600,
            'status' => 'published', 'published_at' => now()->subDay(),
        ]);
        $section = ExamSection::firstOrCreate(['exam_paper_id' => $paper->id, 'position' => 1], [
            'title' => ucfirst($skill).' task', 'skill' => $skill, 'instructions' => 'Complete the original demo task.',
        ]);
        $group = QuestionGroup::firstOrCreate(['exam_section_id' => $section->id, 'position' => 1], [
            'title' => 'Demo task', 'response_format' => $skill === 'speaking' ? 'audio_response' : 'written_response',
            'answer_word_limit' => $skill === 'writing' ? 120 : null,
            'rendering_data' => $skill === 'speaking' ? ['preparation_seconds' => 30, 'recording_seconds' => 60] : null,
        ]);
        $question = Question::firstOrCreate(['question_group_id' => $group->id, 'position' => 1], [
            'exam_section_id' => $section->id,
            'prompt' => $skill === 'speaking'
                ? 'Describe one skill you would like to learn and explain why it is useful to you.'
                : 'A colleague asks to move a meeting from Tuesday to Wednesday. Write a short reply confirming a time and asking one question about the agenda.',
            'type' => $skill === 'speaking' ? 'speaking' : 'essay', 'max_points' => 5,
        ]);
        QuestionAnswerKey::firstOrCreate(['question_id' => $question->id], [
            'rubric' => ['criteria' => ['task_response', 'organization', 'language'], 'note' => 'Rubric minh họa cho chấm thủ công; không quy đổi thành điểm TOEIC.'],
        ]);
        $tag = ExamTag::firstOrCreate(['slug' => $skill === 'speaking' ? 'spoken-response' : 'email-writing'], [
            'name' => $skill === 'speaking' ? 'Trả lời bằng lời nói' : 'Viết email', 'kind' => 'question_type',
        ]);
        $this->pivot('exam_section_tags', ['exam_section_id' => $section->id, 'exam_tag_id' => $tag->id]);

        return $paper;
    }

    private function pivot(string $table, array $keys, array $values = []): void
    {
        if (! DB::table($table)->where($keys)->exists()) {
            DB::table($table)->insert([...$keys, ...$values, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
