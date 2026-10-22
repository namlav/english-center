<?php

namespace Tests\Feature;

use App\Models\ReferenceImage;
use App\Models\ReferencePage;
use Database\Seeders\Study4PublicReferenceSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Study4PublicReferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_reference_import_has_real_local_images_without_creating_fake_lessons_or_questions(): void
    {
        $this->seed(Study4PublicReferenceSeeder::class);
        $this->assertDatabaseCount('reference_pages', 3);
        $this->assertDatabaseCount('reference_images', 8);
        $this->assertDatabaseCount('courses', 0);
        $this->assertDatabaseCount('questions', 0);
        $exam = ReferencePage::where('source_key', 'test-6842')->sole();
        $this->assertSame(40, $exam->metadata['question_count_on_source']);
        $this->assertSame(0, $exam->metadata['questions_imported']);
        $this->assertCount(0, $exam->images);
        foreach (ReferenceImage::all() as $image) {
            $path = public_path($image->local_path);
            $this->assertFileExists($path);
            $this->assertSame('image/webp', getimagesize($path)['mime']);
            $this->assertSame(hash_file('sha256', $path), $image->content_sha256);
            $this->assertGreaterThan(10, $image->width);
            $this->assertStringStartsWith('https://study4.com/', $image->source_url);
        }
        $this->assertStringContainsString('Chiến lược', ReferencePage::where('source_key', 'course-28')->sole()->title);
    }

    public function test_reimport_is_repeatable_and_does_not_overwrite_edits(): void
    {
        $this->seed(Study4PublicReferenceSeeder::class);
        $page = ReferencePage::first();
        $page->update(['summary' => 'Ghi chú do người dùng chỉnh sửa']);
        $this->seed(Study4PublicReferenceSeeder::class);
        $this->assertDatabaseCount('reference_pages', 3);
        $this->assertDatabaseCount('reference_images', 8);
        $this->assertSame('Ghi chú do người dùng chỉnh sửa', $page->fresh()->summary);
    }

    public function test_images_cannot_reference_a_missing_page(): void
    {
        $this->seed(Study4PublicReferenceSeeder::class);
        $image = ReferenceImage::first();
        $this->expectException(QueryException::class);
        $image->update(['reference_page_id' => 999999]);
    }
}
