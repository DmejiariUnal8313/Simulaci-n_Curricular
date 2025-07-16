<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Simulación Curricular</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .subject-card {
            width: 85px;
            height: 75px;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 3px;
            margin: 3px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .subject-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            border-color: #0d6efd;
        }
        
        .subject-card.available {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-color: #28a745;
        }
        
        .subject-card.taken {
            background: linear-gradient(135deg, #cce5ff 0%, #b3d9ff 100%);
            border-color: #007bff;
        }
        
        .subject-card.blocked {
            background: linear-gradient(135deg, #f8d7da 0%, #f1aeb5 100%);
            border-color: #dc3545;
            opacity: 0.7;
        }
        
        .subject-card.prerequisite {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
            border-color: #ffc107 !important;
            border-width: 3px !important;
            transform: scale(1.05) !important;
        }
        
        .subject-card.unlocks {
            background: linear-gradient(135deg, #e7f3ff 0%, #cce7ff 100%) !important;
            border-color: #0066cc !important;
            border-width: 3px !important;
            transform: scale(1.05) !important;
        }
        
        .subject-card.selected {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%) !important;
            border-color: #17a2b8 !important;
            border-width: 3px !important;
            transform: scale(1.1) !important;
        }
        
        .subject-name {
            font-size: 8px;
            font-weight: 600;
            line-height: 1.1;
            color: #333;
            margin-bottom: 2px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .subject-code {
            font-size: 7px;
            color: #666;
            font-weight: 500;
            margin-top: auto;
        }
        
        .semester-column {
            min-height: 500px;
            background: rgba(255,255,255,0.5);
            border-radius: 10px;
            padding: 10px 3px;
            margin: 0 3px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .semester-title {
            writing-mode: vertical-lr;
            text-orientation: mixed;
            font-size: 14px;
            font-weight: bold;
            color: #495057;
            margin-bottom: 15px;
            text-align: center;
            background: linear-gradient(135deg, #007bff, #0056b3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .subjects-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
        }
        
        .curriculum-grid {
            display: flex;
            justify-content: center;
            gap: 5px;
            overflow-x: auto;
            padding: 15px;
            min-height: 550px;
        }
        
        .stats-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            padding: 15px;
            color: white;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            backdrop-filter: blur(10px);
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 3px;
        }
        
        .stat-label {
            font-size: 0.8rem;
            opacity: 0.9;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .main-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }
    </style>
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
    <script>
        // Add click functionality to subject cards
        document.addEventListener('DOMContentLoaded', function() {
            const subjectCards = document.querySelectorAll('.subject-card');
            let selectedCard = null;
            
            // Function to clear all highlights
            function clearHighlights() {
                subjectCards.forEach(card => {
                    card.classList.remove('prerequisite', 'unlocks', 'selected');
                    // Reset transform to avoid visual issues
                    card.style.transform = '';
                });
            }
            
            // Function to highlight prerequisites and unlocks
            function highlightRelated(card) {
                clearHighlights();
                
                const subjectId = card.dataset.subjectId;
                const prerequisites = card.dataset.prerequisites.split(',').filter(p => p.trim());
                const unlocks = card.dataset.unlocks.split(',').filter(u => u.trim());
                
                // Highlight the selected card
                card.classList.add('selected');
                
                // Highlight prerequisites (yellow)
                prerequisites.forEach(prereqCode => {
                    const prereqCard = document.querySelector(`[data-subject-id="${prereqCode}"]`);
                    if (prereqCard) {
                        prereqCard.classList.add('prerequisite');
                    }
                });
                
                // Highlight unlocks (blue)
                unlocks.forEach(unlockCode => {
                    const unlockCard = document.querySelector(`[data-subject-id="${unlockCode}"]`);
                    if (unlockCard) {
                        unlockCard.classList.add('unlocks');
                    }
                });
                
                console.log(`Selected: ${subjectId}`);
                console.log(`Prerequisites: ${prerequisites.join(', ')}`);
                console.log(`Unlocks: ${unlocks.join(', ')}`);
            }
            
            subjectCards.forEach(card => {
                card.addEventListener('click', function() {
                    const subjectId = this.dataset.subjectId;
                    
                    // If clicking the same card, toggle off
                    if (selectedCard === this) {
                        clearHighlights();
                        selectedCard = null;
                        return;
                    }
                    
                    // Highlight related subjects
                    highlightRelated(this);
                    selectedCard = this;
                });
            });
            
            // Clear highlights when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.subject-card')) {
                    clearHighlights();
                    selectedCard = null;
                }
            });
        });
    </script>
</body>
</html>
