<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Simulación Curricular</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/simulation.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="bi bi-mortarboard-fill"></i> Simulación Curricular
            </a>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <h1 class="main-title">Malla Curricular - Administración de Sistemas Informáticos</h1>
        
        <!-- Statistics -->
        <div class="stats-container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">{{ \App\Models\Subject::count() }}</div>
                        <div class="stat-label">Total Materias</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">10</div>
                        <div class="stat-label">Semestres</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">{{ \App\Models\Student::count() }}</div>
                        <div class="stat-label">Estudiantes</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">0</div>
                        <div class="stat-label">Simulaciones</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Curriculum Grid -->
        <div class="curriculum-grid">
            @php
                $subjects = \App\Models\Subject::with(['prerequisites', 'requiredFor'])->orderBy('semester')->get();
                $subjectsBySemester = $subjects->groupBy('semester');
            @endphp

            @for ($semester = 1; $semester <= 10; $semester++)
                <div class="semester-column">
                    <div class="semester-title">{{ $semester }}° Semestre</div>
                    <div class="subjects-container">
                        @if(isset($subjectsBySemester[$semester]))
                            @foreach($subjectsBySemester[$semester] as $subject)
                                <div class="subject-card available" 
                                     data-subject-id="{{ $subject->code }}"
                                     data-prerequisites="{{ $subject->prerequisites->pluck('code')->implode(',') }}"
                                     data-unlocks="{{ $subject->requiredFor->pluck('code')->implode(',') }}">
                                    <div class="subject-name">{{ $subject->name }}</div>
                                    <div class="subject-code">{{ $subject->code }}</div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/simulation.js') }}"></script>
</body>
</html>
