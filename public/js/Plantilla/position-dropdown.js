document.addEventListener('DOMContentLoaded', function() {
    const positionDropdown = document.getElementById('positionDropdown');
    const officeFilter = document.getElementById('officeFilter');
    const personnelTable = document.getElementById('personnelTable');
    const employeeDataRows = personnelTable ? personnelTable.querySelectorAll('tr.employee-data') : [];
    const noResultsMessage = document.createElement('div');
    noResultsMessage.className = 'no-results text-center text-muted py-3';
    noResultsMessage.style.display = 'none';
    noResultsMessage.textContent = 'No matching personnel found';

    // Add no results message to table container
    if (personnelTable) {
        personnelTable.parentNode.insertBefore(noResultsMessage, personnelTable.nextSibling);
    }

    // Function to filter positions based on selected office
    function filterPositions() {
        const selectedOffice = officeFilter ? officeFilter.value : '';
        const options = positionDropdown.getElementsByTagName('option');
        
        Array.from(options).forEach(option => {
            if (option.value === '') {
                option.style.display = '';
                return;
            }
            
            const officeCode = option.getAttribute('data-office');
            option.style.display = selectedOffice && officeCode !== selectedOffice ? 'none' : '';
        });
    }

    // Function to apply filters to the table
    function applyFilters() {
        const officeFilterValue = officeFilter ? officeFilter.value : '';
        const positionFilterValue = positionDropdown ? positionDropdown.value : '';
        
        employeeDataRows.forEach(row => {
            let shouldShow = true;
            
            // Get the position and office values from the row
            const position = row.getAttribute('data-position') || '';
            const office = row.getAttribute('data-office') || '';
            
            // Apply position filter
            if (positionFilterValue && positionFilterValue !== position) {
                shouldShow = false;
            }
            
            // Apply office filter
            if (officeFilterValue && officeFilterValue !== office) {
                shouldShow = false;
            }
            
            row.style.display = shouldShow ? '' : 'none';
        });
        
        // Update no results message
        const visibleRows = Array.from(employeeDataRows).filter(row => row.style.display !== 'none');
        noResultsMessage.style.display = visibleRows.length === 0 ? 'block' : 'none';
    }

    // Office filter change handler
    if (officeFilter) {
        officeFilter.addEventListener('change', function() {
            const selectedOffice = this.value;
            filterPositions();
            applyFilters();
        });
    }

    // Position filter change handler
    if (positionDropdown) {
        positionDropdown.addEventListener('change', function() {
            applyFilters();
        });
    }
});
