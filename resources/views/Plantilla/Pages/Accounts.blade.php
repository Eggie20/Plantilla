@extends('Plantilla.sidebar')

@section('title', 'Municipality of Magallanes - Accounts')

@section('content')
<div class="content-wrapper">
    <div class="content-container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
                        <div class="d-flex gap-3">
                            <h5 class="m-0">Human Resources Personnel List</h5>
                            @if($lockedAccounts->count() > 0)
                                <span class="badge bg-danger">
                                    {{ $lockedAccounts->count() }} Account{{ $lockedAccounts->count() > 1 ? 's' : '' }} Locked
                                </span>
                            @endif
                        </div>
                        @if(Auth::user()->role === 'admin' && in_array('create', Auth::user()->permissions ?? []))
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fas fa-user-plus me-2"></i>Add New User
                            </button>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        <!-- Filter Section -->
                        <div class="p-3 bg-light">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="dateFilter" class="form-label">Filter By Date</label>
                                        <select class="form-select" id="dateFilter">
                                            <option value="">All Records</option>
                                            <option value="today">Today</option>
                                            <option value="week">This Week</option>
                                            <option value="month">This Month</option>
                                            <option value="year">This Year</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="startDate" class="form-label">Start Date</label>
                                        <input type="date" class="form-control" id="startDate">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="endDate" class="form-label">End Date</label>
                                        <input type="date" class="form-control" id="endDate">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="applyFilter" class="form-label">&nbsp;</label>
                                        <button type="button" class="btn btn-primary w-100" id="applyFilter">
                                            <i class="fas fa-filter me-2"></i>Apply Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Active Users Table -->
                        <div class="table-responsive mb-3">
                            <table class="table table-hover table-bordered table-sm" id="accountsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Permissions</th>
                                        @if(Auth::user()->role === 'admin' && in_array('edit', Auth::user()->permissions ?? []))
                                        <th>Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($personnel as $person)
                                    <tr>
                                        <td>{{ $person->name }}</td>
                                        <td>{{ $person->username }}</td>
                                        <td>
                                            <span class="badge {{ $person->role === 'admin' ? 'bg-success' : 'bg-primary' }}">
                                                {{ ucfirst($person->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">Active</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $person->created_at ? $person->created_at->format('Y-m-d') : 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                @php
                                                    $permissions = $person->permissions ?? [];
                                                    if (is_string($permissions)) {
                                                        $permissions = json_decode($permissions, true) ?? [];
                                                    }
                                                    $defaultPermissions = [
                                                        'view' => 'View Records',
                                                        'create' => 'Create Records',
                                                        'edit' => 'Edit Records',
                                                        'approve' => 'Approve Requests'
                                                    ];
                                                @endphp
                                                @foreach($defaultPermissions as $key => $label)
                                                    <span class="badge {{ in_array($key, $permissions) ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $label }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <!-- Edit Button -->
                                                @if(Auth::user()->role === 'superadmin' || 
                                                    (Auth::user()->role === 'admin' && $person->role !== 'superadmin' && $person->role !== 'admin'))
                                                    <button type="button" class="btn btn-sm btn-warning edit-user-btn" 
                                                        data-id="{{ $person->id }}"
                                                        data-name="{{ $person->name }}"
                                                        data-username="{{ $person->username }}"
                                                        data-role="{{ $person->role }}"
                                                        data-permissions="{{ json_encode($permissions) }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editUserModal">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                @endif
                                                
                                                <!-- Lock/Unlock Buttons -->
                                                @if($person->locked && !$person->blocked)
                                                    <button type="button" class="btn btn-sm btn-warning unlock-user-btn"
                                                        data-username="{{ $person->username }}"
                                                        data-name="{{ $person->name }}"
                                                        data-user-id="{{ $person->id }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#unlockUserModal">
                                                        <i class="fas fa-unlock me-1"></i> Unlock
                                                    </button>
                                                @elseif($person->blocked)
                                                    <button type="button" class="btn btn-sm btn-success unlock-user-btn"
                                                        data-username="{{ $person->username }}"
                                                        data-name="{{ $person->name }}"
                                                        data-user-id="{{ $person->id }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#unlockUserModal">
                                                        <i class="fas fa-unlock me-1"></i> Unblock
                                                    </button>
                                                @else
                                                    <!-- Lock Button -->
                                                    @if(Auth::user()->role === 'superadmin' || 
                                                        (Auth::user()->role === 'admin' && $person->role !== 'superadmin' && $person->role !== 'admin'))
                                                        <button type="button" class="btn btn-sm btn-warning lock-user-btn"
                                                            data-username="{{ $person->username }}"
                                                            data-name="{{ $person->name }}"
                                                            data-user-id="{{ $person->id }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#lockUserModal">
                                                            <i class="fas fa-lock"></i> Lock
                                                        </button>
                                                    @endif
                                                    
                                                    <!-- Block Button -->
                                                    @if(Auth::user()->role === 'superadmin' || 
                                                        (Auth::user()->role === 'admin' && $person->role !== 'superadmin' && $person->role !== 'admin'))
                                                        <button type="button" class="btn btn-sm btn-danger block-user-btn"
                                                            data-username="{{ $person->username }}"
                                                            data-name="{{ $person->name }}"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#blockUserModal">
                                                            <i class="fas fa-lock"></i> Block
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Locked Accounts Table -->
                        @if($lockedAccounts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-sm">
                                <thead>
                                    <tr>
                                        <th colspan="6" class="bg-danger text-white">
                                            <h6 class="m-0">Locked Accounts</h6>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Username</th>
                                        <th>IP Address</th>
                                        <th>Locked Until</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lockedAccounts as $account)
                                    <tr>
                                        <td>{{ $account->username }}</td>
                                        <td>{{ $account->ip_address }}</td>
                                        <td>
                                            <span class="badge bg-danger">
                                                {{ \Carbon\Carbon::parse($account->locked_until)->format('F j, Y H:i') }}
                                            </span>
                                        </td>
                                        <td>
                                            <form action="{{ route('auth.unlock') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="username" value="{{ $account->username }}">
                                                <input type="hidden" name="ip_address" value="{{ $account->ip_address }}">
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-unlock me-1"></i> Unlock
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('Plantilla.Modal.user.userModal')

@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Check if table exists before initializing
            if ($('#accountsTable').length) {
                // Destroy existing table if it exists
                if ($.fn.DataTable.isDataTable('#accountsTable')) {
                    $('#accountsTable').DataTable().destroy();
                }

                // Get the number of columns (excluding the conditional actions column)
                const baseColumns = 6; // Name, Username, Role, Status, Created At, Permissions
                const hasActionsColumn = $('#accountsTable thead th:last-child').text().trim() === 'Actions';
                const totalColumns = baseColumns + (hasActionsColumn ? 1 : 0);

                // Initialize DataTable
                const accountsTable = $('#accountsTable').DataTable({
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search records..."
                    },
                    responsive: {
                        details: {
                            type: 'column'
                        }
                    },
                    // Define column definitions
                    columnDefs: [
                        { targets: 0, data: 'name' }, // Name
                        { targets: 1, data: 'username' }, // Username
                        { targets: 2, data: 'role' }, // Role
                        { targets: 3, data: 'status' }, // Status
                        { targets: 4, data: 'created_at' }, // Created At
                        { targets: 5, data: 'permissions' }, // Permissions
                        // Add actions column if it exists
                        hasActionsColumn ? { targets: 6, data: 'actions' } : null
                    ].filter(Boolean),
                    // Order by created_at in descending order
                    order: [[4, 'desc']],
                    // Prevent DataTable from trying to get data from server
                    serverSide: false,
                    // Process data locally
                    ajax: false,
                    // Don't try to parse the data
                    data: null
                });

                // Store table instance globally
                window.accountsTable = accountsTable;

                // Handle date filter changes
                $('#dateFilter').on('change', function() {
                    const filter = $(this).val();
                    $('#applyFilter').click();
                });

                // Handle filter application
                $('#applyFilter').on('click', function() {
                    const dateFilter = $('#dateFilter').val();
                    const startDate = $('#startDate').val();
                    const endDate = $('#endDate').val();

                    const url = new URL(window.location.href);
                    url.searchParams.set('filter', dateFilter);
                    if (startDate) url.searchParams.set('start_date', startDate);
                    if (endDate) url.searchParams.set('end_date', endDate);
                    window.location.href = url.toString();
                });

                // Set filter from URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const selectedFilter = urlParams.get('filter');
                if (selectedFilter) {
                    $('#dateFilter').val(selectedFilter);
                }

                const startDate = urlParams.get('start_date');
                const endDate = urlParams.get('end_date');
                if (startDate && endDate) {
                    $('#startDate').val(startDate);
                    $('#endDate').val(endDate);
                    $('#dateFilter').val(''); // Clear date filter if date range is set
                }

                // Reinitialize DataTables after modal closes
                $('.modal').on('hidden.bs.modal', function() {
                    if (accountsTable && accountsTable.responsive) {
                        accountsTable.responsive.recalc();
                    }
                });

                // Handle lock/unlock user button
                $('.lock-user-btn, .unlock-user-btn').on('click', function() {
                    const username = $(this).data('username');
                    const name = $(this).data('name');
                    const userId = $(this).data('user-id');
                    const isLocked = $(this).hasClass('lock-user-btn');
                    
                    // Show appropriate modal
                    const modalId = isLocked ? '#lockUserModal' : '#unlockUserModal';
                    $(modalId).find('#username').val(username);
                    $(modalId).find('#user-name').text(name);
                    $(modalId).modal('show');
                });

                // Handle lock/unlock confirmation
                $('#lockUserModal, #unlockUserModal').on('click', '.confirm-action', function() {
                    const modal = $(this).closest('.modal');
                    const userId = modal.find('#username').data('user-id');
                    const isLock = modal.attr('id') === 'lockUserModal';
                    
                    $.ajax({
                        url: isLock ? `/users/${userId}/block` : `/users/${userId}/unblock`,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload(); // Refresh page to show updated status
                            } else {
                                alert(response.error || 'Failed to ' + (isLock ? 'lock' : 'unlock') + ' account.');
                            }
                        },
                        error: function(xhr) {
                            try {
                                const error = JSON.parse(xhr.responseText);
                                alert(error.error || 'An error occurred while processing your request.');
                            } catch (e) {
                                alert('An unexpected error occurred while processing your request.');
                            }
                        }
                    });
                });
            }
        });
    </script>
