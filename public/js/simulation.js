// Simulation View JavaScript
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
});
