<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('class_rooms', function (Blueprint $table): void {
            $table->date('expected_end_date')->nullable()->after('start_date');
        });
    }

    public function down(): void
    {
        Schema::table('class_rooms', function (Blueprint $table): void {
            $table->dropColumn('expected_end_date');
        });
    }
};
