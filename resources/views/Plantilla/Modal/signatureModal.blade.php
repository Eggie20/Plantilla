<!-- Modal Structure for Report Form -->
<script>
   window.personnelData = []; // Hiding the real data, using an empty array instead

function generateAndPrintReport() {
    // Get all personnel data from the visible table
    const tableRows = document.querySelectorAll('#personnelTable tbody tr');
    const offices = new Map();
    let currentOffice = '';

    // Process each row in the table
    tableRows.forEach(row => {
        const cells = row.cells;
        // Skip if this is an empty row or has no cells
        if (!cells || cells.length === 0) return;

        // Get office from the first column
        const officeCell = cells[0]?.textContent?.trim();
        if (officeCell) {
            currentOffice = officeCell;
            if (!offices.has(currentOffice)) {
                offices.set(currentOffice, []);
            }
        }

        // Only process rows that have all data cells (not office header rows)
        if (cells.length >= 16) {
            const personnelData = {
                itemNo: cells[1]?.textContent?.trim() || '',
                position: cells[2]?.textContent?.trim() || '',
                salaryGrade: cells[3]?.textContent?.trim() || '',
                authorizedSalary: cells[4]?.textContent?.trim() || '',
                actualSalary: cells[5]?.textContent?.trim() || '',
                step: cells[6]?.textContent?.trim() || '',
                code: cells[7]?.textContent?.trim() || '',
                type: cells[8]?.textContent?.trim() || '',
                level: cells[9]?.textContent?.trim() || '',
                lastName: cells[10]?.textContent?.trim() || '',
                firstName: cells[11]?.textContent?.trim() || '',
                middleName: cells[12]?.textContent?.trim() || '',
                dob: cells[13]?.textContent?.trim() || '',
                originalAppointment: cells[14]?.textContent?.trim() || '',
                lastPromotion: cells[15]?.textContent?.trim() || '',
                status: cells[16]?.textContent?.trim() || ''
            };
            if (currentOffice) {
                offices.get(currentOffice).push(personnelData);
            }
        }
    });

    // Calculate total positions
    const totalPositions = Array.from(offices.values()).reduce((total, office) => total + office.length, 0);

    // Get form values
    const name1 = document.getElementById('name1').value || 'Jessie M. Rodas';
    const position1 = document.getElementById('position1').value || 'MGADH I (HRMO)';
    const name2 = document.getElementById('name2').value || 'Cesar C. Cumba, JR';
    const position2 = document.getElementById('position2').value || 'Municipal Mayor';

    // Create print window with proper styling
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <style>
                    @page {
                        size: legal landscape;
                        margin: 15mm;
                    }
                    body {
                        font-family: 'Times New Roman', Times, serif;
                        font-size: 10px;
                        margin: 0;
                        padding: 0;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        table-layout: auto;
                    }
                    th, td {
                        border: 1px solid black;
                        padding: 4px 6px;
                        font-size: 9px;
                        overflow: hidden;
                        word-wrap: break-word;
                        text-align: left;
                        vertical-align: middle;
                    }
                    th {
                        background-color: #f0f0f0;
                        font-weight: normal;
                        text-align: center; /* Center align headers */
                    }
                    
                    /* Column widths */
                    td:nth-child(1), th:nth-child(1) { min-width: 40px; }     /* Item No. */
                    td:nth-child(2), th:nth-child(2) { min-width: 120px; }    /* Position */
                    td:nth-child(3), th:nth-child(3) { min-width: 25px; }     /* SG */
                    td:nth-child(4), th:nth-child(4) { min-width: 60px; }     /* Auth Salary */
                    td:nth-child(5), th:nth-child(5) { min-width: 60px; }     /* Act Salary */
                    td:nth-child(6), th:nth-child(6) { min-width: 25px; }     /* Step */
                    td:nth-child(7), th:nth-child(7) { min-width: 30px; }     /* Code */
                    td:nth-child(8), th:nth-child(8) { min-width: 30px; }     /* Type */
                    td:nth-child(9), th:nth-child(9) { min-width: 30px; }     /* Level */
                    td:nth-child(10), th:nth-child(10) { min-width: 100px; }  /* Last Name */
                    td:nth-child(11), th:nth-child(11) { min-width: 100px; }  /* First Name */
                    td:nth-child(12), th:nth-child(12) { min-width: 80px; }   /* Middle Name */
                    td:nth-child(13), th:nth-child(13) { min-width: 70px; }   /* Birth */
                    td:nth-child(14), th:nth-child(14) { min-width: 70px; }   /* Original */
                    td:nth-child(15), th:nth-child(15) { min-width: 70px; }   /* Last Promotion */
                    td:nth-child(16), th:nth-child(16) { min-width: 30px; }   /* Status */

                    .office-header {
                        background-color: #f0f0f0;
                        font-weight: bold;
                        text-align: left;
                        padding: 5px;
                    }
                    
                    thead th {
                        vertical-align: middle;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <p>Republic of the Philippines</p>
                    <p style="font-weight: bold;">MUNICIPALITY OF MAGALLANES</p>
                    <p>Plantilla of Personnel</p>
                    <p>For Fiscal Year: ${new Date().getFullYear()}</p>
                </div>
                <div style="margin-bottom: 10px;">
                    <div style="float: left;">(1) Department/GOCC: LG</div>
                    <div style="float: right;">(2) Bureau/Agency/Subsidiary: Municipal Government of Magallanes</div>
                    <div style="clear: both;"></div>
                </div>
                <div class="page-number"></div>
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2">Item No.</th>
                            <th rowspan="2">Position</th>
                            <th rowspan="2">SG</th>
                            <th colspan="2">Annual Salary</th>
                            <th rowspan="2">Step</th>
                            <th colspan="2">Area</th>
                            <th rowspan="2">Level</th>
                            <th colspan="3">Name of Incumbents</th>
                            <th colspan="3">Dates</th>
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
                            <th>Birth</th>
                            <th>Original</th>
                            <th>Last Promotion</th>
                        </tr>
                    </thead>
                    <tbody>`);

    // Add data rows grouped by office
    offices.forEach((personnel, officeName) => {
        printWindow.document.write(`
            <tr>
                <td colspan="16" class="office-header">${officeName}</td>
            </tr>`);
        
        personnel.forEach(person => {
            printWindow.document.write(`
                <tr>
                    <td style="text-align: center;">${person.itemNo}</td>
                    <td>${person.position}</td>
                    <td style="text-align: center;">${person.salaryGrade}</td>
                    <td style="text-align: right;">${person.authorizedSalary}</td>
                    <td style="text-align: right;">${person.actualSalary}</td>
                    <td style="text-align: center;">${person.step}</td>
                    <td style="text-align: center;">${person.code}</td>
                    <td style="text-align: center;">${person.type}</td>
                    <td style="text-align: center;">${person.level}</td>
                    <td>${person.lastName}</td>
                    <td>${person.firstName}</td>
                    <td>${person.middleName}</td>
                    <td style="text-align: center;">${person.dob}</td>
                    <td style="text-align: center;">${person.originalAppointment}</td>
                    <td style="text-align: center;">${person.lastPromotion}</td>
                    <td style="text-align: center;">${person.status}</td>
                </tr>`);
        });
    });

    // Add footer
    printWindow.document.write(`
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="16" class="text-left">
                                <strong>Total number of Position Items: ${totalPositions}</strong>
                                <br><br>
                                <div style="text-align: center;">
                                    <p style="text-align: justify; padding-left: 30px; padding-right: 30px;">
                                        I certify to the correctness of the entries and that the above Position Items are duly approved and authorized
                                        by the agency and in compliance with existing rules and regulations.<br>
                                        I further certify that the employees whose names appear above are the incumbents of the position.
                                    </p>
                                </div>
                                <br>
                                <table style="width: 100%; text-align: center; border: none;">
                                    <tr>
                                        <td style="width: 25%; text-align: center; border: none;">
                                            <strong>${name1}</strong><br>
                                            <span>${position1}</span>
                                        </td>
                                        <td style="width: 25%; text-align: center; border: none;">
                                            ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}
                                        </td>
                                        <td style="width: 25%; text-align: center; border: none;">
                                            <strong>${name2}</strong><br>
                                            <span>${position2}</span>
                                        </td>
                                        <td style="width: 25%; text-align: center; border: none;">
                                            ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </body>
        </html>`);

    printWindow.document.close();
    printWindow.onload = function() {
        printWindow.print();
        printWindow.onafterprint = function() {
            printWindow.close();
        };
    };
}

</script>
