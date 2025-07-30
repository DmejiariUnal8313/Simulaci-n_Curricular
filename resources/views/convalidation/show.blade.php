@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-exchange-alt me-2 text-primary"></i>
                        {{ $externalCurriculum->name }}
                    </h1>
                    <p class="text-muted mb-0">
                        {{ $externalCurriculum->institution }} 
                        <span class="ms-2">•</span>
                        <span class="ms-2">{{ $stats['total_subjects'] }} materias</span>
                        <span class="ms-2">•</span>
                        <span class="ms-2">{{ $stats['completion_percentage'] }}% convalidado</span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('convalidation.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver
                    </a>
                    <button class="btn btn-success" onclick="exportReport()">
                        <i class="fas fa-download me-2"></i>
                        Exportar Reporte
                    </button>
                </div>
            </div>

            <!-- Progress and Stats -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h6>Progreso de Convalidación</h6>
                            <div class="progress mb-2" style="height: 25px;">
                                <div class="progress-bar bg-success" 
                                     role="progressbar" 
                                     style="width: {{ $stats['completion_percentage'] }}%"
                                     id="convalidation-progress">
                                    {{ number_format($stats['completion_percentage'], 1) }}%
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col">
                                    <h5 class="text-success" id="direct-count">{{ $stats['direct_convalidations'] }}</h5>
                                    <small class="text-muted">Convalidaciones Directas</small>
                                </div>
                                <div class="col">
                                    <h5 class="text-info" id="elective-count">{{ $stats['free_electives'] }}</h5>
                                    <small class="text-muted">Libre Elección</small>
                                </div>
                                <div class="col">
                                    <h5 class="text-warning" id="pending-count">{{ $stats['pending_subjects'] }}</h5>
                                    <small class="text-muted">Pendientes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card career-completion-card">
                        <div class="card-body text-center">
                            <h6 class="text-primary mb-3">🎓 Progreso de Carrera</h6>
                            <div class="career-percentage mb-2" id="career-percentage">
                                {{ number_format($stats['career_completion_percentage'], 1) }}%
                            </div>
                            <small class="text-muted mb-3 d-block">
                                <span id="convalidated-credits">{{ number_format($stats['convalidated_credits'], 1) }}</span> de 
                                <span id="total-credits">{{ $stats['total_career_credits'] }}</span> créditos convalidados
                            </small>
                            <div class="progress" style="height: 15px;">
                                <div class="progress-bar bg-primary" 
                                     role="progressbar" 
                                     style="width: {{ $stats['career_completion_percentage'] }}%"
                                     id="career-progress">
                                </div>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle"></i> 
                                Basado en equivalencias directas + libre elección
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h6>Acciones Rápidas</h6>
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="showBulkConvalidation()">
                                    <i class="fas fa-tasks me-2"></i>
                                    Convalidación Masiva
                                </button>
                                <button class="btn btn-outline-info btn-sm" onclick="getSuggestions()">
                                    <i class="fas fa-magic me-2"></i>
                                    Sugerencias Automáticas
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Convalidation Interface -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="semesterTabs" role="tablist">
                        @foreach($subjectsBySemester as $semester => $subjects)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                        id="semester-{{ $semester }}-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#semester-{{ $semester }}" 
                                        type="button" 
                                        role="tab">
                                    Semestre {{ $semester }}
                                    <span class="badge bg-primary ms-2">{{ count($subjects) }}</span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="semesterTabsContent">
                        @foreach($subjectsBySemester as $semester => $subjects)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                                 id="semester-{{ $semester }}" 
                                 role="tabpanel">
                                
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th style="width: 100px;">Código</th>
                                                <th>Materia Externa</th>
                                                <th style="width: 80px;">Créditos</th>
                                                <th>Convalidación</th>
                                                <th style="width: 120px;">Estado</th>
                                                <th style="width: 140px;">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($subjects as $subject)
                                                @php
                                                    $convalidationStatus = $subject->getConvalidationStatus();
                                                    $isConvalidated = $subject->isConvalidated();
                                                @endphp
                                                <tr id="subject-row-{{ $subject->id }}">
                                                    <td>
                                                        <code class="text-primary">{{ $subject->code }}</code>
                                                    </td>
                                                    <td>
                                                        <div>
                                                            <h6 class="mb-1">{{ $subject->name }}</h6>
                                                            @if($subject->description)
                                                                <small class="text-muted">{{ Str::limit($subject->description, 60) }}</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ $subject->credits }}</span>
                                                    </td>
                                                    <td id="convalidation-display-{{ $subject->id }}">
                                                        @if($isConvalidated)
                                                            @if($convalidationStatus['type'] === 'direct')
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-arrow-right text-success me-2"></i>
                                                                    <div>
                                                                        <small class="fw-bold text-success">{{ $convalidationStatus['internal_subject']->name }}</small><br>
                                                                        <small class="text-muted">{{ $convalidationStatus['internal_subject']->code }}</small>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="d-flex align-items-center">
                                                                    <i class="fas fa-star text-info me-2"></i>
                                                                    <span class="fw-bold text-info">Libre Elección</span>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">
                                                                <i class="fas fa-clock me-1"></i>
                                                                Sin convalidar
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($isConvalidated)
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-check me-1"></i>
                                                                Convalidada
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning">
                                                                <i class="fas fa-clock me-1"></i>
                                                                Pendiente
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button type="button" 
                                                                    class="btn btn-outline-primary"
                                                                    onclick="showConvalidationModal({{ $subject->id }})"
                                                                    title="Configurar convalidación">
                                                                <i class="fas fa-cog"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-outline-info"
                                                                    onclick="getSuggestions({{ $subject->id }})"
                                                                    title="Ver sugerencias">
                                                                <i class="fas fa-magic"></i>
                                                            </button>
                                                            @if($isConvalidated)
                                                                <button type="button" 
                                                                        class="btn btn-outline-danger"
                                                                        onclick="removeConvalidation({{ $subject->convalidation->id }})"
                                                                        title="Eliminar convalidación">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Convalidation Modal -->
