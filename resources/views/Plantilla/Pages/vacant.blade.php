@extends('Plantilla.sidebar')

@section('title', 'Municipality of Magallanes - Vacant Positions')

@push('styles')
<link rel="stylesheet" href="/css/Plantilla/vacant/vacant.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="user-permissions" content="{{ json_encode(auth()->user()->permissions) }}">
<style>
    .invalid-feedback {
        display: block;
    }
</style>
@endpush

@section('content')
    <div class="row justify-content-center content-container">
        <div class="col-12" style="max-width: 1400px;">
            <div class="card shadow-sm mx-auto p-3 card-custom-padding">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Position Management</h3>
                    @if(in_array('edit', auth()->user()->permissions) || in_array('create', auth()->user()->permissions))
                    <div>
                        <button type="button" class="btn btn-primary btn-sm" id="addNewPositionBtn">
                            <i class="fas fa-plus me-1"></i>Add New Position
                        </button>
                        <!-- New Add Office Button -->
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addOfficeModal">
                            <i class="fas fa-plus me-1"></i>Add Office
                        </button>
                    </div>
                    @endif
                </div>
                <div class="card-body">
                    <!-- Alert Container -->
                    <div id="alertContainer" class="mb-3"></div>
                    
                    <!-- Tabs for Vacant and Manage Offices -->
                    <ul class="nav nav-tabs mb-3" id="positionTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="vacant-tab" data-bs-toggle="tab" data-bs-target="#vacant-positions" 
                                    type="button" role="tab" aria-controls="vacant-positions" aria-selected="true">
                                Vacant Positions
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="manage-offices-tab" data-bs-toggle="tab" data-bs-target="#manage-offices" 
                                    type="button" role="tab" aria-controls="manage-offices" aria-selected="false">
                                Manage Offices
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="positionTabContent">
                        <!-- Vacant Positions Tab -->
                        <div class="tab-pane fade show active" id="vacant-positions" role="tabpanel" aria-labelledby="vacant-tab">
                            <!-- Search and Filter Controls -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" id="positionSearch" placeholder="Search vacant positions...">
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item No.</th>
                                            <th>Office</th>
                                            <th>Position</th>
                                            <th>SG</th>
                                            <th>Step</th>
                                            <th>Code</th>
                                            <th>Type</th>
                                            <th>Level</th>
                                            <th>Status</th>
                                            @if(in_array('edit', auth()->user()->permissions) || in_array('create', auth()->user()->permissions))
                                            <th class="text-center">Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody id="vacantTableBody">
                                    </tbody>
                                </table>
                                
                                <!-- No vacant positions message -->
                                <div id="noVacantData" class="alert alert-info text-center" style="display: none;">
                                    No vacant positions found
                                </div>
                            </div>
                        </div>
                        
                        <!-- Manage Offices Tab -->
                        <div class="tab-pane fade" id="manage-offices" role="tabpanel" aria-labelledby="manage-offices-tab">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="officesTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Code</th>
                                            <th>Office Name</th>
                                            <th>Abbreviation</th>
                                            <th>Parent Office</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Offices will be loaded here dynamically -->
                                    </tbody>
                                </table>
                            </div>
                            <div id="noOfficesMessage" class="alert alert-info text-center" style="display: none;">
                                No offices found
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Add Position Modal -->
<div class="modal fade" id="positionModal" tabindex="-1" aria-labelledby="positionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="positionModalLabel">Add New Position</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="positionForm" action="{{ route('position.store') }}" method="POST" novalidate>
                @csrf
                <input type="hidden" id="position_id" name="id">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="itemNo" class="form-label">Item Number</label>
                            <input type="text" class="form-control" id="itemNo" name="itemNo" required pattern="[0-9-]*">
                            <div class="invalid-feedback">Please enter numbers and hyphens only</div>
                        </div>
                        <div class="col-md-6">
                            <label for="office_id" class="form-label">Office</label>
                            <select class="form-select" id="office_id" name="office_id" required>
                                <option value="">Select Office</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback">Please fill out this field</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="position" class="form-label">Position Title</label>
                            <input type="text" class="form-control" id="position" name="position" required pattern="[A-Z.\'\s]*">
                            <div class="invalid-feedback">Please enter capital letters, periods, apostrophes, and spaces only</div>
                        </div>
                        <div class="col-md-6">
                            <label for="salaryGrade" class="form-label">Salary Grade</label>
                            <select class="form-select" id="salaryGrade" name="salaryGrade" required>
                                <option value="">Select Salary Grade</option>
                                @for($i = 1; $i <= 24; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <div class="invalid-feedback">Please fill out this field</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="step" class="form-label">Step</label>
                            <select class="form-select" id="step" name="step" required>
                                <option value="">Select Step</option>
                                @for($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                            <div class="invalid-feedback">Please fill out this field</div>
                        </div>
                        <div class="col-md-4">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" name="code" required pattern="\d{2}">
                            <div class="invalid-feedback">Please enter exactly 2 digits</div>
                        </div>
                        <div class="col-md-4">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="M">M</option>
                            </select>
                            <div class="invalid-feedback">Please fill out this field</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="level" class="form-label">Level</label>
                            <select class="form-select" id="level" name="level" required>
                                <option value="">Select Level</option>
                                <option value="K">K</option>
                                <option value="A">A</option>
                            </select>
                            <div class="invalid-feedback">Please select either K or A</div>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Vacant">Vacant</option>
                                <option value="Unfunded">Unfunded</option>
                            </select>
                            <div class="invalid-feedback">Please fill out this field</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Save Position</button>
                </div>
            </form>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const form = document.getElementById('positionForm');
                    const submitBtn = document.getElementById('submitBtn');
                    const itemNoInput = document.getElementById('itemNo');
                    
                    // Add real-time validation for itemNo
                    itemNoInput.addEventListener('input', function(e) {
                        const value = e.target.value;
                        // Remove any character that is not a number or hyphen
                        const cleanedValue = value.replace(/[^0-9-]/g, '');
                        if (cleanedValue !== value) {
                            e.target.value = cleanedValue;
                        }
                    });

                    // Add real-time validation for position
                    const positionInput = document.getElementById('position');
                    positionInput.addEventListener('input', function(e) {
                        const value = e.target.value;
                        // Remove any character that is not a capital letter, period, apostrophe, or space
                        const cleanedValue = value.replace(/[^A-Z.\'\s]/g, '');
                        
                        // Automatically remove invalid characters
                        e.target.value = cleanedValue;
                        
                        // Show error message if invalid characters were removed
                        if (cleanedValue !== value) {
                            e.target.classList.add('is-invalid');
                            e.target.setCustomValidity('Please enter capital letters, periods, apostrophes, and spaces only');
                        } else {
                            e.target.classList.remove('is-invalid');
                            e.target.setCustomValidity('');
                        }
                    });

                    // Add real-time validation for code
                    const codeInput = document.getElementById('code');
                    codeInput.addEventListener('input', function(e) {
                        const value = e.target.value;
                        // Remove any character that is not a digit
                        const cleanedValue = value.replace(/[^0-9]/g, '');
                        
                        // Automatically remove invalid characters
                        e.target.value = cleanedValue;
                        
                        // Show error message if invalid characters were removed
                        if (cleanedValue !== value) {
                            e.target.classList.add('is-invalid');
                            e.target.setCustomValidity('Please enter exactly 2 digits');
                        } else {
                            e.target.classList.remove('is-invalid');
                            e.target.setCustomValidity('');
                        }
                    });

                    // Add real-time validation for office name
                    const officeNameInput = document.getElementById('officeName');
                    officeNameInput.addEventListener('input', function(e) {
                        const value = e.target.value;
                        // Remove any character that is not a capital letter, comma, period, ampersand, or space
                        const cleanedValue = value.replace(/[^A-Z.,&\s]/g, '');
                        
                        // Automatically remove invalid characters
                        e.target.value = cleanedValue;
                        
                        // Show error message if invalid characters were removed
                        if (cleanedValue !== value) {
                            e.target.classList.add('is-invalid');
                            e.target.setCustomValidity('Please enter capital letters, commas, periods, ampersands, and spaces only');
                        } else {
                            e.target.classList.remove('is-invalid');
                            e.target.setCustomValidity('');
                        }
                    });

                    // Add real-time validation for office abbreviation
                    const abbreviationInput = document.getElementById('officeAbbreviation');
                    abbreviationInput.addEventListener('input', function(e) {
                        const value = e.target.value;
                        // Remove any character that is not a capital letter
                        const cleanedValue = value.replace(/[^A-Z]/g, '');
                        
                        // Automatically remove invalid characters
                        e.target.value = cleanedValue;
                        
                        // Show error message if invalid characters were removed
                        if (cleanedValue !== value) {
                            e.target.classList.add('is-invalid');
                            e.target.setCustomValidity('Please enter capital letters only');
                        } else {
                            e.target.classList.remove('is-invalid');
                            e.target.setCustomValidity('');
                        }
                    });

                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        
                        // Check if form is valid
                        let isValid = true;
                        const inputs = form.querySelectorAll('input[required], select[required]');
                        
                        inputs.forEach(input => {
                            if (!input.value.trim()) {
                                isValid = false;
                                input.classList.add('is-invalid');
                            } else {
                                input.classList.remove('is-invalid');
                            }
                        });

                        // Validate itemNo format
                        const itemNoValue = itemNoInput.value.trim();
                        if (itemNoValue && !/^[0-9-]+$/.test(itemNoValue)) {
                            isValid = false;
                            itemNoInput.classList.add('is-invalid');
                            itemNoInput.setCustomValidity('Please enter numbers and hyphens only');
                        } else {
                            itemNoInput.classList.remove('is-invalid');
                            itemNoInput.setCustomValidity('');
                        }
                        


                        if (isValid) {
                            form.submit();
                        }
                    });
                });
            </script>
        </div>
    </div>
