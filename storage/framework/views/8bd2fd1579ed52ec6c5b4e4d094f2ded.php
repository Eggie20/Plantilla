<?php
// Add this at the top of your blade file
use App\Helpers\OfficeFormatter;
?>



<?php $__env->startSection('title', 'Local Governement Unit - Magallanes'); ?>

<?php $__env->startPush('styles'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/Plantilla/index.css')); ?>">
    <style>
        .table-scroll-container {
            max-height: 500px;
            overflow-y: auto;
        }
        
        .table-scroll-container table {
            width: 100%;
        }
        
        .table-scroll-container th,
        .table-scroll-container td {
            white-space: nowrap;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <div class="content-container">
        <!-- Alert Container -->
        <div class="alert-container mb-3">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if(session('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo e(session('error')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Dynamic alert container for JavaScript messages -->
            <div id="dynamicAlertContainer"></div>
        </div>
        
        <!-- Filter and Search Section -->
        <div class="filter-section">
            <!-- Filters -->
            <div class="d-flex gap-2 flex-grow-1 flex-wrap">
                <select id="officeFilter" class="form-select" style="max-width: 250px;">
                    <option value="">Select Office</option>
                    <?php $__currentLoopData = $offices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($office->code); ?>"><?php echo e($office->name); ?> (<?php echo e($office->abbreviation); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <!-- Initialize office mapping for JavaScript -->
                <script>
                    window.officeMapping = <?php echo json_encode($offices->mapWithKeys(function($office) {
                        return [$office->code => $office->name . ' (' . $office->abbreviation . ')'];
                    })->toArray(), 15, 512) ?>;
                </script>

                <select id="statusFilter" class="form-select" style="max-width: 180px;">
                    <option value="">Status</option>
                    <option value="casual">Casual</option>
                    <option value="contractual">Contractual</option>            
                    <option value="coterminous">Coterminous</option>
                    <option value="coterminousTemporary">Coterminous-Temporary</option>
                    <option value="elected">Elected</option>
                    <option value="permanent">Permanent</option>
                    <option value="provisional">Provisional</option>
                    <option value="regularPermanent">Regular Permanent</option>
                    <option value="substitute">Substitute</option>
                    <option value="temporary">Temporary</option>
                </select>
            </div>
    
            <!-- Search Bar -->
            <div class="search-container">
                <div class="search-wrapper">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input 
                            type="text" 
                            id="tableSearch" 
                            class="form-control search-input" 
                            placeholder=""
                            aria-label="Search personnel"
                        >
                        <button 
                            type="button" 
                            id="clearSearch" 
                            class="btn-clear" 
                            title="Clear search"
                        >
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Selected Office Display -->
        <div class="text-center mb-4">
            <h4 id="selectedOfficeDisplay" class="text-dark fw-bold"></h4>
        </div>
    
        <!-- Table Section -->
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                <h5 class="mb-0">List of Personnel</h5>
                <div class="d-flex gap-2 align-items-center">
                    <!-- Print Button -->
                    <div class="mb-0">
                        <button id="printPersonnelTable" class="btn btn-primary">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                
                <!-- Table 1: Original Personnel Table -->
                <div id="originalPersonnelTableContainer" class="table-container">
                    <div class="table-scroll-container table-responsive table-responsive-with-shadow" style="max-width: 100%; overflow-x: auto; white-space: nowrap;">
                        
                        <table id="personnelTable" class="table table-hover table-bordered table-sm table-striped">
                            <thead class="table-light sticky-header">
                                <tr>
                                    <th style="min-width: 120px; max-width: 150px;">Office</th>
                                    <th style="min-width: 60px;">Item No.</th>
                                    <th style="min-width: 120px; max-width: 150px;">Position</th>
                                    <th style="min-width: 40px;">SG</th>
                                    <th >Auth. Salary</th>
                                    <th >Actual Salary</th>
                                    <th >Step</th>
                                    <th >Code</th>
                                    <th >Type</th>
                                    <th >Level</th>
                                    <th style="min-width: 80px;">Last Name</th>
                                    <th style="min-width: 80px;">First Name</th>
                                    <th style="min-width: 80px;">Middle Name</th>
                                    <th style="min-width: 80px;">Date of Birth</th>
                                    <th >Original Appointment</th>
                                    <th style="min-width: 80px;">Last Promotion</th>
                                    <th style="min-width: 80px;">Status</th>
                                    <th style="min-width: 60px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="employeeTable">
                                <?php if(isset($personnels) && count($personnels) > 0): ?>
                                    <?php $__currentLoopData = $personnels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $personnel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="employee-data" 
                                            data-id="<?php echo e($personnel->id); ?>"
                                            data-office="<?php echo e($personnel->office); ?>"
                                            data-position="<?php echo e($personnel->position); ?>"
                                            data-status="<?php echo e($personnel->status); ?>">
                                            <td data-field="office" style="white-space: normal; word-break: break-word; overflow-wrap: break-word;">
                                                <?php echo e($offices->firstWhere('code', $personnel->office)?->name ?? $personnel->office); ?>

                                            </td>
                                            <td data-field="itemNo"><?php echo e($personnel->itemNo); ?></td>
                                            <td data-field="position" style="white-space: normal; word-break: break-word; overflow-wrap: break-word;"><?php echo e($personnel->position); ?></td>
                                            <td data-field="salaryGrade"><?php echo e($personnel->salaryGrade); ?></td>
                                            <td data-field="authorizedSalary"><?php echo e($personnel->authorizedSalary); ?></td>
                                            <td data-field="actualSalary"><?php echo e($personnel->actualSalary); ?></td>
                                            <td data-field="step"><?php echo e($personnel->step); ?></td>
                                            <td data-field="code"><?php echo e($personnel->code); ?></td>
                                            <td data-field="type"><?php echo e($personnel->type); ?></td>
                                            <td data-field="level"><?php echo e($personnel->level); ?></td>
                                            <td data-field="lastName"><?php echo e($personnel->lastName); ?></td>
                                            <td data-field="firstName"><?php echo e($personnel->firstName); ?></td>
                                            <td data-field="middleName"><?php echo e($personnel->middleName ?? ''); ?></td>
                                            <td data-field="dob"><?php echo e($personnel->dob ? $personnel->dob->format('Y-m-d') : ''); ?></td>
                                            <td data-field="originalAppointment"><?php echo e($personnel->originalAppointment ? $personnel->originalAppointment->format('Y-m-d') : ''); ?></td>
                                            <td data-field="lastPromotion"><?php echo e($personnel->lastPromotion ? $personnel->lastPromotion->format('Y-m-d') : ''); ?></td>
                                            <td data-field="status"><?php echo e($personnel->status); ?></td>
                    
                                            <!-- Add the Edit and Delete buttons in the last column -->
                                            <td>
                                                <?php if(in_array('edit', $userPermissions) || in_array('delete', $userPermissions)): ?>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-warning btn-sm plantilla-edit-btn" 
                                                            data-id="<?php echo e($personnel->id); ?>"
                                                            data-office="<?php echo e($personnel->office); ?>"
                                                            data-item-no="<?php echo e($personnel->itemNo); ?>"
                                                            data-position="<?php echo e($personnel->position); ?>"
                                                            data-salary-grade="<?php echo e($personnel->salaryGrade); ?>"
                                                            data-authorized-salary="<?php echo e($personnel->authorizedSalary); ?>"
                                                            data-actual-salary="<?php echo e($personnel->actualSalary); ?>"
                                                            data-step="<?php echo e($personnel->step); ?>"
                                                            data-code="<?php echo e($personnel->code); ?>"
                                                            data-type="<?php echo e($personnel->type); ?>"
                                                            data-level="<?php echo e($personnel->level); ?>"
                                                            data-last-name="<?php echo e($personnel->lastName); ?>"
                                                            data-first-name="<?php echo e($personnel->firstName); ?>"
                                                            data-middle-name="<?php echo e($personnel->middleName); ?>"
                                                            data-dob="<?php echo e($personnel->dob ? $personnel->dob->format('Y-m-d') : ''); ?>"
                                                            data-original-appointment="<?php echo e($personnel->originalAppointment ? $personnel->originalAppointment->format('Y-m-d') : ''); ?>"
                                                            data-last-promotion="<?php echo e($personnel->lastPromotion ? $personnel->lastPromotion->format('Y-m-d') : ''); ?>"
                                                            data-status="<?php echo e($personnel->status); ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-sm plantilla-delete-btn" 
                                                            data-id="<?php echo e($personnel->id); ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="18" class="text-center">No active personnel found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>         
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('modals'); ?>
    <?php echo $__env->make('plantilla.modal.signatureModal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('plantilla.modal.reportFormModal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <!-- Edit Personnel Modal -->
    <div class="modal fade" id="editPersonnelModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Personnel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editPersonnelForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('POST'); ?>
                    <div class="modal-body">
                        <input type="hidden" id="edit-id" name="id">
                        
                        <div class="row g-3">
                            <!-- First Column -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit-office" class="form-label">Office</label>
                                    <select class="form-select" id="edit-office" name="office" required autocomplete="organization">
                                        <?php $__currentLoopData = $offices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($office->code); ?>"><?php echo e($office->name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit-authorizedSalary" class="form-label">Authorized Salary</label>
                                    <input type="number" class="form-control" id="edit-authorizedSalary" name="authorizedSalary" required autocomplete="salary">
                                </div>

                                <div class="mb-3">
                                    <label for="edit-type" class="form-label">Type</label>
                                    <input type="text" class="form-control" id="edit-type" name="type" required autocomplete="organization-title">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit-middleName" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="edit-middleName" name="middleName" autocomplete="additional-name">
                                </div>
                            </div>

                            <!-- Second Column -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit-itemNo" class="form-label">Item No.</label>
                                    <input type="text" class="form-control" id="edit-itemNo" name="itemNo" required autocomplete="organization-item">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit-actualSalary" class="form-label">Actual Salary</label>
                                    <input type="number" class="form-control" id="edit-actualSalary" name="actualSalary" required autocomplete="salary">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit-level" class="form-label">Level</label>
                                    <input type="text" class="form-control" id="edit-level" name="level" required autocomplete="level">
                                </div>

                                <div class="mb-3">
                                    <label for="edit-dob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="edit-dob" name="dob" required autocomplete="bday">
                                </div>
                            </div>

                            <!-- Third Column -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit-position" class="form-label">Position</label>
                                    <input type="text" class="form-control" id="edit-position" name="position" required autocomplete="job-title">
                                </div>

                                <div class="mb-3">
                                    <label for="edit-step" class="form-label">Step</label>
                                    <input type="number" class="form-control" id="edit-step" name="step" required autocomplete="step">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="edit-lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="edit-lastName" name="lastName" required autocomplete="family-name">
                                </div>

                                <div class="mb-3">
                                    <label for="edit-originalAppointment" class="form-label">Original Appointment</label>
                                    <input type="date" class="form-control" id="edit-originalAppointment" name="originalAppointment" required autocomplete="appointment-date">
                                </div>
                            </div>

                            <!-- Fourth Column -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit-salaryGrade" class="form-label">Salary Grade</label>
                                    <input type="number" class="form-control" id="edit-salaryGrade" name="salaryGrade" required autocomplete="salary-grade">
                                </div>

                                <div class="mb-3">
                                    <label for="edit-code" class="form-label">Code</label>
                                    <input type="text" class="form-control" id="edit-code" name="code" required autocomplete="code">
                                </div>

                                <div class="mb-3">
                                    <label for="edit-firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="edit-firstName" name="firstName" required autocomplete="given-name">
                                </div>

                                <div class="mb-3">
                                    <label for="edit-lastPromotion" class="form-label">Last Promotion</label>
                                    <input type="date" class="form-control" id="edit-lastPromotion" name="lastPromotion" autocomplete="promotion-date">
                                </div>
                            </div>

                            <!-- Fifth Column -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="edit-status" class="form-label">Status</label>
                                    <select class="form-select" id="edit-status" name="status" required autocomplete="organization-status">
                                        <option value="regularPermanent">Regular Permanent</option>
                                        <option value="substitute">Substitute</option>
                                        <option value="temporary">Temporary</option>
                                        <option value="casual">Casual</option>
                                        <option value="contractual">Contractual</option>
                                        <option value="coterminous">Coterminous</option>
                                        <option value="elected">Elected</option>
                                        <option value="permanent">Permanent</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <script>
        const PlantillaRoute = "<?php echo e(url('/Plantilla')); ?>";
    </script>
    
    <script>
        var filterUrl = "<?php echo e(url('/filtered-personnel')); ?>";
    </script>
    
    <script>
        const vacantPositionsUrl = "<?php echo e(route('Plantilla.Pages.vacant')); ?>";
    </script>
    
    <script src="<?php echo e(asset('js/Plantilla/plantilla_filtering.js')); ?>"></script>
    <script src="<?php echo e(asset('js/Plantilla/plantilla_print.js')); ?>"></script>
    <script src="<?php echo e(asset('js/Plantilla/search.js')); ?>"></script>
    <script src="<?php echo e(asset('js/Plantilla/personnel-actions.js')); ?>"></script>

    <!-- Delete Personnel Modal -->
    <div class="modal fade" id="deletePersonnelModal" tabindex="-1" aria-labelledby="deletePersonnelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePersonnelModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this personnel record?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deletePersonnelForm" method="POST" action="" style="display: inline;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <input type="hidden" name="personnel_id" id="personnel_id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle delete personnel button click
        $(document).on('click', '.delete-personnel', function() {
            const personnelId = $(this).data('id');
            const personnelName = $(this).data('name');
            
            // Update the form action and personnel ID
            $('#deletePersonnelForm').attr('action', `/delete-personnel/${personnelId}`);
            $('#personnel_id').val(personnelId);
            
            // Update the modal message
            $('#deletePersonnelModal .modal-body').text(`Are you sure you want to delete ${personnelName}?`);
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('deletePersonnelModal'));
            modal.show();
        });
    </script>
<?php $__env->stopPush(); ?>

<style>

</style>
<?php echo $__env->make('Plantilla.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\plantilla versions\plantilla v1.9 - Copy\resources\views/Plantilla/index.blade.php ENDPATH**/ ?>