<div class="modal fade" id="convalidationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configurar Convalidación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="convalidationForm">
                    <input type="hidden" id="external_subject_id" name="external_subject_id">
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6 id="external_subject_info"></h6>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Tipo de Convalidación</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="convalidation_type" id="type_direct" value="direct">
                                <label class="form-check-label" for="type_direct">
                                    <strong>Convalidación Directa</strong><br>
                                    <small class="text-muted">Equivale a una materia específica de nuestra malla curricular</small>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="convalidation_type" id="type_free" value="free_elective">
                                <label class="form-check-label" for="type_free">
                                    <strong>Libre Elección</strong><br>
                                    <small class="text-muted">Se reconoce como créditos electivos, sin equivalencia específica</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3" id="internal_subject_selection" style="display: none;">
                        <div class="col-12">
                            <label for="internal_subject_code" class="form-label">Materia de Nuestra Malla</label>
                            <select class="form-select" id="internal_subject_code" name="internal_subject_code">
                                <option value="">Seleccionar materia...</option>
                                @foreach($internalSubjects as $subject)
                                    <option value="{{ $subject->code }}" data-semester="{{ $subject->semester }}" data-credits="{{ $subject->credits }}">
                                        {{ $subject->name }} ({{ $subject->code }}) - Semestre {{ $subject->semester }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="equivalence_percentage" class="form-label">Porcentaje de Equivalencia</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="equivalence_percentage" name="equivalence_percentage" value="100" min="0" max="100">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="notes" class="form-label">Notas Adicionales</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Observaciones sobre la convalidación..."></textarea>
                        </div>
                    </div>

                    <div class="row" id="suggestions_container" style="display: none;">
                        <div class="col-12">
                            <h6>Sugerencias Automáticas:</h6>
                            <div id="suggestions_list"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveConvalidation()">
                    <i class="fas fa-save me-2"></i>
                    Guardar Convalidación
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentExternalSubjectId = null;

function showConvalidationModal(externalSubjectId) {
    currentExternalSubjectId = externalSubjectId;
    
    // Get subject info and show modal
    const row = document.getElementById(`subject-row-${externalSubjectId}`);
    const subjectCode = row.querySelector('code').textContent;
    const subjectName = row.querySelector('h6').textContent;
    const subjectCredits = row.querySelector('.badge').textContent;
    
    document.getElementById('external_subject_id').value = externalSubjectId;
    document.getElementById('external_subject_info').innerHTML = 
        `<strong>${subjectName}</strong> (${subjectCode}) - ${subjectCredits} créditos`;
    
    // Reset form
    document.getElementById('convalidationForm').reset();
    document.getElementById('external_subject_id').value = externalSubjectId;
    document.getElementById('internal_subject_selection').style.display = 'none';
    
    const modal = new bootstrap.Modal(document.getElementById('convalidationModal'));
    modal.show();
}

// Handle convalidation type change
document.querySelectorAll('input[name="convalidation_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const internalSubjectSelection = document.getElementById('internal_subject_selection');
        if (this.value === 'direct') {
            internalSubjectSelection.style.display = 'block';
        } else {
            internalSubjectSelection.style.display = 'none';
        }
    });
});