</div>

<!-- Assign Personnel Modal -->
<div class="modal fade" id="assignPersonnelModal" tabindex="-1" aria-labelledby="assignPersonnelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="assignPersonnelModalLabel">Assign Personnel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="assignPersonnelForm" method="POST" action="{{ route('personnel.assign') }}">
                    @csrf
                    <!-- Position Information (Read-only) -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Position Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="assign_office_name" class="form-label">Office</label>
                                    <input type="text" class="form-control" id="assign_office_name" readonly>
                                    <input type="hidden" id="assign_office_id" name="office_id">
                                </div>
                                <div class="col-md-6">
                                    <label for="assign_item_no" class="form-label">Item No.</label>
                                    <input type="text" class="form-control" id="assign_item_no" name="itemNo" readonly>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="assign_position_title" class="form-label">Position Title</label>
                                    <input type="text" class="form-control" id="assign_position_title" name="position" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="assign_salary_grade" class="form-label">Salary Grade</label>
                                    <input type="text" class="form-control" id="assign_salary_grade" name="salaryGrade" readonly>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="assign_code" class="form-label">Code</label>
                                    <input type="text" class="form-control" id="assign_code" name="code" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="assign_type" class="form-label">Type</label>
                                    <input type="text" class="form-control" id="assign_type" name="type" readonly>
                                </div>
                                <div class="col-md-4">
                                    <label for="assign_level" class="form-label">Level</label>
                                    <input type="text" class="form-control" id="assign_level" name="level" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Personnel Details -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Personnel Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="assign_lastName" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="assign_lastName" name="lastName" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="assign_firstName" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="assign_firstName" name="firstName" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="assign_middleName" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="assign_middleName" name="middleName">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="assign_dob" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="assign_dob" name="dob">
                                </div>
                                <div class="col-md-4">
                                    <label for="assign_originalAppointment" class="form-label">Date of Original Appointment</label>
                                    <input type="date" class="form-control" id="assign_originalAppointment" name="originalAppointment" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="assign_lastPromotion" class="form-label">Date of Last Promotion</label>
                                    <input type="date" class="form-control" id="assign_lastPromotion" name="lastPromotion">
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="assign_authorizedSalary" class="form-label">Authorized Salary</label>
                                    <input type="text" class="form-control" id="assign_authorizedSalary" name="authorizedSalary" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="assign_actualSalary" class="form-label">Actual Salary</label>
                                    <input type="text" class="form-control" id="assign_actualSalary" name="actualSalary" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="assign_step" class="form-label">Step</label>
                                    <input type="text" class="form-control" id="assign_step" name="step">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <label for="assign_status" class="form-label">Status</label>
                                    <select id="assign_status" name="status" class="form-select" required>
                                        <option value="" disabled selected>Select Status</option>
                                        <option value="casual">Casual</option>
                                        <option value="contractual">Contractual</option>            
                                        <option value="coterminous">Coterminous</option>
                                        <option value="coterminousTemporary">Coterminous-Temporary</option>
                                        <option value="elected">Elected</option>
                                        <option value="permanent">Permanent</option>
                                        <option value="provisional">Provisional</option>
                                        <option value="substitute">Substitute</option>
                                        <option value="temporary">Temporary</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                        </div>
                        
                    <input type="hidden" id="assign_position_id" name="position_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Assign Personnel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Office Management Modal -->
