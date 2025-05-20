// Initialize Bootstrap components when the document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Check if Bootstrap is available
    if (typeof bootstrap !== 'undefined') {
        // Get all modals on the page
        var modals = document.querySelectorAll('.modal');
        
        // Initialize each modal
        modals.forEach(function(modalEl) {
            var modal = new bootstrap.Modal(modalEl);
            
            // Store the modal instance on the element for later use
            modalEl._bootstrapModal = modal;
        });
    }
    
    // Legacy support for custom openModal function
    window.openModal = function() {
        var modalEl = document.getElementById('customModal');
        
        if (modalEl) {
            if (typeof bootstrap !== 'undefined') {
                // Use Bootstrap's modal if available
                var modal = modalEl._bootstrapModal || new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                // Fallback to manual display
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                document.body.classList.add('modal-open');
            }
        }
    };
    
    // Legacy support for custom closeModal function
    window.closeModal = function() {
        var modalEl = document.getElementById('customModal');
        
        if (modalEl) {
            if (typeof bootstrap !== 'undefined') {
                // Use Bootstrap's modal if available
                var modal = modalEl._bootstrapModal || bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
            } else {
                // Fallback to manual hide
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
        }
    };
});