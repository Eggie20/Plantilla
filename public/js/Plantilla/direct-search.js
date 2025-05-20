/**
 * Direct Table Search Script
 * This is a simplified script that directly targets the personnel table
 */

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('Direct search script loaded');
    
    // Get the search input, clear button, and table
    const searchInput = document.getElementById('tableSearch');
    const clearButton = document.getElementById('clearSearch');
    const table = document.getElementById('personnelTable');
    
    // Log elements for debugging
    console.log('Search input found:', !!searchInput);
    console.log('Clear button found:', !!clearButton);
    console.log('Personnel table found:', !!table);
    
    // Only proceed if all elements exist
    if (!searchInput || !clearButton || !table) {
        console.error('Required elements not found');
        return;
    }
    
    // Function to filter the table
    function filterTable() {
        const searchText = searchInput.value.toLowerCase().trim();
        console.log('Filtering table with:', searchText);
        
        // Get all rows in the table body
        const rows = table.querySelectorAll('tbody tr');
        console.log('Found', rows.length, 'rows to filter');
        
        let visibleCount = 0;
        
        // Loop through each row
        rows.forEach(row => {
            // Get the text content of the row
            const rowText = row.textContent.toLowerCase();
            
            // Check if the row contains the search text
            const isVisible = searchText === '' || rowText.includes(searchText);
            
            // Show or hide the row
            row.style.display = isVisible ? '' : 'none';
            
            // Count visible rows
            if (isVisible) visibleCount++;
        });
        
        console.log('Visible rows after filtering:', visibleCount);
        
        // Show or hide the no data message
        const noDataMessage = document.getElementById('noDataMessage');
        if (noDataMessage) {
            noDataMessage.style.display = (visibleCount === 0 && searchText !== '') ? 'block' : 'none';
        }
        
        // Update clear button visibility
        updateClearButtonVisibility();
    }
    
    // Function to update clear button visibility
    function updateClearButtonVisibility() {
        const hasText = searchInput.value.trim() !== '';
        clearButton.style.display = hasText ? 'block' : 'none';
        console.log('Clear button visibility:', clearButton.style.display);
    }
    
    // Add event listener for search input
    searchInput.addEventListener('input', filterTable);
    
    // Add event listener for clear button
    clearButton.addEventListener('click', function(e) {
        console.log('Clear button clicked');
        e.preventDefault();
        
        // Clear the search input
        searchInput.value = '';
        
        // Filter the table
        filterTable();
        
        // Focus back on the search input
        searchInput.focus();
    });
    
    // Initialize clear button visibility
    updateClearButtonVisibility();
    
    // Initial filter in case there's text in the search input
    filterTable();
    
    console.log('Search functionality initialized');
});