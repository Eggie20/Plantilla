$(document).ready(function() {
    // Get user permissions at the start
    const userPermissions = JSON.parse(document.querySelector('meta[name="user-permissions"]').content);
    const hasOnlyViewPermission = userPermissions.length === 1 && userPermissions[0] === 'view';


    // Function to load offices
    function loadOffices() {
        $.ajax({
            url: '/office',
            type: 'GET',
            success: function(response) {
                const tbody = $('#officesTable tbody');
                tbody.empty();
                
                if (response.success && response.offices.length > 0) {
                    response.offices.forEach(office => {
                        // Only show view button if user has only view permission
                        let actionButtons = '';
                        if (hasOnlyViewPermission) {
                            actionButtons = `
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-info view-details" data-id="${office.id}">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </div>
                            `;
                        } else {
                            actionButtons = `
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-warning edit-office" 
                                        data-id="${office.id}"
                                        data-code="${office.code}"
                                        data-name="${office.name}"
                                        data-abbreviation="${office.abbreviation}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger delete-office" 
                                        data-id="${office.id}"
                                        data-name="${office.name}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            `;
                        }

                        tbody.append(`
                            <tr>
                                <td>${office.code}</td>
                                <td>${office.name}</td>
                                <td>${office.abbreviation}</td>
                                <td>${office.type || 'N/A'}</td>
                                <td class="text-center">${actionButtons}</td>
                            </tr>
                        `);
                    });
                    $('#noOfficesMessage').hide();
                } else {
                    $('#noOfficesMessage').show();
                }
            },
            error: function() {
                $('#noOfficesMessage').show().text('Error loading offices');
            }
        });
    }

    // Function to load vacant positions
    function loadVacantPositions() {
        const filter = $('input[name="positionFilter"]:checked').val();
        
        $.ajax({
            url: '/positions/vacant',
            type: 'GET',
            data: {
                filter: filter
            },
            success: function(response) {
                const tbody = $('#vacantTableBody');
                tbody.empty();
                
                if (response.success && response.positions.length > 0) {
                    response.positions.forEach(position => {
                        tbody.append(`
                            <tr>
                                <td>${position.itemNo}</td>
                                <td>${position.office?.abbreviation || ''}</td>
                                <td>${position.position}</td>
                                <td>${position.salaryGrade}</td>
                                <td>${position.step}</td>
                                <td>${position.code || ''}</td>
                                <td>${position.level || ''}</td>
                                <td>${position.status || ''}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-info view-details" data-id="${position.id}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        ${hasOnlyViewPermission ? '' : `
                                            <button type="button" class="btn btn-sm btn-success assign-personnel" data-id="${position.id}">
                                                <i class="fas fa-user-plus"></i>
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
            error: function(xhr, status, error) {
                $('#noVacantData').show().text('Error loading vacant positions');
                console.error('Error loading vacant positions:', error);
            }
        });
    }

    // Handle filter change
    $('input[name="positionFilter"]').change(function() {
        loadVacantPositions();
    });

    // Initial load of offices
    loadOffices();

    // Tab change handlers
    $('#positionTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (e.target.id === 'vacant-tab') {
            loadVacantPositions();
        } else if (e.target.id === 'manage-offices-tab') {
            loadOffices();
        }
    });


});