function saveConvalidation() {
    const formData = new FormData(document.getElementById('convalidationForm'));
    
    // Store current active semester before making the request
    const currentActiveSemester = getCurrentActiveSemester();
    
    fetch('{{ route("convalidation.store-convalidation") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the convalidation display
            updateConvalidationDisplay(currentExternalSubjectId, data.convalidation);
            
            // Update statistics without page reload
            if (data.stats) {
                updateStatistics(data.stats);
            }
            
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('convalidationModal'));
            modal.hide();
            
            // Restore active semester
            restoreActiveSemester(currentActiveSemester);
            
            // Show success message
            showAlert('success', 'Convalidación guardada exitosamente');
        } else {
            showAlert('danger', data.error || 'Error al guardar la convalidación');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Error de conexión');
    });
}

function getCurrentActiveSemester() {
    // Find which semester tab is currently active
    const activeTab = document.querySelector('.nav-link.active[data-bs-target^="#semester"]');
    if (activeTab) {
        const href = activeTab.getAttribute('data-bs-target');
        return href.replace('#semester-', '');
    }
    return '1'; // Default to semester 1
}

function restoreActiveSemester(semesterNumber) {
    // Restore the active semester tab
    setTimeout(() => {
        const targetTab = document.querySelector(`[data-bs-target="#semester-${semesterNumber}"]`);
        if (targetTab) {
            const tab = new bootstrap.Tab(targetTab);
            tab.show();
        }
    }, 100);
}

function updateStatistics(stats) {
    // Update convalidation progress
    const progressBar = document.getElementById('convalidation-progress');
    if (progressBar) {
        progressBar.style.width = `${stats.completion_percentage}%`;
        progressBar.textContent = `${stats.completion_percentage.toFixed(1)}%`;
    }
    
    // Update counts
    const directCount = document.getElementById('direct-count');
    if (directCount) directCount.textContent = stats.direct_convalidations;
    
    const electiveCount = document.getElementById('elective-count');
    if (electiveCount) electiveCount.textContent = stats.free_electives;
    
    const pendingCount = document.getElementById('pending-count');
    if (pendingCount) pendingCount.textContent = stats.pending_subjects;
    
    // Update career completion stats
    const careerPercentage = document.getElementById('career-percentage');
    if (careerPercentage) careerPercentage.textContent = `${stats.career_completion_percentage.toFixed(1)}%`;
    
    const convalidatedCredits = document.getElementById('convalidated-credits');
    if (convalidatedCredits) convalidatedCredits.textContent = stats.convalidated_credits.toFixed(1);
    
    const careerProgress = document.getElementById('career-progress');
    if (careerProgress) careerProgress.style.width = `${stats.career_completion_percentage}%`;
}

function updateConvalidationDisplay(subjectId, convalidation) {
    const displayElement = document.getElementById(`convalidation-display-${subjectId}`);
    
    if (convalidation.convalidation_type === 'direct') {
        displayElement.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-arrow-right text-success me-2"></i>
                <div>
                    <small class="fw-bold text-success">${convalidation.internal_subject.name}</small><br>
                    <small class="text-muted">${convalidation.internal_subject.code}</small>
                </div>
            </div>
        `;
    } else {
        displayElement.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-star text-info me-2"></i>
                <span class="fw-bold text-info">Libre Elección</span>
            </div>
        `;
    }
}

function getSuggestions(externalSubjectId = null) {
    const targetId = externalSubjectId || currentExternalSubjectId;
    
    fetch(`{{ route('convalidation.suggestions') }}?external_subject_id=${targetId}`)
    .then(response => response.json())
    .then(data => {
        const container = document.getElementById('suggestions_container');
        const list = document.getElementById('suggestions_list');
        
        if (data.suggestions && data.suggestions.length > 0) {
            list.innerHTML = data.suggestions.map(suggestion => `
                <div class="card mb-2">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${suggestion.subject.name}</strong>
                                <small class="text-muted">(${suggestion.subject.code})</small>
                                <div class="mt-1">
                                    <span class="badge bg-info">${suggestion.match_percentage}% similitud</span>
                                    <span class="badge bg-secondary">Semestre ${suggestion.subject.semester}</span>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="selectSuggestion('${suggestion.subject.code}')">
                                Seleccionar
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
            container.style.display = 'block';
        } else {
            list.innerHTML = '<p class="text-muted">No se encontraron sugerencias automáticas</p>';
            container.style.display = 'block';
        }
    });
}

function selectSuggestion(subjectCode) {
    document.getElementById('type_direct').checked = true;
    document.getElementById('internal_subject_code').value = subjectCode;
    document.getElementById('internal_subject_selection').style.display = 'block';
}

function removeConvalidation(convalidationId) {
    if (confirm('¿Está seguro de que desea eliminar esta convalidación?')) {
        fetch(`/convalidation/convalidation/${convalidationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showAlert('danger', 'Error al eliminar la convalidación');
            }
        });
    }
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Insert at top of page
    document.querySelector('.container-fluid').insertAdjacentHTML('afterbegin', alertHtml);
}

function exportReport() {
    window.location.href = '{{ route("convalidation.export", $externalCurriculum) }}';
}
</script>
@endpush
