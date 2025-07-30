@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-exchange-alt me-2 text-primary"></i>
                        Sistema de Convalidaciones
                    </h1>
                    <p class="text-muted mb-0">Gestiona convalidaciones de mallas curriculares externas</p>
                </div>
                <div>
                    <a href="{{ route('convalidation.create') }}" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>
                        Realizar Convalidación
                    </a>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ $stats['total_curriculums'] }}</h5>
                                    <p class="card-text">Mallas Externas</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-list-alt fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ $stats['total_external_subjects'] }}</h5>
                                    <p class="card-text">Materias Externas</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-book fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ $stats['total_convalidations'] }}</h5>
                                    <p class="card-text">Convalidaciones</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 mb-3">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h5 class="card-title">{{ $stats['pending_convalidations'] }}</h5>
                                    <p class="card-text">Materias Pendientes</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x opacity-75"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- External Curriculums List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-university me-2"></i>
                        Mallas Curriculares Externas
                    </h5>
                </div>
                <div class="card-body">
                    @if($externalCurriculums->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Malla Curricular</th>
                                        <th>Institución</th>
                                        <th>Materias</th>
                                        <th>Convalidaciones</th>
                                        <th>Progreso</th>
                                        <th>Fecha</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($externalCurriculums as $curriculum)
                                        @php
                                            $stats = $curriculum->getStats();
                                            $progressPercentage = $stats['completion_percentage'];
                                            $progressClass = $progressPercentage >= 80 ? 'success' : ($progressPercentage >= 50 ? 'warning' : 'danger');
                                        @endphp
                                        <tr>
                                            <td>
                                                <div>
                                                    <h6 class="mb-1">{{ $curriculum->name }}</h6>
                                                    @if($curriculum->description)
                                                        <small class="text-muted">{{ Str::limit($curriculum->description, 80) }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $curriculum->institution ?? 'No especificada' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    {{ $stats['total_subjects'] }} materias
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <span class="badge bg-success">{{ $stats['direct_convalidations'] }} directas</span>
                                                    <span class="badge bg-info">{{ $stats['free_electives'] }} libres</span>
                                                </div>
                                            </td>
                                            <td style="width: 200px;">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                        <div class="progress-bar bg-{{ $progressClass }}" 
                                                             role="progressbar" 
                                                             style="width: {{ $progressPercentage }}%">
                                                            {{ number_format($progressPercentage, 1) }}%
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $stats['convalidated_subjects'] }}/{{ $stats['total_subjects'] }}
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $curriculum->created_at->format('d/m/Y') }}
                                                </small>
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('convalidation.show', $curriculum) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Ver y editar convalidaciones">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-warning"
                                                            onclick="showImpactConfigModal({{ $curriculum->id }})"
                                                            title="Analizar impacto en estudiantes">
                                                        <i class="fas fa-users"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-info"
                                                            onclick="exportReport({{ $curriculum->id }})"
                                                            title="Exportar reporte">
                                                        <i class="fas fa-download"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger"
                                                            onclick="deleteCurriculum({{ $curriculum->id }})"
                                                            title="Eliminar malla">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5>No hay mallas curriculares externas</h5>
                            <p class="text-muted">Comienza cargando una malla curricular externa desde Excel</p>
                            <a href="{{ route('convalidation.create') }}" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>
                                Cargar Primera Malla
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de que desea eliminar esta malla curricular?</p>
                <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Impact Analysis Modal -->
