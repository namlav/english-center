<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function learnerProfile(): HasOne
    {
        return $this->hasOne(LearnerProfile::class);
    }

    public function courseLearningProfiles(): HasMany
    {
        return $this->hasMany(CourseLearningProfile::class);
    }

    public function taughtOnlineCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_instructors')->withPivot('position')->withTimestamps();
    }

    public function lessonProgress(): HasMany
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function authoredExamPapers(): HasMany
    {
        return $this->hasMany(ExamPaper::class, 'created_by');
    }

    public function flashcardDecks(): HasMany
    {
        return $this->hasMany(FlashcardDeck::class);
    }

    public function flashcardReviews(): HasMany
    {
        return $this->hasMany(FlashcardReview::class);
    }

    public function courseReviews(): HasMany
    {
        return $this->hasMany(CourseReview::class);
    }

    public function examComments(): HasMany
    {
        return $this->hasMany(ExamComment::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'password' => 'hashed'];
    }
}
