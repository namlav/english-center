<?php

namespace Database\Seeders;

use App\Models\ReferencePage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublicCourseCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $records = json_decode(file_get_contents(database_path('data/study4-course-catalog.json')), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($records): void {
            foreach ($records as $record) {
                $page = ReferencePage::firstOrCreate(['provider' => $record['provider'], 'source_key' => $record['source_key']], [
                    'source_url' => $record['source_url'], 'content_type' => 'course', 'title' => $record['title'],
                    'summary' => $record['summary'], 'metadata' => $record['metadata'], 'captured_at' => $record['captured_at'],
                ]);
                // Older reference imports may have only a subset of the catalog fields.
                $metadata = array_replace($record['metadata'], $page->metadata ?? []);
                if ($metadata !== $page->metadata) { $page->update(['metadata' => $metadata]); }
                foreach ($record['images'] as $index => $image) {
                    if (!is_file(public_path($image['local_path']))) { throw new \RuntimeException('Missing public catalog cover.'); }
                    $page->images()->firstOrCreate(['asset_key' => hash('sha256', $image['source_url'])], array_merge($image, ['position' => $index + 1]));
                }
            }
        });
    }
}