<div class="modal fade" id="addOfficeModal" tabindex="-1" aria-labelledby="addOfficeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addOfficeModalLabel">Office Management</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Success Alert Template (hidden by default) -->
                <div id="officeSuccessAlert" class="alert alert-success alert-dismissible fade" role="alert" style="display: none; margin-bottom: 1rem;">
                    <span class="alert-message"></span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <!-- Tabs for Add/Manage -->
                <ul class="nav nav-tabs" id="officeModalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="add-office-tab" data-bs-toggle="tab" data-bs-target="#add-office" type="button" role="tab" aria-controls="add-office" aria-selected="true">Add Office</button>
                    </li>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content mt-3" id="officeModalTabContent">
                    <!-- Add Office Tab -->
                    <div class="tab-pane fade show active" id="add-office" role="tabpanel" aria-labelledby="add-office-tab">
                        <form id="addOfficeForm" class="needs-validation" novalidate>
                            @csrf
                            <!-- Parent Office Selection -->
                            <div class="mb-3">
                                <label for="parentOffice" class="form-label">Parent Office (Optional)</label>
                                <select class="form-select" id="parentOffice" name="parent_id">
                                    <option value="">None (Main Office)</option>
                                </select>
                                <div class="form-text">Select a parent office if this is a sub-office.</div>
                            </div>
                            <div class="mb-3">
                                <label for="officeCode" class="form-label">Office Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="officeCode" name="code" placeholder="e.g., mao, hrmo" required>
                                <div class="invalid-feedback">
                                    Please provide an office code.
                                </div>
                           </div>
                            <div class="mb-3">
                                <label for="officeName" class="form-label">Office Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control auto-capitalize" id="officeName" name="name" placeholder="e.g., MUNICIPAL AGRICULTURE OFFICE" required pattern="[A-Z.,&\s]*">
                                <div class="invalid-feedback">
                                    Please provide an office name.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="officeAbbreviation" class="form-label">Abbreviation <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="officeAbbreviation" name="abbreviation" placeholder="e.g., MAO" required pattern="[A-Z]*">
                                <div class="invalid-feedback">
                                    Please provide an abbreviation.
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary" id="saveOfficeBtn">Save Office</button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Manage Offices Tab -->
                    <div class="tab-pane fade" id="manage-offices" role="tabpanel" aria-labelledby="manage-offices-tab">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="officesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Code</th>
                                        <th>Office Name</th>
                                        <th>Abbreviation</th>
                                        <th>Parent Office</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Offices will be loaded here dynamically -->
                                </tbody>
                            </table>
                        </div>
                        <div id="noOfficesMessage" class="alert alert-info text-center" style="display: none;">
                            No offices found
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Office Modal -->
<div class="modal fade" id="editOfficeModal" tabindex="-1" aria-labelledby="editOfficeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editOfficeModalLabel">Edit Office</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editOfficeForm">
                    @csrf
                    <input type="hidden" id="edit_office_id" name="id">
                    <div class="mb-3">
                        <label for="edit_office_code" class="form-label">Office Code</label>
                        <input type="text" class="form-control" id="edit_office_code" name="code" required pattern="[A-Z0-9\-]*" title="Office code can only contain uppercase letters, numbers, and hyphens" maxlength="20">
                        <div class="invalid-feedback">Please enter a valid office code (letters, numbers, and hyphens only)</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_office_name" class="form-label">Office Name</label>
                        <input type="text" class="form-control" id="edit_office_name" name="name" required pattern="[A-Za-z\s]*" title="Office name can only contain letters and spaces" maxlength="255">
                        <div class="invalid-feedback">Please enter a valid office name (letters and spaces only)</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_office_abbreviation" class="form-label">Abbreviation</label>
                        <input type="text" class="form-control" id="edit_office_abbreviation" name="abbreviation" required pattern="[A-Z\s]*" title="Abbreviation can only contain uppercase letters and spaces" maxlength="50">
                        <div class="invalid-feedback">Please enter a valid abbreviation (uppercase letters and spaces only)</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_office_parent" class="form-label">Parent Office (Optional)</label>
                        <select class="form-select" id="edit_office_parent" name="parent_id">
                            <option value="">None (Main Office)</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="updateOfficeBtn">Update Office</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Office Confirmation Modal -->
