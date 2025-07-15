<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code',
        'name',
        'semester',
    ];

    /**
     * Get the students that are enrolled in this subject.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_subject', 'subject_code', 'student_id')
                    ->withPivot(['grade', 'status'])
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include subjects from a specific semester.
     */
    public function scopeSemester($query, $semester)
    {
        return $query->where('semester', $semester);
    }
}
