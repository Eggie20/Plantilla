/**
 * Direct Office Dropdown Loader
 * This script specifically targets the office dropdown in add_personnel.blade.php
 */
document.addEventListener('DOMContentLoaded', function() {
    // Function to directly load offices into the specific dropdown
    function loadOfficesDirectly() {
        console.log('Direct Office Loader: Starting');
        
        // Get the specific dropdown by ID
        const officeDropdown = document.getElementById('office');
        
        if (!officeDropdown) {
            console.log('Direct Office Loader: Dropdown with ID "office" not found');
            return;
        }
        
        console.log('Direct Office Loader: Found dropdown with ID "office"');
        
        try {
            // Get offices from localStorage
            const officesJson = localStorage.getItem('offices');
            if (!officesJson) {
                console.log('Direct Office Loader: No offices found in localStorage');
                return;
            }
            
            const offices = JSON.parse(officesJson);
            if (!Array.isArray(offices) || offices.length === 0) {
                console.log('Direct Office Loader: No offices array found in localStorage or array is empty');
                return;
            }
            
            console.log(`Direct Office Loader: Found ${offices.length} offices in localStorage:`, offices);
            
            // Get existing option values to avoid duplicates
            const existingValues = [];
            for (let i = 0; i < officeDropdown.options.length; i++) {
                existingValues.push(officeDropdown.options[i].value);
            }
            
            console.log('Direct Office Loader: Existing values:', existingValues);
            
            // Add each office to the dropdown
            let addedCount = 0;
            offices.forEach(office => {
                // Skip if this office code already exists in the dropdown
                if (existingValues.includes(office.code)) {
                    console.log(`Direct Office Loader: Skipping existing office: ${office.code}`);
                    return;
                }
                
                // Create and add the new option
                const option = document.createElement('option');
                option.value = office.code;
                option.textContent = `${office.name} (${office.abbreviation || ''})`.trim();
                officeDropdown.appendChild(option);
                
                console.log(`Direct Office Loader: Added office: ${office.name} with code ${office.code}`);
                addedCount++;
            });
            
            console.log(`Direct Office Loader: Added ${addedCount} new offices to the dropdown`);
        } catch (error) {
            console.error('Direct Office Loader: Error loading offices:', error);
        }
    }
    
    // Call the function immediately
    loadOfficesDirectly();
    
    // Also call it when the modal is shown
    const addPersonnelModal = document.getElementById('addPersonnelModal');
    if (addPersonnelModal) {
        addPersonnelModal.addEventListener('shown.bs.modal', function() {
            console.log('Direct Office Loader: Modal shown, updating dropdown');
            loadOfficesDirectly();
        });
    }
    
    // Add a button to manually trigger the loader (for debugging)
    const officeDropdown = document.getElementById('office');
    if (officeDropdown && officeDropdown.parentNode) {
        const debugButton = document.createElement('button');
        debugButton.type = 'button';
        debugButton.className = 'btn btn-sm btn-outline-secondary mt-1';
        debugButton.textContent = 'Refresh Offices';
        debugButton.style.fontSize = '0.75rem';
        debugButton.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Direct Office Loader: Manual refresh triggered');
            loadOfficesDirectly();
        });
        
        // Add the button after the dropdown
        officeDropdown.parentNode.appendChild(debugButton);
    }
    
    // Make the function available globally for debugging
    window.loadOfficesDirectly = loadOfficesDirectly;
});