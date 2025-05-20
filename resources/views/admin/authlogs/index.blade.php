    @extends('Plantilla.sidebar')

    @section('title', 'Municipality of Magallanes - Auth Logs')

    @section('styles')
        <!-- DataTables CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
        
        <!-- Date Range Picker CSS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        
        <!-- Custom Styles -->
        <style>
            /* Table Styles */
            .table {
                font-size: 10px;
                margin-bottom: 0;
            }
            .table th {
                white-space: nowrap;
                background-color: #f8f9fa;
                position: sticky;
                top: 0;
                z-index: 1;
                padding: 0.5rem;
                border-bottom: 2px solid #dee2e6;
            }
            .table td {
                padding: 0.4rem 0.5rem;
                vertical-align: middle;
                border-bottom: 1px solid #dee2e6;
            }
            .table td:first-child,
            .table th:first-child {
                border-left: none;
            }
            .table td:last-child,
            .table th:last-child {
                border-right: none;
            }

            /* Badge Styles */
            .badge {
                font-size: 9px;
                padding: 0.25em 0.5em;
                border-radius: 4px;
            }

            /* Button Styles */
            .btn-sm {
                font-size: 10px;
                padding: 0.25rem 0.5rem;
            }

            /* DataTables Controls */
            .dataTables_wrapper {
                padding: 1rem 0;
            }
            .dataTables_wrapper .dataTables_empty {
                text-align: center;
                padding: 2rem 0;
                font-size: 12px;
                color: #6c757d;
            }
            .dataTables_wrapper .dataTables_empty::before {
                content: "\f06a";
                font-family: 'Font Awesome 5 Free';
                font-weight: 900;
                display: block;
                font-size: 24px;
                margin-bottom: 1rem;
                color: #ced4da;
            }
            .dataTables_wrapper .dataTables_empty p {
                margin: 0;
                padding: 0.5rem 0;
            }
            .dataTables_wrapper .dataTables_empty small {
                display: block;
                color: #adb5bd;
                margin-top: 0.5rem;
            }

            /* DataTables Controls */
            .dataTables_wrapper .dataTables_length {
                display: inline-block;
                margin-right: 1rem;
                font-size: 10px;
                padding: 0.5rem;
            }
            .dataTables_wrapper .dataTables_length select {
                padding: 0.25rem 0.5rem;
                border: 1px solid #ced4da;
                border-radius: 4px;
                margin-left: 0.5rem;
            }
            .dataTables_wrapper .dataTables_info {
                display: inline-block;
                margin-left: 1rem;
                font-size: 10px;
                padding: 0.5rem;
            }
            .dataTables_wrapper .dataTables_filter {
                text-align: right;
                margin-bottom: 0;
                display: inline-block;
                padding: 0.5rem;
            }
            .dataTables_wrapper .dataTables_filter input {
                font-size: 10px;
                padding: 0.25rem 0.5rem;
                border: 1px solid #ced4da;
                border-radius: 4px;
                margin-left: 0.5rem;
            }
            .dataTables_paginate {
                margin-top: 1rem;
                font-size: 10px;
                padding: 0.5rem;
            }
            .dataTables_paginate .paginate_button {
                padding: 0.25rem 0.5rem;
                border: 1px solid #ced4da;
                border-radius: 4px;
                margin: 0 2px;
                min-width: 30px;
                text-align: center;
            }
            .dataTables_paginate .paginate_button.current {
                background-color: #0d6efd;
                border-color: #0d6efd;
                color: white;
            }
            .dataTables_paginate .paginate_button:hover {
                background-color: #0b5ed7;
                border-color: #0a58ca;
            }
            .dataTables_paginate .paginate_button.disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }

            /* Form Styles */
            .form-group {
                margin-bottom: 0;
                padding: 0.5rem;
            }
            .form-control-sm {
                font-size: 10px;
                padding: 0.25rem 0.5rem;
                border: 1px solid #ced4da;
                border-radius: 4px;
            }
            label {
                font-size: 10px;
                margin-bottom: 0.5rem;
            }

            /* Date Range Picker Fix */
            .daterangepicker {
                z-index: 9999 !important;
            }
            .daterangepicker .drp-buttons {
                border-top: 1px solid #e0e0e0;
            }
            .daterangepicker .applyBtn,
            .daterangepicker .cancelBtn {
                border-radius: 4px;
                padding: 0.25rem 0.75rem;
            }
            
            /* Responsive Design */
            @media (max-width: 768px) {
                .table td,
                .table th {
                    font-size: 9px;
                    padding: 0.3rem 0.4rem;
                }
                .dataTables_wrapper .dataTables_length,
                .dataTables_wrapper .dataTables_info,
                .dataTables_wrapper .dataTables_filter {
                    display: block;
                    margin-bottom: 0.5rem;
                    padding: 0.5rem;
                }
                .dataTables_wrapper .dataTables_length select {
                    width: 100%;
                }
                .dataTables_wrapper .dataTables_filter input {
                    width: 100%;
                }
            }

            /* Custom DataTable Padding */
            .dataTables_wrapper .dt-buttons {
                padding: 0.5rem;
            }
            .dataTables_wrapper .dt-button {
                margin: 0 0.25rem;
                padding: 0.25rem 0.5rem;
            }
        </style>
    @endsection

    @section('content')
    <div class="content-wrapper">
        <div class="content-container">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body p-0">
                            <div class="p-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filterDay">Filter by Day</label>
                                            <select class="form-control form-control-sm" id="filterDay">
                                                <option value="">All Days</option>
                                                <option value="today">Today</option>
                                                <option value="yesterday">Yesterday</option>
                                                <option value="2days">Last 2 Days</option>
                                                <option value="3days">Last 3 Days</option>
                                                <option value="4days">Last 4 Days</option>
                                                <option value="5days">Last 5 Days</option>
                                                <option value="6days">Last 6 Days</option>
                                                <option value="7days">Last 7 Days</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filterWeek">Filter by Week</label>
                                            <select class="form-control form-control-sm" id="filterWeek">
                                                <option value="">All Weeks</option>
                                                <option value="current">Current Week</option>
                                                <option value="last">Last Week</option>
                                                <option value="2weeks">Last 2 Weeks</option>
                                                <option value="3weeks">Last 3 Weeks</option>
                                                <option value="4weeks">Last 4 Weeks</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="filterYear">Filter by Year</label>
                                            <select class="form-control form-control-sm" id="filterYear">
                                                <option value="">All Years</option>
                                                <option value="current">Current Year</option>
                                                <option value="last">Last Year</option>
                                                <option value="2years">Last 2 Years</option>
                                                <option value="3years">Last 3 Years</option>
                                                <option value="4years">Last 4 Years</option>
                                                <option value="5years">Last 5 Years</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="searchInput">Search</label>
                                            <input type="search" class="form-control form-control-sm" id="searchInput" placeholder="Search logs...">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="date-range-label">
                                                <label class="me-2">Date Range:</label>
                                                <span id="dateRange" class="badge bg-secondary">All Time</span>
                                            </div>
                                            <div class="reset-filters">
                                                <button class="btn btn-sm btn-outline-secondary" id="resetFilters">Reset Filters</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive p-3">
                                <table class="table table-hover table-bordered table-sm" id="authLogsTable">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>IP Address</th>
                                            <th>User Agent</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($logs) === 0)
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <div class="dataTables_empty">
                                                        <p>No authentication logs found</p>
                                                        <small>Please adjust your filters or try a different search term</small>
                                                    </div>
                                                </td>
                                            </tr>
                                        @else
                                            @foreach ($logs as $log)
                                                <tr>
                                                    <td>{{ $log->user->name ?? 'Unknown' }}</td>
                                                    <td>{{ $log->ip_address }}</td>
                                                    <td>{{ $log->user_agent }}</td>
                                                    <td>
                                                        <span class="badge {{ $log->status === 'success' ? 'bg-success' : 'bg-danger' }}">
                                                            {{ ucfirst($log->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection

    @push('scripts')
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- Moment.js -->
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
        
        <!-- Date Range Picker -->
        <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        
        <!-- DataTables -->
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

        <script>
            $(document).ready(function() {
                // Initialize DataTable
                const authLogsTable = $('#authLogsTable').DataTable({
                    responsive: true,
                    autoWidth: false,
                    scrollY: '50vh',
                    scrollCollapse: true,
                    pageLength: 25,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    order: [[4, 'desc']],
                    language: {
                        search: "",
                        searchPlaceholder: "Search logs...",
                        lengthMenu: "<select class='form-select form-select-sm'>" +
                            "<option value='10'>10</option>" +
                            "<option value='25'>25</option>" +
                            "<option value='50'>50</option>" +
                            "<option value='-1'>All</option>" +
                            "</select> entries",
                        info: "Showing <strong>_START_</strong> to <strong>_END_</strong> of <strong>_TOTAL_</strong> entries",
                        paginate: {
                            first: "<i class='fas fa-angle-double-left'></i>",
                            last: "<i class='fas fa-angle-double-right'></i>",
                            next: "<i class='fas fa-angle-right'></i>",
                            previous: "<i class='fas fa-angle-left'></i>"
                        }
                    },
                    dom: '<"top"f>rt<"bottom"<"row"<"col-md-6"i><"col-md-6 d-flex justify-content-end"l>><"col-12"p>><"clear">',
                    
                    // Add custom filtering
                    initComplete: function() {
                        // Hide default search input
                        $('.dataTables_filter input').hide();
                        
                        // Connect search input to DataTable
                        $('#searchInput').on('keyup', function() {
                            authLogsTable.search(this.value).draw();
                        });
                        
                        // Connect filter inputs to DataTable
                        $('#filterDay, #filterWeek, #filterYear').on('change', function() {
                            applyFilters();
                        });
                        
                        // Add reset filters button functionality
                        $('#resetFilters').on('click', function() {
                            $('#filterDay, #filterWeek, #filterYear').val('');
                            applyFilters();
                        });
                    },
                    
                    // Custom filtering function
                    drawCallback: function() {
                        // Get current filter values
                        let dayFilter = $('#filterDay').val();
                        let weekFilter = $('#filterWeek').val();
                        let yearFilter = $('#filterYear').val();
                        
                        // Get date ranges for each filter
                        let dayRange = getDayRange(dayFilter);
                        let weekRange = getWeekRange(weekFilter);
                        let yearRange = getYearRange(yearFilter);
                        
                        // Combine date ranges (use the most restrictive range)
                        let finalStartDate = null;
                        let finalEndDate = null;
                        
                        // Get the most restrictive start date
                        if (dayRange.startDate) finalStartDate = dayRange.startDate;
                        if (weekRange.startDate && (!finalStartDate || weekRange.startDate.isAfter(finalStartDate))) {
                            finalStartDate = weekRange.startDate;
                        }
                        if (yearRange.startDate && (!finalStartDate || yearRange.startDate.isAfter(finalStartDate))) {
                            finalStartDate = yearRange.startDate;
                        }
                        
                        // Get the most restrictive end date
                        if (dayRange.endDate) finalEndDate = dayRange.endDate;
                        if (weekRange.endDate && (!finalEndDate || weekRange.endDate.isBefore(finalEndDate))) {
                            finalEndDate = weekRange.endDate;
                        }
                        if (yearRange.endDate && (!finalEndDate || yearRange.endDate.isBefore(finalEndDate))) {
                            finalEndDate = yearRange.endDate;
                        }
                        
                        // Update date range display
                        if (finalStartDate && finalEndDate) {
                            $('#dateRange').text(finalStartDate.format('MMM D, YYYY') + ' - ' + finalEndDate.format('MMM D, YYYY'));
                        } else {
                            $('#dateRange').text('All Time');
                        }
                        
                        // Apply filtering
                        this.api().rows().every(function(rowIdx, tableLoop, rowLoop) {
                            let row = this.node();
                            let data = this.data();
                            
                            if (finalStartDate && finalEndDate) {
                                let logDate = moment(data[4]); // Assuming date is in column 4
                                if (logDate.isValid() && (logDate.isBefore(finalStartDate) || logDate.isAfter(finalEndDate))) {
                                    $(row).hide();
                                } else {
                                    $(row).show();
                                }
                            } else {
                                $(row).show();
                            }
                        });
                    }
                });

                // Add custom CSS for right alignment
                $('.dataTables_length').addClass('ms-auto');
                $('.dataTables_paginate').addClass('justify-content-end');

                // Helper function to apply all filters
                function applyFilters() {
                    authLogsTable.draw();
                }

                // Helper function to get day range
                function getDayRange(filter) {
                    let startDate = null;
                    let endDate = null;
                    
                    switch(filter) {
                        case 'today':
                            startDate = moment().startOf('day');
                            endDate = moment().endOf('day');
                            break;
                        case 'yesterday':
                            startDate = moment().subtract(1, 'days').startOf('day');
                            endDate = moment().subtract(1, 'days').endOf('day');
                            break;
                        case '2days':
                            startDate = moment().subtract(1, 'days').startOf('day');
                            endDate = moment().endOf('day');
                            break;
                        case '3days':
                            startDate = moment().subtract(2, 'days').startOf('day');
                            endDate = moment().endOf('day');
                            break;
                        case '4days':
                            startDate = moment().subtract(3, 'days').startOf('day');
                            endDate = moment().endOf('day');
                            break;
                        case '5days':
                            startDate = moment().subtract(4, 'days').startOf('day');
                            endDate = moment().endOf('day');
                            break;
                        case '6days':
                            startDate = moment().subtract(5, 'days').startOf('day');
                            endDate = moment().endOf('day');
                            break;
                        case '7days':
                            startDate = moment().subtract(6, 'days').startOf('day');
                            endDate = moment().endOf('day');
                            break;
                    }
                    
                    return { startDate, endDate };
                }

                // Helper function to get week range
                function getWeekRange(filter) {
                    let startDate = null;
                    let endDate = null;
                    
                    switch(filter) {
                        case 'current':
                            startDate = moment().startOf('week');
                            endDate = moment().endOf('week');
                            break;
                        case 'last':
                            startDate = moment().subtract(1, 'weeks').startOf('week');
                            endDate = moment().subtract(1, 'weeks').endOf('week');
                            break;
                        case '2weeks':
                            startDate = moment().subtract(2, 'weeks').startOf('week');
                            endDate = moment().endOf('week');
                            break;
                        case '3weeks':
                            startDate = moment().subtract(3, 'weeks').startOf('week');
                            endDate = moment().endOf('week');
                            break;
                        case '4weeks':
                            startDate = moment().subtract(4, 'weeks').startOf('week');
                            endDate = moment().endOf('week');
                            break;
                    }
                    
                    return { startDate, endDate };
                }

                // Helper function to get year range
                function getYearRange(filter) {
                    let startDate = null;
                    let endDate = null;
                    
                    switch(filter) {
                        case 'current':
                            startDate = moment().startOf('year');
                            endDate = moment().endOf('year');
                            break;
                        case 'last':
                            startDate = moment().subtract(1, 'years').startOf('year');
                            endDate = moment().subtract(1, 'years').endOf('year');
                            break;
                        case '2years':
                            startDate = moment().subtract(2, 'years').startOf('year');
                            endDate = moment().endOf('year');
                            break;
                        case '3years':
                            startDate = moment().subtract(3, 'years').startOf('year');
                            endDate = moment().endOf('year');
                            break;
                        case '4years':
                            startDate = moment().subtract(4, 'years').startOf('year');
                            endDate = moment().endOf('year');
                            break;
                        case '5years':
                            startDate = moment().subtract(5, 'years').startOf('year');
                            endDate = moment().endOf('year');
                            break;
                    }
                    
                    return { startDate, endDate };
                }
            });
        </script>
    @endpush
