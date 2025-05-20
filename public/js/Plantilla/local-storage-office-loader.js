/**
 * Local Storage Office Loader
 * This script loads offices from localStorage into the office dropdown
 */
document.addEventListener('DOMContentLoaded', function() {
    // Function to load offices from localStorage into the dropdown
    function loadOfficesFromLocalStorage() {
        console.log('Loading offices from localStorage');
        
        // Get the office dropdown
        const officeDropdown = document.getElementById('office');
        if (!officeDropdown) {
            console.error('Office dropdown not found');
            return;
        }
        
        try {
            // Get offices from localStorage
            const officesJson = localStorage.getItem('offices');
            if (!officesJson) {
                console.log('No offices found in localStorage');
                return;
            }
            
            const offices = JSON.parse(officesJson);
            if (!Array.isArray(offices) || offices.length === 0) {
                console.log('No offices array found in localStorage or array is empty');
                return;
            }
            
            console.log(`Found ${offices.length} offices in localStorage:`, offices);
            
            // Add a separator before custom offices
            const separator = document.createElement('option');
            separator.disabled = true;
            separator.textContent = '--- Custom Offices ---';
            officeDropdown.appendChild(separator);
            
            // Add each office to the dropdown
            offices.forEach(office => {
                // Check if this office option already exists
                const exists = Array.from(officeDropdown.options).some(
                    option => option.value === office.code
                );
                
                // If it doesn't exist, add it
                if (!exists) {
                    const option = document.createElement('option');
                    option.value = office.code;
                    option.textContent = `${office.name} (${office.abbreviation})`;
                    officeDropdown.appendChild(option);
                    console.log(`Added office: ${office.name} (${office.abbreviation})`);
                } else {
                    console.log(`Office ${office.code} already exists in dropdown`);
                }
            });
            
            console.log('Finished loading offices from localStorage');
        } catch (error) {
            console.error('Error loading offices from localStorage:', error);
        }
    }
    
    // Load offices when the page loads
    loadOfficesFromLocalStorage();
    
    // Also load offices when the modal is shown
    $('#addPersonnelModal').on('shown.bs.modal', function() {
        console.log('Add Personnel modal shown, loading offices');
        loadOfficesFromLocalStorage();
    });
    
    // Listen for the custom event that might be triggered when offices are added
    document.addEventListener('officesUpdated', function() {
        console.log('Offices updated event received');
        loadOfficesFromLocalStorage();
    });
    
    // Make the function available globally for debugging
    window.loadOfficesFromLocalStorage = loadOfficesFromLocalStorage;
});