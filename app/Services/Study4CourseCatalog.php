<?php

namespace App\Services;

use App\Models\ReferencePage;
use Illuminate\Support\Collection;

class Study4CourseCatalog
{
    public const GROUPS = [
        'combo' => 'Combo đặc biệt', 'toeic' => 'TOEIC', 'ielts-academic' => 'IELTS Academic',
        'ielts-general' => 'IELTS General', 'english-foundation' => 'Tiếng Anh cơ bản', 'hsk' => 'HSK',
    ];

    public function all(): Collection
    {
        $catalog = collect();
        foreach (ReferencePage::where('provider', 'study4')->where('content_type', 'course')->with('images')->get() as $page) {
            $record = $page->toArray();
            $record['captured_at'] = $page->captured_at->toIso8601String();
            $record['metadata'] = array_replace(['catalog_group'=>'','catalog_group_label'=>'','price'=>0,'list_price'=>0,'currency'=>'VND','kind'=>'course','source_statistics'=>['rating'=>null,'review_count'=>0,'learner_count'=>0],'facts'=>[],'curriculum_topics'=>[],'included_courses'=>[],'highlights'=>[],'intro_video_url'=>null], $page->metadata ?? []);
            if (($record['metadata']['is_visible'] ?? true) === false) { continue; }
            $cover = $page->images->firstWhere('role', 'cover');
            if (!$cover || !$this->safeImage($cover->local_path)) { continue; }
            $record['images'] = [$cover->toArray()];
            $catalog[$page->source_key] = $record;
        }

        return $catalog->filter(fn (array $record) => isset(self::GROUPS[$record['metadata']['catalog_group'] ?? ''])
            && str_starts_with($record['source_url'], 'https://study4.com/courses/')
            && $this->safeImage($record['images'][0]['local_path'] ?? ''))->sortBy(fn ($course) => $course['metadata']['position'] ?? 0)->values();
    }

    public function find(string $key): ?array
    {
        return $this->all()->firstWhere('source_key', $key);
    }

    private function safeImage(string $path): bool
    {
        return str_starts_with($path, 'images/') && ! str_contains($path, '..') && is_file(public_path($path));
    }
}
