<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;

class CurriculumController extends Controller
{
    public function index()
    {
        // Obtener todas las materias agrupadas por semestre
        $subjects = Subject::orderBy('semester')->orderBy('name')->get();
        
        // Agrupar materias por semestre
        $curriculumBySemester = [];
        for ($i = 1; $i <= 10; $i++) {
            $curriculumBySemester[$i] = $subjects->where('semester', $i)->values();
        }
        
        return view('curriculum.index', compact('curriculumBySemester'));
    }
}
