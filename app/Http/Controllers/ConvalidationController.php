<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExternalCurriculum;
use App\Models\ExternalSubject;
use App\Models\SubjectConvalidation;
use App\Models\Subject;
use App\Models\Student;
use App\Models\StudentConvalidation;
use App\Services\ExcelImportService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ConvalidationController extends Controller
{
    /**
     * Display the main convalidation dashboard.
     */
    public function index()
    {
        $externalCurriculums = ExternalCurriculum::with('externalSubjects')
            ->where('status', 'active')
            ->latest()
            ->get();

        $stats = [
            'total_curriculums' => $externalCurriculums->count(),
            'total_external_subjects' => ExternalSubject::count(),
            'total_convalidations' => SubjectConvalidation::count(),
            'pending_convalidations' => ExternalSubject::pendingConvalidation()->count(),
        ];

        return view('convalidation.index', compact('externalCurriculums', 'stats'));
    }

    /**
     * Show the form for uploading a new external curriculum.
     */
    public function create()
    {
        return view('convalidation.create');
    }

    /**
     * Store a newly uploaded external curriculum.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'excel_file' => 'required|file|mimes:csv,txt|max:10240' // 10MB max, CSV only for now
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Store the uploaded file
            $file = $request->file('excel_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('external_curriculums', $filename, 'public');

            // Create the external curriculum record
            $externalCurriculum = ExternalCurriculum::create([
                'name' => $request->name,
                'institution' => $request->institution,
                'description' => $request->description,
                'uploaded_file' => $filePath,
                'metadata' => [
                    'original_filename' => $file->getClientOriginalName(),
                    'uploaded_at' => now(),
                    'file_size' => $file->getSize()
                ]
            ]);

            // Import the CSV data
            $importService = new ExcelImportService();
            $importService->validateFile($file);
            $importedCount = $importService->importCurriculum($file, $externalCurriculum->id);

            return redirect()->route('convalidation.show', $externalCurriculum)
                ->with('success', 'Malla externa cargada exitosamente. ' . $importedCount . ' materias importadas.');

        } catch (\Exception $e) {
            return back()->withErrors(['excel_file' => 'Error al procesar el archivo: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified external curriculum for convalidation.
     */
    public function show(ExternalCurriculum $externalCurriculum)
    {
        $externalCurriculum->load(['externalSubjects.convalidation.internalSubject']);
        $subjectsBySemester = $externalCurriculum->getConvalidationsBySemester();
        $internalSubjects = Subject::orderBy('semester')->orderBy('name')->get();
        $stats = $externalCurriculum->getStats();

        return view('convalidation.show', compact('externalCurriculum', 'subjectsBySemester', 'internalSubjects', 'stats'));
    }

    /**
     * Create or update a convalidation mapping.
     */
    public function storeConvalidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'external_subject_id' => 'required|exists:external_subjects,id',
            'convalidation_type' => 'required|in:direct,free_elective,not_convalidated',
            'internal_subject_code' => 'nullable|exists:subjects,code',
            'notes' => 'nullable|string',
            'equivalence_percentage' => 'nullable|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Validate that direct convalidations have an internal subject
        if ($request->convalidation_type === 'direct' && !$request->internal_subject_code) {
            return response()->json(['error' => 'Las convalidaciones directas requieren una materia interna'], 422);
        }

        // Validate that not_convalidated type doesn't have an internal subject
        if ($request->convalidation_type === 'not_convalidated' && $request->internal_subject_code) {
            return response()->json(['error' => 'Las materias no convalidadas no deben tener una materia interna asignada'], 422);
        }

        try {
            $externalSubject = ExternalSubject::findOrFail($request->external_subject_id);

            // Delete existing convalidation if any
            SubjectConvalidation::where('external_subject_id', $externalSubject->id)->delete();

            // Create new convalidation
            $convalidation = SubjectConvalidation::create([
                'external_curriculum_id' => $externalSubject->external_curriculum_id,
                'external_subject_id' => $externalSubject->id,
                'internal_subject_code' => $request->convalidation_type === 'direct' ? $request->internal_subject_code : null,
                'convalidation_type' => $request->convalidation_type,
                'notes' => $request->notes,
                'equivalence_percentage' => $request->convalidation_type === 'not_convalidated' ? 0.00 : ($request->equivalence_percentage ?? 100.00),
                'status' => 'pending'
            ]);

            $convalidation->load('internalSubject');
            
            // Get updated statistics
            $curriculum = ExternalCurriculum::find($externalSubject->external_curriculum_id);
            $updatedStats = $curriculum->getStats();

            return response()->json([
                'success' => true,
                'message' => 'Convalidación creada exitosamente',
                'convalidation' => $convalidation,
                'stats' => $updatedStats
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al crear la convalidación: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove a convalidation mapping.
     */
    public function destroyConvalidation($convalidationId)
    {
        try {
            $convalidation = SubjectConvalidation::findOrFail($convalidationId);
            $convalidation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Convalidación eliminada exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al eliminar la convalidación: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get suggestions for convalidation based on subject name similarity.
     */
    public function getSuggestions(Request $request)
    {
        $externalSubjectId = $request->external_subject_id;
        $externalSubject = ExternalSubject::findOrFail($externalSubjectId);
        
        // Get internal subjects and calculate similarity
        $internalSubjects = Subject::all();
        $suggestions = [];

        foreach ($internalSubjects as $internal) {
            $similarity = $this->calculateSimilarity($externalSubject->name, $internal->name);
            
            if ($similarity > 0.3) { // 30% similarity threshold
                $suggestions[] = [
                    'subject' => $internal,
                    'similarity' => $similarity,
                    'match_percentage' => round($similarity * 100, 2)
                ];
            }
        }

        // Sort by similarity descending
        usort($suggestions, function($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        // Take top 5 suggestions
        $suggestions = array_slice($suggestions, 0, 5);

        return response()->json(['suggestions' => $suggestions]);
    }

    /**
     * Calculate similarity between two strings.
     */
    private function calculateSimilarity($str1, $str2)
    {
        // Normalize strings
        $str1 = strtolower(trim($str1));
        $str2 = strtolower(trim($str2));

        // Remove common words that don't add meaning
        $commonWords = ['de', 'la', 'el', 'y', 'del', 'las', 'los', 'con', 'para', 'por', 'en', 'a', 'un', 'una', 'al'];
        
        foreach ($commonWords as $word) {
            $str1 = str_replace(' ' . $word . ' ', ' ', ' ' . $str1 . ' ');
            $str2 = str_replace(' ' . $word . ' ', ' ', ' ' . $str2 . ' ');
        }

        $str1 = trim($str1);
        $str2 = trim($str2);

        // Calculate Levenshtein distance similarity
        $levenshtein = levenshtein($str1, $str2);
        $maxLen = max(strlen($str1), strlen($str2));
        $levenshteinSimilarity = $maxLen > 0 ? 1 - ($levenshtein / $maxLen) : 0;

        // Calculate word-based similarity
        $words1 = explode(' ', $str1);
        $words2 = explode(' ', $str2);
        $commonWordsCount = count(array_intersect($words1, $words2));
        $totalWords = count(array_unique(array_merge($words1, $words2)));
        $wordSimilarity = $totalWords > 0 ? $commonWordsCount / $totalWords : 0;

        // Combine similarities with weights
        return ($levenshteinSimilarity * 0.6) + ($wordSimilarity * 0.4);
    }

    /**
     * Export convalidation report.
     */
    public function exportReport(ExternalCurriculum $externalCurriculum)
    {
        // This would generate a detailed report
        // For now, return JSON data
        $data = [
            'curriculum' => $externalCurriculum,
            'subjects' => $externalCurriculum->externalSubjects()->with('convalidation.internalSubject')->get(),
            'stats' => $externalCurriculum->getStats()
        ];

        return response()->json($data);
    }

    /**
     * Delete an external curriculum and all its data.
     */
    public function destroy(ExternalCurriculum $externalCurriculum)
    {
        try {
            // Delete the uploaded file
            if ($externalCurriculum->uploaded_file) {
                Storage::disk('public')->delete($externalCurriculum->uploaded_file);
            }

            // Delete the curriculum (cascade will handle related records)
            $externalCurriculum->delete();

            return redirect()->route('convalidation.index')
                ->with('success', 'Malla externa eliminada exitosamente');

        } catch (\Exception $e) {
            return redirect()->route('convalidation.index')
                ->withErrors(['error' => 'Error al eliminar la malla: ' . $e->getMessage()]);
        }
    }

    /**
     * Analyze the impact of migrating students from original curriculum to external curriculum with convalidations
     */
    public function analyzeConvalidationImpact(ExternalCurriculum $externalCurriculum, Request $request)
    {
        try {
            // Log para debugging
            \Log::info('Iniciando análisis de impacto', [
                'curriculum_id' => $externalCurriculum->id,
                'request_data' => $request->all()
            ]);

            // Get configuration parameters
            $maxFreeElectiveCredits = $request->input('max_free_elective_credits', 12);
            $priorityCriteria = $request->input('priority_criteria', 'credits');

            \Log::info('Parámetros obtenidos', [
                'max_credits' => $maxFreeElectiveCredits,
                'criteria' => $priorityCriteria
            ]);

            // Get all students from the original curriculum
            $students = Student::with([
                'subjects' => function($query) {
                    $query->wherePivot('status', 'passed');
                },
                'currentSubjects.subject'
            ])->get();

            // Get all convalidations for this external curriculum
            $convalidations = SubjectConvalidation::where('external_curriculum_id', $externalCurriculum->id)
                ->with(['externalSubject', 'internalSubject'])
                ->get();

            // Separate direct, free elective, and not convalidated convalidations
            $directConvalidations = $convalidations->where('convalidation_type', 'direct');
            $freeElectiveConvalidations = $convalidations->where('convalidation_type', 'free_elective');
            $notConvalidatedConvalidations = $convalidations->where('convalidation_type', 'not_convalidated');

            // Apply credit limit and priority to free electives
            $selectedFreeElectives = $this->selectFreeElectives(
                $freeElectiveConvalidations, 
                $maxFreeElectiveCredits, 
                $priorityCriteria
            );

            // Get all subjects from original curriculum
            $originalSubjects = Subject::with(['prerequisites', 'requiredFor'])->get()->keyBy('code');
            $totalOriginalSubjects = $originalSubjects->count();

            $results = [
                'total_students' => $students->count(),
                'affected_students' => 0,
                'students_improved' => 0,
                'students_same' => 0,
                'students_worsened' => 0,
                'affected_percentage' => 0,
                'average_progress_change' => 0,
                'total_convalidated_subjects' => $directConvalidations->count() + $selectedFreeElectives->count(),
                'direct_convalidations_count' => $directConvalidations->count(),
                'free_electives_used' => $selectedFreeElectives->count(),
                'free_electives_available' => $freeElectiveConvalidations->count(),
                'free_electives_credits_used' => $selectedFreeElectives->sum('externalSubject.credits'),
                'free_electives_credits_available' => $freeElectiveConvalidations->sum('externalSubject.credits'),
                'max_free_elective_credits' => $maxFreeElectiveCredits,
                'excess_free_electives' => $freeElectiveConvalidations->count() - $selectedFreeElectives->count(),
                'additional_subjects_required' => $notConvalidatedConvalidations->count(),
                'total_credits_added' => $notConvalidatedConvalidations->sum(function($conv) { 
                    return $conv->externalSubject->credits ?? 0; 
                }),
                'student_details' => [],
                'subject_impact' => [],
                'configuration' => [
                    'max_free_elective_credits' => $maxFreeElectiveCredits,
                    'priority_criteria' => $priorityCriteria
                ]
            ];

            $totalProgressChange = 0;
            $subjectImpactMap = [];

            foreach ($students as $student) {
                $impact = $this->calculateStudentConvalidationImpactWithLimits(
                    $student, 
                    $directConvalidations, 
                    $selectedFreeElectives, 
                    $originalSubjects, 
                    $totalOriginalSubjects
                );
                
                if ($impact['has_impact']) {
                    $results['affected_students']++;
                    
                    if ($impact['progress_change'] > 0) {
                        $results['students_improved']++;
                    } elseif ($impact['progress_change'] < 0) {
                        $results['students_worsened']++;
                    } else {
                        $results['students_same']++;
                    }

                    $totalProgressChange += $impact['progress_change'];
                    
                    $results['student_details'][] = [
                        'student_id' => $student->id,
                        'name' => $student->name,
                        'original_progress' => round($impact['original_progress'], 1),
                        'new_progress' => round($impact['new_progress'], 1),
                        'progress_change' => round($impact['progress_change'], 1),
                        'convalidated_count' => $impact['convalidated_subjects_count'],
                        'convalidated_subjects' => $impact['convalidated_subjects'],
                        'direct_convalidations' => $impact['direct_convalidations'],
                        'free_electives' => $impact['free_electives']
                    ];

                    // Track subject impact
                    foreach ($impact['convalidated_subjects'] as $subjectCode) {
                        if (!isset($subjectImpactMap[$subjectCode])) {
                            $subjectImpactMap[$subjectCode] = [
                                'students_benefited' => 0,
                                'total_benefit' => 0
                            ];
                        }
                        $subjectImpactMap[$subjectCode]['students_benefited']++;
                        $subjectImpactMap[$subjectCode]['total_benefit'] += max(0, $impact['progress_change']);
                    }
                }
            }

            // Calculate final statistics
            $results['affected_percentage'] = $results['total_students'] > 0 
                ? round(($results['affected_students'] / $results['total_students']) * 100, 1)
                : 0;

            $results['average_progress_change'] = $results['affected_students'] > 0 
                ? round($totalProgressChange / $results['affected_students'], 1)
                : 0;

            // Calculate subject impact details for both direct and free electives
            $allSelectedConvalidations = $directConvalidations->merge($selectedFreeElectives);
            
            foreach ($allSelectedConvalidations as $convalidation) {
                $externalSubjectCode = $convalidation->externalSubject->code;
                $impactData = $subjectImpactMap[$externalSubjectCode] ?? ['students_benefited' => 0, 'total_benefit' => 0];
                
                $results['subject_impact'][] = [
                    'external_subject_code' => $externalSubjectCode,
                    'external_subject_name' => $convalidation->externalSubject->name,
                    'internal_subject_name' => $convalidation->internalSubject ? $convalidation->internalSubject->name : 'Libre Elección',
                    'convalidation_type' => $convalidation->convalidation_type,
                    'credits' => $convalidation->externalSubject->credits,
                    'students_benefited' => $impactData['students_benefited'],
                    'average_benefit' => $impactData['students_benefited'] > 0 ? round($impactData['total_benefit'] / $impactData['students_benefited'], 1) : 0,
                    'impact_type' => $this->classifyImpactType($impactData['students_benefited'], $results['total_students']),
                    'is_selected' => true
                ];
            }

            // Add information about excess free electives
            $excessFreeElectives = $freeElectiveConvalidations->diff($selectedFreeElectives);
            foreach ($excessFreeElectives as $convalidation) {
                $results['subject_impact'][] = [
                    'external_subject_code' => $convalidation->externalSubject->code,
                    'external_subject_name' => $convalidation->externalSubject->name,
                    'internal_subject_name' => 'Libre Elección (Excedente)',
                    'convalidation_type' => 'free_elective_excess',
                    'credits' => $convalidation->externalSubject->credits,
                    'students_benefited' => 0,
                    'average_benefit' => 0,
                    'impact_type' => 'none',
                    'is_selected' => false
                ];
            }

            // Calculate average reductions
            $results['average_credits_reduced'] = round($results['total_convalidated_subjects'] * 3, 1);
            $results['average_semesters_reduced'] = round($results['average_progress_change'] / 10, 1);

            return response()->json([
                'success' => true,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en análisis de impacto', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al analizar el impacto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get summary of convalidations for preview
     */
    public function getConvalidationsSummary(ExternalCurriculum $externalCurriculum)
    {
        try {
            $convalidations = SubjectConvalidation::where('external_curriculum_id', $externalCurriculum->id)
                ->with(['externalSubject', 'internalSubject'])
                ->get();

            $summary = $convalidations->map(function ($convalidation) {
                return [
                    'id' => $convalidation->id,
                    'external_subject_code' => $convalidation->externalSubject->code,
                    'external_subject_name' => $convalidation->externalSubject->name,
                    'internal_subject_code' => $convalidation->internalSubject ? $convalidation->internalSubject->code : null,
                    'internal_subject_name' => $convalidation->internalSubject ? $convalidation->internalSubject->name : null,
                    'type' => $convalidation->convalidation_type,
                    'credits' => $convalidation->externalSubject->credits,
                    'semester' => $convalidation->externalSubject->semester
                ];
            });

            return response()->json([
                'success' => true,
                'convalidations' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el resumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Select free electives based on credit limit and priority criteria
     */
    private function selectFreeElectives($freeElectiveConvalidations, $maxCredits, $priorityCriteria)
    {
        // Convert to array for sorting
        $electives = $freeElectiveConvalidations->toArray();

        // Sort based on priority criteria
        switch ($priorityCriteria) {
            case 'credits':
                usort($electives, function($a, $b) {
                    return $b['external_subject']['credits'] - $a['external_subject']['credits'];
                });
                break;
            case 'semester':
                usort($electives, function($a, $b) {
                    return $a['external_subject']['semester'] - $b['external_subject']['semester'];
                });
                break;
            case 'students':
                // For now, use credits as fallback since we'd need additional analysis
                usort($electives, function($a, $b) {
                    return $b['external_subject']['credits'] - $a['external_subject']['credits'];
                });
                break;
        }

        // Select electives up to the credit limit
        $selectedElectives = collect();
        $usedCredits = 0;

        foreach ($electives as $elective) {
            $electiveCredits = $elective['external_subject']['credits'];
            if ($usedCredits + $electiveCredits <= $maxCredits) {
                $selectedElectives->push(
                    SubjectConvalidation::with(['externalSubject', 'internalSubject'])
                        ->find($elective['id'])
                );
                $usedCredits += $electiveCredits;
            }
        }

        return $selectedElectives;
    }

    /**
     * Calculate the impact of convalidations on a specific student with credit limits
     */
    private function calculateStudentConvalidationImpactWithLimits(
        Student $student, 
        $directConvalidations, 
        $selectedFreeElectives, 
        $originalSubjects, 
        $totalOriginalSubjects
    ) {
        $passedSubjects = $student->subjects->keyBy('code');
        
        // Calculate original progress
        $originalProgress = ($passedSubjects->count() / $totalOriginalSubjects) * 100;
        
        // Apply direct convalidations
        $convalidatedSubjects = [];
        $directConvalidationsApplied = [];
        $additionalPassedCount = 0;
        
        foreach ($directConvalidations as $convalidation) {
            if ($convalidation->internalSubject) {
                $internalSubjectCode = $convalidation->internalSubject->code;
                
                // If student hasn't passed this subject, they benefit from convalidation
                if (!isset($passedSubjects[$internalSubjectCode])) {
                    $convalidatedSubjects[] = $internalSubjectCode;
                    $directConvalidationsApplied[] = $convalidation->externalSubject->code;
                    $additionalPassedCount++;
                }
            }
        }

        // Apply selected free electives (these don't map to specific internal subjects)
        $freeElectivesApplied = [];
        foreach ($selectedFreeElectives as $convalidation) {
            $freeElectivesApplied[] = $convalidation->externalSubject->code;
            // Free electives contribute to progress but don't map to specific subjects
            $additionalPassedCount++;
        }
        
        // Calculate new progress with convalidations
        $newPassedCount = $passedSubjects->count() + $additionalPassedCount;
        $newProgress = ($newPassedCount / $totalOriginalSubjects) * 100;
        $progressChange = $newProgress - $originalProgress;
        
        return [
            'has_impact' => $additionalPassedCount > 0,
            'original_progress' => $originalProgress,
            'new_progress' => $newProgress,
            'progress_change' => $progressChange,
            'convalidated_subjects_count' => $additionalPassedCount,
            'convalidated_subjects' => array_merge($convalidatedSubjects, $freeElectivesApplied),
            'direct_convalidations' => $directConvalidationsApplied,
            'free_electives' => $freeElectivesApplied
        ];
    }

    /**
     * Test endpoint to verify CSRF and basic functionality
     */
    public function testEndpoint(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Endpoint funciona correctamente',
            'csrf_token' => $request->header('X-CSRF-TOKEN'),
            'method' => $request->method(),
            'data' => $request->all()
        ]);
    }

    /**
     * Classify the impact type based on how many students benefit from a convalidation
     */
    private function classifyImpactType(int $studentsBenefited, int $totalStudents): string
    {
        if ($totalStudents == 0) {
            return 'none';
        }

        $benefitPercentage = ($studentsBenefited / $totalStudents) * 100;

        if ($benefitPercentage >= 75) {
            return 'high';
        } elseif ($benefitPercentage >= 50) {
            return 'medium';
        } elseif ($benefitPercentage >= 25) {
            return 'low';
        } elseif ($benefitPercentage > 0) {
            return 'minimal';
        } else {
            return 'none';
        }
    }

    /**
     * Save a modified curriculum from simulation as a new external curriculum for convalidation
     */
    public function saveModifiedCurriculum(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'curriculum' => 'required|array',
                'changes' => 'array',
                'institution' => 'nullable|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $curriculumData = $request->input('curriculum');
            $changes = $request->input('changes', []);
            $name = $request->input('name');
            $institution = $request->input('institution', 'Simulación Curricular');

            // Calculate total subjects
            $totalSubjects = 0;
            foreach ($curriculumData as $semester => $subjects) {
                $totalSubjects += count($subjects);
            }

            // Create external curriculum using the correct fields
            $externalCurriculum = ExternalCurriculum::create([
                'name' => $name,
                'institution' => $institution,
                'description' => 'Malla curricular modificada desde simulación. Total de cambios realizados: ' . count($changes) . '. Total de materias: ' . $totalSubjects,
                'uploaded_file' => null, // No file uploaded, created from simulation
                'metadata' => [
                    'source' => 'simulation',
                    'total_subjects' => $totalSubjects,
                    'changes_count' => count($changes),
                    'changes' => $changes,
                    'created_at' => now()->toISOString()
                ],
                'status' => 'active'
            ]);

            // Process curriculum data and create external subjects
            foreach ($curriculumData as $semester => $subjects) {
                foreach ($subjects as $subjectData) {
                    // Prepare additional_data with information about prerequisites and simulation details
                    $additionalData = [
                        'prerequisites' => $subjectData['prerequisites'] ?? [],
                        'is_added_in_simulation' => $subjectData['isAdded'] ?? false,
                        'original_description' => $subjectData['description'] ?? null,
                        'source' => 'simulation'
                    ];

                    ExternalSubject::create([
                        'external_curriculum_id' => $externalCurriculum->id,
                        'code' => $subjectData['code'],
                        'name' => $subjectData['name'],
                        'semester' => (int) $subjectData['semester'],
                        'credits' => $subjectData['credits'] ?? 3, // Default to 3 credits if not specified
                        'description' => $subjectData['description'] ?? $subjectData['name'],
                        'additional_data' => $additionalData
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Malla curricular guardada exitosamente para convalidación',
                'curriculum_id' => $externalCurriculum->id,
                'redirect_url' => route('convalidation.show', $externalCurriculum->id)
            ]);

        } catch (\Exception $e) {
            \Log::error('Error saving modified curriculum: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al guardar la malla curricular',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
