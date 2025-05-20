/**
 * Service Record Management
 * Handles loading and searching service records
 */

// Initialize service record functionality when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    initServiceRecordSearch();
    loadServiceRecordData();
});

/**
 * Initialize the search functionality for service records
 */
function initServiceRecordSearch() {
    const searchInput = document.getElementById('searchInput');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const table = document.getElementById('serviceTable');
            
            if (table) {
                const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
                
                for (let i = 0; i < rows.length; i++) {
                    const rowText = rows[i].textContent.toLowerCase();
                    
                    if (rowText.includes(searchValue)) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        });
    }
}

/**
 * Load service record data from the server
 */
function loadServiceRecordData() {
    // Get the table body element
    const tableBody = document.querySelector('#serviceTable tbody');
    
    if (!tableBody) return;
    
    // Show loading indicator
    tableBody.innerHTML = '<tr><td colspan="7" class="text-center">Loading data...</td></tr>';
    
    // Fetch data from the server
    fetch('/api/service-records')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Clear loading indicator
            tableBody.innerHTML = '';
            
            // If no data, show message
            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center">No records found</td></tr>';
                return;
            }
            
            // Populate table with data
            data.forEach(record => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${record.office}</td>
                    <td>${record.position}</td>
                    <td>${record.lastName}</td>
                    <td>${record.firstName}</td>
                    <td>${record.middleName || ''}</td>
                    <td>${record.status}</td>
                    <td>
                        <button class="btn btn-sm btn-primary view-record" data-id="${record.id}">
                            <i class="bi bi-eye"></i> View
                        </button>
                        <button class="btn btn-sm btn-success edit-record" data-id="${record.id}">
                            <i class="bi bi-pencil"></i> Edit
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
            
            // Initialize action buttons
            initActionButtons();
        })
        .catch(error => {
            console.error('Error fetching service records:', error);
            tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading data. Please try again.</td></tr>';
        });
}

/**
 * Initialize action buttons for each record
 */
function initActionButtons() {
    // View record buttons
    document.querySelectorAll('.view-record').forEach(button => {
        button.addEventListener('click', function() {
            const recordId = this.getAttribute('data-id');
            viewServiceRecord(recordId);
        });
    });
    
    // Edit record buttons
    document.querySelectorAll('.edit-record').forEach(button => {
        button.addEventListener('click', function() {
            const recordId = this.getAttribute('data-id');
            editServiceRecord(recordId);
        });
    });
}

/**
 * View a service record
 * @param {string} recordId - The ID of the record to view
 */
function viewServiceRecord(recordId) {
    // Implement view functionality
    console.log('Viewing record:', recordId);
    // You would typically fetch the record details and show them in another modal
}

/**
 * Edit a service record
 * @param {string} recordId - The ID of the record to edit
 */
function editServiceRecord(recordId) {
    // Implement edit functionality
    console.log('Editing record:', recordId);
    // You would typically fetch the record details and populate an edit form
}