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
            'convalidation_type' => 'required|in:direct,free_elective',
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
                'equivalence_percentage' => $request->equivalence_percentage ?? 100.00,
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
}
