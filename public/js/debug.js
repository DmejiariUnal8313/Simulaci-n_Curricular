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
    }
    
    const firstColumn = semesterColumns[0];
    if (firstColumn) {
        console.log('First column attributes:');
        console.log('- data-semester:', firstColumn.dataset.semester);
        console.log('- has subject-list:', firstColumn.querySelector('.subject-list') !== null);
    }
    
    // Test 3: Check drag event listeners
    if (firstCard) {
        const events = getEventListeners(firstCard);
        console.log('Event listeners on first card:', Object.keys(events));
    }
    
    if (firstColumn) {
        const events = getEventListeners(firstColumn);
        console.log('Event listeners on first column:', Object.keys(events));
    }
    
    // Test 4: Manually trigger drag start
    if (firstCard) {
        console.log('Manually triggering dragstart on first card...');
        const dragEvent = new DragEvent('dragstart', {
            dataTransfer: new DataTransfer()
        });
        firstCard.dispatchEvent(dragEvent);
        console.log('Drag event triggered');
    }
    
    console.log('=== End of test ===');
}

// Auto-run test when page loads
window.addEventListener('load', function() {
    setTimeout(testDragAndDrop, 1000);
});
