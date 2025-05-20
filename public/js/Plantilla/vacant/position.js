// Declare loadVacantPositions in the global scope
window.loadVacantPositions = function() {
    // Get user permissions
    const userPermissions = JSON.parse(document.querySelector('meta[name="user-permissions"]').content);
    const hasOnlyViewPermission = userPermissions.length === 1 && userPermissions[0] === 'view';

    $.ajax({
        url: '/positions/vacant',
        type: 'GET',
        success: function(response) {
            const tbody = $('#vacantTableBody');
            tbody.empty();
            
            if (response.success && response.positions.length > 0) {
                response.positions.forEach(position => {
                    tbody.append(`
                        <tr>
                            <td>${position.itemNo}</td>
                            <td>${position.office?.name || ''}</td>
                            <td>${position.position}</td>
                            <td>${position.salaryGrade}</td>
                            <td>${position.step || ''}</td>
                            <td>${position.code || ''}</td>
                            <td>${position.type || 'M'}</td>
                            <td>${position.level || ''}</td>
                            <td>${position.status || 'Vacant'}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    ${hasOnlyViewPermission ? '' : `
                                    <button type="button" class="btn btn-sm btn-success assign-position" data-id="${position.id}">
                                        <i class="fas fa-user-plus"></i> Assign
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning edit-position" data-id="${position.id}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-position" 
                                        data-id="${position.id}"
                                        data-item-no="${position.itemNo}"
                                        data-position="${position.position}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                    `}
                                </div>
                            </td>
                        </tr>
                    `);
                });
                $('#noVacantData').hide();
            } else {
                $('#noVacantData').show();
            }
        },
        error: function() {
            $('#noVacantData').show().text('Error loading positions');
        }
    });
};

$(document).ready(function() {
    // Get user permissions
    const userPermissions = JSON.parse(document.querySelector('meta[name="user-permissions"]').content);
    const hasOnlyViewPermission = userPermissions.length === 1 && userPermissions[0] === 'view';

    // Initial load of vacant positions
    window.loadVacantPositions();

    // Hide add new button if user has only view permission
    if (hasOnlyViewPermission) {
        $('#addNewPositionBtn').hide();
    }

    // Handle Add New Position button click
    $('#addNewPositionBtn').on('click', function() {
        // Reset form and clear any previous data
        $('#positionForm')[0].reset();
        $('#position_id').val('');
        $('#positionModalLabel').text('Add New Position');
        
        // Show the modal using Bootstrap's modal method
        const modal = new bootstrap.Modal(document.getElementById('positionModal'));
        modal.show();
    });

    // Handle Assign Position button click
    if (!hasOnlyViewPermission) {
        $(document).on('click', '.assign-position', function() {
            const positionId = $(this).data('id');
            
            $.ajax({
                url: `/positions/${positionId}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        populateAssignForm(response.position);
                        $('#assignPersonnelModal').modal('show');
                    }
                },
                error: function() {
                    alert('Error loading position details');
                }
            });
        });
    }

    // Handle Edit Position button click
    if (!hasOnlyViewPermission) {
        $(document).on('click', '.edit-position', function() {
            const positionId = $(this).data('id');
            
            $.ajax({
                url: `/positions/${positionId}/edit`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const position = response.position;
                        
                        // Update form for editing
                        $('#position_id').val(position.id);
                        $('#itemNo').val(position.itemNo);
                        $('#office_id').val(position.office_id);
                        $('#position').val(position.position);
                        $('#salaryGrade').val(position.salaryGrade);
                        $('#step').val(position.step);
                        $('#code').val(position.code);
                        $('#type').val(position.type);
                        $('#level').val(position.level);
                        $('#status').val(position.status);
                        
                        // Update modal title and show
                        $('#positionModalLabel').text('Edit Position');
                        $('#positionModal').modal('show');
                    }
                },
                error: function() {
                    alert('Error loading position details');
                }
            });
        });
    }

    // Handle Delete Position button click
    if (!hasOnlyViewPermission) {
        $(document).on('click', '.delete-position', function() {
            const id = $(this).data('id');
            const itemNo = $(this).data('item-no');
            const position = $(this).data('position');
            
            // Update delete confirmation modal
            $('#deleteItemNo').text(itemNo);
            $('#deletePosition').text(position);
            $('#positionId').val(id); // Set the hidden ID field
            
            $('#deletePositionModal').modal('show');
        });

        // Handle Delete Confirmation
        $('#confirmDeleteBtn').on('click', function() {
            const $modal = $('#deletePositionModal');
            const $form = $('#deletePositionForm');
            const id = $('#positionId').val();
            
            if (!id) {
                alert('No position selected for deletion');
                return;
            }

            // Show loading state
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Deleting...');

            // Submit the form using AJAX
            $.ajax({
                url: `/positions/${id}`,
                type: 'DELETE',
                data: $form.serialize(),
                success: function(response) {
                    if (response.success) {
                        $modal.modal('hide');
                        window.loadVacantPositions(); // Refresh the positions list
                        alert('Position deleted successfully');
                    } else {
                        alert(response.message || 'Failed to delete position');
                    }
                },
                error: function(xhr) {
                    alert('Error deleting position: ' + (xhr.responseJSON?.message || 'Please try again later'));
                },
                complete: function() {
                    // Reset the button
                    $('#confirmDeleteBtn')
                        .prop('disabled', false)
                        .html('Delete');
                }
            });
        });
    }

    // Initialize real-time validation
    const $form = $('#positionForm');
    
    // Real-time validation for step
    $('#step').on('input', function() {
        const value = $(this).val().trim();
        const $formGroup = $(this).closest('.mb-3');
        
        // Remove existing error messages
        $formGroup.find('.invalid-feedback').remove();
        $(this).removeClass('is-invalid');
        
        if (value) {
            const step = parseInt(value);
            if (isNaN(step) || step < 1 || step > 8) {
                $(this).addClass('is-invalid');
                const errorDiv = $('<div>', {
                    class: 'invalid-feedback',
                    text: 'Step must be between 1 and 8'
                });
                $(this).after(errorDiv);
            }
        }
    });

    // Real-time validation for itemNo uniqueness
    $('#itemNo').on('input', function() {
        const $input = $(this);
        const value = $input.val().trim();
        
        // Clear existing error messages
        $input.next('.invalid-feedback').remove();
        
        if (!value) {
            $input.addClass('is-invalid');
            $input.after('<div class="invalid-feedback">Please fill out this field</div>');
            return;
        }
        
        // Check if itemNo is unique
        $.ajax({
            url: '/positions/check-itemno',
            type: 'POST',
            data: {
                itemNo: value,
                id: $('#position_id').val() || ''
            },
            success: function(response) {
                if (response.exists) {
                    $input.addClass('is-invalid');
                    $input.after('<div class="invalid-feedback">This item number is already in use</div>');
                } else {
                    $input.removeClass('is-invalid');
                    $input.next('.invalid-feedback').remove();
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    $input.addClass('is-invalid');
                    $input.after(`<div class="invalid-feedback">${xhr.responseJSON.message}</div>`);
                } else {
                    $input.removeClass('is-invalid');
                    $input.next('.invalid-feedback').remove();
                }
            }
        });
    });

    // Handle Position Form Submit
    $form.on('submit', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent event bubbling
        
        const $submitBtn = $form.find('button[type="submit"]');
        const isEdit = $('#position_id').val() ? true : false;
        
        // Client-side validation
        let isValid = true;
        const validationRules = {
            'itemNo': {
                message: 'Item Number is required and must be unique',
                validate: (value) => value && value.trim().length <= 50
            },
            'office_id': {
                message: 'Please select a valid office',
                validate: (value) => value && value !== ''
            },
            'position': {
                message: 'Position Title is required and must be less than 255 characters',
                validate: (value) => value && value.trim().length <= 255
            },
            'salaryGrade': {
                message: 'Salary Grade is required and must be a valid grade',
                validate: (value) => value && value.trim().length <= 20
            }
        };
        
        // Check required fields
        Object.entries(validationRules).forEach(([field, config]) => {
            const $input = $form.find(`[name="${field}"]`);
            const value = $input.val();
            
            if (!config.validate(value)) {
                isValid = false;
                $input.addClass('is-invalid');
                if ($input.next('.invalid-feedback').length === 0) {
                    $input.after(`<div class="invalid-feedback">${config.message}</div>`);
                } else {
                    $input.next('.invalid-feedback').text(config.message).show();
                }
            } else {
                $input.removeClass('is-invalid');
                $input.next('.invalid-feedback').hide();
            }
        });
        
        if (!isValid) {
            return;
        }
        
        // Disable submit button to prevent double submission
        $submitBtn.prop('disabled', true);
        
        $.ajax({
            url: isEdit ? `/positions/${$('#position_id').val()}` : '/positions',
            type: isEdit ? 'PUT' : 'POST',
            data: $form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#positionModal').modal('hide');
                    window.loadVacantPositions();
                } else {
                    alert(response.message || 'Failed to save position');
                }
            },
            error: function(xhr) {
                // Show error message using simple alert
                alert(xhr.responseJSON?.message || 'An error occurred while saving the position.');
            },
            complete: function() {
                $submitBtn.prop('disabled', false);
            }
        });
    });

    // Handle search functionality
    $('#positionSearch').on('input', function() {
        const searchText = $(this).val().toLowerCase();
        $('#vacantTableBody tr').each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchText));
        });
    });

    // Handle position filter
    $('input[name="positionFilter"]').on('change', function() {
        const filter = $(this).val();
        $('#vacantTableBody tr').each(function() {
            const status = $(this).find('td:eq(7)').text().toLowerCase(); // Status is 8th column
            if (filter === 'all') {
                $(this).show();
            } else {
                $(this).toggle(status === filter);
            }
        });
    });

    // Reset form when modal is closed
    $('#positionModal').on('hidden.bs.modal', function() {
        const $form = $('#positionForm');
        $form[0].reset();
        $('#position_id').val('');
        $('#positionModalLabel').text('Add New Position');
        $('.is-invalid').removeClass('is-invalid');
    });

    // Add scrollable container for the table
    $('.table-responsive').addClass('table-scroll-container');
});