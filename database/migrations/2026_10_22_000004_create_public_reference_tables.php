<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reference_pages', function (Blueprint $table): void {
            $table->id();
            $table->string('provider', 50);
            $table->string('source_key', 100);
            $table->string('source_url', 500)->unique();
            $table->enum('content_type', ['course', 'exam']);
            $table->string('title');
            $table->text('summary');
            $table->json('metadata')->nullable();
            $table->timestamp('captured_at');
            $table->timestamps();
            $table->unique(['provider', 'source_key']);
        });

        Schema::create('reference_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('reference_page_id')->constrained()->cascadeOnDelete();
            $table->char('asset_key', 64);
            $table->text('source_url');
            $table->string('local_path')->unique();
            $table->string('alt_text');
            $table->enum('role', ['cover', 'illustration']);
            $table->unsignedInteger('position');
            $table->string('mime_type', 40);
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedInteger('file_size');
            $table->char('content_sha256', 64);
            $table->timestamps();
            $table->unique(['reference_page_id', 'asset_key']);
            $table->unique(['reference_page_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reference_images');
        Schema::dropIfExists('reference_pages');
    }
};
