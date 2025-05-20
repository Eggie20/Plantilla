$(document).ready(function() {
    var officeMapping = {
        'mayor': 'Office of the Mayor (MO)',
        'sbo': 'Office of the Sanguniang Bayan (SBO)',
        'mpdo': 'Municipal Planning & Development Coordinator (MPDO)',
        'lcr': 'Office of the Local Registrar (LCR)',
        'mbo': 'Office of the Municipal Budget Officer (MBO)',
        'macco': 'Office of the Municipal Accountant (MACCO)',
        'mto': 'Office of the Municipal Treasurer (MTO)',
        'masso': 'Office of the Municipal Assessor (MASSO)',
        'mho': 'Office of the Municipal Health Officer (MHO/RHU)',
        'mswdo': 'Social Welfare & Development Officer (MSWDO)',
        'mao': 'Office of the Municipal Agriculturist (MAO)',
        'meo': 'Office of the Municipal Engineer (MEO)',
        'mee': 'Ergonomic Enterprise Development Management (MEE)',
        'mdrrmo': 'Local Disaster Risk Reduction & Management (MDRRMO)'
    };

    // Function to update position dropdown
    function updatePositionDropdown(positions) {
        var $dropdown = $('#positionDropdown');
        $dropdown.empty().append('<option value="">Select Position</option>');
        
        positions.forEach(function(position) {
            $dropdown.append(
                $('<option>', {
                    value: position.id,
                    text: position.name
                })
            );
        });
    }

    // Function to apply all filters
    function applyFilters() {
        var officeFilter = $('#officeFilter').val();
        var statusFilter = $('#statusFilter').val();
        var positionFilter = $('#positionDropdown').val();
        var searchText = $('#tableSearch').val().toLowerCase();
        
        // Filter the table
        var rows = $('#personnelTable tbody tr.employee-data');
        rows.each(function() {
            var $row = $(this);
            var shouldShow = true;
            
            // Get row data from data attributes
            var officeCode = $row.data('office');
            var position = $row.data('position');
            var status = $row.data('status');
            
            // Apply office filter
            if (officeFilter) {
                // Check if office code matches
                shouldShow = shouldShow && (officeCode === officeFilter);
            }
            
            // Apply status filter
            if (statusFilter) {
                shouldShow = shouldShow && (status === statusFilter);
            }
            
            // Apply position filter
            if (positionFilter) {
                shouldShow = shouldShow && (position === positionFilter);
            }
            
            // Apply search filter
            if (searchText) {
                var text = $row.text().toLowerCase();
                shouldShow = shouldShow && text.includes(searchText);
            }
            
            $row.toggle(shouldShow);
        });
        
        // Show/hide no results message
        const noDataMessage = $('#noDataMessage');
        if (noDataMessage.length) {
            noDataMessage.toggle(rows.filter(':visible').length === 0);
        }
    }

    // Initialize filters
    applyFilters();

    // Office filter change
    $('#officeFilter').on('change', function() {
        var selectedOffice = $(this).val();
        
        // Update the selected office display
        if (selectedOffice) {
            $('#selectedOfficeDisplay').text(window.officeMapping[selectedOffice]);
            
            // Clear position dropdown and fetch positions
            $('#positionDropdown').empty().append('<option value="">Select Position</option>');
            if (selectedOffice) {
                $.ajax({
                    url: '/get-positions',
                    type: 'GET',
                    data: { office: selectedOffice },
                    success: function(response) {
                        if (response.success && response.positions) {
                            updatePositionDropdown(response.positions);
                        }
                        applyFilters();
                    },
                    error: function() {
                        console.error('Error fetching positions');
                        applyFilters();
                    }
                });
            }
        } else {
            $('#selectedOfficeDisplay').text('');
            $('#positionDropdown').empty().append('<option value="">Select Position</option>');
            applyFilters();
        }
    });

    // Position filter change
    $('#positionDropdown').on('change', function() {
        applyFilters();
    });

    // Status filter change
    $('#statusFilter').on('change', function() {
        applyFilters();
    });

    // Search input change
    $('#tableSearch').on('input', function() {
        applyFilters();
    });

    // DOM Content Loaded handlers
    $(document).ready(function() {
        // Hide skeleton loader and show table container
        setTimeout(function() {
            $('.skeleton-loader').hide();
            $('#originalPersonnelTableContainer').show();
        }, 500);

        // Add loaded class to body
        $('body').addClass('loaded');
    });
});
