/**
 * jQuery Search Solution
 * This script uses jQuery for the search functionality
 */

$(document).ready(function() {
    console.log('jQuery search solution loaded');
    
    // Get references to the elements
    const $searchInput = $('#tableSearch');
    const $clearButton = $('#clearSearch');
    const $table = $('#personnelTable');
    const $noDataMessage = $('#noDataMessage');
    
    // Log elements for debugging
    console.log('Search input found:', $searchInput.length > 0);
    console.log('Clear button found:', $clearButton.length > 0);
    console.log('Personnel table found:', $table.length > 0);
    
    // Only proceed if all elements exist
    if ($searchInput.length === 0 || $clearButton.length === 0 || $table.length === 0) {
        console.error('Required elements not found');
        return;
    }
    
    // Function to filter the table
    function filterTable() {
        const searchText = $searchInput.val().toLowerCase().trim();
        console.log('Filtering table with:', searchText);
        
        // Get all rows in the table body
        const $rows = $table.find('tbody tr');
        console.log('Found', $rows.length, 'rows to filter');
        
        let visibleCount = 0;
        
        // Loop through each row
        $rows.each(function() {
            const $row = $(this);
            const rowText = $row.text().toLowerCase();
            
            // Check if the row contains the search text
            const isVisible = searchText === '' || rowText.includes(searchText);
            
            // Show or hide the row
            $row.toggle(isVisible);
            
            // Count visible rows
            if (isVisible) visibleCount++;
        });
        
        console.log('Visible rows after filtering:', visibleCount);
        
        // Show or hide the no data message
        if ($noDataMessage.length > 0) {
            $noDataMessage.toggle(visibleCount === 0 && searchText !== '');
        }
        
        // Update clear button visibility
        updateClearButtonVisibility();
    }
    
    // Function to update clear button visibility
    function updateClearButtonVisibility() {
        const hasText = $searchInput.val().trim() !== '';
        $clearButton.toggle(hasText);
        console.log('Clear button visibility:', $clearButton.is(':visible'));
    }
    
    // Add event listener for search input
    $searchInput.on('input', filterTable);
    
    // Add event listener for clear button
    $clearButton.on('click', function(e) {
        console.log('Clear button clicked');
        e.preventDefault();
        
        // Clear the search input
        $searchInput.val('');
        
        // Filter the table
        filterTable();
        
        // Focus back on the search input
        $searchInput.focus();
    });
    
    // Initialize clear button visibility
    updateClearButtonVisibility();
    
    // Initial filter in case there's text in the search input
    filterTable();
    
    console.log('jQuery search functionality initialized');
});