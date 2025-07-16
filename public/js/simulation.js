// Simulation View JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const subjectCards = document.querySelectorAll('.subject-card');
    let selectedCard = null;
    let draggedCard = null;
    let simulationChanges = [];
    let originalCurriculum = {};
    
    // Store original curriculum state
    function storeOriginalCurriculum() {
        // First, get the original order from server
        fetch('/simulation/original-order')
            .then(response => response.json())
            .then(originalOrder => {
                // Store the original order for reset
                window.originalOrder = originalOrder;
                
                // Store current state
                subjectCards.forEach(card => {
                    const subjectId = card.dataset.subjectId;
                    const semester = card.closest('.semester-column').dataset.semester;
                    const prerequisites = card.dataset.prerequisites.split(',').filter(p => p.trim());
                    
                    originalCurriculum[subjectId] = {
                        semester: semester,
                        prerequisites: prerequisites,
                        element: card
                    };
                });
                
                console.log('Original curriculum stored with proper order');
            })
            .catch(error => {
                console.error('Error loading original order:', error);
                // Fallback to current state
                subjectCards.forEach(card => {
                    const subjectId = card.dataset.subjectId;
                    const semester = card.closest('.semester-column').dataset.semester;
                    const prerequisites = card.dataset.prerequisites.split(',').filter(p => p.trim());
                    
                    originalCurriculum[subjectId] = {
                        semester: semester,
                        prerequisites: prerequisites,
                        element: card
                    };
                });
            });
    }
    
    // Initialize simulation system
    function initializeSimulation() {
        storeOriginalCurriculum();
        enableDragAndDrop();
        addSimulationControls();
        
        // Debug: Log initialization
        console.log('Simulation initialized');
        console.log('Subject cards found:', subjectCards.length);
        console.log('Semester columns found:', document.querySelectorAll('.semester-column').length);
        console.log('Original curriculum stored:', Object.keys(originalCurriculum).length);
    }
    
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
    
    // Enable drag and drop functionality
    function enableDragAndDrop() {
        subjectCards.forEach(card => {
            card.draggable = true;
            
            card.addEventListener('dragstart', function(e) {
                draggedCard = this;
                this.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', this.outerHTML);
                console.log('Drag started:', this.dataset.subjectId);
            });
            
            card.addEventListener('dragend', function(e) {
                this.classList.remove('dragging');
                console.log('Drag ended:', this.dataset.subjectId);
                draggedCard = null;
            });
        });
        
        // Add drop zones to semester columns
        const semesterColumns = document.querySelectorAll('.semester-column');
        console.log('Found semester columns:', semesterColumns.length);
        
        semesterColumns.forEach(column => {
            column.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                this.classList.add('drag-over');
            });
            
            column.addEventListener('dragleave', function(e) {
                // Only remove drag-over if we're actually leaving the column
                if (!this.contains(e.relatedTarget)) {
                    this.classList.remove('drag-over');
                }
            });
            
            column.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('drag-over');
                
                console.log('Drop event:', {
                    draggedCard: draggedCard?.dataset.subjectId,
                    targetSemester: this.dataset.semester
                });
                
                if (draggedCard) {
                    const newSemester = this.dataset.semester;
                    const subjectId = draggedCard.dataset.subjectId;
                    const oldSemester = draggedCard.closest('.semester-column').dataset.semester;
                    
                    console.log('Moving subject:', {
                        subjectId,
                        from: oldSemester,
                        to: newSemester
                    });
                    
                    if (newSemester !== oldSemester) {
                        // Show modal to optionally edit prerequisites
                        showMoveSubjectModal(draggedCard, this, newSemester, oldSemester);
                    }
                }
            });
        });
    }
    
    // Move subject to new semester
    function moveSubjectToSemester(card, newColumn, newSemester) {
        const subjectList = newColumn.querySelector('.subject-list');
        subjectList.appendChild(card);
        
        // Update visual feedback
        card.classList.add('moved');
        
        // Update semester display
        const semesterBadge = card.querySelector('.semester-badge');
        if (semesterBadge) {
            semesterBadge.textContent = `Semestre ${newSemester}`;
        }
    }
    
    // Record simulation changes
    function recordSimulationChange(subjectId, changeType, newValue, oldValue) {
        // Remove existing change for this subject and type
        simulationChanges = simulationChanges.filter(change => 
            !(change.subject_code === subjectId && change.type === changeType)
        );
        
        // Add new change
        simulationChanges.push({
            subject_code: subjectId,
            type: changeType,
            new_value: newValue,
            old_value: oldValue
        });
        
        updateSimulationStatus();
    }
    
    // Update simulation status display
    function updateSimulationStatus() {
        const statusDiv = document.getElementById('simulation-status');
        if (statusDiv) {
            statusDiv.innerHTML = `
                <div class="alert alert-info">
                    <strong>Simulación activa:</strong> ${simulationChanges.length} cambio(s) temporal(es)
                    <button class="btn btn-sm btn-outline-primary ms-2" onclick="showChangesModal()">
                        Ver cambios
                    </button>
                </div>
            `;
        }
    }
    
    // Update affected percentage display
    function updateAffectedPercentage(percentage) {
        const percentageElement = document.getElementById('affected-percentage');
        if (percentageElement) {
            percentageElement.textContent = percentage + '%';
            
            // Add color coding
            if (percentage > 50) {
                percentageElement.style.color = '#dc3545'; // Red
            } else if (percentage > 25) {
                percentageElement.style.color = '#fd7e14'; // Orange
            } else if (percentage > 0) {
                percentageElement.style.color = '#ffc107'; // Yellow
            } else {
                percentageElement.style.color = '#28a745'; // Green
            }
        }
    }
    
    // Add simulation controls
    function addSimulationControls() {
        const controlsHtml = `
            <div class="simulation-controls mb-3">
                <div class="row">
                    <div class="col-md-6">
                        <div id="simulation-status"></div>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-primary me-2" onclick="analyzeImpact()">
                            <i class="fas fa-chart-line me-1"></i>
                            Analizar impacto
                        </button>
                        <button class="btn btn-warning me-2" onclick="resetSimulation()">
                            <i class="fas fa-undo me-1"></i>
                            Resetear
                        </button>
                        <button class="btn btn-success" onclick="saveSimulation()">
                            <i class="fas fa-save me-1"></i>
                            Guardar simulación
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        const gridContainer = document.querySelector('.curriculum-grid');
        gridContainer.insertAdjacentHTML('beforebegin', controlsHtml);
    }
    
    // Analyze impact of changes
    window.analyzeImpact = function() {
        if (simulationChanges.length === 0) {
            // Reset percentage if no changes
            updateAffectedPercentage(0);
            return;
        }
        
        const loadingModal = showLoadingModal('Analizando impacto...');
        
        fetch('/simulation/analyze-impact', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                changes: simulationChanges
            })
        })
        .then(response => response.json())
        .then(data => {
            hideLoadingModal(loadingModal);
            
            // Update the affected percentage display
            updateAffectedPercentage(data.affected_percentage);
            
            // Show detailed impact analysis
            showImpactAnalysis(data);
        })
        .catch(error => {
            hideLoadingModal(loadingModal);
            console.error('Error:', error);
            alert('Error al analizar el impacto');
        });
    };
    
    // Reset simulation to original state
    window.resetSimulation = function() {
        if (confirm('¿Está seguro de que desea resetear todos los cambios? Esto recargará la página.')) {
            // Simple solution: reload the page to restore original state
            window.location.reload();
        }
    };
    
    // Reset using original order from materias.txt
    function resetToOriginalOrder() {
        // Clear all semester columns first
        const semesterColumns = document.querySelectorAll('.semester-column');
        semesterColumns.forEach(column => {
            const subjectList = column.querySelector('.subject-list');
            subjectList.innerHTML = '';
        });
        
        // Place subjects in original order
        Object.keys(window.originalOrder).forEach(semester => {
            const semesterColumn = document.querySelector(`[data-semester="${semester}"]`);
            const subjectList = semesterColumn.querySelector('.subject-list');
            
            window.originalOrder[semester].forEach(subjectCode => {
                const card = document.querySelector(`[data-subject-id="${subjectCode}"]`);
                if (card) {
                    // Reset visual changes
                    card.classList.remove('moved');
                    
                    // Reset prerequisites if they were changed
                    if (originalCurriculum[subjectCode]) {
                        card.dataset.prerequisites = originalCurriculum[subjectCode].prerequisites.join(',');
                    }
                    
                    // Reset semester badge
                    const semesterBadge = card.querySelector('.semester-badge');
                    if (semesterBadge) {
                        semesterBadge.textContent = `Semestre ${semester}`;
                    }
                    
                    // Add to correct semester
                    subjectList.appendChild(card);
                }
            });
        });
        
        console.log('Reset to original order from materias.txt');
    }
    
    // Reset using stored positions (fallback)
    function resetToStoredPositions() {
        Object.keys(originalCurriculum).forEach(subjectId => {
            const original = originalCurriculum[subjectId];
            const card = document.querySelector(`[data-subject-id="${subjectId}"]`);
            
            if (card) {
                // Find original semester column
                const originalColumn = document.querySelector(`[data-semester="${original.semester}"]`);
                const subjectList = originalColumn.querySelector('.subject-list');
                
                // Move card back
                subjectList.appendChild(card);
                
                // Reset visual changes
                card.classList.remove('moved');
                
                // Reset semester badge
                const semesterBadge = card.querySelector('.semester-badge');
                if (semesterBadge) {
                    semesterBadge.textContent = `Semestre ${original.semester}`;
                }
                
                // Reset prerequisites if they were changed
                card.dataset.prerequisites = original.prerequisites.join(',');
            }
        });
        
        console.log('Reset to stored positions');
    }
    
    // Show impact analysis modal
    function showImpactAnalysis(data) {
        const modalHtml = `
            <div class="modal fade" id="impactModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Análisis de Impacto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="card-title">${data.total_students}</h5>
                                            <p class="card-text">Total estudiantes</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="card-title text-warning">${data.affected_students}</h5>
                                            <p class="card-text">Estudiantes afectados</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="card-title text-danger">${data.students_with_delays}</h5>
                                            <p class="card-text">Con retrasos</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card text-center">
                                        <div class="card-body">
                                            <h5 class="card-title text-info">${data.affected_percentage}%</h5>
                                            <p class="card-text">Porcentaje afectado</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            ${data.details.length > 0 ? `
                                <h6>Detalles de estudiantes afectados:</h6>
                                <div class="accordion" id="studentsAccordion">
                                    ${data.details.map((detail, index) => `
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}">
                                                    ${detail.student_name} (ID: ${detail.student_id}) - Semestre ${detail.current_semester}
                                                    <span class="badge bg-info ms-2">${detail.current_subjects.length} materias cursando</span>
                                                </button>
                                            </h2>
                                            <div id="collapse${index}" class="accordion-collapse collapse" data-bs-parent="#studentsAccordion">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6>Materias actuales:</h6>
                                                            <ul class="list-group list-group-flush">
                                                                ${detail.current_subjects.map(subject => `
                                                                    <li class="list-group-item">${subject}</li>
                                                                `).join('')}
                                                            </ul>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>Problemas identificados:</h6>
                                                            <ul class="list-group list-group-flush">
                                                                ${detail.issues.map(issue => `
                                                                    <li class="list-group-item text-danger">${issue}</li>
                                                                `).join('')}
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : '<p class="text-success">No se detectaron estudiantes afectados por los cambios.</p>'}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('impactModal');
        if (existingModal) {
            const existingModalInstance = bootstrap.Modal.getInstance(existingModal);
            if (existingModalInstance) {
                existingModalInstance.dispose();
            }
            existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Show modal with proper event handling
        const modalElement = document.getElementById('impactModal');
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        
        // Add event listeners to ensure proper cleanup
        modalElement.addEventListener('hidden.bs.modal', function () {
            // Clean up when modal is hidden
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.dispose();
            }
            modalElement.remove();
        });
        
        modal.show();
    }
    
    // Show loading modal
    function showLoadingModal(message) {
        const modalHtml = `
            <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">${message}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('loadingModal'));
        modal.show();
        
        return modal;
    }
    
    // Hide loading modal
    function hideLoadingModal(modal) {
        modal.hide();
        setTimeout(() => {
            document.getElementById('loadingModal').remove();
        }, 300);
    }
    
    // Add click event listeners to subject cards
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
    
    // Show changes modal
    window.showChangesModal = function() {
        const modalHtml = `
            <div class="modal fade" id="changesModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Cambios Temporales</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            ${simulationChanges.length > 0 ? `
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Materia</th>
                                                <th>Tipo de Cambio</th>
                                                <th>Valor Anterior</th>
                                                <th>Valor Nuevo</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${simulationChanges.map((change, index) => `
                                                <tr>
                                                    <td>${change.subject_code}</td>
                                                    <td>${change.type === 'semester' ? 'Semestre' : 'Prerrequisitos'}</td>
                                                    <td>${change.old_value}</td>
                                                    <td>${change.new_value}</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-danger" onclick="removeChange(${index})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            ` : '<p class="text-muted">No hay cambios temporales.</p>'}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('changesModal');
        if (existingModal) {
            const existingModalInstance = bootstrap.Modal.getInstance(existingModal);
            if (existingModalInstance) {
                existingModalInstance.dispose();
            }
            existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Show modal with proper event handling
        const modalElement = document.getElementById('changesModal');
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        
        // Add event listeners to ensure proper cleanup
        modalElement.addEventListener('hidden.bs.modal', function () {
            // Clean up when modal is hidden
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.dispose();
            }
            modalElement.remove();
        });
        
        modal.show();
    };
    
    // Remove individual change
    window.removeChange = function(index) {
        const change = simulationChanges[index];
        
        // Revert visual changes
        if (change.type === 'semester') {
            const card = document.querySelector(`[data-subject-id="${change.subject_code}"]`);
            if (card) {
                // Find original semester column
                const originalColumn = document.querySelector(`[data-semester="${change.old_value}"]`);
                const subjectList = originalColumn.querySelector('.subject-list');
                
                // Move card back
                subjectList.appendChild(card);
                
                // Reset visual changes
                card.classList.remove('moved');
                
                // Reset semester badge
                const semesterBadge = card.querySelector('.semester-badge');
                if (semesterBadge) {
                    semesterBadge.textContent = `Semestre ${change.old_value}`;
                }
            }
        }
        
        // Remove change from array
        simulationChanges.splice(index, 1);
        updateSimulationStatus();
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('changesModal'));
        if (modal) {
            modal.hide();
        }
    };
    
    // Save simulation (placeholder for future implementation)
    window.saveSimulation = function() {
        if (simulationChanges.length === 0) {
            alert('No hay cambios para guardar');
            return;
        }
        
        if (confirm('¿Está seguro de que desea guardar estos cambios permanentemente?')) {
            alert('Funcionalidad de guardado no implementada. Los cambios son temporales.');
        }
    };
    
    // Show modal when moving a subject to allow prerequisite editing
    function showMoveSubjectModal(card, newColumn, newSemester, oldSemester) {
        const subjectId = card.dataset.subjectId;
        const subjectName = card.querySelector('.subject-name').textContent;
        const currentPrereqs = card.dataset.prerequisites.split(',').filter(p => p.trim());
        
        const modalHtml = `
            <div class="modal fade" id="moveSubjectModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-arrows-alt me-2"></i>
                                Mover Materia
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle me-1"></i> Información del Cambio</h6>
                                        <p><strong>Materia:</strong> ${subjectName}</p>
                                        <p><strong>Código:</strong> ${subjectId}</p>
                                        <p><strong>Semestre actual:</strong> ${oldSemester}</p>
                                        <p><strong>Nuevo semestre:</strong> ${newSemester}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="alert alert-warning">
                                        <h6><i class="fas fa-exclamation-triangle me-1"></i> Prerrequisitos Actuales</h6>
                                        <div id="current-prereqs-display">
                                            ${currentPrereqs.length > 0 ? currentPrereqs.map(prereq => `
                                                <span class="badge bg-secondary me-1 mb-1">${prereq}</span>
                                            `).join('') : '<span class="text-muted">Sin prerrequisitos</span>'}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="editPrerequisites">
                                        <label class="form-check-label" for="editPrerequisites">
                                            <i class="fas fa-edit me-1"></i>
                                            Modificar prerrequisitos (opcional)
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3" id="prerequisiteEditor" style="display: none;">
                                <div class="col-12">
                                    <label class="form-label">
                                        <i class="fas fa-list me-1"></i>
                                        Nuevos prerrequisitos:
                                    </label>
                                    <textarea class="form-control" id="new-prerequisites" rows="3" 
                                        placeholder="Ingrese códigos de materias separados por comas">${currentPrereqs.join(', ')}</textarea>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        Ejemplo: 4100400, 4100401, 4100402
                                    </small>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-light">
                                        <h6><i class="fas fa-chart-line me-1"></i> Análisis de Impacto</h6>
                                        <p class="mb-0">Después de confirmar el cambio, se analizará automáticamente el impacto en los estudiantes.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" onclick="confirmMoveSubject('${subjectId}', '${newSemester}', '${oldSemester}')">
                                <i class="fas fa-check me-1"></i>
                                Confirmar Cambio
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('moveSubjectModal');
        if (existingModal) {
            const existingModalInstance = bootstrap.Modal.getInstance(existingModal);
            if (existingModalInstance) {
                existingModalInstance.dispose();
            }
            existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Store references for later use
        window.tempMoveData = {
            card: card,
            newColumn: newColumn,
            newSemester: newSemester,
            oldSemester: oldSemester
        };
        
        // Add event listener for prerequisite editor toggle
        document.getElementById('editPrerequisites').addEventListener('change', function() {
            const editor = document.getElementById('prerequisiteEditor');
            if (this.checked) {
                editor.style.display = 'block';
            } else {
                editor.style.display = 'none';
            }
        });
        
        // Show modal with proper event handling
        const modalElement = document.getElementById('moveSubjectModal');
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        
        // Add event listeners to ensure proper cleanup
        modalElement.addEventListener('hidden.bs.modal', function () {
            // Clean up when modal is hidden
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.dispose();
            }
            modalElement.remove();
        });
        
        modal.show();
    }
    
    // Confirm subject move with optional prerequisite changes
    window.confirmMoveSubject = function(subjectId, newSemester, oldSemester) {
        const moveData = window.tempMoveData;
        const editPrereqs = document.getElementById('editPrerequisites').checked;
        
        // Move the subject
        moveSubjectToSemester(moveData.card, moveData.newColumn, newSemester);
        recordSimulationChange(subjectId, 'semester', newSemester, oldSemester);
        
        // Handle prerequisite changes if enabled
        if (editPrereqs) {
            const newPrereqs = document.getElementById('new-prerequisites').value
                .split(',')
                .map(p => p.trim())
                .filter(p => p);
            
            const oldPrereqs = moveData.card.dataset.prerequisites.split(',').filter(p => p.trim());
            
            // Check if there are prerequisite changes
            const hasPrereqChanges = JSON.stringify(newPrereqs.sort()) !== JSON.stringify(oldPrereqs.sort());
            
            if (hasPrereqChanges) {
                // Update card data
                moveData.card.dataset.prerequisites = newPrereqs.join(',');
                
                // Record prerequisite change
                recordSimulationChange(subjectId, 'prerequisites', newPrereqs.join(','), oldPrereqs.join(','));
                
                // Update visual feedback for prerequisite change
                moveData.card.classList.add('moved');
                
                console.log('Prerequisites changed:', {
                    subject: subjectId,
                    old: oldPrereqs,
                    new: newPrereqs
                });
            }
        }
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('moveSubjectModal'));
        if (modal) {
            modal.hide();
        }
        
        // Clean up temp data
        delete window.tempMoveData;
        
        // Auto-analyze impact after move
        setTimeout(() => analyzeImpact(), 500);
    };
    
    // Right-click context menu for editing prerequisites
    subjectCards.forEach(card => {
        card.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            showPrerequisiteEditor(this);
        });
    });
    
    // Show prerequisite editor
    function showPrerequisiteEditor(card) {
        const subjectId = card.dataset.subjectId;
        const subjectName = card.querySelector('.subject-name').textContent;
        const currentPrereqs = card.dataset.prerequisites.split(',').filter(p => p.trim());
        
        const modalHtml = `
            <div class="modal fade" id="prereqModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Prerrequisitos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <h6>Materia: ${subjectName} (${subjectId})</h6>
                            <div class="mt-3">
                                <label class="form-label">Prerrequisitos actuales:</label>
                                <div id="current-prereqs">
                                    ${currentPrereqs.length > 0 ? currentPrereqs.map(prereq => `
                                        <span class="badge bg-secondary me-1">${prereq}</span>
                                    `).join('') : '<span class="text-muted">Sin prerrequisitos</span>'}
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label">Modificar prerrequisitos:</label>
                                <textarea class="form-control" id="new-prereqs" rows="3" 
                                    placeholder="Ingrese códigos de materias separados por comas">${currentPrereqs.join(', ')}</textarea>
                                <small class="form-text text-muted">
                                    Ejemplo: 4100400, 4100401, 4100402
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn btn-primary" onclick="updatePrerequisites('${subjectId}')">
                                Actualizar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('prereqModal');
        if (existingModal) {
            const existingModalInstance = bootstrap.Modal.getInstance(existingModal);
            if (existingModalInstance) {
                existingModalInstance.dispose();
            }
            existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Show modal with proper event handling
        const modalElement = document.getElementById('prereqModal');
        const modal = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        
        // Add event listeners to ensure proper cleanup
        modalElement.addEventListener('hidden.bs.modal', function () {
            // Clean up when modal is hidden
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
                modalInstance.dispose();
            }
            modalElement.remove();
        });
        
        modal.show();
    }
    
    // Update prerequisites
    window.updatePrerequisites = function(subjectId) {
        const newPrereqs = document.getElementById('new-prereqs').value
            .split(',')
            .map(p => p.trim())
            .filter(p => p);
        
        const card = document.querySelector(`[data-subject-id="${subjectId}"]`);
        const oldPrereqs = card.dataset.prerequisites.split(',').filter(p => p.trim());
        
        // Check if there are changes
        const hasChanges = JSON.stringify(newPrereqs.sort()) !== JSON.stringify(oldPrereqs.sort());
        
        if (hasChanges) {
            // Update card data
            card.dataset.prerequisites = newPrereqs.join(',');
            
            // Record change
            recordSimulationChange(subjectId, 'prerequisites', newPrereqs.join(','), oldPrereqs.join(','));
            
            // Update visual feedback
            card.classList.add('moved');
            
            // If card was selected, update highlights
            if (selectedCard === card) {
                highlightRelated(card);
            }
        }
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('prereqModal'));
        if (modal) {
            modal.hide();
        }
    };
    
    // Initialize simulation when page loads
    initializeSimulation();
});
