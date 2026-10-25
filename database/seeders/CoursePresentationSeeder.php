<?php

namespace Database\Seeders;

use App\Models\{Course, CourseSection, Lesson, LearningCategory};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CoursePresentationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $curriculumJson = database_path('data/study4-full-curriculum.json');
            $curriculumData = (!app()->runningUnitTests() && file_exists($curriculumJson)) ? json_decode(file_get_contents($curriculumJson), true) : [];

            foreach (json_decode(file_get_contents(database_path('data/course-presentations.json')), true, 512, JSON_THROW_ON_ERROR) as $record) {
                $category = LearningCategory::firstOrCreate(['slug' => $record['category_slug']], ['name' => $record['category_name'], 'is_active' => true]);
                $course = Course::firstOrCreate(['code' => $record['code']], collect($record)->except(['code', 'category_slug', 'category_name'])->all() + [
                    'learning_category_id' => $category->id, 'delivery_mode' => 'online',
                    'is_active' => true, 'is_for_sale' => true, 'published_at' => now(), 'duration_hours' => 12, 'access_days' => 365,
                ]);

                if ($course->sections()->count() === 0) {
                    $catKey = $course->catalog_key;
                    if ($catKey && isset($curriculumData[$catKey])) {
                        $cData = $curriculumData[$catKey];
                        $units = $cData['units'] ?? [];
                        $trials = $cData['trials'] ?? [];
                        $trialTitles = array_map(fn($t) => mb_strtolower(trim($t['title'])), $trials);

                        foreach ($units as $uIndex => $unit) {
                            $section = CourseSection::create([
                                'course_id' => $course->id,
                                'title' => $unit['title'],
                                'position' => $unit['position'],
                            ]);

                            $lessonsToInsert = [];
                            foreach ($unit['lessons'] as $lIndex => $l) {
                                $cleanTitle = trim($l['title']);
                                $lowerTitle = mb_strtolower($cleanTitle);
                                $isTrial = $l['is_trial'] || in_array($lowerTitle, $trialTitles, true);
                                if (!$isTrial && $uIndex === 0 && $lIndex === 0) {
                                    $isTrial = true;
                                }

                                $slug = Str::slug($course->slug . '-u' . ($uIndex + 1) . '-l' . ($lIndex + 1) . '-' . Str::random(4));
                                $lessonsToInsert[] = [
                                    'course_section_id' => $section->id,
                                    'title' => $cleanTitle,
                                    'slug' => $slug,
                                    'type' => (stripos($cleanTitle, 'video') !== false || stripos($cleanTitle, 'nghe') !== false || stripos($cleanTitle, 'speaking') !== false) ? 'video' : 'text',
                                    'content' => "MỤC TIÊU BÀI HỌC\n- Nắm vững kiến thức trọng tâm cho chuyên đề: {$unit['title']} — Bài học: {$cleanTitle}.\n- Rèn luyện kỹ năng giải quyết bài thi, nhận diện bẫy và áp dụng mẹo làm bài chuẩn Study4.\n\nKIẾN THỨC CỐT LÕI\n1. Các khái niệm và quy tắc ngữ pháp/từ vựng then chốt.\n2. Phân tích ví dụ mẫu từ đề thi thực tế.\n\nBÀI TẬP VẬN DỤNG\n- Làm bài tập tự luyện và đối chiếu giải thích chi tiết.",
                                    'duration_seconds' => 1800,
                                    'position' => $lIndex + 1,
                                    'is_preview' => $isTrial ? 1 : 0,
                                    'is_published' => 1,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                            if (!empty($lessonsToInsert)) {
                                DB::table('lessons')->insert($lessonsToInsert);
                            }
                        }
                    } else {
                        // Fallback
                        $topics = $course->curriculum_outline ?? ['Tổng quan & Phương pháp', 'Kỹ thuật làm bài thực tế'];
                        foreach (array_slice($topics, 0, 5) as $secPos => $topicTitle) {
                            $cleanTitle = trim(preg_replace('/^\[[^\]]+\]\s*/', '', $topicTitle));
                            $section = CourseSection::create(['course_id' => $course->id, 'title' => $cleanTitle, 'position' => $secPos + 1]);
                            Lesson::create([
                                'course_section_id' => $section->id,
                                'title' => 'Lý thuyết & Phương pháp: ' . Str::limit($cleanTitle, 60),
                                'slug' => Str::slug($course->slug . '-s' . ($secPos + 1) . '-p1-' . Str::random(4)),
                                'type' => 'video',
                                'duration_seconds' => 1800,
                                'is_preview' => ($secPos === 0),
                                'is_published' => true,
                                'position' => 1,
                                'content' => "Nội dung bài học cho phần: {$cleanTitle}.",
                            ]);
                        }
                    }

                    $course->duration_hours = max((int)$course->duration_hours, (int)ceil($course->lessons()->count() * 0.75));
                    $course->save();
                }
            }
            foreach (['EC-GRAMMAR' => 'course-13', 'EC-TOEIC-READ' => 'course-28', 'EC-IELTS-READ' => 'course-22'] as $code => $key) {
                $course = Course::where('code', $code)->first();
                if (!$course || $course->catalog_key) continue;
                $course->catalog_key = 'practice-'.strtolower($code);
                if (!$course->thumbnail_path) $course->thumbnail_path = 'images/catalog/study4/'.$key.'.webp';
                if (!$course->intro_video_url) $course->intro_video_url = match ($code) {
                    'EC-TOEIC-READ' => 'https://youtu.be/QV-X5DpteLk',
                    'EC-IELTS-READ' => 'https://youtu.be/Bjb5i-tfAMk', default => null,
                };
                $course->save();
            }
        });
    }
}
