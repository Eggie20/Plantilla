// Load assigned positions
// window.loadAssignedPositions = function() {
//     $.ajax({
//         url: '/positions/assigned',
//         type: 'GET',
//         success: function(response) {
//             const tbody = $('#assignedTableBody');
//             tbody.empty();
            
//             if (response.success && response.positions && response.positions.length > 0) {
//                 response.positions.forEach(position => {
//                     const personnelName = position.personnel ? `${position.personnel.lastName}, ${position.personnel.firstName}` : '';
//                     tbody.append(`
//                         <tr>
//                             <td>${position.itemNo || ''}</td>
//                             <td>${position.office?.name || ''}</td>
//                             <td>${position.position || ''}</td>
//                             <td>${position.salaryGrade || ''}</td>
//                             <td>${personnelName}</td>
//                             <td>${position.status || ''}</td>
//                             <td class="text-center">
//                                 <div class="btn-group" role="group">
//                                     <button type="button" class="btn btn-sm btn-warning assigned-edit" 
//                                         data-id="${position.id}" 
//                                         data-item-no="${position.itemNo}" 
//                                         data-office-id="${position.office?.id || ''}" 
//                                         data-position="${position.position}" 
//                                         data-salary-grade="${position.salaryGrade}" 
//                                         data-code="${position.code}" 
//                                         data-type="${position.type}" 
//                                         data-level="${position.level}" 
//                                         data-status="${position.status}">
//                                         <i class="fas fa-edit"></i> Edit
//                                     </button>
//                                     <button type="button" class="btn btn-sm btn-danger assigned-delete" 
//                                         data-id="${position.id}"
//                                         data-item-no="${position.itemNo}"
//                                         data-position="${position.position}"
//                                         data-personnel="${position.personnel ? 'true' : 'false'}">
//                                         <i class="fas fa-trash"></i> Delete
//                                     </button>
//                                 </div>
//                             </td>
//                         </tr>
//                     `);
//                 });
//                 $('#noAssignedData').hide();
//             } else {
//                 $('#noAssignedData').show().text('No assigned positions found');
//             }
//         },
//         error: function(xhr, status, error) {
//             console.error('Error loading assigned positions:', error);
//             $('#noAssignedData').show().text('Error loading assigned positions. Please try again.');
//         }
//     });
// };

// Handle view position button click
$(document).on('click', '.view-position', function() {
    const positionId = $(this).data('id');
    const itemNo = $(this).data('item-no');
    const office = $(this).data('office');
    const position = $(this).data('position');
    const salaryGrade = $(this).data('salary-grade');
    const employee = $(this).data('employee');
    const status = $(this).data('status');

    // Create and show the view modal
    const modalContent = `
        <div class="modal-header">
            <h5 class="modal-title">Position Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Item No:</strong> ${itemNo}</p>
                    <p><strong>Office:</strong> ${office}</p>
                    <p><strong>Position:</strong> ${position}</p>
                    <p><strong>Salary Grade:</strong> ${salaryGrade}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Employee:</strong> ${employee}</p>
                    <p><strong>Status:</strong> ${status}</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    `;

    // Create the modal dynamically
    const modalHtml = `
        <div class="modal fade" id="viewPositionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    ${modalContent}
                </div>
            </div>
        </div>
    `;

    // Add modal to body
    $('body').append(modalHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('viewPositionModal'));
    modal.show();

    // Clean up when modal is hidden
    $('#viewPositionModal').on('hidden.bs.modal', function () {
        $(this).remove();
    });
});

// Handle edit position button click
// $(document).on('click', '.assigned-edit', function() {
//     const positionId = $(this).data('id');
//     const itemNo = $(this).data('item-no');
//     const officeId = $(this).data('office-id');
//     const position = $(this).data('position');
//     const salaryGrade = $(this).data('salary-grade');
//     const code = $(this).data('code');
//     const type = $(this).data('type');
//     const level = $(this).data('level');
//     const status = $(this).data('status');

//     // Fill the form with existing data
//     $('#position_id').val(positionId);
//     $('#itemNo').val(itemNo);
//     $('#office_id').val(officeId);
//     $('#position').val(position);
//     $('#salaryGrade').val(salaryGrade);
//     $('#code').val(code);
//     $('#type').val(type);
//     $('#level').val(level);
//     $('#status').val(status);

//     // Show the modal
//     const modal = new bootstrap.Modal(document.getElementById('positionModal'));
//     $('#positionModalLabel').text('Edit Position');
//     modal.show();
// });

// Handle delete position button click
// $(document).on('click', '.assigned-delete', function() {
//     const positionId = $(this).data('id');
//     const itemNo = $(this).data('item-no');
//     const position = $(this).data('position');
//     const personnel = $(this).data('personnel');

//     // Show confirmation dialog
//     if (personnel) {
//         if (!confirm(`This position has assigned personnel. Deleting it will remove the assignment.\n\nAre you sure you want to delete position ${position} (Item No: ${itemNo})?`)) {
//             return;
//         }
//     } else {
//         if (!confirm(`Are you sure you want to delete position ${position} (Item No: ${itemNo})?`)) {
//             return;
//         }
//     }

//     $.ajax({
//         url: `/positions/${positionId}`,
//         type: 'DELETE',
//         data: {
//             _token: $('meta[name="csrf-token"]').attr('content')
//         },
//         success: function(response) {
//             if (response.success) {
//                 window.loadAssignedPositions();
//                 alert('Position deleted successfully');
//             } else {
//                 alert(response.message || 'Failed to delete position');
//             }
//         },
//         error: function(xhr) {
//             const error = xhr.responseJSON?.message || 'An error occurred';
//             console.error('Error:', error);
//             alert(error);
//         }
//     });
// });

// Initialize assigned positions when the assigned tab is clicked
$(document).ready(function() {
    // Remove assigned positions tab click handler
    // Remove assigned positions search functionality
    
    // Keep other event handlers
    $(document).on('click', '.view-position', function() {
        const positionId = $(this).data('id');
        const itemNo = $(this).data('item-no');
        const office = $(this).data('office');
        const position = $(this).data('position');
        const salaryGrade = $(this).data('salary-grade');
        const employee = $(this).data('employee');
        const status = $(this).data('status');

        // Create and show the view modal
        const modalContent = `
            <div class="modal-header">
                <h5 class="modal-title">Position Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Item No:</strong> ${itemNo}</p>
                        <p><strong>Office:</strong> ${office}</p>
                        <p><strong>Position:</strong> ${position}</p>
                        <p><strong>Salary Grade:</strong> ${salaryGrade}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Employee:</strong> ${employee}</p>
                        <p><strong>Status:</strong> ${status}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        `;

        // Create the modal dynamically
        const modalHtml = `
            <div class="modal fade" id="viewPositionModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        ${modalContent}
                    </div>
                </div>
            </div>
        `;

        // Add modal to body
        $('body').append(modalHtml);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('viewPositionModal'));
        modal.show();
    });
});