<div class="modal fade" id="deleteOfficeModal" tabindex="-1" aria-labelledby="deleteOfficeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteOfficeModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the office: <strong id="officeToDelete"></strong>?</p>
                <p>This action cannot be undone. Any personnel assigned to this office will need to be reassigned.</p>
                <input type="hidden" id="deleteOfficeId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteOfficeBtn">Delete Office</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deletePositionModal" tabindex="-1" aria-labelledby="deletePositionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deletePositionModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this position?</p>
                <p><strong>Item No:</strong> <span id="deleteItemNo"></span></p>
                <p><strong>Position:</strong> <span id="deletePosition"></span></p>
                
                <!-- Hidden form for deletion -->
                <form id="deletePositionForm" action="{{ route('position.delete', 'id') }}" method="POST" class="d-none">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="positionId" name="id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/Plantilla/vacant/vacant.js') }}"></script>
    <script src="{{ asset('js/Plantilla/vacant/position.js') }}"></script>
    <script src="{{ asset('js/Plantilla/vacant/assign.js') }}"></script>
    <script src="{{ asset('js/Plantilla/vacant/offices/offices.js') }}"></script>
    <script src="{{ asset('js/Plantilla/vacant/offices/add-office.js') }}"></script>
    <script src="{{ asset('js/Plantilla/vacant/assigned.js') }}"></script>
    <script>
        // Add CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
@endpush

@endsection