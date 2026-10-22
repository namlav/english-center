<?php

namespace Database\Seeders;

use App\Models\{Course, LearningCategory};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursePresentationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (json_decode(file_get_contents(database_path('data/course-presentations.json')), true, 512, JSON_THROW_ON_ERROR) as $record) {
                $category = LearningCategory::firstOrCreate(['slug' => $record['category_slug']], ['name' => $record['category_name'], 'is_active' => true]);
                $course = Course::firstOrCreate(['code' => $record['code']], collect($record)->except(['code', 'category_slug', 'category_name'])->all() + [
                    'learning_category_id' => $category->id, 'delivery_mode' => 'online',
                    'is_active' => true, 'is_for_sale' => false, 'published_at' => now(), 'duration_hours' => 1, 'access_days' => 365,
                ]);
                // Re-running an upgrade must preserve edits made by the administrator.
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
