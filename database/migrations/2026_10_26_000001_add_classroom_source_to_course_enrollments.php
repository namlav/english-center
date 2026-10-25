<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE course_enrollments MODIFY COLUMN source ENUM('free','manual','purchase','classroom') NOT NULL DEFAULT 'manual'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE course_enrollments MODIFY COLUMN source ENUM('free','manual','purchase') NOT NULL DEFAULT 'manual'");
    }
};
