

<?php $__env->startSection('title', 'Municipality of Magallanes - Years of Service'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Success and Error Messages -->
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Personnel Years of Service</h3>
                    <button type="button" class="btn btn-primary" id="generateReportBtn">
                        <i class="fas fa-file-export me-2"></i>Generate Report
                    </button>
                </div>
                <div class="card-body">
                    <!-- Filter and Search Section -->
                    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                        <!-- Filters -->
                        <div class="d-flex gap-2 flex-grow-1 flex-wrap">
                            <select id="officeFilter" class="form-select" style="max-width: 250px;">
                                <option value="">All Offices</option>
                                <?php $__currentLoopData = $offices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $office): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($office->code); ?>"><?php echo e($office->name); ?> (<?php echo e($office->abbreviation); ?>)</option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                            <select id="serviceYearsFilter" class="form-select" style="max-width: 200px;">
                                <option value="">All Service Years</option>
                                <option value="0-5">0-5 Years</option>
                                <option value="6-10">6-10 Years</option>
                                <option value="11-15">11-15 Years</option>
                                <option value="16-20">16-20 Years</option>
                                <option value="21-25">21-25 Years</option>
                                <option value="26+">26+ Years</option>
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
                                        placeholder="Search personnel..."
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

                    <div class="table-responsive">
                        <!-- No data message -->
                        <div id="noDataMessage" class="text-center d-none">
                            <i class="fas fa-info-circle me-2"></i>
                            <span>No personnel found matching your search criteria.</span>
                        </div>
                        
                        <table class="table table-hover table-bordered" id="serviceYearsTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Office</th>
                                    <th>Position</th>
                                    <th>Original Appointment</th>
                                    <th>Years of Service</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $personnels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $personnel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    // Calculate years of service
                                    $originalAppointment = \Carbon\Carbon::parse($personnel->originalAppointment);
                                    $today = \Carbon\Carbon::now();
                                    $yearsOfService = floor($originalAppointment->diffInYears($today));
                                    // Ensure years of service is not negative
                                    $yearsOfService = max(0, $yearsOfService);
                                ?>
                                <tr>
                                    <td><?php echo e($personnel->lastName); ?>, <?php echo e($personnel->firstName); ?> <?php echo e($personnel->middleName); ?></td>
                                    <td>
                                        <?php if(is_object($personnel->office) && property_exists($personnel->office, 'abbreviation')): ?>
                                            <?php echo e($personnel->office->abbreviation); ?>

                                        <?php else: ?>
                                            <?php echo e($personnel->office ?? 'N/A'); ?>

                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($personnel->position); ?></td>
                                    <td><?php echo e($originalAppointment->format('M d, Y')); ?></td>
                                    <td><?php echo e($yearsOfService); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Filter functions
        function filterTable() {
            const officeFilter = $('#officeFilter').val();
            const serviceYearsFilter = $('#serviceYearsFilter').val();
            const searchValue = $('#tableSearch').val().toLowerCase();
            
            let found = false;
            const rows = $('#serviceYearsTable tbody tr');
            
            rows.each(function() {
                const row = $(this);
                const text = row.text().toLowerCase();
                const officeCell = row.find('td:eq(1)').text().toLowerCase();
                const yearsCell = parseInt(row.find('td:eq(4)').text());
                
                // Check office filter
                const officeMatch = !officeFilter || officeCell.includes(officeFilter.toLowerCase());
                
                // Check service years filter
                let yearsMatch = true;
                if (serviceYearsFilter) {
                    const [min, max] = serviceYearsFilter.split('-');
                    if (max === '+') {
                        yearsMatch = yearsCell >= parseInt(min);
                    } else {
                        yearsMatch = yearsCell >= parseInt(min) && yearsCell <= parseInt(max);
                    }
                }
                
                // Check search
                const searchMatch = !searchValue || text.includes(searchValue);
                
                // Show/hide row based on all filters
                if (officeMatch && yearsMatch && searchMatch) {
                    row.show();
                    found = true;
                } else {
                    row.hide();
                }
            });
            
            $('#noDataMessage').toggleClass('d-none', found);
        }

        // Print functionality
        $('#generateReportBtn').click(function() {
            // Create a new window for printing
            const printWindow = window.open('', '_blank', 'width=800,height=600');
            
            // Get current date
            const currentDate = new Date();
            const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = currentDate.toLocaleDateString('en-US', dateOptions);
            
            // Get current filter values for the report title
            const office = $('#officeFilter').val();
            const serviceYears = $('#serviceYearsFilter').val();
            
            let reportTitle = 'Personnel Years of Service Report';
            let filterDescription = '';
            
            if (office) {
                const officeSelect = $('#officeFilter');
                const officeText = officeSelect.find('option:selected').text();
                filterDescription += `${officeText} | `;
            }
            
            if (serviceYears) {
                const yearsSelect = $('#serviceYearsFilter');
                const yearsText = yearsSelect.find('option:selected').text();
                filterDescription += `Years of Service: ${yearsText} | `;
            }
            
            // Remove trailing separator if exists
            if (filterDescription) {
                filterDescription = filterDescription.slice(0, -3);
                reportTitle += ` (${filterDescription})`;
            }
            
            // Get the table content
            const table = $('#serviceYearsTable');
            const rows = table.find('tbody tr:visible');
            
            let tableContent = '';
            rows.each(function() {
                const row = $(this);
                tableContent += `
                    <tr>
                        <td>${row.find('td:eq(0)').text()}</td>
                        <td>${row.find('td:eq(1)').text()}</td>
                        <td>${row.find('td:eq(2)').text()}</td>
                        <td>${row.find('td:eq(3)').text()}</td>
                        <td>${row.find('td:eq(4)').text()}</td>
                    </tr>
                `;
            });
            
            // Create print content
            const printContent = `
                <html>
                <head>
                    <title>${reportTitle}</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 10px; 
                            font-size: 11px;
                        }
                        .header {
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        .title {
                            font-size: 14px;
                            font-weight: bold;
                        }
                        .date {
                            font-size: 10px;
                            margin-top: 5px;
                        }
                        .table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 20px;
                        }
                        th, td {
                            border: 1px solid #000;
                            padding: 5px;
                            text-align: left;
                        }
                        th {
                            background-color: #f8f9fa;
                            font-weight: bold;
                        }
                        .footer {
                            margin-top: 30px;
                            text-align: center;
                            font-size: 10px;
                        }
                        .certification {
                            margin-top: 20px;
                            text-align: left;
                            font-size: 10px;
                            line-height: 1.3;
                        }
                        .action-buttons {
                            position: fixed;
                            top: 10px;
                            right: 10px;
                            display: flex;
                            gap: 10px;
                            z-index: 1000;
                        }
                        .action-button {
                            padding: 8px 16px;
                            border: none;
                            border-radius: 4px;
                            cursor: pointer;
                            font-size: 12px;
                        }
                        .print-button {
                            background-color: #28a745;
                            color: white;
                        }
                        .close-button {
                            background-color: #dc3545;
                            color: white;
                        }
                    </style>
                </head>
                <body>
                    <div class="action-buttons">
                        <button class="action-button print-button" onclick="window.print()">Print Report</button>
                        <button class="action-button close-button" onclick="window.close()">Close Preview</button>
                    </div>
                    
                    <div class="header">
                        <div class="title">${reportTitle}</div>
                        <div class="date">As of ${formattedDate}</div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Office</th>
                                    <th>Position</th>
                                    <th>Original Appointment</th>
                                    <th>Years of Service</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableContent}
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="footer">
                        <div class="certification">
                            I certify to the correctness of the entries and that the above information is accurate and up-to-date.
                        </div>
                    </div>
                </body>
                </html>
            `;
            
            printWindow.document.write(printContent);
            printWindow.document.close();
        });

        // Search functionality
        $('#tableSearch').on('input', function() {
            filterTable();
        });

        // Office filter
        $('#officeFilter').on('change', function() {
            filterTable();
        });

        // Service years filter
        $('#serviceYearsFilter').on('change', function() {
            filterTable();
        });

        // Clear search
        $('#clearSearch').on('click', function() {
            $('#tableSearch').val('');
            $('#officeFilter').val('');
            $('#serviceYearsFilter').val('');
            $('#serviceYearsTable tbody tr').show();
            $('#noDataMessage').addClass('d-none');
        });
    });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('Plantilla.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\plantilla versions\plantilla v1.9 - Copy\resources\views/Plantilla/Pages/YearsOfService.blade.php ENDPATH**/ ?>