<?php

namespace Database\Seeders;

use App\Models\ReferenceImage;
use App\Models\ReferencePage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Study4PublicReferenceSeeder extends Seeder
{
    public function run(): void
    {
        $pages = json_decode(file_get_contents(database_path('data/study4-public-reference.json')), true, 512, JSON_THROW_ON_ERROR);
        DB::transaction(function () use ($pages): void {
            foreach ($pages as $data) {
                $page = ReferencePage::firstOrCreate(['provider' => $data['provider'], 'source_key' => $data['source_key']], [
                    'source_url' => $data['source_url'], 'content_type' => $data['content_type'], 'title' => $data['title'],
                    'summary' => $data['summary'], 'metadata' => $data['metadata'], 'captured_at' => $data['captured_at'],
                ]);
                foreach ($data['images'] as $index => $image) {
                    if (! str_starts_with($image['local_path'], 'images/references/study4/') || str_contains($image['local_path'], '..')) {
                        throw new \RuntimeException('Invalid reference image path.');
                    }
                    $path = public_path($image['local_path']);
                    $dimensions = is_file($path) ? getimagesize($path) : false;
                    if ($dimensions === false || $dimensions[0] <= 0 || $dimensions[1] <= 0 || ! str_starts_with($dimensions['mime'], 'image/')) {
                        throw new \RuntimeException('Reference image is missing or invalid: '.$image['local_path']);
                    }
                    ReferenceImage::firstOrCreate(['reference_page_id' => $page->id, 'asset_key' => hash('sha256', $image['source_url'])], [
                        ...$image, 'position' => $index + 1, 'mime_type' => $dimensions['mime'],
                        'width' => $dimensions[0], 'height' => $dimensions[1], 'file_size' => filesize($path), 'content_sha256' => hash_file('sha256', $path),
                    ]);
                }
            }
        });
    }
}
