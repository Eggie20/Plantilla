/**
 * Simple script to load offices from localStorage into all office dropdowns
 */
document.addEventListener('DOMContentLoaded', function() {
    // Function to load offices into all dropdowns with id="office"
    function loadOfficesIntoDropdowns() {
        console.log('Loading offices into dropdowns');
        
        // Find all office dropdowns
        const officeDropdowns = document.querySelectorAll('select#office');
        
        if (officeDropdowns.length === 0) {
            console.log('No office dropdowns found on page');
            return;
        }
        
        console.log(`Found ${officeDropdowns.length} office dropdowns to update`);
        
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
            
            console.log(`Found ${offices.length} offices in localStorage`);
            
            // Update each dropdown
            officeDropdowns.forEach((dropdown, index) => {
                console.log(`Updating dropdown #${index + 1}`);
                
                // Get existing option values to avoid duplicates
                const existingValues = [];
                for (let i = 0; i < dropdown.options.length; i++) {
                    existingValues.push(dropdown.options[i].value);
                }
                
                // Add each office to the dropdown
                offices.forEach(office => {
                    // Skip if this office code already exists in the dropdown
                    if (existingValues.includes(office.code)) {
                        return;
                    }
                    
                    // Create and add the new option
                    const option = document.createElement('option');
                    option.value = office.code;
                    option.textContent = `${office.name} (${office.abbreviation})`;
                    dropdown.appendChild(option);
                    
                    console.log(`Added office: ${office.name} (${office.abbreviation})`);
                });
            });
            
            console.log('Finished loading offices into dropdowns');
        } catch (error) {
            console.error('Error loading offices into dropdowns:', error);
        }
    }
    
    // Call the function when the page loads
    loadOfficesIntoDropdowns();
    
    // Also call it when any modal is shown
    document.addEventListener('shown.bs.modal', function() {
        console.log('Modal shown, updating office dropdowns');
        loadOfficesIntoDropdowns();
    });
    
    // Make the function available globally
    window.loadOfficesIntoDropdowns = loadOfficesIntoDropdowns;
});