/**
 * Plantilla Table Print Functionality
 * Handles printing the plantilla table with proper formatting
 */
document.addEventListener('DOMContentLoaded', function() {
    const printButton = document.getElementById('printPersonnelTable');
    
    if (printButton) {
        printButton.addEventListener('click', function() {
            // Create a new window for printing
            const printWindow = window.open('', '_blank', 'width=800,height=600');
            
            // Get current date in a formatted string
            const currentDate = new Date();
            const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = currentDate.toLocaleDateString('en-US', dateOptions);
            
            // Get current filter values for the report title
            const office = document.getElementById('officeFilter').value || '';
            const status = document.getElementById('statusFilter').value || '';
            
            let reportTitle = 'Personnel Plantilla Report';
            let filterDescription = '';
            
            if (office) {
                const officeSelect = document.getElementById('officeFilter');
                const officeText = officeSelect.options[officeSelect.selectedIndex].text;
                filterDescription += `${officeText} | `;
            }
            
            if (status) {
                const statusSelect = document.getElementById('statusFilter');
                const statusText = statusSelect.options[statusSelect.selectedIndex].text;
                filterDescription += `Status: ${statusText} | `;
            }
            
            // Remove trailing separator if exists
            if (filterDescription) {
                filterDescription = filterDescription.slice(0, -3);
                reportTitle += ` (${filterDescription})`;
            }
            
            // Get the table data
            const table = document.getElementById('personnelTable');
            const rows = table.querySelectorAll('tbody tr');
            
            // Group personnel by office
            const personnelsByOffice = {};
            let totalPersonnel = 0;
            
            rows.forEach(row => {
                // Skip rows that are hidden by filters
                if (row.style.display === 'none') return;
                
                const cells = row.querySelectorAll('td');
                if (cells.length > 0) {
                    const officeName = cells[0].textContent.trim();
                    
                    if (!personnelsByOffice[officeName]) {
                        personnelsByOffice[officeName] = [];
                    }
                    
                    // Create a personnel object with all the data
                    const personnel = {
                        itemNo: cells[1].textContent.trim(),
                        position: cells[2].textContent.trim(),
                        salaryGrade: cells[3].textContent.trim(),
                        authorizedSalary: cells[4].textContent.trim(),
                        actualSalary: cells[5].textContent.trim(),
                        step: cells[6].textContent.trim(),
                        code: cells[7].textContent.trim(),
                        type: cells[8].textContent.trim(),
                        level: cells[9].textContent.trim(),
                        lastName: cells[10].textContent.trim(),
                        firstName: cells[11].textContent.trim(),
                        middleName: cells[12].textContent.trim(),
                        dob: cells[13].textContent.trim(),
                        originalAppointment: cells[14].textContent.trim(),
                        lastPromotion: cells[15].textContent.trim(),
                        status: cells[16].textContent.trim()
                    };
                    
                    personnelsByOffice[officeName].push(personnel);
                    totalPersonnel++;
                }
            });
            
            // Generate the table rows grouped by office
            let tableContent = '';
            
            for (const officeName in personnelsByOffice) {
                // Add office header row
                tableContent += `
                    <tr>
                        <td colspan="16" class="text-left" style="text-transform: uppercase;">
                            <strong> ${officeName}</strong>
                        </td>
                    </tr>
                `;
                
                // Add personnel rows for this office
                personnelsByOffice[officeName].forEach(personnel => {
                    tableContent += `
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
                            <td>${personnel.middleName}</td>
                            <td>${personnel.dob}</td>
                            <td>${personnel.originalAppointment}</td>
                            <td>${personnel.lastPromotion}</td>
                            <td>${personnel.status}</td>
                        </tr>
                    `;
                });
            }
            
            // Generate the print content
            let printContent = `
                <html>
                <head>
                    <title>${reportTitle}</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            margin: 10px; 
                            font-size: 11px;
                        }
                        
                        table { 
                            width: 100%; 
                            border-collapse: collapse; 
                            margin-top: 10px;
                            font-size: 10px;
                        }
                        
                        th, td { 
                            border: 1px solid #ddd; 
                            padding: 3px 4px; 
                            text-align: left; 
                            vertical-align: middle;
                            line-height: 1.2;
                        }
                        
                        th { 
                            background-color: #f2f2f2; 
                            font-weight: bold; 
                            text-align: center;
                        }
                        
                        .align-middle {
                            vertical-align: middle;
                        }
                        
                        .text-center {
                            text-align: center;
                        }
                        
                        .text-left {
                            text-align: left;
                        }
                        
                        h1, h2, h3, h6 { 
                            text-align: center; 
                            margin: 3px 0; 
                            font-size: 12px;
                        }
                        
                        .header { 
                            text-align: center; 
                            margin-bottom: 10px; 
                        }
                        
                        .report-date { 
                            text-align: center; 
                            font-style: italic; 
                            margin-bottom: 10px; 
                            font-size: 10px;
                        }
                        
                        .filter-info { 
                            text-align: center; 
                            font-size: 10px; 
                            margin-bottom: 10px; 
                            color: #555; 
                        }
                        
                        .footer { 
                            margin-top: 15px; 
                            font-size: 10px;
                        }
                        
                        .signature-section { 
                            display: flex; 
                            justify-content: space-between; 
                            margin-top: 25px; 
                        }
                        
                        .signature-line { 
                            width: 45%; 
                            text-align: center; 
                        }
                        
                        .signature-line p { 
                            margin: 3px 0; 
                        }
                        
                        .signature-name { 
                            font-weight: bold; 
                            border-bottom: 1px solid #000; 
                            padding-bottom: 3px; 
                        }
                        
                        .signature-title { 
                            font-style: italic; 
                        }
                        
                        .print-button { 
                            text-align: center; 
                            margin: 10px 0; 
                        }
                        
                        .print-button button { 
                            padding: 5px 10px; 
                            background: #007bff; 
                            color: white; 
                            border: none; 
                            border-radius: 4px; 
                            cursor: pointer; 
                            margin-right: 5px; 
                        }
                        
                        .print-button button:hover { 
                            background: #0056b3; 
                        }
                        
                        .close-button { 
                            background: #6c757d !important; 
                        }
                        
                        .close-button:hover { 
                            background: #5a6268 !important; 
                        }
                        
                        .certification { 
                            margin-top: 10px; 
                            text-align: left; 
                            font-size: 10px; 
                            line-height: 1.3; 
                        }
                        
                        .department-info { 
                            display: flex; 
                            justify-content: space-between; 
                            margin: 5px 0; 
                        }
                        
                        /* Office header styling */
                        .office-header {
                            background-color: #f8f9fa;
                            font-weight: bold;
                            text-align: left;
                            padding: 6px 8px;
                            text-transform: uppercase;
                        }
                        
                        /* Ensure we fit rows per page */
                        table tbody tr {
                            height: auto;
                            page-break-inside: avoid;
                        }
                        
                        /* Repeat table headers on each page */
                        thead { display: table-header-group; }
                        tfoot { display: table-footer-group; }
                        
                        /* Page numbering */
                        .page-number {
                            position: absolute;
                            top: 10px;
                            right: 10px;
                            font-size: 10px;
                            font-weight: bold;
                            background-color: white;
                            padding: 2px 5px;
                            border: 1px solid #ddd;
                            z-index: 1000;
                        }
                        
                        /* Ensure we fit more rows per page */
                        table tr {
                            height: auto;
                            page-break-inside: avoid;
                        }
                        
                        /* Numbered cells styling */
                        .numbered-cell {
                            text-align: center;
                            font-weight: bold;
                            background-color: #f8f9fa;
                        }
                        
                        /* Font size controls */
                        .font-size-controls {
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            margin-bottom: 10px;
                        }
                        
                        .font-size-controls button {
                            padding: 3px 8px;
                            margin: 0 5px;
                            background: #f8f9fa;
                            border: 1px solid #ddd;
                            border-radius: 3px;
                            cursor: pointer;
                        }
                        
                        .font-size-controls button:hover {
                            background: #e9ecef;
                        }
                        
                        .font-size-controls span {
                            margin: 0 5px;
                        }
                        
                        @page { margin: 0.5cm; }
                        @media print {
                            .print-button, .font-size-controls { display: none; }
                            body { margin: 0; padding: 10px; }
                            .page-break { page-break-after: always; }
                            .page { position: relative; }
                        }
                    </style>
                </head>
                <body>
                    <div style="white-space: nowrap; position: absolute; top: 10px; right: 10px;">
                        <span class="page-number"></span>
                    </div>
                    
                    <div class="header">   
                        <h6>Republic of the Philippines</h6>
                        <h6><strong>MUNICIPALITY OF MAGALLANES</strong></h6>
                        <h6>Plantilla of Personnel</h6>
                        <h6>For Fiscal Year: ${new Date().getFullYear()}</h6>
                        
                        <div class="department-info">
                            <div style="width: 100%; display: flex; justify-content: space-between;">
                                <h6>(1) Department/GOCC: LGU</h6>
                                <h6>(2) Bureau/Agency/Subsidiary: Municipal Government of Magallanes</h6>
                            </div>
                        </div>
                        
                        <div class="filter-info">
                            ${filterDescription ? `<small>${filterDescription}</small>` : ''}
                        </div>
                    </div>
                    
                    <div class="print-button">
                        <button onclick="window.print();">Print Report</button>
                        <button class="close-button" onclick="window.close();">Close</button>
                    </div>
                    
                    <div class="font-size-controls">
                        <button onclick="changeFontSize(-1)">A-</button>
                        <span>Font Size</span>
                        <button onclick="changeFontSize(1)">A+</button>
                        <button onclick="resetFontSize()">Reset</button>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered" id="printTable">
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
                                <tr>
                                    <td class="numbered-cell">-3</td>
                                    <td class="numbered-cell">-4</td>
                                    <td class="numbered-cell">-5</td>
                                    <td class="numbered-cell">-6</td>
                                    <td class="numbered-cell">-7</td>
                                    <td class="numbered-cell">-8</td>
                                    <td class="numbered-cell">-9</td>
                                    <td class="numbered-cell">-10</td>
                                    <td class="numbered-cell">-11</td>
                                    <td class="numbered-cell">-12</td>
                                    <td class="numbered-cell">-13</td>
                                    <td class="numbered-cell">-14</td>
                                    <td class="numbered-cell">-15</td>
                                    <td class="numbered-cell">-16</td>
                                    <td class="numbered-cell">-17</td>
                                    <td class="numbered-cell">-18</td>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableContent}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="16" class="text-left">
                                        <strong>(19) Total number of Position Items: ${totalPersonnel}</strong>
                                        <br><br>
<div style="max-width: 700px; margin: 0 20px; text-align: left;">
    <p contenteditable="true">
        <span style="white-space: nowrap;">
            I certify to the correctness of the entries and that the above Position Items are duly approved and authorized by the agency and in compliance with existing rules and regulations.
        </span><br>
        I further certify that the employees whose names appear above are the incumbents of the position.
    </p>
</div>


                                        <br>
                                        <table style="width: 100%; text-align: center; border: none;">
                                            <tr>
                                                <td style="width: 25%; text-align: center; border: none;">
                                                    <strong contenteditable="true">JESSIE M. RODAS</strong> <br>
                                                 <span style="text-transform: none;" contenteditable="true">MGADH I (HRMO)</span>
                                                </td>
                                                <td style="width: 25%; text-align: center; border: none;">
                                                    ${formattedDate}
                                                </td>
                                                <td style="width: 25%; text-align: center; border: none;">
                                                 <strong contenteditable="true">CESAR C. CUMBA, JR</strong><br>
                                                    <span contenteditable="true" style="text-transform: none;">Municipal Mayor</span>
                                                </td>
                                                <td style="width: 25%; text-align: center; border: none;">
                                                    ${formattedDate}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <script>
                        // Default font sizes
                        const defaultFontSizes = {
                            body: 11,
                            table: 10
                        };
                        
                        // Current font sizes
                        let currentFontSizes = {...defaultFontSizes};
                        
                        // Function to change font size
                        function changeFontSize(change) {
                            // Update font sizes
                            currentFontSizes.body += change;
                            currentFontSizes.table += change;
                            
                            // Apply new font sizes
                            document.body.style.fontSize = currentFontSizes.body + 'px';
                            document.getElementById('printTable').style.fontSize = currentFontSizes.table + 'px';
                            
                            // Update page numbers after font size change
                            setTimeout(addPageNumbers, 200);
                        }
                        
                        // Function to reset font size
                        function resetFontSize() {
                            currentFontSizes = {...defaultFontSizes};
                            document.body.style.fontSize = currentFontSizes.body + 'px';
                            document.getElementById('printTable').style.fontSize = currentFontSizes.table + 'px';
                            
                            // Update page numbers after font size reset
                            setTimeout(addPageNumbers, 200);
                        }
                        
                        // Function to add page numbers
                        function addPageNumbers() {
                            // Create page number elements for each page
                            var height = window.innerHeight;
                            var pages = Math.ceil(document.body.scrollHeight / height);
                            
                            // Remove any existing page numbers first
                            const existingPageNumbers = document.querySelectorAll('.page-number');
                            existingPageNumbers.forEach(el => el.remove());
                            
                            // Add new page numbers
                            for (var i = 0; i < pages; i++) {
                                var pageNumberDiv = document.createElement('div');
                                pageNumberDiv.className = 'page-number';
                                pageNumberDiv.style.top = (10 + (height * i)) + 'px';
                                pageNumberDiv.textContent = '(' + (i + 1) + ')';
                                document.body.appendChild(pageNumberDiv);
                            }
                        }
                        
                        // Run after the document is fully loaded
                        window.onload = function() {
                            // Add page numbers
                            setTimeout(addPageNumbers, 500);
                            
                            // Update page numbers when window is resized
                            window.onresize = function() {
                                setTimeout(addPageNumbers, 200);
                            };
                            
                            // Update page numbers before printing
                            window.onbeforeprint = function() {
                                addPageNumbers();
                            };
                        };
                    </script>
                </body>
                </html>
            `;
            
            // Write to the new window
            printWindow.document.open();
            printWindow.document.write(printContent);
            printWindow.document.close();
            
            // Focus on the new window
            printWindow.focus();
        });
    }
});