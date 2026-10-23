<?php

namespace Database\Seeders;

use App\Models\ExamCollection;
use App\Models\ExamPaper;
use App\Models\LearningCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BcReadsPracticeSeeder extends Seeder
{
    public function run(): void
    {
        $data = json_decode(file_get_contents(database_path('data/bc-reads-practice.json')), true, 512, JSON_THROW_ON_ERROR);

        DB::transaction(function () use ($data): void {
            $category = LearningCategory::firstOrCreate(['slug' => 'english-foundation'], ['name' => 'Tiếng Anh nền tảng']);
            $collection = ExamCollection::firstOrCreate(['slug' => $data['collection']['slug']], [
                'title' => $data['collection']['title'], 'publication_year' => $data['collection']['publication_year'],
                'description' => $data['source']['attribution'].' '.$data['source']['license_url'],
            ]);

            $baseTime = now()->startOfMinute();
            foreach ($data['sets'] as $index => $set) {
                // Preserve a whole existing paper, including any author edits to its questions/keys.
                if (ExamPaper::where('slug', $set['slug'])->exists()) {
                    continue;
                }

                $notice = $data['source']['attribution'].' '.$data['source']['license_url'].' Source: '.$set['source_url'];
                $paper = ExamPaper::create([
                    'learning_category_id' => $category->id, 'exam_collection_id' => $collection->id,
                    'title' => $set['title'], 'slug' => $set['slug'], 'skill' => 'reading',
                    'duration_seconds' => $set['duration_seconds'], 'status' => 'published', 'published_at' => (clone $baseTime)->subMinutes($index),
                    'description' => count($set['questions']).' câu, 1 điểm/câu. Bài luyện ngữ pháp/từ vựng, không quy đổi điểm IELTS/TOEIC. '
                        .$notice.' '.$data['source']['adaptation'],
                ]);
                $section = $paper->sections()->create([
                    'position' => 1, 'title' => $set['title'], 'skill' => 'reading',
                    'instructions' => $set['instructions'],
                ]);
                $group = $section->groups()->create([
                    'position' => 1, 'title' => $set['source_exercise'], 'passage' => $set['lesson'],
                    'instructions' => $set['instructions']."\n\n".$notice,
                    'rendering_data' => [
                        'source' => $data['source'], 'source_url' => $set['source_url'],
                        'exercise' => $set['source_exercise'],
                        'items' => array_map(static fn (array $question): array => [
                            'source_item' => $question['source_item'],
                            'source_url' => $question['source_url'] ?? $set['source_url'],
                        ], $set['questions']),
                    ],
                ]);
                foreach ($set['questions'] as $index => $item) {
                    $question = $group->questions()->create([
                        'exam_section_id' => $section->id, 'position' => $index + 1,
                        'prompt' => $item['prompt'], 'type' => isset($item['options']) ? 'single_choice' : 'short_answer',
                        'max_points' => 1,
                    ]);
                    foreach (array_keys($item['options'] ?? []) as $position => $label) {
                        $question->options()->create([
                            'label' => $label, 'content' => $item['options'][$label], 'position' => $position + 1,
                        ]);
                    }
                    $question->answerKey()->create([
                        'accepted_answers' => $item['answers'], 'case_sensitive' => false,
                        'explanation' => $item['explanation'].' Source: '.($item['source_url'] ?? $set['source_url'])
                            .' — '.$set['source_exercise'].', item '.$item['source_item'].'. '.$data['source']['attribution'],
                    ]);
                }
            }
        });
    }
}
