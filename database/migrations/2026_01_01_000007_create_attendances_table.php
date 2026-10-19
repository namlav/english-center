<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_room_id')->constrained('class_rooms')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late'])->default('present');
            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['class_room_id', 'student_id', 'attendance_date'], 'uniq_class_student_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
