{{-- <!-- Modal Structure for Report Form -->
<div class="modal fade" id="reportFormModal" tabindex="-1" aria-labelledby="reportFormModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportFormModalLabel">Generate Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Report content form -->
                <form id="reportForm" class="container my-4" style="max-width: 500px;">

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="generateAndPrintReport()">Proceed</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
   fetch('/get-personnel-data')
    .then(response => response.json())
    .then(data => {
        window.personnelData = data; // Store the data
        console.log(window.personnelData); // Log the data to ensure it's fetched correctly

        // Now, generate and print the report
        generateAndPrintReport();
    })
    .catch(error => {
        console.error('Error fetching personnel data:', error);
    });
    function generateAndPrintReport() {
        // Retrieve values from the form fields
        const name1 = document.getElementById('name1').value || 'Jessie M. Rodas'; // Default value
        const position1 = document.getElementById('position1').value || 'MGADH I (HRMO)'; // Default value
        const name2 = document.getElementById('name2').value || 'Cesar C. Cumba, JR'; // Default value
        const position2 = document.getElementById('position2').value || 'Municipal Mayor'; // Default value
        
        // Build report content dynamically
        let reportContent = `
            <div class="text-center">
                <h6 class="mb-1">Republic of the Philippines</h6>
                <h6 class="mb-1 fw-bold">MUNICIPALITY OF MAGALLANES</h6>
                <h6 class="mb-1">Plantilla of Personnel</h6>
                <h6 class="mb-1">For Fiscal Year: ${new Date().getFullYear()}</h6>
            </div>

            <br>

            <div class="d-flex justify-content-between">
                <h6>(1) Department/GOCC: LGU</h6>
                <h6>(2) Bureau/Agency/Subsidiary: Municipal Government of Magallanes</h6>
            </div>

            <table class="table table-bordered">
                <thead class="table-light text-center align-middle">
                    <tr>
                        <th rowspan="2">Item No.</th>
                        <th rowspan="2">Position</th>
                        <th rowspan="2">Salary Grade</th>
                        <th colspan="2" class="text-center">Annual Salary</th>
                        <th rowspan="2">Step</th>
                        <th colspan="2" class="text-center">Area</th>
                        <th rowspan="2">Level</th>
                        <th colspan="3" class="text-center">Incumbents</th>
                        <th rowspan="2">Date of Birth</th>
                        <th rowspan="2">Date of Original Appointment</th>
                        <th rowspan="2">Date of Last Promotion</th>
                        <th rowspan="2">Status</th>
                    </tr>
                    <tr>
                        <th>Authorized</th>
                        <th>Actual</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                    </tr>
                </thead>
                <tbody>
                    ${window.personnelData.map(office => `
                        <tr>
                            <td colspan="17" class="text-left"><strong>Office: ${office}</strong></td>
                        </tr>
                        ${office.personnels.map(personnel => `
                            <tr>
                                <td>${personnel.itemNo}</td>
                                <td>${personnel.position}</td>
                                <td>${personnel.salaryGrade}</td>
                                <td>${personnel.authorizedSalary}</td>
                                <td>${personnel.actualSalary}</td>
                                <td>${personnel.step}</td>
                                <td>${personnel.code}</td>
                                <td>${personnel.type}</td>
                                <td>${personnel.level}</td>
                                <td>${personnel.lastName}</td>
                                <td>${personnel.firstName}</td>
                                <td>${personnel.middleName || ''}</td>
                                <td>${personnel.dob}</td>
                                <td>${personnel.originalAppointment}</td>
                                <td>${personnel.lastPromotion || ''}</td>
                                <td>${personnel.status}</td>
                            </tr>
                        `).join('')}`
                    ).join('')}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="17">
                            <strong>Total number of Position Items: ${window.personnelData.length}</strong>
                            <br><br>
                            <p>I certify the correctness of the entries...</p>
                            <table style="width: 100%; text-align: center;">
                                <tr>
                                    <td style="width: 25%"><strong>${name1}</strong><br><span>${position1}</span></td>
                                    <td style="width: 25%">${new Date().toLocaleDateString()}</td>
                                    <td style="width: 25%"><strong>${name2}</strong><br><span>${position2}</span></td>
                                    <td style="width: 25%">${new Date().toLocaleDateString()}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </tfoot>
            </table>
        `;

        // Create a new window for printing
        const printWindow = window.open('', '', 'width=800,height=600');
        
        // Add the content and styles to the new window
        printWindow.document.write(`
            <html>
                <head>
                    <title>Report</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .text-center { text-align: center; }
                        table { width: 100%; border-collapse: collapse; }
                        table, th, td { border: 1px solid black; }
                        th, td { padding: 8px; text-align: center; }
                    </style>
                </head>
                <body>
                    <h1>Generated Report</h1>
                    <div>${reportContent}</div>
                </body>
            </html>
        `);

        // Wait for the content to load and then trigger the print dialog
        printWindow.document.close(); // Required for some browsers
        printWindow.onload = function () {
            printWindow.print();
            printWindow.close(); // Close the print window after printing
        };
    }

    if (Array.isArray(window.personnelData) && window.personnelData.length > 0) {
    // Proceed with generating the report
    const reportContent = window.personnelData.map(office => {
        // your logic here
    }).join('');
} else {
    console.error("Personnel data is not available or empty.");
}
</script> --}}
