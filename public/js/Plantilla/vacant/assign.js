$(document).ready(function() {
    // Handle personnel assignment form submission
    $('#assignPersonnelForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get the office name from the form
        const officeName = $('#assign_office_name').val();
        
        // Create a new FormData object
        const formData = new FormData();
        
        // Add all form fields
        formData.append('position_id', $('#assign_position_id').val());
        formData.append('office', $('#assign_office_id').val()); // Changed from office_id to office
        formData.append('itemNo', $('#assign_item_no').val());
        formData.append('position', $('#assign_position_title').val());
        formData.append('salaryGrade', $('#assign_salary_grade').val());
        formData.append('code', $('#assign_code').val());
        formData.append('type', $('#assign_type').val());
        formData.append('authorizedSalary', $('#assign_authorizedSalary').val().replace(/,/g, ''));
        formData.append('actualSalary', $('#assign_actualSalary').val().replace(/,/g, ''));
        formData.append('step', $('#assign_step').val());
        formData.append('level', $('#assign_level').val());
        formData.append('lastName', $('#assign_lastName').val());
        formData.append('firstName', $('#assign_firstName').val());
        formData.append('middleName', $('#assign_middleName').val());
        formData.append('dob', $('#assign_dob').val());
        formData.append('originalAppointment', $('#assign_originalAppointment').val());
        formData.append('lastPromotion', $('#assign_lastPromotion').val());
        formData.append('status', 'Filled');

        // Add other form fields as needed
        
        $.ajax({
            url: '/assign-personnel',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#assignPersonnelModal').modal('hide');
                    window.location.reload();
                } else {
                    alert(response.message || 'Failed to assign personnel');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while assigning personnel.';
                try {
                    const errorData = xhr.responseJSON;
                    if (errorData && errorData.message) {
                        errorMessage = errorData.message;
                    } else if (errorData && errorData.errors) {
                        errorMessage = Object.values(errorData.errors).flat().join('\n');
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                alert(errorMessage);
            }
        });
    });

    // Format salary inputs with commas while typing
    $('#assign_authorizedSalary, #assign_actualSalary').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value) {
            value = parseInt(value, 10).toLocaleString();
            $(this).val(value);
        }
    });

    // Populate form when assigning to a position
    window.populateAssignForm = function(position) {
        $('#assign_position_id').val(position.id);
        $('#assign_office_id').val(position.office_id);
        $('#assign_office_name').val(position.office.name);
        $('#assign_item_no').val(position.itemNo);
        $('#assign_position_title').val(position.position);
        $('#assign_salary_grade').val(position.salaryGrade);
        $('#assign_code').val(position.code);
        $('#assign_type').val(position.type);
        $('#assign_level').val(position.level);
        
        // Set default authorized and actual salary based on monthly salary
        if (position.monthlySalary) {
            $('#assign_authorizedSalary, #assign_actualSalary').val(
                parseFloat(position.monthlySalary).toLocaleString()
            );
        }
    };
});