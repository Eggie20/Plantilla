<script>
    alert('Script loaded in modal!');
    
    $(document).ready(function() {
        alert('Document ready in modal!');
        
        const addUserForm = $('#addUserForm');
        if (!addUserForm.length) {
            alert('Form not found in modal!');
            return;
        }
        
        alert('Form found in modal!');
        
        // Test validation
        addUserForm.find('input, select').on('keyup change', function() {
            const field = $(this);
            const fieldName = field.attr('name');
            
            alert('Field changed: ' + fieldName);
            
            // Add red border for testing
            field.css('border-color', 'red');
        });
    });
</script>
