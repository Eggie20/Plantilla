$(document).ready(function() {
    // Get user permissions at the start
    const userPermissions = JSON.parse(document.querySelector('meta[name="user-permissions"]').content);
    const hasOnlyViewPermission = userPermissions.length === 1 && userPermissions[0] === 'view';

    // Setup CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Global variables
    let isSubmitting = false;

    // Function to show error messages
    function showError(message) {
        // Clear any existing error messages
        $('.error-message').remove();
        
        // Create error message element
        const errorDiv = $('<div>', {
            class: 'alert alert-danger error-message',
            role: 'alert',
            text: message
        });
        
        // Add error message to the form
        $('#addOfficeForm, #editOfficeForm').prepend(errorDiv);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            errorDiv.fadeOut(300, () => errorDiv.remove());
        }, 5000);
    }

    // Function to handle AJAX errors
    function handleAjaxError(xhr, error, returnMessage) {
        if (xhr.responseJSON) {
            // If we have validation errors
            if (xhr.responseJSON.errors) {
                // Display validation errors on the form
                showFormErrors(xhr.responseJSON.errors, 'addOfficeForm');
            } else if (xhr.responseJSON.message) {
                if (returnMessage) {
                    return xhr.responseJSON.message;
                } else {
                    showError(xhr.responseJSON.message);
                }
            } else {
                if (returnMessage) {
                    return 'An error occurred. Please try again.';
                } else {
                    showError('An error occurred. Please try again.');
                }
            }
        } else {
            if (returnMessage) {
                return 'An error occurred. Please try again.';
            } else {
                showError('An error occurred. Please try again.');
            }
        }
        console.error('AJAX Error:', error);
    }

    // Function to show form validation errors
    function showFormErrors(errors, formId) {
        // Clear any existing error messages
        $(`#${formId} .error-message`).remove();
        $(`#${formId} .form-control, ${formId} .form-select`).removeClass('is-invalid');
        
        Object.entries(errors).forEach(([field, messages]) => {
            // Get the field element
            const $field = $(`#${formId} input[name="${field}"]`);
            if (!$field.length) {
                // Try with select element
                $field = $(`#${formId} select[name="${field}"]`);
            }
            
            if ($field.length) {
                const $formGroup = $field.closest('.mb-3');
                if ($formGroup.length) {
                    // Add error class to input
                    $field.addClass('is-invalid');
                    
                    // Get the appropriate error message based on the validation rules
                    let errorMessage = '';
                    const value = $field.val(); // Get the field value
                    
                    if (typeof messages === 'object' && messages !== null) {
                        // Check for unique validation error first
                        if (messages.unique) {
                            errorMessage = messages.unique;
                        } else if (!value) {
                            errorMessage = messages.required || 'This field is required';
                        } else if (field === 'code' && (value.length < 3 || value.length > 7)) {
                            errorMessage = messages.between || 'Must be between 3 and 7 characters';
                        } else if (field === 'name' && (value.length < 1 || value.length > 60)) {
                            errorMessage = messages.between || 'Must be between 1 and 60 characters';
                        } else if (field === 'abbreviation' && (value.length < 3 || value.length > 7)) {
                            errorMessage = messages.between || 'Must be between 3 and 7 characters';
                        } else if (field === 'code' && !/^[a-z]+$/.test(value)) {
                            errorMessage = messages.regex || 'Must contain only lowercase letters';
                        } else if (field === 'name' && !/^[A-Z\s\.,\/\']+/.test(value)) {
                            errorMessage = messages.regex || 'Must contain only uppercase letters and special characters';
                        } else if (field === 'abbreviation' && !/^[A-Z]+$/.test(value)) {
                            errorMessage = messages.regex || 'Must contain only uppercase letters';
                        } else {
                            errorMessage = Object.values(messages)[0]; // Fallback to first message
                        }
                    } else {
                        errorMessage = messages;
                    }
                    
                    // Add error message
                    const errorDiv = $('<div>', {
                        class: 'invalid-feedback',
                        text: errorMessage
                    });
                    
                    // Add error message to the form group
                    $formGroup.find('.invalid-feedback').remove();
                    $field.after(errorDiv);
                }
            }
        });
    }

    // Define loadOffices function
    window.loadOffices = function() {
        $.ajax({
            url: '/office',
            type: 'GET',
            success: function(response) {
                const tbody = $('#officesTable tbody');
                tbody.empty();
                
                if (response.success && response.offices.length > 0) {
                    $('#noOfficesMessage').hide();
                    response.offices.forEach(office => {
                        tbody.append(`
                            <tr>
                                <td>${office.code}</td>
                                <td>${office.name}</td>
                                <td>${office.abbreviation}</td>
                                <td>${office.parentOffice ? office.parentOffice.name : 'None'}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        ${hasOnlyViewPermission ? '' : `
                                        <button type="button" class="btn btn-sm btn-warning edit-office" 
                                            data-id="${office.id}"
                                            data-code="${office.code}"
                                            data-name="${office.name}"
                                            data-abbreviation="${office.abbreviation}"
                                            data-parentOfficeId="${office.parentOffice ? office.parentOffice.id : ''}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-office" 
                                            data-id="${office.id}"
                                            data-name="${office.name}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        `}
                                    </div>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#noOfficesMessage').show().text('No offices found');
                }
            },
            error: function(xhr, status, error) {
                handleAjaxError(xhr, error);
            }
        });
    };

    // Function to refresh parent office dropdown
    window.refreshParentOfficeDropdown = function() {
        return $.ajax({
            url: '/office',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const offices = response.offices;
                    const populateDropdown = ($select) => {
                        $select.empty();
                        $select.append($('<option>', {
                            value: '',
                            text: 'None (Main Office)'
                        }));
                        offices.forEach(office => {
                            if (!office.isSubOffice) {
                                $select.append($('<option>', {
                                    value: office.id,
                                    text: `${office.name} (${office.abbreviation})`
                                }));
                            }
                        });
                    };
                    populateDropdown($('#parentOffice'));
                    populateDropdown($('#edit_office_parent'));
                }
            },
            error: function(xhr, status, error) {
                handleAjaxError(xhr, error);
            }
        });
    };

    // Function to validate form
    function validateForm(formData) {
        const errors = {};
        const officeId = $('#edit_office_id').val(); // Get the office ID from the form
        
        // Validate Office Code
        if (!formData.code) {
            errors.code = 'Office code is required';
        } else if (formData.code.length < 3 || formData.code.length > 7) {
            errors.code = 'Office code must be between 3 and 7 characters';
        } else if (!/^[a-z]+$/.test(formData.code)) {
            errors.code = 'Office code must contain only lowercase letters';
        }
        
        // Validate Office Name
        if (!formData.name) {
            errors.name = 'Office name is required';
        } else if (formData.name.length < 1 || formData.name.length > 60) {
            errors.name = 'Office name must be between 1 and 60 characters';
        } else if (!/^[A-Z\s\.,\/\']+/.test(formData.name)) {
            errors.name = 'Office name must be in uppercase and can include spaces and these special characters: .,/'
        }
        
        // Validate Abbreviation
        if (!formData.abbreviation) {
            errors.abbreviation = 'Abbreviation is required';
        } else if (formData.abbreviation.length < 3 || formData.abbreviation.length > 7) {
            errors.abbreviation = 'Abbreviation must be between 3 and 7 characters';
        } else if (!/^[A-Z]+$/.test(formData.abbreviation)) {
            errors.abbreviation = 'Abbreviation must contain only uppercase letters';
        }
        
        // Only check for duplicate name if name is being changed
        if (formData.name && formData.name !== $('#edit_office_name').data('original')) {
            $.ajax({
                url: '/office/check-duplicate',
                type: 'GET',
                data: {
                    name: formData.name,
                    abbreviation: formData.abbreviation,
                    id: officeId // Pass the office ID to exclude current office from check
                },
                async: false, // Make this synchronous to block form submission
                success: function(response) {
                    // Only show error if we're editing and the duplicate is not our own office
                    if (response.duplicateName && response.duplicateName !== officeId) {
                        errors.name = 'This office name is already in use';
                    }
                }
            });
        }
        
        return errors;
    }

    // Helper function to show Bootstrap alert
    function showBootstrapAlert(type, message) {
        const alertContainer = $('#alertContainer');
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Clear existing alerts
        alertContainer.html('');
        
        // Add new alert
        alertContainer.append(alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            alertContainer.find('.alert').alert('close');
        }, 5000);
    }

    // Initialize
    loadOffices();
    refreshParentOfficeDropdown();

    // Hide add office button if user has only view permission
    if (hasOnlyViewPermission) {
        $('#addOfficeBtn').hide();
    }

    // Delete office button handler
    if (!hasOnlyViewPermission) {
        $(document).on('click', '.delete-office', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            $('#officeToDelete').text(name);
            $('#deleteOfficeId').val(id);
            $('#deleteOfficeModal').modal('show');
        });

        // Confirm delete office
        $('#confirmDeleteOfficeBtn').on('click', function() {
            const id = $('#deleteOfficeId').val();
            $.ajax({
                url: `/office/${id}`,
                type: 'DELETE',
                success: function(response) {
                    if (response.success) {
                        $('#deleteOfficeModal').modal('hide');
                        loadOffices();
                        refreshParentOfficeDropdown();
                        showBootstrapAlert('success', 'Office deleted successfully');
                    } else {
                        showBootstrapAlert('danger', response.message || 'Failed to delete office');
                    }
                },
                error: function(xhr, status, error) {
                    const errorMessage = handleAjaxError(xhr, error, true); // true to return message instead of showing alert
                    showBootstrapAlert('danger', errorMessage);
                }
            });
        });
    }

    // Real-time validation for office code
    $('#officeCode').on('input', function() {
        const value = $(this).val().trim();
        
        // Clear existing error messages
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
        
        if (!value) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Office code is required</div>');
            return;
        }
        
        // Validate length
        if (value.length < 3 || value.length > 7) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Office code must be between 3 and 7 characters</div>');
            return;
        }
        
        // Validate lowercase letters only
        if (!/^[a-z]+$/.test(value)) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Office code must contain only lowercase letters</div>');
            return;
        }
    });

    // Real-time validation for office name
    $('#officeName').on('input', function() {
        const value = $(this).val().trim();
        
        // Clear existing error messages
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
        
        if (!value) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Office name is required</div>');
            return;
        }
        
        // Validate length
        if (value.length < 1 || value.length > 60) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Office name must be between 1 and 60 characters</div>');
            return;
        }
        
        // Validate uppercase letters and special characters
        if (!/^[A-Z\s\.,\/\']+$/.test(value)) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Office name must be in uppercase and can include spaces and these special characters: .,/\'</div>');
            return;
        }
    });

    // Real-time validation for abbreviation
    $('#officeAbbreviation').on('input', function() {
        const value = $(this).val().trim();
        
        // Clear existing error messages
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();
        
        if (!value) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Abbreviation is required</div>');
            return;
        }
        
        // Validate length
        if (value.length < 3 || value.length > 7) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Abbreviation must be between 3 and 7 characters</div>');
            return;
        }
        
        // Validate uppercase letters only
        if (!/^[A-Z]+$/.test(value)) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Abbreviation must contain only uppercase letters</div>');
            return;
        }
    });

    // Real-time validation for edit office abbreviation
    $('#edit_office_abbreviation').on('input', function() {
        const value = $(this).val().trim();
        const $formGroup = $(this).closest('.mb-3');
        
        // Remove existing error messages
        $formGroup.find('.invalid-feedback').remove();
        $(this).removeClass('is-invalid');
        
        if (value) {
            if (value.length < 3 || value.length > 7) {
                $(this).addClass('is-invalid');
                const errorDiv = $('<div>', {
                    class: 'invalid-feedback',
                    text: 'Abbreviation must be between 3 and 7 characters'
                });
                $(this).after(errorDiv);
            } else if (!/^[A-Z]+$/.test(value)) {
                $(this).addClass('is-invalid');
                const errorDiv = $('<div>', {
                    class: 'invalid-feedback',
                    text: 'Abbreviation must contain only uppercase letters'
                });
                $(this).after(errorDiv);
            }
        }
    });

    // Add office form submission
    $('#addOfficeForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get the submit button
        const $submitBtn = $('#saveOfficeBtn');
        
        if (isSubmitting) return;
        isSubmitting = true;
        
        // Get form data
        const formData = {
            code: $('#officeCode').val().trim(),
            name: $('#officeName').val().trim(),
            abbreviation: $('#officeAbbreviation').val().trim(),
            parent_id: $('#parentOffice').val() || null,
            _token: $('meta[name="csrf-token"]').attr('content')
        };
        
        // Validate form
        const validationErrors = validateForm(formData);
        
        if (Object.keys(validationErrors).length > 0) {
            console.log('Form validation failed');
            showFormErrors(validationErrors, 'addOfficeForm');
            $('#addOfficeModal .modal-body').removeClass('loading');
            $submitBtn.prop('disabled', false);
            isSubmitting = false;
            return;
        }
        
        console.log('Form validation passed, proceeding with submission');
        
        // Show loading state
        $('#addOfficeModal .modal-body').addClass('loading');
        $submitBtn.prop('disabled', true);
        
        $.ajax({
            url: '/office',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Clear form and close modal
                    $('#addOfficeForm')[0].reset();
                    $('#addOfficeModal').modal('hide');
                    
                    // Add success message
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Office created successfully
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    $('#dynamicAlertContainer').append(alertHtml);
                    
                    // Remove alert after 5 seconds
                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 5000);
                    
                    // Refresh the offices list
                    loadOffices();
                } else {
                    showError(response.message || 'Failed to create office');
                }
            },
            error: function(xhr, status, error) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    showFormErrors(xhr.responseJSON.errors, 'addOfficeForm');
                } else {
                    showError('An error occurred. Please try again.');
                }
                console.error('AJAX Error:', error);
            },
            complete: function() {
                isSubmitting = false;
                $('#addOfficeModal .modal-body').removeClass('loading');
                $submitBtn.prop('disabled', false);
            }
        });
    });

    // Edit office button handler
    if (!hasOnlyViewPermission) {
        $(document).on('click', '.edit-office', function() {
            const id = $(this).data('id');
            const code = $(this).data('code');
            const name = $(this).data('name');
            const abbreviation = $(this).data('abbreviation');
            const parentOfficeId = $(this).data('parentOfficeId');

            // Set form values
            $('#edit_office_id').val(id);
            $('#edit_office_code').val(code);
            $('#edit_office_name').val(name);
            $('#edit_office_abbreviation').val(abbreviation);

            // Refresh dropdown and show modal
            refreshParentOfficeDropdown().then(() => {
                $('#edit_office_parent').val(parentOfficeId || '');
                $('#editOfficeModal').modal('show');
            });
        });

        // Edit office form submission
        $('#editOfficeForm').on('submit', function(e) {
            e.preventDefault();
            
            // Get the submit button
            const $submitBtn = $('#updateOfficeBtn');
            
            // Get form data
            const formData = {
                code: $('#edit_office_code').val().trim(),
                name: $('#edit_office_name').val().trim(),
                abbreviation: $('#edit_office_abbreviation').val().trim(),
                parent_id: $('#edit_office_parent').val() || null
            };

            // Validate form
            const validationErrors = validateForm(formData);
            if (Object.keys(validationErrors).length > 0) {
                showFormErrors(validationErrors, 'editOfficeForm');
                return;
            }

            // Show loading state
            $('#editOfficeModal .modal-body').addClass('loading');
            $submitBtn.prop('disabled', true);

            $.ajax({
                url: `/office/${$('#edit_office_id').val()}`,
                type: 'PUT',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        // Close modal
                        $('#editOfficeModal').modal('hide');
                        $('.modal-backdrop').remove();
                        
                        // Refresh data
                        loadOffices();
                        refreshParentOfficeDropdown();
                        
                        // Show success message
                        showBootstrapAlert('success', 'Office updated successfully');
                    } else {
                        showError(response.message || 'Failed to update office');
                    }
                },
                error: function(xhr, status, error) {
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            // Display validation errors
                            Object.entries(xhr.responseJSON.errors).forEach(([field, errorMessages]) => {
                                const $field = $(`#edit_${field}`); 
                                if ($field.length) {
                                    const $formGroup = $field.closest('.mb-3');
                                    
                                    // Remove existing error messages
                                    $formGroup.find('.invalid-feedback').remove();
                                    
                                    // Add Bootstrap validation class
                                    $field.addClass('is-invalid');
                                    
                                    // Add new error message with Bootstrap styling
                                    const errorDiv = $('<div>', {
                                        class: 'invalid-feedback',
                                        text: Array.isArray(errorMessages) ? errorMessages[0] : errorMessages
                                    });
                                    $field.after(errorDiv);
                                }
                            });
                        } else if (xhr.responseJSON.message) {
                            showError(xhr.responseJSON.message);
                        }
                    } else {
                        handleAjaxError(xhr, error);
                    }
                },
                complete: function() {
                    $('#editOfficeModal .modal-body').removeClass('loading');
                    $submitBtn.prop('disabled', false);
                }
            });
        });

        // Update office button handler - Ensure it always submits the form
        $('#updateOfficeBtn').on('click', function(e) {
            e.preventDefault();
            $('#editOfficeForm').submit();
        });
    }

    // Clear form state when modals are hidden
    $('#addOfficeModal, #editOfficeModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('.error-message').remove();
        $(this).find('.form-group').removeClass('has-error');
        isSubmitting = false;
        $(this).find('button[type="submit"]').prop('disabled', false);
    });
});