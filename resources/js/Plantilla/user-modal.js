$(document).ready(function() {
    alert('JavaScript loaded!');
    
    const addUserForm = $('#addUserForm');
    if (!addUserForm.length) {
        alert('Form not found!');
        return;
    }
    
    alert('Form found!');
    
    // Test validation
    addUserForm.find('input, select').on('keyup change', function() {
        const field = $(this);
        const fieldName = field.attr('name');
        
        alert('Field changed: ' + fieldName);
        
        // Add red border for testing
        field.css('border-color', 'red');
    });
});

// Add a global test function for debugging
window.testValidation = function() {
    alert('Test function called!');
    const addUserForm = $('#addUserForm');
    if (addUserForm.length) {
        alert('Form found!');
    } else {
        alert('Form NOT found!');
    }
}

    // Check if form exists
    if (!addUserForm.length) {
        console.error('Form not found!');
        return;
    }

    // Real-time validation
    addUserForm.find('input, select').on('keyup change', function() {
        console.log('Field changed:', $(this).attr('name'), $(this).val());
        const field = $(this);
        const fieldName = field.attr('name');
        const value = field.val();
        const passwordField = addUserForm.find('#password');
        const confirmPasswordField = addUserForm.find('#confirmPassword');
        
        console.log('Validating field:', fieldName, 'Value:', value);
        
        // Clear existing error messages for this field
        field.removeClass('is-invalid');
        field.next('.invalid-feedback').remove();

        // Perform validation based on field type
        switch(fieldName) {
            case 'firstName':
            case 'lastName':
                if (!value) {
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback">This field is required.</div>');
                } else if (value.length > 50) {
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback">Maximum 50 characters allowed.</div>');
                } else if (!/^[a-zA-Z\s'-]+$/.test(value)) {
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback">Only letters, spaces, apostrophes, and hyphens are allowed.</div>');
                }
                break;

            case 'username':
                if (!value) {
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback">Username is required.</div>');
                } else if (value.length > 50) {
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback">Maximum 50 characters allowed.</div>');
                } else if (!/^[a-zA-Z0-9._-]+$/.test(value)) {
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback">Username can only contain letters, numbers, dots, underscores, and hyphens.</div>');
                }
                break;

            case 'password':
                if (!value) {
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback">Password is required.</div>');
                } else if (value.length < 8) {
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback">Password must be at least 8 characters long.</div>');
                } else if (confirmPasswordField.val() && value !== confirmPasswordField.val()) {
                    confirmPasswordField.addClass('is-invalid');
                    confirmPasswordField.after('<div class="invalid-feedback">Password confirmation does not match.</div>');
                }
                break;

            case 'confirmPassword':
                if (!value) {
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback">Please confirm your password.</div>');
                } else if (passwordField.val() && value !== passwordField.val()) {
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback">Password confirmation does not match.</div>');
                }
                break;

            case 'role':
                if (!value) {
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback">Role is required.</div>');
                } else if (!['admin', 'user'].includes(value)) {
                    field.addClass('is-invalid');
                    field.after('<div class="invalid-feedback">Please select a valid role.</div>');
                }
                break;
        }
    });

    // Add additional validation for form submission
    addUserForm.on('submit', function(e) {
        const password = addUserForm.find('#password').val();
        const confirmPassword = addUserForm.find('#confirmPassword').val();
        
        if (password !== confirmPassword) {
            e.preventDefault();
            addUserForm.find('#password').addClass('is-invalid');
            addUserForm.find('#confirmPassword').addClass('is-invalid');
            addUserForm.find('#confirmPassword').after('<div class="invalid-feedback">Password confirmation does not match.</div>');
        }
    });

    // Form submission
    addUserForm.on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const url = $(this).attr('action');

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .done(function(data) {
            if (data.success) {
                addUserModal.hide();
                window.location.reload();
            } else {
                // Handle validation errors
                addUserForm.find('.alert-danger').remove();
                
                // Add error summary at the top
                const errorSummary = $('<div class="alert alert-danger"></div>');
                errorSummary.html('<ul>' + 
                    Object.values(data.errors).map(error => `<li>${error}</li>`).join('') + 
                    '</ul>');
                addUserForm.prepend(errorSummary);

                // Add error classes to fields
                Object.keys(data.errors).forEach(field => {
                    const input = addUserForm.find(`[name="${field}"]`);
                    if (input.length) {
                        input.addClass('is-invalid');
                        const feedback = $('<div class="invalid-feedback"></div>');
                        feedback.text(data.errors[field]);
                        input.parent().append(feedback);
                    }
                });
            }
        })
        .fail(function(xhr, status, error) {
            console.error('Error:', error);
            alert('An error occurred while processing your request.');
        });
    });
});
