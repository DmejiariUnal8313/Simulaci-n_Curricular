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
                                                            onclick="analyzeImpact({{ $curriculum->id }})"
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
        <div class="row">
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
                        <h6 class="mb-0">Beneficios de la Convalidación</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Total materias convalidadas:</span>
                                <strong>${results.total_convalidated_subjects}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Créditos promedio reducidos:</span>
                                <strong>${results.average_credits_reduced}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Semestres promedio reducidos:</span>
                                <strong>${results.average_semesters_reduced}</strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info mt-3">
            <h6 class="alert-heading">
                <i class="fas fa-info-circle me-2"></i>
                Interpretación de Resultados
            </h6>
            <p class="mb-0">
                Este análisis simula la migración de todos los estudiantes de la malla original 
                a la nueva malla con convalidaciones. Los resultados muestran cómo cambiaría 
                el progreso académico de cada estudiante, considerando las materias que ya han 
                aprobado y las convalidaciones disponibles.
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
    
    let html = `
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Materia Externa</th>
                        <th>Convalidación</th>
                        <th>Estudiantes Beneficiados</th>
                        <th>Tipo de Impacto</th>
                        <th>Beneficio Promedio</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    results.subject_impact.forEach(subject => {
        html += `
            <tr>
                <td>
                    <div>
                        <strong>${subject.external_subject_name}</strong>
                        <br><small class="text-muted">${subject.external_subject_code}</small>
                    </div>
                </td>
                <td>
                    ${subject.convalidation_type === 'direct' ? 
                        `<span class="badge bg-success">Directa</span><br><small>${subject.internal_subject_name}</small>` :
                        '<span class="badge bg-info">Electiva Libre</span>'
                    }
                </td>
                <td>
                    <span class="badge bg-primary">${subject.students_benefited}</span>
                </td>
                <td>
                    ${subject.impact_type === 'high' ? 
                        '<span class="badge bg-success">Alto</span>' :
                        subject.impact_type === 'medium' ?
                        '<span class="badge bg-warning">Medio</span>' :
                        '<span class="badge bg-secondary">Bajo</span>'
                    }
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
    `;
    
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
</script>
@endpush
