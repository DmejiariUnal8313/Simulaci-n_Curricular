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
@endsection

@push('scripts')
<script>
let currentImpactResults = null;

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

function analyzeImpact(curriculumId) {
    // Open modal first
    const modal = new bootstrap.Modal(document.getElementById('impactAnalysisModal'));
    modal.show();
    
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
    
    // Make AJAX request
    fetch(`/convalidation/${curriculumId}/analyze-impact`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
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
        showErrorMessage('Error de conexión al analizar el impacto');
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
                                <strong class="text-success">${results.students_improved}</strong>
                            </div>
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar bg-success" style="width: ${(results.students_improved / results.total_students) * 100}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Estudiantes con progreso igual:</span>
                                <strong class="text-info">${results.students_same}</strong>
                            </div>
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar bg-info" style="width: ${(results.students_same / results.total_students) * 100}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Estudiantes con progreso reducido:</span>
                                <strong class="text-danger">${results.students_worsened}</strong>
                            </div>
                            <div class="progress mb-2" style="height: 20px;">
                                <div class="progress-bar bg-danger" style="width: ${(results.students_worsened / results.total_students) * 100}%"></div>
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
                            Beneficios de la Convalidación
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Convalidaciones directas:</span>
                                <strong>${results.direct_convalidations_count}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Libre elección utilizadas:</span>
                                <strong>${results.free_electives_used} de ${results.free_electives_available}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Créditos de libre elección:</span>
                                <strong>${results.free_electives_credits_used} de ${results.max_free_elective_credits}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Materias excedentes:</span>
                                <strong class="text-danger">${results.excess_free_electives}</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        ${results.excess_free_electives > 0 ? `
        <div class="alert alert-warning">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Materias Excedentes Detectadas
            </h6>
            <p class="mb-0">
                ${results.excess_free_electives} materias de libre elección exceden el límite de 
                ${results.max_free_elective_credits} créditos configurado. Estas materias podrían 
                afectar negativamente a algunos estudiantes al no poder ser convalidadas.
            </p>
        </div>
        ` : ''}
        
        <div class="alert alert-info">
            <h6 class="alert-heading">
                <i class="fas fa-info-circle me-2"></i>
                Configuración Aplicada
            </h6>
            <p class="mb-0">
                <strong>Límite de libre elección:</strong> ${results.max_free_elective_credits} créditos<br>
                <strong>Criterio de prioridad:</strong> ${results.configuration.priority_criteria === 'credits' ? 'Materias con más créditos' : 
                    results.configuration.priority_criteria === 'semester' ? 'Materias de semestres tempranos' : 'Materias que benefician más estudiantes'}
            </p>
        </div>
    `;
}

function generateStudentsTab(results) {
    if (!results.student_details || results.student_details.length === 0) {
        return '<div class="alert alert-warning">No hay estudiantes afectados por esta convalidación.</div>';
    }
    
    let html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Estudiante</th>
                        <th>Progreso Original</th>
                        <th>Progreso con Convalidación</th>
                        <th>Cambio</th>
                        <th>Materias Convalidadas</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    results.student_details.forEach(student => {
        const changeClass = student.progress_change > 0 ? 'text-success' : 
                           student.progress_change < 0 ? 'text-danger' : 'text-muted';
        const changeIcon = student.progress_change > 0 ? 'fas fa-arrow-up' : 
                          student.progress_change < 0 ? 'fas fa-arrow-down' : 'fas fa-minus';
        
        html += `
            <tr>
                <td>
                    <div>
                        <strong>${student.name}</strong>
                        <br><small class="text-muted">ID: ${student.student_id}</small>
                    </div>
                </td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-primary" style="width: ${student.original_progress}%">
                            ${student.original_progress}%
                        </div>
                    </div>
                </td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" style="width: ${student.new_progress}%">
                            ${student.new_progress}%
                        </div>
                    </div>
                </td>
                <td class="${changeClass}">
                    <i class="${changeIcon} me-1"></i>
                    ${student.progress_change > 0 ? '+' : ''}${student.progress_change}%
                </td>
                <td>
                    <span class="badge bg-info">${student.convalidated_count}</span>
                </td>
                <td>
                    ${student.progress_change > 0 ? 
                        '<span class="badge bg-success">Beneficiado</span>' : 
                        student.progress_change < 0 ? 
                        '<span class="badge bg-danger">Perjudicado</span>' : 
                        '<span class="badge bg-secondary">Sin cambio</span>'
                    }
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    return html;
}

function generateSubjectsTab(results) {
    if (!results.subject_impact || results.subject_impact.length === 0) {
        return '<div class="alert alert-warning">No hay información de impacto por materias.</div>';
    }
    
    // Separate subjects by type and selection status
    const directConvalidations = results.subject_impact.filter(s => s.convalidation_type === 'direct');
    const selectedFreeElectives = results.subject_impact.filter(s => s.convalidation_type === 'free_elective' && s.is_selected);
    const excessFreeElectives = results.subject_impact.filter(s => s.convalidation_type === 'free_elective_excess');
    
    let html = `
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Convalidaciones Directas (${directConvalidations.length})
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Materia Externa</th>
                                        <th>Convalida a</th>
                                        <th>Beneficiados</th>
                                        <th>Beneficio</th>
                                    </tr>
                                </thead>
                                <tbody>
    `;
    
    directConvalidations.forEach(subject => {
        html += `
            <tr>
                <td>
                    <div>
                        <strong>${subject.external_subject_code}</strong>
                        <br><small class="text-muted">${subject.external_subject_name}</small>
                    </div>
                </td>
                <td>
                    <span class="badge bg-success">Directa</span>
                    <br><small>${subject.internal_subject_name}</small>
                </td>
                <td>
                    <span class="badge bg-primary">${subject.students_benefited}</span>
                </td>
                <td>
                    <strong class="text-success">+${subject.average_benefit}%</strong>
                </td>
            </tr>
        `;
    });
    
    html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Libre Elección Utilizadas (${selectedFreeElectives.length})
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Materia Externa</th>
                                        <th>Créditos</th>
                                        <th>Beneficiados</th>
                                        <th>Beneficio</th>
                                    </tr>
                                </thead>
                                <tbody>
    `;
    
    selectedFreeElectives.forEach(subject => {
        html += `
            <tr>
                <td>
                    <div>
                        <strong>${subject.external_subject_code}</strong>
                        <br><small class="text-muted">${subject.external_subject_name}</small>
                    </div>
                </td>
                <td>
                    <span class="badge bg-info">${subject.credits} cr.</span>
                </td>
                <td>
                    <span class="badge bg-primary">${subject.students_benefited}</span>
                </td>
                <td>
                    <strong class="text-success">+${subject.average_benefit}%</strong>
                </td>
            </tr>
        `;
    });
    
    html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add excess subjects section if any
    if (excessFreeElectives.length > 0) {
        const totalExcessCredits = excessFreeElectives.reduce((sum, s) => sum + s.credits, 0);
        
        html += `
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Materias Excedentes - No Convalidadas (${excessFreeElectives.length} materias, ${totalExcessCredits} créditos)
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-danger mb-3">
                                <strong>⚠️ Estas materias exceden el límite de ${results.max_free_elective_credits} créditos</strong>
                                <br>Los estudiantes que las tengan aprobadas no recibirán beneficio y podrían verse perjudicados.
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Materia Externa</th>
                                            <th>Créditos</th>
                                            <th>Razón de Exclusión</th>
                                            <th>Impacto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
        `;
        
        excessFreeElectives.forEach(subject => {
            html += `
                <tr>
                    <td>
                        <div>
                            <strong>${subject.external_subject_code}</strong>
                            <br><small class="text-muted">${subject.external_subject_name}</small>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-secondary">${subject.credits} cr.</span>
                    </td>
                    <td>
                        <span class="badge bg-danger">Excede límite</span>
                    </td>
                    <td>
                        <span class="text-danger">
                            <i class="fas fa-times-circle me-1"></i>
                            Sin beneficio
                        </span>
                    </td>
                </tr>
            `;
        });
        
        html += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    return html;
}

function exportImpactResults() {
    if (!currentImpactResults) {
        alert('No hay resultados para exportar');
        return;
    }
    
    const dataStr = JSON.stringify(currentImpactResults, null, 2);
    const blob = new Blob([dataStr], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = `analisis_impacto_${new Date().toISOString().split('T')[0]}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function showErrorMessage(message) {
    document.getElementById('impactAnalysisContent').innerHTML = `
        <div class="alert alert-danger">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error en el Análisis
            </h6>
            <p class="mb-0">${message}</p>
        </div>
    `;
}

// New functions for credit limit configuration
let currentCurriculumId = null;

function showImpactConfigModal(curriculumId) {
    currentCurriculumId = curriculumId;
    
    // Open config modal first
    const modal = new bootstrap.Modal(document.getElementById('impactConfigModal'));
    modal.show();
    
    // Load convalidation preview
    loadConvalidationPreview(curriculumId);
    
    // Update preview when credits limit changes
    document.getElementById('maxFreeElectiveCredits').addEventListener('input', function() {
        updateExcessSubjectsPreview();
    });
    
    // Update preview when priority criteria changes
    document.querySelectorAll('input[name="priorityCriteria"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateExcessSubjectsPreview();
        });
    });
}

