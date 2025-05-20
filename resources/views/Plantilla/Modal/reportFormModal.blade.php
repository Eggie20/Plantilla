<!-- Report Form Modal -->
<div class="modal fade" id="reportFormModal" tabindex="-1" aria-labelledby="reportFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportFormModalLabel">Generate Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Report Preview -->
                <div class="mt-4">
                    <div id="reportContent" class="border p-3">
                        <!-- Content of the Report -->
                        <div class="text-center">
                            <h6 class="mb-1">Republic of the Philippines</h6>
                            <h6 class="mb-1 fw-bold">MUNICIPALITY OF MAGALLANES</h6>
                            <h6 class="mb-1">Plantilla of Personnel</h6>
                            <h6 class="mb-1">For Fiscal Year: {{ now()->year }}</h6>
                        </div>
                        <br>
                        <div class="d-flex justify-content-between">
                            <h6>(1) Department/GOCC: LGU</h6>
                            <h6>(2) Bureau/Agency/Subsidiary: Municipal Government of Magallanes</h6>
                        </div>
                        <table class="table table-bordered">
                            <thead class="table-light text-center align-middle">
                                <tr>
                                    <th rowspan="2" class="align-middle">Item No.</th>
                                    <th rowspan="2" class="align-middle">Position</th>
                                    <th rowspan="2" class="align-middle">Salary Grade</th>
                                    <th colspan="2" class="text-center align-middle">Annual Salary</th>
                                    <th rowspan="2" class="align-middle">Step</th>
                                    <th colspan="2" class="text-center align-middle">Area</th>
                                    <th rowspan="2" class="align-middle">Level</th>
                                    <th colspan="3" class="text-center align-middle">Incumbents</th>
                                    <th rowspan="2" class="align-middle">Date of Birth</th>
                                    <th rowspan="2" class="align-middle">Date of Original Appointment</th>
                                    <th rowspan="2" class="align-middle">Date of Last Promotion</th>
                                    <th rowspan="2" class="align-middle">Status</th>
                                </tr>
                                <tr>
                                    <th class="align-middle">Authorized</th>
                                    <th class="align-middle">Actual</th>
                                    <th class="align-middle">Code</th>
                                    <th class="align-middle">Type</th>
                                    <th class="align-middle">Last Name</th>
                                    <th class="align-middle">First Name</th>
                                    <th class="align-middle">Middle Name</th>
                                </tr>
                            </thead>
                            <tbody id="reportPreviewBody">
                                <!-- Report data will be loaded here -->
                                <tr>
                                    <td colspan="17" class="text-center">Select report options and click Generate to preview data</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="17" class="text-left">
                                        <strong>(19) Total number of Position Items: <span id="totalPositions">0</span></strong>
                                        <br><br>
                                        <p style="text-align: left;">
                                            I certify to the correctness of the entries and that the above Position Items are duly approved and authorized
                                            by the agency and in compliance with existing rules and regulations.<br>
                                            I further certify that the employees whose names appear above are the incumbents of the position.
                                        </p>
                                        <br>
                                        <table style="width: 100%; text-align: center;">
                                            <tr>
             
                                                <td style="width: 25%; text-align: center;">
                                                    {{ \Carbon\Carbon::now()->format('F d, Y') }}
                                                </td>
                                                <td style="width: 25%; text-align: center;">
                                                    {{ \Carbon\Carbon::now()->format('F d, Y') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="generateReportBtn">Generate</button>
                <button type="button" class="btn btn-success" id="printDirectBtn">
                    <i class="bi bi-printer"></i> Print Report
                </button>
            </div>
        </div>
    </div>
</div>