<div class="modal fade" id="impactAnalysisModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-users me-2"></i>
                    Análisis de Impacto en Estudiantes
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="impactAnalysisContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Analizando...</span>
                        </div>
                        <p class="mt-3">Analizando impacto en estudiantes...</p>
                        <small class="text-muted">Esto puede tomar unos momentos</small>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="exportImpactBtn" style="display: none;" onclick="exportImpactResults()">
                    <i class="fas fa-download me-1"></i>
                    Exportar Resultados
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Impact Configuration Modal -->
<div class="modal fade" id="impactConfigModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-cog me-2"></i>
                    Configuración del Análisis de Impacto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading">
                        <i class="fas fa-info-circle me-2"></i>
                        ¿Qué hace este análisis?
                    </h6>
                    <p class="mb-0">
                        Simula la migración de todos los estudiantes actuales de la malla original 
                        a esta nueva malla con convalidaciones, mostrando cómo cambiaría su progreso académico.
                    </p>
                </div>
                
                <form id="impactConfigForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-graduation-cap me-2"></i>
                                        Límites de Convalidación
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="maxFreeElectiveCredits" class="form-label">
                                            <strong>Créditos máximos de libre elección</strong>
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="maxFreeElectiveCredits" 
                                                   value="12" min="0" max="50" step="1">
                                            <span class="input-group-text">créditos</span>
                                        </div>
                                        <div class="form-text">
                                            Solo se convalidarán las materias de libre elección hasta este límite. 
                                            Las materias excedentes afectarán negativamente el progreso.
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <strong>Criterio de prioridad para libre elección</strong>
                                        </label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="priorityCriteria" 
                                                   id="priorityCredits" value="credits" checked>
                                            <label class="form-check-label" for="priorityCredits">
                                                Priorizar materias con más créditos
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="priorityCriteria" 
                                                   id="prioritySemester" value="semester">
                                            <label class="form-check-label" for="prioritySemester">
                                                Priorizar materias de semestres tempranos
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="priorityCriteria" 
                                                   id="priorityStudents" value="students">
                                            <label class="form-check-label" for="priorityStudents">
                                                Priorizar materias que beneficien a más estudiantes
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-chart-line me-2"></i>
                                        Vista Previa de Convalidaciones
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="convalidationPreview">
                                        <div class="text-center text-muted">
                                            <i class="fas fa-spinner fa-spin"></i>
                                            Cargando convalidaciones...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Materias Excedentes (No se convalidarán)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div id="excessSubjects">
                                        <p class="text-muted mb-0">Las materias excedentes se mostrarán aquí una vez configurado el límite.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="runImpactAnalysis()">
                    <i class="fas fa-play me-1"></i>
                    Ejecutar Análisis
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Progress Explanation Modal -->
<div class="modal fade" id="progressExplanationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-chart-line me-2"></i>
                    Explicación del Cambio de Progreso
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 id="student-name-title" class="text-primary"></h6>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="badge bg-secondary fs-6 mb-2" id="original-progress-badge">0%</div>
                            <div class="small text-muted">Progreso Original</div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <i class="fas fa-arrow-right text-muted fs-4"></i>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center">
                            <div class="badge fs-6 mb-2" id="new-progress-badge">0%</div>
                            <div class="small text-muted">Progreso con Nueva Malla</div>
                        </div>
                    </div>
                </div>
                
                <div class="alert" id="change-summary"></div>
                
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Explicación Detallada
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="detailed-explanation" style="white-space: pre-line;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let currentImpactResults = null;
let currentCurriculumId = null;

function deleteCurriculum(curriculumId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/convalidation/${curriculumId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function exportReport(curriculumId) {
    // Implement export functionality
    window.location.href = `/convalidation/${curriculumId}/export`;
}

document.getElementById('impactConfigForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const curriculumId = formData.get('curriculum_id');
    
    // Make AJAX request to save configuration
    fetch(`/convalidation/${curriculumId}/set-impact-config`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('impactConfigModal'));
            modal.hide();
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Configuración Guardada',
                text: 'La configuración del análisis de impacto se ha guardado correctamente.',
                confirmButtonText: 'Aceptar'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al guardar la configuración',
                confirmButtonText: 'Aceptar'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error de Conexión',
            text: 'No se pudo conectar al servidor. Inténtelo de nuevo más tarde.',
            confirmButtonText: 'Aceptar'
        });
    });
});

function showImpactConfigModal(curriculumId) {
    currentCurriculumId = curriculumId;
    const modal = new bootstrap.Modal(document.getElementById('impactConfigModal'));
    modal.show();
}

