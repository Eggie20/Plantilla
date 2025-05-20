$(document).ready(function() {
    // Function to load vacant positions
    function loadVacantPositions() {
        const tbody = $('#vacantTableBody');
        tbody.empty();
        
        $.ajax({
            url: '{{ route("position.vacant") }}',
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    if (response.positions.length) {
                        response.positions.forEach(position => {
                            tbody.append(`
                                <tr>
                                    <td>${position.itemNo}</td>
                                    <td>${position.office.name}</td>
                                    <td>${position.position}</td>
                                    <td>${position.salaryGrade}</td>
                                    <td>${position.monthlySalary}</td>
                                    <td>${position.step || ''}</td>
                                    <td>${position.code || ''}</td>
                                    <td>${position.type || 'M'}</td>
                                    <td>${position.level || ''}</td>
                                    <td>${position.status}</td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-warning btn-sm edit-position" data-id="${position.id}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm delete-position" data-id="${position.id}" data-item-no="${position.itemNo}" data-position="${position.position}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            `);
                        });
                        $('#noVacantData').hide();
                    } else {
                        $('#noVacantData').show();
                    }
                }
            }
        });
    }

    // Initial load of vacant positions
    loadVacantPositions();

    // Add New Position button handler
    $('#addNewPositionBtn').on('click', function() {
        // Reset the form
        $('#positionForm')[0].reset();
        $('#position_id').val('');
        
        // Update modal title
        $('#positionModalLabel').text('Add New Position');
        
        // Show the modal
        $('#positionModal').modal('show');
    });

    // Handle position form submission
    $('#positionForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const url = form.attr('action');
        const method = $('#position_id').val() ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            type: method,
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    $('#positionModal').modal('hide');
                    loadVacantPositions();
                    alert('Position saved successfully');
                } else {
                    alert(response.message || 'Failed to save position');
                }
            },
            error: function(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    alert(errors.join('\n'));
                } else {
                    alert('Error saving position');
                }
            }
        });
    });

    // Handle edit button click
    $(document).on('click', '.edit-position', function() {
        const id = $(this).data('id');
        
        // Fetch position details
        $.ajax({
            url: '{{ route("position.edit", ["id" => "__ID__"]) }}'.replace('__ID__', id),
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const position = response.position;
                    
                    // Update form action for update
                    $('#positionForm').attr('action', '{{ route("plantilla-item.update", ["id" => "__ID__"]) }}'.replace('__ID__', id));
                    
                    // Populate the form
                    $('#position_id').val(position.id);
                    $('#itemNo').val(position.itemNo);
                    $('#office_id').val(position.office_id);
                    $('#position').val(position.position);
                    $('#salaryGrade').val(position.salaryGrade);
                    $('#monthlySalary').val(position.monthlySalary);
                    $('#step').val(position.step);
                    
                    // Update modal title
                    $('#positionModalLabel').text('Edit Position');
                    
                    // Show the modal
                    $('#positionModal').modal('show');
                }
            }
        });
    });

    // Handle delete button click
    $(document).on('click', '.delete-position', function() {
        const id = $(this).data('id');
        const itemNo = $(this).data('item-no');
        const position = $(this).data('position');
        
        // Populate the delete confirmation modal
        $('#deletePositionId').val(id);
        $('#deleteItemNo').text(itemNo);
        $('#deletePosition').text(position);
        
        // Show the delete modal
        $('#deletePositionModal').modal('show');
    });

    // Handle delete confirmation
    $('#confirmDeleteBtn').on('click', function() {
        const id = $('#deletePositionId').val();
        
        $.ajax({
            url: '{{ route("plantilla-item.delete", ["id" => "__ID__"]) }}'.replace('__ID__', id),
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    $('#deletePositionModal').modal('hide');
                    loadVacantPositions();
                    alert('Position deleted successfully');
                } else {
                    alert(response.message || 'Failed to delete position');
                }
            },
            error: function() {
                alert('Error deleting position');
            }
        });
    });

    // Position search functionality
    $('#positionSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const rows = $('#vacantTableBody tr');
        
        rows.each(function() {
            const text = $(this).text().toLowerCase();
            $(this).toggle(text.includes(searchTerm));
        });
        
        // Show/hide no data message
        const visibleRows = $('#vacantTableBody tr:visible').length;
        $('#noVacantData').toggle(visibleRows === 0);
    });

    // Position filter functionality
    $('input[name="positionFilter"]').on('change', function() {
        const filter = $(this).val();
        const rows = $('#vacantTableBody tr');
        
        rows.each(function() {
            const status = $(this).find('td:eq(9)').text().toLowerCase();
            if (filter === 'all') {
                $(this).show();
            } else if (filter === 'vacant') {
                $(this).toggle(status === 'vacant');
            } else if (filter === 'unfunded') {
                $(this).toggle(status === 'unfunded');
            }
        });
        
        // Show/hide no data message
        const visibleRows = $('#vacantTableBody tr:visible').length;
        $('#noVacantData').toggle(visibleRows === 0);
    });
});