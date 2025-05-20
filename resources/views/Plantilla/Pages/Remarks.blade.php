@php
use App\Helpers\OfficeFormatter;
@endphp

@extends('Plantilla.sidebar')

@section('title', 'Retirement Remarks')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/Plantilla/index.css') }}">
    <style>
        .content-wrapper {
            height: calc(100vh - 60px);
            display: flex;
            flex-direction: column;
        }
        .content-container {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
        }
        .table-container {
            margin-top: 1rem;
        }
        .table {
            margin-bottom: 1rem;
            width: 100%;
            border-collapse: collapse;
        }
        .table th {
            white-space: nowrap;
            font-weight: 600;
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            padding: 1rem;
        }
        .table td {
            padding: 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
        }
        .table th:first-child, .table td:first-child {
            width: 25%;
        }
        .table th:nth-child(2), .table td:nth-child(2) {
            width: 10%;
            text-align: center;
        }
        .table th:nth-child(3), .table td:nth-child(3) {
            width: 10%;
            text-align: center;
        }
        .table th:nth-child(4), .table td:nth-child(4) {
            width: 25%;
        }
        .table th:last-child, .table td:last-child {
            width: 20%;
        }
        .filter-buttons {
            margin-bottom: 1.5rem;
        }
        .filter-buttons button {
            margin-right: 0.5rem;
            padding: 0.5rem 1rem;
        }
        .active-filter {
            background-color: #007bff;
            color: white;
        }
        
        /* Add hover effect for rows */
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        /* Add ellipsis for long text */
        .table td {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        /* Format office name */
        .office-name {
            font-weight: 500;
        }
        
        /* Retire button styles */
        .retire-button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .retire-button:hover {
            background-color: #c82333;
        }
        
        .pending-button {
            background-color: #ffc107;
            color: #000;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .pending-button:hover {
            background-color: #e0a800;
        }
        
        .retire-button:disabled,
        .pending-button:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        
        .modal-content {
            background-color: white;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            position: relative;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
        }
        
        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #6c757d;
        }
        
        .modal-body {
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
@endpush

@section('content')
<div class="content-wrapper">
    <div class="content-container">
        <div class="table-container">
            <h2 class="mb-4">Personnel Retirement Status</h2>
            
            <div class="filter-buttons">
                <button class="btn btn-outline-primary active-filter" data-status="all">All Personnel</button>
                <button class="btn btn-outline-primary" data-status="retirable">Retirable Personnel</button>
                <button class="btn btn-outline-primary" data-status="pending">Pending Retirement</button>
                <button class="btn btn-outline-primary" data-status="retired">Retired Personnel</button>
                
            </div>

            <div id="personnelTable">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Complete Name</th>
                            <th>Age</th>
                            <th>Date of Birth</th>
                            <th>Position</th>
                            <th>Office</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($personnels as $personnel)
                            @php
                                $dob = \Carbon\Carbon::parse($personnel->dob);
                                $age = abs(floor(now()->diffInYears($dob)));
                                $officeName = $personnel->office ? OfficeFormatter::formatOfficeName($personnel->office) : 'N/A';
                                $isRetirable = $age >= 60 && $age < 65;
                                $hasPendingRetirement = $personnel->pendingRetirement; // Add this property in your controller
                            @endphp
                            <tr class="{{ getRetirementStatus($personnel) }}" data-personnel-id="{{ $personnel->id }}">
                                <td>{{ $personnel->lastName }}, {{ $personnel->firstName }} {{ $personnel->middleName }}</td>
                                <td>{{ $age }}</td>
                                <td>{{ $dob->format('F j, Y') }}</td>
                                <td>{{ $personnel->position }}</td>
                                <td class="office-name">{{ $officeName }}</td>
                                <td>
                                    @if($hasPendingRetirement)
                                        <button class="pending-button" onclick="viewPendingRetirement({{ $personnel->id }})">
                                            View Pending
                                        </button>
                                    @elseif($isRetirable)
                                        <button class="retire-button" onclick="showRetirementModal({{ $personnel->id }}, '{{ $personnel->lastName }}, {{ $personnel->firstName }} {{ $personnel->middleName }}', '{{ $age }}', '{{ $dob->format('F j, Y') }}', '{{ $personnel->originalAppointment->format('F j, Y') }}')">
                                            Retire
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Retirement Modal -->
<div id="retirementModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Retirement Details</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="retirementForm">
                <input type="hidden" id="personnelId" name="personnel_id">
                
                <div class="form-group">
                    <label class="form-label">Complete Name</label>
                    <input type="text" id="personnelName" class="form-control" readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Age</label>
                    <input type="text" id="personnelAge" class="form-control" readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date of Birth</label>
                    <input type="text" id="personnelDob" class="form-control" readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Date of Original Appointment</label>
                    <input type="text" id="originalAppointment" class="form-control" readonly>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Desired Retirement Date</label>
                    <input type="date" id="retirementDate" name="retirement_date" class="form-control" required>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal()">Cancel</button>
            <button class="btn-primary" onclick="submitRetirementForm()">Submit</button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.filter-buttons button');
            const rows = document.querySelectorAll('#personnelTable tbody tr');

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    buttons.forEach(btn => btn.classList.remove('active-filter'));
                    // Add active class to clicked button
                    this.classList.add('active-filter');

                    const status = this.dataset.status;
                    
                    // Show/hide rows based on status
                    rows.forEach(row => {
                        if (status === 'all') {
                            row.style.display = '';
                        } else {
                            row.style.display = row.classList.contains(status) ? '' : 'none';
                        }
                    });
                });
            });

            // Initialize date input with minimum date (today)
            const retirementDateInput = document.getElementById('retirementDate');
            if (retirementDateInput) {
                const today = new Date().toISOString().split('T')[0];
                retirementDateInput.min = today;
            }
        });

        function showRetirementModal(id, name, age, dob, originalAppointment) {
            const modal = document.getElementById('retirementModal');
            document.getElementById('personnelId').value = id;
            document.getElementById('personnelName').value = name;
            document.getElementById('personnelAge').value = age;
            document.getElementById('personnelDob').value = dob;
            document.getElementById('originalAppointment').value = originalAppointment;
            
            modal.style.display = 'block';
            
            // Close modal when clicking outside
            window.onclick = function(event) {
                if (event.target == modal) {
                    closeModal();
                }
            }
        }

        function closeModal() {
            const modal = document.getElementById('retirementModal');
            modal.style.display = 'none';
            // Reset form
            document.getElementById('retirementForm').reset();
        }

        async function submitRetirementForm() {
            const form = document.getElementById('retirementForm');
            const formData = new FormData(form);
            
            if (!confirm('Are you sure you want to retire this personnel? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`{{ url('/retire-personnel') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    // Update the table row to show as pending
                    const personnelId = formData.get('personnel_id');
                    const row = document.querySelector(`tr[data-personnel-id="${personnelId}"]`);
                    if (row) {
                        row.classList.remove('retirable');
                        row.classList.add('pending');
                        // Remove the retire button and add pending button
                        const retireButton = row.querySelector('.retire-button');
                        if (retireButton) {
                            const pendingButton = document.createElement('button');
                            pendingButton.className = 'pending-button';
                            pendingButton.onclick = () => viewPendingRetirement(personnelId);
                            pendingButton.textContent = 'View Pending';
                            retireButton.parentNode.replaceChild(pendingButton, retireButton);
                        }
                    }

                    alert('Retirement request has been submitted and is pending approval.');
                    closeModal();
                } else {
                    throw new Error(data.message || 'Failed to submit retirement request');
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        // Function to view pending retirement details
        function viewPendingRetirement(personnelId) {
            // Get the personnel's name from the table row
            const row = document.querySelector(`tr[data-personnel-id="${personnelId}"]`);
            const name = row.querySelector('td:first-child').textContent;
            
            // You can create another modal or redirect to a detailed view page
            alert('View pending retirement details for: ' + name);
        }
    </script>
@endpush

@php
function getRetirementStatus($personnel) {
    $age = abs(floor(now()->diffInYears($personnel->dob)));
    if ($personnel->pendingRetirement) {
        return 'pending';
    } elseif ($age >= 65) {
        return 'retired';
    } elseif ($age >= 60) {
        return 'retirable';
    }
    return 'not-retirable';
}
@endphp
@endsection