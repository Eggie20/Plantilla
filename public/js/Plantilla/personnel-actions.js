$(document).ready(function() {
    // Format date function
    function formatDateForInput(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return ''; // Invalid date
        return date.toISOString().split('T')[0];
    }

    // Edit Personnel Button Click
    $('.plantilla-edit-btn').on('click', function() {
        // Get data from button attributes
        const data = {
            id: $(this).data('id'),
            office: $(this).data('office'),
            itemNo: $(this).data('item-no'),
            position: $(this).data('position'),
            salaryGrade: $(this).data('salary-grade'),
            authorizedSalary: $(this).data('authorized-salary'),
            actualSalary: $(this).data('actual-salary'),
            step: $(this).data('step'),
            code: $(this).data('code'),
            type: $(this).data('type'),
            level: $(this).data('level'),
            lastName: $(this).data('last-name'),
            firstName: $(this).data('first-name'),
            middleName: $(this).data('middle-name'),
            dob: $(this).data('dob'),
            originalAppointment: $(this).data('original-appointment'),
            lastPromotion: $(this).data('last-promotion'),
            status: $(this).data('status')
        };

        // Debug log
        console.log('Data from button:', data);

        // First show modal without validation
        $('#editPersonnelModal').modal({
            backdrop: 'static',
            keyboard: false
        });

        // Disable form validation temporarily
        $('#editPersonnelForm').find('select, input').prop('required', false);

        // Handle office selection
        try {
            const officeSelect = $('#edit-office');
            const officeValue = data.office;
            
            // First clear any existing value
            officeSelect.val('');
            
            // If the office exists in the officeMapping, use it
            if (window.officeMapping && window.officeMapping[officeValue]) {
                officeSelect.val(officeValue).trigger('change');
            } else {
                // If office doesn't exist, create a new option
                const newOption = new Option(officeValue, officeValue, true, true);
                officeSelect.append(newOption).val(officeValue).trigger('change');
            }
        } catch(e) {
            console.error('Error setting office:', e);
        }
        
        // Populate other fields
        $('#edit-itemNo').val(data.itemNo);
        $('#edit-position').val(data.position);
        $('#edit-salaryGrade').val(data.salaryGrade);
        $('#edit-authorizedSalary').val(data.authorizedSalary);
        $('#edit-actualSalary').val(data.actualSalary);
        $('#edit-step').val(data.step);
        $('#edit-code').val(data.code); // Set the code value
        $('#edit-type').val(data.type);
        $('#edit-level').val(data.level);
        $('#edit-lastName').val(data.lastName);
        $('#edit-firstName').val(data.firstName);
        $('#edit-middleName').val(data.middleName);
        
        // Handle date fields
        if (data.dob) {
            $('#edit-dob').val(formatDateForInput(data.dob));
        }
        if (data.originalAppointment) {
            $('#edit-originalAppointment').val(formatDateForInput(data.originalAppointment));
        }
        if (data.lastPromotion) {
            $('#edit-lastPromotion').val(formatDateForInput(data.lastPromotion));
        }
        
        // Set status
        $('#edit-status').val(data.status);

        // In your edit button click handler
        $('#edit-id').val(data.id); // Make sure this line is present and working

        // Re-enable form validation after all fields are populated
        setTimeout(() => {
            $('#editPersonnelForm').find('select, input[required]').prop('required', true);
            $('#editPersonnelModal').modal('show');
        }, 200);

        // Trigger change event to ensure validation states are updated
        $('#editPersonnelForm select, #editPersonnelForm input').trigger('change');
    });

    // Delete Personnel Button Click
    $('.plantilla-delete-btn').on('click', function() {
        const personnelId = $(this).data('id');
        const $row = $(this).closest('tr');
        
        if (!confirm('Are you sure you want to delete this personnel?')) {
            return;
        }

        $.ajax({
            url: `/delete-personnel/${personnelId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    // Remove the row from the table with animation
                    $row.fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    // Show success message with specific personnel name
                    const personnelName = $row.find('td[data-field="firstName"]').text() + ' ' + 
                                        $row.find('td[data-field="lastName"]').text();
                    showAlert(`Successfully deleted ${personnelName}`, 'success');
                    
                    // Refresh vacant positions table
                    loadVacantPositions();
                } else {
                    // Handle specific error cases
                    const errorMessage = response.message || `Error deleting personnel`;
                    showAlert(errorMessage, 'error');
                }
            },
            error: function(xhr, status, error) {
                // Handle different types of errors
                let errorMessage = 'Error deleting personnel';
                if (xhr.responseJSON) {
                    errorMessage = xhr.responseJSON.message || 
                                 `Error deleting personnel: ${xhr.responseJSON.error}`;
                } else if (xhr.status === 404) {
                    errorMessage = `Personnel not found`;
                } else if (xhr.status === 422) {
                    errorMessage = `Validation error: ${error}`;
                } else {
                    errorMessage = `Error deleting personnel: ${error}`;
                }
                console.error('Delete error:', {
                    status: xhr.status,
                    response: xhr.responseJSON,
                    message: errorMessage,
                    error: error
                });
                showAlert(errorMessage, 'error');
            }
        });
    });

    // Form submission handler
    $('#editPersonnelForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const $submitBtn = $(this).find('button[type="submit"]');
        const $modal = $('#editPersonnelModal');
        
        // Clear any existing error messages
        $('.text-danger').remove();
        
        // Disable submit button and show loading state
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        
        // Handle office selection
        const officeSelect = $('#edit-office');
        const officeCode = officeSelect.val();
        if (officeCode) {
            // Only send the office code
            formData.append('office', officeCode);
        }
        
        // Explicitly add middle name to formData
        const middleName = $('#edit-middleName').val();
        formData.append('middleName', middleName || '');
        
        $.ajax({
            url: '/update-personnel/' + $('#edit-id').val(),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $modal.modal('hide');
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Personnel updated successfully
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    
                    // Add alert to dynamic alert container
                    $('#dynamicAlertContainer').append(alertHtml);
                    
                    // Remove alert after 5 seconds
                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 5000);
                    
                    // Update the specific row in the table
                    const row = $(`tr[data-id="${$('#edit-id').val()}"]`);
                    updateTableRow(response.data);
                    
                    // Update the edit button's data attributes for this row
                    const editBtn = row.find('.plantilla-edit-btn');
                    editBtn.data('office', response.data.office);
                    editBtn.data('item-no', response.data.itemNo);
                    editBtn.data('position', response.data.position);
                    editBtn.data('salary-grade', response.data.salaryGrade);
                    editBtn.data('authorized-salary', response.data.authorizedSalary);
                    editBtn.data('actual-salary', response.data.actualSalary);
                    editBtn.data('step', response.data.step);
                    editBtn.data('code', response.data.code);
                    editBtn.data('type', response.data.type);
                    editBtn.data('level', response.data.level);
                    editBtn.data('last-name', response.data.lastName);
                    editBtn.data('first-name', response.data.firstName);
                    editBtn.data('middle-name', response.data.middleName);
                    editBtn.data('dob', response.data.dob);
                    editBtn.data('original-appointment', response.data.originalAppointment);
                    editBtn.data('last-promotion', response.data.lastPromotion);
                    editBtn.data('status', response.data.status);
                } else {
                    showAlert(response.message || 'Error updating personnel', 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Error updating personnel';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        // Clear any existing alerts
                        $('#dynamicAlertContainer').empty();
                        
                        // Create a new alert for each validation error
                        Object.entries(xhr.responseJSON.errors).forEach(([field, errors]) => {
                            const errorDiv = $('<div>', {
                                class: 'alert alert-danger alert-dismissible fade show mb-2',
                                role: 'alert'
                            });
                            
                            const errorText = $(`<div>${errors.join('<br>')}</div>`);
                            errorDiv.append(errorText);
                            
                            const closeButton = $('<button>', {
                                type: 'button',
                                class: 'btn-close',
                                'data-bs-dismiss': 'alert',
                                'aria-label': 'Close'
                            });
                            
                            errorDiv.append(closeButton);
                            $('#dynamicAlertContainer').append(errorDiv);
                        });
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                        showAlert(errorMessage, 'error');
                    }
                } else {
                    showAlert(errorMessage, 'error');
                }
            },
            complete: function() {
                // Reset submit button
                $submitBtn.prop('disabled', false).html('Update Personnel');
            }
        });
    });

    function updateTableRow(data) {
        // Find the row that needs to be updated
        const row = $(`button[data-id="${data.id}"]`).closest('tr');
        if (row.length) {
            // Get the office name - handle both formatted and unformatted cases
            let officeName = '';
            if (typeof data.office === 'string') {
                // If office is already formatted string
                officeName = data.office;
            } else if (typeof data.office === 'object' && data.office !== null) {
                // If office is an object with name property
                officeName = data.office.name || '';
            } else {
                // If office is just a code, try to get the name from the mapping
                const officeCode = data.office || '';
                officeName = window.officeMapping[officeCode] || officeCode;
            }
            
            // Update all relevant cells in exact order
            row.find('td:eq(0)').text(officeName); // Office
            row.find('td:eq(1)').text(data.itemNo); // Item No.
            row.find('td:eq(2)').text(data.position); // Position
            row.find('td:eq(3)').text(data.salaryGrade); // SG
            row.find('td:eq(4)').text(data.authorizedSalary); // Auth. Salary
            row.find('td:eq(5)').text(data.actualSalary); // Actual Salary
            row.find('td:eq(6)').text(data.step || ''); // Step
            row.find('td:eq(7)').text(data.code || ''); // Code
            row.find('td:eq(8)').text(data.type || ''); // Type
            row.find('td:eq(9)').text(data.level || ''); // Level
            row.find('td:eq(10)').text(data.lastName); // Last Name
            row.find('td:eq(11)').text(data.firstName); // First Name
            row.find('td:eq(12)').text(data.middleName || ''); // Middle Name
            row.find('td:eq(13)').text(formatDate(data.dob)); // Date of Birth
            row.find('td:eq(14)').text(formatDate(data.originalAppointment)); // Original Appointment
            row.find('td:eq(15)').text(formatDate(data.lastPromotion)); // Last Promotion
            row.find('td:eq(16)').text(data.status || ''); // Status
        }
    }

    function showAlert(message, type = 'success') {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Add alert to modal body
        $('#editPersonnelModal .modal-body').prepend(alertHtml);
        
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }

    // Helper function to format dates
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString();
    }

    // Add this function to load vacant positions
    function loadVacantPositions() {
        const tbody = $('#vacantTableBody');
        tbody.empty();
        
        $.ajax({
            url: vacantPositionsUrl,  // Use the route variable defined in Blade
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    if (response.positions.length) {
                        response.positions.forEach(position => {
                            tbody.append(`
                                <tr>
                                    <td>${position.itemNo}</td>
                                    <td>${position.office?.abbreviation || ''}</td>
                                    <td>${position.position}</td>
                                    <td>${position.salaryGrade}</td>
                                    <td>${position.status}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-info view-details" data-id="${position.id}">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-position" data-id="${position.id}">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `);
                        });
                        $('#noVacantData').hide();
                    } else {
                        $('#noVacantData').show();
                    }
                }
            },
            error: function() {
                $('#noVacantData').show().text('Error loading vacant positions');
            }
        });
    }

    // Call loadVacantPositions when the document is ready
    $(document).ready(function() {
        loadVacantPositions();
    });
});
