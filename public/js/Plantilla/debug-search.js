/**
 * Debug Script for Search Functionality
 * This script helps identify issues with the search functionality
 * Add this script temporarily to your page to debug the search issues
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== SEARCH DEBUG SCRIPT STARTED ===');
    
    // Check if jQuery is available (for DataTables)
    console.log('jQuery available:', typeof jQuery !== 'undefined');
    
    // Check if DataTables is initialized on the table
    const hasDataTable = typeof jQuery !== 'undefined' && 
                         typeof jQuery.fn.DataTable !== 'undefined' && 
                         jQuery('table.table').hasClass('dataTable');
    console.log('Table has DataTable initialized:', hasDataTable);
    
    // Log all scripts loaded on the page
    const scripts = document.querySelectorAll('script');
    console.log('Loaded scripts:', scripts.length);
    scripts.forEach(script => {
        if (script.src) {
            console.log('- Script:', script.src);
        }
    });
    
    // Check search elements
    const searchInput = document.getElementById('tableSearch');
    const clearButton = document.getElementById('clearSearch');
    
    console.log('Search input element:', searchInput);
    console.log('Clear button element:', clearButton);
    
    if (searchInput) {
        // Check if the search input has any event listeners
        const searchEvents = getEventListeners(searchInput);
        console.log('Search input event listeners:', searchEvents);
        
        // Add a test event listener
        searchInput.addEventListener('input', function() {
            console.log('DEBUG: Search input changed to:', this.value);
        });
        console.log('Added test event listener to search input');
    }
    
    if (clearButton) {
        // Add a test event listener
        clearButton.addEventListener('click', function() {
            console.log('DEBUG: Clear button clicked');
            alert('Clear button clicked - debug message');
        });
        console.log('Added test event listener to clear button');
    }
    
    // Helper function to check for event listeners (only works in Chrome DevTools)
    function getEventListeners(element) {
        try {
            // This only works in Chrome DevTools console
            return window.getEventListeners ? window.getEventListeners(element) : 'Cannot detect (not in DevTools)';
        } catch (e) {
            return 'Error detecting event listeners';
        }
    }
    
    console.log('=== SEARCH DEBUG SCRIPT COMPLETED ===');
});