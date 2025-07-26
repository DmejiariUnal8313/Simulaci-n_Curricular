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
@endsection

@push('scripts')
<script>
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
</script>
@endpush
