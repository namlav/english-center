<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('class_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('restrict');
            $table->date('start_date');
            $table->string('schedule');
            $table->decimal('tuition_fee', 12, 2)->default(0);
            $table->unsignedInteger('max_students')->default(25);
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_rooms');
    }
};
