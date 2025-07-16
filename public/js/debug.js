// Debug script to test drag and drop functionality
// Open browser console and run this to test

function testDragAndDrop() {
    console.log('=== Testing Drag and Drop ===');
    
    // Test 1: Check if elements exist
    const subjectCards = document.querySelectorAll('.subject-card');
    const semesterColumns = document.querySelectorAll('.semester-column');
    
    console.log('Subject cards found:', subjectCards.length);
    console.log('Semester columns found:', semesterColumns.length);
    
    // Test 2: Check if elements have required attributes
    const firstCard = subjectCards[0];
    if (firstCard) {
        console.log('First card attributes:');
        console.log('- draggable:', firstCard.draggable);
        console.log('- data-subject-id:', firstCard.dataset.subjectId);
        console.log('- data-prerequisites:', firstCard.dataset.prerequisites);
        console.log('- data-semester:', firstCard.dataset.semester);
        console.log('- drag handlers:', {
            onDragStart: typeof firstCard.ondragstart === 'function',
            onDragEnd: typeof firstCard.ondragend === 'function'
        });
    }
    
    const firstColumn = semesterColumns[0];
    if (firstColumn) {
        console.log('First column attributes:');
        console.log('- data-semester:', firstColumn.dataset.semester);
        console.log('- has subject-list:', firstColumn.querySelector('.subject-list') !== null);
    }
    
    // Test 3: Check drag event listeners
    if (firstCard) {
        console.log('First card drag properties:');
        console.log('- draggable:', firstCard.draggable);
        console.log('- has ondragstart:', typeof firstCard.ondragstart);
        console.log('- has ondragend:', typeof firstCard.ondragend);
    }
    
    if (firstColumn) {
        console.log('First column drop properties:');
        console.log('- has ondragover:', typeof firstColumn.ondragover);
        console.log('- has ondrop:', typeof firstColumn.ondrop);
    }
    
    return { subjectCards, semesterColumns };
}

// Additional debug functions
function showAllSubjectData() {
    const subjects = document.querySelectorAll('.subject-card');
    const data = Array.from(subjects).map(subject => ({
        id: subject.dataset.subjectId,
        name: subject.querySelector('.subject-name')?.textContent,
        semester: subject.dataset.semester,
        prerequisites: subject.dataset.prerequisites,
        draggable: subject.draggable
    }));
    
    console.table(data);
    return data;
}

function cleanupModals() {
    const modals = document.querySelectorAll('.modal');
    const backdrops = document.querySelectorAll('.modal-backdrop');
    
    modals.forEach(modal => {
        const instance = bootstrap.Modal.getInstance(modal);
        if (instance) {
            instance.dispose();
        }
        modal.remove();
    });
    
    backdrops.forEach(backdrop => {
        backdrop.remove();
    });
    
    // Remove modal-open class from body
    document.body.classList.remove('modal-open');
    document.body.style.paddingRight = '';
    
    console.log('✅ Modals cleaned up');
}

// Make functions available globally
window.testDragAndDrop = testDragAndDrop;
window.showAllSubjectData = showAllSubjectData;
window.cleanupModals = cleanupModals;
