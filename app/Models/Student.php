<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get the subjects that this student is enrolled in.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subject', 'student_id', 'subject_code')
                    ->withPivot(['grade', 'status'])
                    ->withTimestamps();
    }

    /**
     * Get the subjects that this student has passed.
     */
    public function passedSubjects()
    {
        return $this->subjects()->wherePivot('status', 'passed');
    }

    /**
     * Get the subjects that this student has failed.
     */
    public function failedSubjects()
    {
        return $this->subjects()->wherePivot('status', 'failed');
    }

    /**
     * Get the subjects that this student is currently enrolled in.
     */
    public function enrolledSubjects()
    {
        return $this->subjects()->wherePivot('status', 'enrolled');
    }

    /**
     * Get the student's GPA (Grade Point Average).
     */
    public function getGpaAttribute()
    {
        $grades = $this->subjects()
                      ->wherePivot('status', 'passed')
                      ->get()
                      ->pluck('pivot.grade')
                      ->filter();

        return $grades->isEmpty() ? 0 : $grades->avg();
    }
}
