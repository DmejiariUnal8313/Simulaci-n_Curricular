<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimulationVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'curriculum_changes',
        'status',
        'exported_at',
        'external_curriculum_id'
    ];

    protected $casts = [
        'curriculum_changes' => 'array',
        'exported_at' => 'datetime'
    ];

    /**
     * Get the simulation subjects for this version.
     */
    public function simulationSubjects()
    {
        return $this->hasMany(SimulationSubject::class);
    }

    /**
     * Get the student simulation results for this version.
     */
    public function studentResults()
    {
        return $this->hasMany(StudentSimulationResult::class);
    }

    /**
     * Get the external curriculum if exported.
     */
    public function externalCurriculum()
    {
        return $this->belongsTo(ExternalCurriculum::class);
    }

    /**
     * Get subjects grouped by semester.
     */
    public function getSubjectsBySemester()
    {
        return $this->simulationSubjects()
            ->orderBy('semester')
            ->orderBy('name')
            ->get()
            ->groupBy('semester');
    }

    /**
     * Get statistics for this simulation.
     */
    public function getStats()
    {
        $totalSubjects = $this->simulationSubjects()->count();
        $newSubjects = $this->simulationSubjects()->where('change_type', 'new')->count();
        $modifiedSubjects = $this->simulationSubjects()->where('change_type', 'modified')->count();
        $movedSubjects = $this->simulationSubjects()->where('change_type', 'moved')->count();
        
        return [
            'total_subjects' => $totalSubjects,
            'new_subjects' => $newSubjects,
            'modified_subjects' => $modifiedSubjects,
            'moved_subjects' => $movedSubjects,
            'unchanged_subjects' => $totalSubjects - ($newSubjects + $modifiedSubjects + $movedSubjects),
            'total_credits' => $this->simulationSubjects()->sum('credits'),
            'affected_students' => $this->studentResults()->count()
        ];
    }

    /**
     * Create simulation from current curriculum.
     */
    public static function createFromCurrentCurriculum($name, $description = null)
    {
        $simulation = self::create([
            'name' => $name,
            'description' => $description,
            'curriculum_changes' => [],
            'status' => 'draft'
        ]);

        // Copy current subjects to simulation
        $subjects = Subject::with('prerequisites')->get();
        foreach ($subjects as $subject) {
            SimulationSubject::create([
                'simulation_version_id' => $simulation->id,
                'code' => $subject->code,
                'name' => $subject->name,
                'credits' => $subject->credits,
                'semester' => $subject->semester,
                'description' => $subject->description,
                'prerequisites' => $subject->prerequisites->pluck('code')->toArray(),
                'change_type' => 'unchanged',
                'original_code' => $subject->code
            ]);
        }

        return $simulation;
    }
}