@endpush

<style>
.content-wrapper {
    height: calc(100vh - 60px);
    margin: 0;
    padding: 1rem;
}

.content-container {
    height: 100%;
}

.table-responsive {
    max-height: calc(100vh - 250px);
}

.table {
    margin-bottom: 0;
    width: auto;
    font-size: 10px;
}

.table th {
    white-space: nowrap;
    background-color: #f8f9fa;
    position: sticky;
    top: 0;
    z-index: 1;
    padding: 0.5rem;
}

.table td {
    padding: 0.4rem 0.5rem;
    vertical-align: middle;
}

/* Column widths */
.table th:nth-child(1), .table td:nth-child(1) { width: 150px; } /* Name */
.table th:nth-child(2), .table td:nth-child(2) { width: 100px; } /* Username */
.table th:nth-child(3), .table td:nth-child(3) { width: 80px; }  /* Role */
.table th:nth-child(4), .table td:nth-child(4) { width: 80px; }  /* Status */
.table th:nth-child(5), .table td:nth-child(5) { width: 200px; } /* Permissions */
.table th:nth-child(6), .table td:nth-child(6) { width: 120px; } /* Actions */

/* Card styling */
.card {
    height: 100%;
    display: flex;
    flex-direction: column;
}

.card-body {
    flex: 1;
    overflow: auto;
}

/* DataTables customization */
.dataTables_wrapper .dataTables_filter input {
    font-size: 10px;
    height: 30px;
}

.dataTables_wrapper .dataTables_length select {
    font-size: 10px;
    height: 30px;
}

.dataTables_info, .dataTables_paginate {
    font-size: 10px;
}

/* Badge adjustments */
.badge {
    font-size: 9px;
    padding: 0.25em 0.5em;
}

/* Button size adjustments */
.btn-sm {
    font-size: 10px;
    padding: 0.25rem 0.5rem;
}
</style>