function runImpactAnalysis() {
    if (!currentCurriculumId) {
        showErrorMessage('Error: No se ha seleccionado una malla curricular');
        return;
    }

    // Cerrar el modal de configuración
    const configModal = bootstrap.Modal.getInstance(document.getElementById('impactConfigModal'));
    configModal.hide();

    // Abrir el modal de análisis
    const analysisModal = new bootstrap.Modal(document.getElementById('impactAnalysisModal'));
    analysisModal.show();

    // Obtener valores del formulario
    const maxFreeElectiveCredits = document.getElementById('maxFreeElectiveCredits').value || 12;
    const priorityCriteria = document.querySelector('input[name="priority_criteria"]:checked')?.value || 'credits';

    // Reset content
    document.getElementById('impactAnalysisContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Analizando...</span>
            </div>
            <p class="mt-3">Analizando impacto en estudiantes...</p>
            <small class="text-muted">Simulando migración de estudiantes a la nueva malla curricular</small>
        </div>
    `;
    
    document.getElementById('exportImpactBtn').style.display = 'none';

    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showErrorMessage('Error: Token CSRF no encontrado');
        return;
    }

    // Realizar la petición AJAX
    fetch(`/convalidation/${currentCurriculumId}/analyze-impact`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken.getAttribute('content')
        },
        body: JSON.stringify({
            max_free_elective_credits: maxFreeElectiveCredits,
            priority_criteria: priorityCriteria
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            currentImpactResults = data.results;
            displayImpactResults(data.results);
            document.getElementById('exportImpactBtn').style.display = 'inline-block';
        } else {
            showErrorMessage(data.message || 'Error al analizar el impacto');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('Error: ' + error.message);
    });
}

function displayImpactResults(results) {
    const content = `
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4>${results.total_students}</h4>
                        <small>Total Estudiantes</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h4>${results.affected_students}</h4>
                        <small>Estudiantes Afectados</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h4>${results.affected_percentage}%</h4>
                        <small>Porcentaje Afectado</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4>${results.average_progress_change > 0 ? '+' : ''}${results.average_progress_change}%</h4>
                        <small>Cambio Promedio</small>
                    </div>
                </div>
            </div>
        </div>

        <ul class="nav nav-tabs" id="impactTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary" type="button" role="tab">
                    <i class="fas fa-chart-pie me-1"></i>
                    Resumen
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students" type="button" role="tab">
                    <i class="fas fa-users me-1"></i>
                    Estudiantes Afectados
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="subjects-tab" data-bs-toggle="tab" data-bs-target="#subjects" type="button" role="tab">
                    <i class="fas fa-book me-1"></i>
                    Impacto por Materias
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="impactTabContent">
            <div class="tab-pane fade show active" id="summary" role="tabpanel">
                ${generateSummaryTab(results)}
            </div>
            <div class="tab-pane fade" id="students" role="tabpanel">
                ${generateStudentsTab(results)}
            </div>
            <div class="tab-pane fade" id="subjects" role="tabpanel">
                ${generateSubjectsTab(results)}
            </div>
        </div>
    `;
    
    document.getElementById('impactAnalysisContent').innerHTML = content;
}

function generateSummaryTab(results) {
    return `
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Distribución de Impacto</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Estudiantes con progreso mejorado:</span>
                                <span class="badge bg-success">${results.students_with_improved_progress || 0}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: ${((results.students_with_improved_progress || 0) / results.total_students * 100)}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Estudiantes con progreso reducido:</span>
                                <span class="badge bg-danger">${results.students_with_reduced_progress || 0}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: ${((results.students_with_reduced_progress || 0) / results.total_students * 100)}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Estudiantes sin cambio:</span>
                                <span class="badge bg-secondary">${results.students_with_no_change || 0}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-secondary" style="width: ${((results.students_with_no_change || 0) / results.total_students * 100)}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Estadísticas Generales</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <h5 class="text-primary">${results.total_convalidated_credits || 0}</h5>
                                <small>Créditos Convalidados</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-warning">${results.total_new_credits || 0}</h5>
                                <small>Créditos Nuevos</small>
                            </div>
                            <div class="col-4">
                                <h5 class="text-danger">${results.total_lost_credits || 0}</h5>
                                <small>Créditos Perdidos</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function generateStudentsTab(results) {
    if (!results.student_details || results.student_details.length === 0) {
        return '<div class="alert alert-info">No hay estudiantes afectados por el cambio de malla.</div>';
    }

    let html = `
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Progreso Original</th>
                        <th>Progreso Nuevo</th>
                        <th>Cambio</th>
                        <th>Convalidadas</th>
                        <th>Nuevas</th>
                        <th>Perdidas</th>
                        <th>Explicación</th>
                    </tr>
                </thead>
                <tbody>
    `;

    results.student_details.forEach(student => {
        const changeClass = student.progress_change > 0.1 ? 'text-success' : 
                          student.progress_change < -0.1 ? 'text-danger' : 'text-muted';
        const changeIcon = student.progress_change > 0.1 ? 'fa-arrow-up' : 
                         student.progress_change < -0.1 ? 'fa-arrow-down' : 'fa-minus';
        
        const progressBarClass = student.progress_change > 0.1 ? 'bg-success' : 
                               student.progress_change < -0.1 ? 'bg-danger' : 'bg-warning';
        
        html += `
            <tr>
                <td>
                    <strong>${escapeHtml(student.name || 'Sin nombre')}</strong><br>
                    <small class="text-muted">${escapeHtml(student.email || 'Sin email')}</small>
                </td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-secondary" style="width: ${student.original_progress || 0}%">
                            ${(student.original_progress || 0).toFixed(1)}%
                        </div>
                    </div>
                    <small class="text-muted">${student.original_subjects_passed || 0} materias</small>
                </td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar ${progressBarClass}" style="width: ${student.new_progress || 0}%">
                            ${(student.new_progress || 0).toFixed(1)}%
                        </div>
                    </div>
                    <small class="text-muted">${student.convalidated_subjects_count || 0} convalidadas</small>
                </td>
                <td class="${changeClass}">
                    <i class="fas ${changeIcon}"></i>
                    ${student.progress_change > 0 ? '+' : ''}${(student.progress_change || 0).toFixed(1)}%
                </td>
                <td>
                    <span class="badge bg-success">${student.convalidated_subjects_count || 0}</span>
                </td>
                <td>
                    <span class="badge bg-warning">${student.new_subjects_count || 0}</span>
                </td>
                <td>
                    <span class="badge bg-danger">${student.lost_credits_count || 0}</span>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-info show-explanation-btn" 
                            data-student-name="${escapeHtml(student.name || 'Sin nombre')}"
                            data-explanation="${encodeURIComponent(student.progress_explanation || 'Sin explicación disponible')}"
                            data-original-progress="${student.original_progress || 0}"
                            data-new-progress="${student.new_progress || 0}"
                            data-progress-change="${student.progress_change || 0}"
                            data-convalidated-count="${student.convalidated_subjects_count || 0}"
                            data-new-subjects-count="${student.new_subjects_count || 0}"
                            data-lost-credits-count="${student.lost_credits_count || 0}"
                            title="Ver explicación detallada del cambio de progreso">
                        <i class="fas fa-info-circle"></i> Detalles
                    </button>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle me-2"></i>Interpretación de los Datos</h6>
                <div class="row">
                    <div class="col-md-4">
                        <strong>Convalidadas:</strong> Materias que el estudiante cursó y pueden ser reconocidas en la nueva malla.
                    </div>
                    <div class="col-md-4">
                        <strong>Nuevas:</strong> Materias adicionales que debe cursar en la nueva malla.
                    </div>
                    <div class="col-md-4">
                        <strong>Perdidas:</strong> Materias que cursó pero no tienen equivalencia en la nueva malla.
                    </div>
                </div>
            </div>
        </div>
    `;

    return html;
}

function generateSubjectsTab(results) {
    return `
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Materias Más Convalidadas</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">Funcionalidad en desarrollo</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Materias Problemáticas</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">Funcionalidad en desarrollo</div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function exportImpactResults() {
    if (!currentImpactResults) {
        showErrorMessage('No hay resultados para exportar');
        return;
    }

    const csvContent = generateCSVContent(currentImpactResults);
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `impacto_convalidacion_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

function generateCSVContent(results) {
    let csv = 'Estudiante,Email,Progreso Original,Progreso Nuevo,Cambio,Materias Convalidadas\n';
    
    if (results.student_details) {
        results.student_details.forEach(student => {
            csv += `"${student.name}","${student.email}",${student.original_progress}%,${student.new_progress}%,${student.progress_change}%,${student.convalidated_subjects}\n`;
        });
    }
    
    return csv;
}

function showErrorMessage(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Event delegation para los botones de explicación
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('show-explanation-btn') || 
        e.target.closest('.show-explanation-btn')) {
        
        const button = e.target.classList.contains('show-explanation-btn') ? 
                      e.target : e.target.closest('.show-explanation-btn');
        
        const studentName = button.getAttribute('data-student-name') || 'Estudiante';
        const explanation = decodeURIComponent(button.getAttribute('data-explanation') || '');
        const originalProgress = parseFloat(button.getAttribute('data-original-progress')) || 0;
        const newProgress = parseFloat(button.getAttribute('data-new-progress')) || 0;
        const progressChange = parseFloat(button.getAttribute('data-progress-change')) || 0;
        const convalidatedCount = parseInt(button.getAttribute('data-convalidated-count')) || 0;
        const newSubjectsCount = parseInt(button.getAttribute('data-new-subjects-count')) || 0;
        const lostCreditsCount = parseInt(button.getAttribute('data-lost-credits-count')) || 0;
        
        // Mostrar el modal con todos los datos
        showProgressExplanationDetailed(
            studentName, 
            explanation, 
            originalProgress, 
            newProgress, 
            progressChange, 
            convalidatedCount, 
            newSubjectsCount, 
            lostCreditsCount
        );
    }
});

function showProgressExplanationDetailed(studentName, explanation, originalProgress, newProgress, progressChange, convalidatedCount, newSubjectsCount, lostCreditsCount) {
    // Set student name
    document.getElementById('student-name-title').textContent = `Estudiante: ${studentName}`;
    
    // Set progress badges
    document.getElementById('original-progress-badge').textContent = `${originalProgress}%`;
    
    const newProgressBadge = document.getElementById('new-progress-badge');
    newProgressBadge.textContent = `${newProgress}%`;
    
    // Set badge color based on change
    if (progressChange > 0.1) {
        newProgressBadge.className = 'badge bg-success fs-6 mb-2';
    } else if (progressChange < -0.1) {
        newProgressBadge.className = 'badge bg-danger fs-6 mb-2';
    } else {
        newProgressBadge.className = 'badge bg-warning fs-6 mb-2';
    }
    
    // Set change summary
    const changeSummary = document.getElementById('change-summary');
    let summaryHTML = '';
    let alertClass = '';
    
    if (progressChange > 0.1) {
        alertClass = 'alert-success';
        summaryHTML = `
            <h6><i class="fas fa-arrow-up me-2"></i>Progreso Mejorado</h6>
            <p class="mb-2"><strong>Aumento de ${Math.abs(progressChange).toFixed(1)} puntos porcentuales</strong></p>
            <div class="row text-center">
                <div class="col-4">
                    <small class="text-muted">Convalidadas</small><br>
                    <strong class="text-success">${convalidatedCount}</strong>
                </div>
                <div class="col-4">
                    <small class="text-muted">Nuevas</small><br>
                    <strong class="text-warning">${newSubjectsCount}</strong>
                </div>
                <div class="col-4">
                    <small class="text-muted">Perdidas</small><br>
                    <strong class="text-danger">${lostCreditsCount}</strong>
                </div>
            </div>
        `;
    } else if (progressChange < -0.1) {
        alertClass = 'alert-danger';
        summaryHTML = `
            <h6><i class="fas fa-arrow-down me-2"></i>Progreso Reducido</h6>
            <p class="mb-2"><strong>Disminución de ${Math.abs(progressChange).toFixed(1)} puntos porcentuales</strong></p>
            <div class="row text-center">
                <div class="col-4">
                    <small class="text-muted">Convalidadas</small><br>
                    <strong class="text-success">${convalidatedCount}</strong>
                </div>
                <div class="col-4">
                    <small class="text-muted">Nuevas</small><br>
                    <strong class="text-warning">${newSubjectsCount}</strong>
                </div>
                <div class="col-4">
                    <small class="text-muted">Perdidas</small><br>
                    <strong class="text-danger">${lostCreditsCount}</strong>
                </div>
            </div>
        `;
    } else {
        alertClass = 'alert-info';
        summaryHTML = `
            <h6><i class="fas fa-minus me-2"></i>Progreso Sin Cambios Significativos</h6>
            <p class="mb-0">El cambio es menor a 0.1 puntos porcentuales</p>
        `;
    }
    
    changeSummary.className = `alert ${alertClass}`;
    changeSummary.innerHTML = summaryHTML;
    
    // Set detailed explanation
    document.getElementById('detailed-explanation').innerHTML = explanation || 'No hay explicación detallada disponible.';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('progressExplanationModal'));
    modal.show();
}

// Inicialización cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema de convalidación cargado correctamente');
});
</script>
@endpush
