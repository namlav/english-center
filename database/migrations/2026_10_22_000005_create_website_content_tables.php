<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('public_teachers', function (Blueprint $table): void {
            $table->id(); $table->string('name'); $table->string('slug')->unique(); $table->text('bio'); $table->string('photo_path');
            $table->string('provider',50); $table->string('source_url',500)->nullable(); $table->json('qualifications')->nullable();
            $table->unsignedInteger('position')->default(0); $table->boolean('is_active')->default(true); $table->timestamps();
        });
        Schema::create('site_settings', function (Blueprint $table): void { $table->id(); $table->string('key')->unique(); $table->json('value'); $table->timestamps(); });
    }
    public function down(): void { Schema::dropIfExists('site_settings'); Schema::dropIfExists('public_teachers'); }
};