function loadConvalidationPreview(curriculumId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    
    fetch(`/convalidation/${curriculumId}/convalidations-summary`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
        }
    })
        .then(response => {
            if (!response.ok) {
                if (response.status === 419) {
                    throw new Error('Token CSRF expirado. Recarga la página.');
                } else {
                    throw new Error(`Error HTTP ${response.status}`);
                }
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayConvalidationPreview(data.convalidations);
                window.currentConvalidations = data.convalidations;
                updateExcessSubjectsPreview();
            } else {
                document.getElementById('convalidationPreview').innerHTML = 
                    `<div class="alert alert-warning">Error: ${data.message || 'Error al cargar convalidaciones'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error completo en preview:', error);
            document.getElementById('convalidationPreview').innerHTML = 
                `<div class="alert alert-danger">Error: ${error.message}</div>`;
        });
}

function displayConvalidationPreview(convalidations) {
    const directConvalidations = convalidations.filter(c => c.type === 'direct');
    const freeElectives = convalidations.filter(c => c.type === 'free_elective');
    
    const html = `
        <div class="mb-3">
            <h6 class="text-success">
                <i class="fas fa-check-circle me-1"></i>
                Convalidaciones Directas (${directConvalidations.length})
            </h6>
            <small class="text-muted">Estas siempre se aplicarán</small>
        </div>
        
        <div class="mb-3">
            <h6 class="text-info">
                <i class="fas fa-graduation-cap me-1"></i>
                Libre Elección (${freeElectives.length} materias, ${freeElectives.reduce((sum, c) => sum + c.credits, 0)} créditos)
            </h6>
            <small class="text-muted">Sujetas al límite configurado</small>
        </div>
        
        <div class="list-group" style="max-height: 200px; overflow-y: auto;">
            ${freeElectives.map(c => `
                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <div>
                        <small><strong>${c.external_subject_code}</strong> - ${c.external_subject_name}</small>
                    </div>
                    <span class="badge bg-info">${c.credits} cr.</span>
                </div>
            `).join('')}
        </div>
    `;
    
    document.getElementById('convalidationPreview').innerHTML = html;
}

function updateExcessSubjectsPreview() {
    if (!window.currentConvalidations) return;
    
    const maxCredits = parseInt(document.getElementById('maxFreeElectiveCredits').value) || 0;
    const priorityCriteria = document.querySelector('input[name="priorityCriteria"]:checked').value;
    
    const freeElectives = window.currentConvalidations.filter(c => c.type === 'free_elective');
    
    // Sort based on priority criteria
    let sortedElectives = [...freeElectives];
    switch (priorityCriteria) {
        case 'credits':
            sortedElectives.sort((a, b) => b.credits - a.credits);
            break;
        case 'semester':
            sortedElectives.sort((a, b) => a.semester - b.semester);
            break;
        case 'students':
            // This would require additional data, for now use credits as fallback
            sortedElectives.sort((a, b) => b.credits - a.credits);
            break;
    }
    
    // Determine which subjects will be accepted and which will be excess
    let acceptedCredits = 0;
    const acceptedSubjects = [];
    const excessSubjects = [];
    
    sortedElectives.forEach(subject => {
        if (acceptedCredits + subject.credits <= maxCredits) {
            acceptedSubjects.push(subject);
            acceptedCredits += subject.credits;
        } else {
            excessSubjects.push(subject);
        }
    });
    
    // Update the UI
    const excessContainer = document.getElementById('excessSubjects');
    
    if (excessSubjects.length === 0) {
        excessContainer.innerHTML = `
            <div class="alert alert-success mb-0">
                <i class="fas fa-check-circle me-2"></i>
                Todas las materias de libre elección caben dentro del límite de ${maxCredits} créditos.
                <br><small>Se convalidarán ${acceptedCredits} créditos de ${freeElectives.reduce((sum, c) => sum + c.credits, 0)} disponibles.</small>
            </div>
        `;
    } else {
        const excessCredits = excessSubjects.reduce((sum, c) => sum + c.credits, 0);
        excessContainer.innerHTML = `
            <div class="alert alert-warning mb-3">
                <strong>⚠️ ${excessSubjects.length} materias exceden el límite</strong>
                <br><small>Se perderán ${excessCredits} créditos que podrían afectar negativamente a los estudiantes.</small>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-success">✅ Se Convalidarán (${acceptedCredits} créditos)</h6>
                    <div class="list-group" style="max-height: 150px; overflow-y: auto;">
                        ${acceptedSubjects.map(s => `
                            <div class="list-group-item py-2">
                                <small><strong>${s.external_subject_code}</strong> - ${s.external_subject_name}</small>
                                <span class="badge bg-success float-end">${s.credits} cr.</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
                <div class="col-md-6">
                    <h6 class="text-danger">❌ Excedentes (${excessCredits} créditos)</h6>
                    <div class="list-group" style="max-height: 150px; overflow-y: auto;">
                        ${excessSubjects.map(s => `
                            <div class="list-group-item py-2">
                                <small><strong>${s.external_subject_code}</strong> - ${s.external_subject_name}</small>
                                <span class="badge bg-danger float-end">${s.credits} cr.</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
    }
}

function runImpactAnalysis() {
    const maxFreeElectiveCredits = parseInt(document.getElementById('maxFreeElectiveCredits').value) || 0;
    const priorityCriteria = document.querySelector('input[name="priorityCriteria"]:checked').value;
    
    // Close config modal
    bootstrap.Modal.getInstance(document.getElementById('impactConfigModal')).hide();
    
    // Open analysis modal
    const modal = new bootstrap.Modal(document.getElementById('impactAnalysisModal'));
    modal.show();
    
    // Reset content
    document.getElementById('impactAnalysisContent').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Analizando...</span>
            </div>
            <p class="mt-3">Analizando impacto en estudiantes...</p>
            <small class="text-muted">
                Simulando migración con límite de ${maxFreeElectiveCredits} créditos de libre elección
            </small>
        </div>
    `;
    
    document.getElementById('exportImpactBtn').style.display = 'none';
    
    // Make AJAX request with new parameters
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        showErrorMessage('Error: No se encontró el token CSRF. Recarga la página.');
        return;
    }

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
            if (response.status === 419) {
                throw new Error('Token CSRF expirado. Recarga la página.');
            } else if (response.status === 500) {
                throw new Error('Error interno del servidor. Revisa los logs.');
            } else {
                throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
            }
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
        console.error('Error completo:', error);
        showErrorMessage('Error: ' + error.message);
    });
}
</script>
@endpush
