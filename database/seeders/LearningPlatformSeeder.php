<?php

namespace Database\Seeders;

use App\Models\AttemptAnswer;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseReview;
use App\Models\CourseSection;
use App\Models\ExamAttempt;
use App\Models\ExamAttemptSection;
use App\Models\ExamComment;
use App\Models\ExamPaper;
use App\Models\ExamSection;
use App\Models\Flashcard;
use App\Models\FlashcardDeck;
use App\Models\FlashcardReview;
use App\Models\LearningCategory;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Question;
use App\Models\QuestionAnswerKey;
use App\Models\QuestionGroup;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LearningPlatformSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('LearningPlatformSeeder is only available in local/testing environments.');
        }

        DB::transaction(function (): void {
            $author = User::firstOrCreate(['email' => 'author.demo@example.test'], [
                'name' => 'Biên soạn Demo', 'role' => 'teacher',
                'password' => 'DemoStudy2026!', 'is_active' => true,
            ]);
            $learner = User::firstOrCreate(['email' => 'learner.demo@example.test'], [
                'name' => 'Học viên Demo', 'role' => 'student',
                'password' => 'DemoStudy2026!', 'is_active' => true,
            ]);

            $ielts = LearningCategory::firstOrCreate(['slug' => 'ielts-academic'], ['name' => 'IELTS Academic']);
            $toeic = LearningCategory::firstOrCreate(['slug' => 'toeic'], ['name' => 'TOEIC']);
            LearningCategory::firstOrCreate(['slug' => 'english-foundation'], ['name' => 'Tiếng Anh nền tảng']);

            $course = $this->course($ielts, 'DEMO-ONLINE-IELTS', 'ielts-reading-foundation-demo', 'Nền tảng đọc hiểu IELTS — Demo', 590000);
            $this->course($toeic, 'DEMO-ONLINE-TOEIC', 'toeic-reading-foundation-demo', 'Đọc hiểu công sở TOEIC — Demo', 390000);

            $paper = ExamPaper::firstOrCreate(['slug' => 'demo-ielts-reading-01'], [
                'learning_category_id' => $ielts->id, 'created_by' => $author->id,
                'title' => 'Bài luyện Reading 01 — Nội dung tự biên soạn',
                'description' => '4 câu minh họa cấu trúc dữ liệu. Không phải đề IELTS chuẩn, không quy đổi band.',
                'skill' => 'reading', 'duration_seconds' => 1200,
                'status' => 'published', 'published_at' => now()->subDay(),
            ]);
            $section = $this->section($paper, 1, 'A community library');
            $group = $this->group($section, 'The Oak Street library opens at nine each morning. It has a reading room and a computer room. Visitors may borrow three books for two weeks.');
            $this->question($group, 1, 'What time does the library open?', 'single_choice', ['B'], [
                'A' => 'At eight', 'B' => 'At nine', 'C' => 'At ten',
            ]);
            $this->question($group, 2, 'Visitors may borrow books for how many weeks?', 'short_answer', ['two', '2']);

            $section = $this->section($paper, 2, 'Learning in the garden');
            $group = $this->group($section, 'The school garden club meets on Friday. Members plant vegetables and keep a weather diary. The club does not sell its vegetables.');
            $this->question($group, 1, 'The garden club meets on Friday.', 'true_false', ['TRUE'], [
                'TRUE' => 'True', 'FALSE' => 'False', 'NOT_GIVEN' => 'Not given',
            ]);
            $this->question($group, 2, 'Choose the TWO activities mentioned in the passage.', 'multiple_choice', ['A', 'C'], [
                'A' => 'Planting vegetables', 'B' => 'Selling vegetables', 'C' => 'Keeping a weather diary',
            ]);

            $toeicPaper = ExamPaper::firstOrCreate(['slug' => 'demo-toeic-reading-01'], [
                'learning_category_id' => $toeic->id, 'created_by' => $author->id,
                'title' => 'Bài luyện tiếng Anh công sở 01 — Demo',
                'description' => '2 câu tự biên soạn để kiểm tra dữ liệu, không quy đổi điểm TOEIC.',
                'skill' => 'reading', 'duration_seconds' => 600,
                'status' => 'published', 'published_at' => now()->subDay(),
            ]);
            $group = $this->group($this->section($toeicPaper, 1, 'Office notice'), 'Please send the monthly report by Thursday. The team meeting will take place in Room 204 on Friday morning.');
            $this->question($group, 1, 'When is the report due?', 'single_choice', ['A'], [
                'A' => 'Thursday', 'B' => 'Friday', 'C' => 'Monday',
            ]);
            $this->question($group, 2, 'Where will the meeting take place?', 'single_choice', ['C'], [
                'A' => 'Room 102', 'B' => 'Room 203', 'C' => 'Room 204',
            ]);

            $enrollment = CourseEnrollment::firstOrCreate(['user_id' => $learner->id, 'course_id' => $course->id], [
                'source' => 'manual', 'status' => 'active',
                'started_at' => now()->subDay(), 'expires_at' => now()->addDays(364),
            ]);
            $firstLesson = $course->sections()->first()->lessons()->first();
            LessonProgress::firstOrCreate(['user_id' => $learner->id, 'lesson_id' => $firstLesson->id], [
                'position_seconds' => 0, 'last_accessed_at' => $enrollment->started_at,
                'completed_at' => $enrollment->started_at,
            ]);
            $this->attempt($paper, $learner);

            $deck = FlashcardDeck::firstOrCreate(['slug' => 'demo-study-words'], [
                'user_id' => $author->id, 'title' => 'Từ vựng học tập — Demo',
                'description' => 'Các từ và ví dụ do dự án tự biên soạn.', 'visibility' => 'public',
            ]);
            foreach ([['borrow', 'mượn', 'May I borrow this book?'], ['report', 'báo cáo', 'I finished my report today.'], ['garden', 'khu vườn', 'We grow beans in the garden.']] as $index => [$front, $back, $example]) {
                $card = Flashcard::firstOrCreate(['flashcard_deck_id' => $deck->id, 'position' => $index + 1], compact('front', 'back', 'example'));
                FlashcardReview::firstOrCreate(['user_id' => $learner->id, 'flashcard_id' => $card->id], ['due_at' => now()]);
            }
            CourseReview::firstOrCreate(['course_id' => $course->id, 'user_id' => $learner->id], [
                'rating' => 4, 'content' => 'Đánh giá minh họa cho dữ liệu demo.', 'status' => 'published',
            ]);
            ExamComment::firstOrCreate(['exam_paper_id' => $paper->id, 'user_id' => $learner->id, 'content' => 'Bình luận minh họa, không phải nhận xét của học viên thực tế.'], ['status' => 'published']);
        });
    }

    private function course(LearningCategory $category, string $code, string $slug, string $name, int $price): Course
    {
        $course = Course::firstOrCreate(['code' => $code], [
            'learning_category_id' => $category->id, 'name' => $name, 'slug' => $slug,
            'delivery_mode' => 'online', 'level' => 'Cơ bản', 'duration_hours' => 12,
            'summary' => 'Khóa học minh họa do dự án tự biên soạn.',
            'description' => 'Dữ liệu phục vụ phát triển giao diện, không phải khóa học thương mại.',
            'price' => $price, 'sale_price' => $price - 100000, 'access_days' => 365,
            'is_active' => true, 'published_at' => now()->subDay(),
        ]);
        foreach (['Kỹ năng đọc hiểu', 'Luyện tập theo chủ đề'] as $index => $title) {
            $section = CourseSection::firstOrCreate(['course_id' => $course->id, 'position' => $index + 1], ['title' => $title]);
            foreach (['Tìm ý chính', 'Tìm thông tin chi tiết'] as $lessonIndex => $lessonTitle) {
                Lesson::firstOrCreate(['course_section_id' => $section->id, 'position' => $lessonIndex + 1], [
                    'title' => $lessonTitle, 'slug' => 'bai-'.($lessonIndex + 1), 'type' => 'text',
                    'content' => 'Đọc một đoạn văn ngắn, xác định chủ đề và gạch chân thông tin quan trọng. Nội dung minh họa tự biên soạn.',
                    'is_preview' => $index === 0 && $lessonIndex === 0, 'is_published' => true,
                ]);
            }
        }

        return $course;
    }

    private function section(ExamPaper $paper, int $position, string $title): ExamSection
    {
        return ExamSection::firstOrCreate(['exam_paper_id' => $paper->id, 'position' => $position], [
            'title' => $title, 'skill' => 'reading', 'instructions' => 'Read the passage and answer the questions.',
        ]);
    }

    private function group(ExamSection $section, string $passage): QuestionGroup
    {
        return QuestionGroup::firstOrCreate(['exam_section_id' => $section->id, 'position' => 1], [
            'title' => 'Reading passage', 'passage' => $passage,
        ]);
    }

    private function question(QuestionGroup $group, int $position, string $prompt, string $type, array $answers, array $options = []): void
    {
        $question = Question::firstOrCreate(['question_group_id' => $group->id, 'position' => $position], [
            'exam_section_id' => $group->exam_section_id, 'prompt' => $prompt, 'type' => $type, 'max_points' => 1,
        ]);
        $optionPosition = 0;
        foreach ($options as $label => $content) {
            QuestionOption::firstOrCreate(['question_id' => $question->id, 'label' => $label], ['content' => $content, 'position' => ++$optionPosition]);
        }
        QuestionAnswerKey::firstOrCreate(['question_id' => $question->id], [
            'accepted_answers' => $answers, 'explanation' => 'The answer follows directly from the original demo passage.',
        ]);
    }

    private function attempt(ExamPaper $paper, User $learner): void
    {
        // Do not append or rewrite a learner's history when this demo seeder is run again.
        if ($learner->examAttempts()->where('exam_paper_id', $paper->id)->exists()) {
            return;
        }
        $started = now()->subHours(2);
        $submitted = $started->copy()->addMinutes(10);
        $attempt = ExamAttempt::create([
            'user_id' => $learner->id, 'exam_paper_id' => $paper->id,
            'mode' => 'full', 'status' => 'in_progress', 'title_snapshot' => $paper->title,
            'started_at' => $started, 'time_limit_seconds' => $paper->duration_seconds,
        ]);
        $position = 0;
        $earnedPoints = 0;
        $maxPoints = 0;
        foreach ($paper->sections()->with('groups.questions.options', 'groups.questions.answerKey')->get() as $section) {
            ExamAttemptSection::create([
                'exam_attempt_id' => $attempt->id, 'exam_paper_id' => $paper->id, 'exam_section_id' => $section->id,
                'title_snapshot' => $section->title, 'instructions_snapshot' => $section->instructions,
                'position' => $section->position,
            ]);
            foreach ($section->groups as $group) {
                foreach ($group->questions as $question) {
                    $correct = ++$position !== 4;
                    $maxPoints += (float) $question->max_points;
                    $earnedPoints += $correct ? (float) $question->max_points : 0;
                    AttemptAnswer::create([
                        'exam_attempt_id' => $attempt->id, 'exam_section_id' => $section->id,
                        'question_id' => $question->id, 'position' => $position,
                        'type_snapshot' => $question->type, 'prompt_snapshot' => $question->prompt,
                        'group_snapshot' => $group->only(['title', 'instructions', 'passage', 'audio_path', 'image_path']),
                        'options_snapshot' => $question->options->map(fn (QuestionOption $option): array => $option->only(['label', 'content']))->all(),
                        'grading_snapshot' => [
                            'accepted_answers' => $question->answerKey->accepted_answers,
                            'case_sensitive' => $question->answerKey->case_sensitive,
                            'explanation' => $question->answerKey->explanation,
                        ],
                        'response' => $correct
                            ? ($question->type === 'multiple_choice' ? $question->answerKey->accepted_answers : [$question->answerKey->accepted_answers[0]])
                            : ['B'],
                        'is_correct' => $correct, 'awarded_points' => $correct ? $question->max_points : 0,
                        'max_points' => $question->max_points, 'answered_at' => $submitted,
                    ]);
                }
            }
        }
        $attempt->update([
            'status' => 'graded', 'submitted_at' => $submitted, 'graded_at' => $submitted,
            'elapsed_seconds' => 600, 'earned_points' => $earnedPoints, 'max_points' => $maxPoints,
        ]);
    }
}
