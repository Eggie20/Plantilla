$(document).ready(function () {
    function populateRemarksTable(remarksFilter) {
        var rowsToDisplay = [];
        console.log("Applying filter:", remarksFilter);

        $('#remarksTable tbody').empty(); // Clear the remarks table

        $('#employeeTable .employee-data').each(function (index) {
            var $row = $(this);
            var remarks = ($row.data('remarks') || '').trim().toLowerCase(); // Normalize case
            var office = $row.data('office') || 'N/A';
            var position = $row.find('td:eq(1)').text().trim();
            var lastName = $row.find('td:eq(10)').text().trim();
            var firstName = $row.find('td:eq(11)').text().trim();
            var middleName = $row.find('td:eq(12)').text().trim();
            var dob = $row.find('td:eq(13)').text().trim();
            var eligibleDate = calculateEligibilityDate(dob);
            var age = calculateAge(dob);

            console.log(`Row ${index + 1}: Remarks = '${remarks}', Office = '${office}', Position = '${position}'`);

            // Check if the row matches the filter
            if (remarksFilter === '' || remarks === remarksFilter.toLowerCase()) {
                rowsToDisplay.push(`
                    <tr>
                        <td>${office}</td>
                        <td>${position}</td>
                        <td>${lastName}</td>
                        <td>${firstName}</td>
                        <td>${middleName}</td>
                        <td>${age}</td>
                        <td>${eligibleDate}</td>
                    </tr>
                `);
            }
        });

        $('#remarksTable tbody').html(rowsToDisplay.join(''));
        console.log(`Updated Remarks Table - ${rowsToDisplay.length} rows displayed`);
    }

    function calculateAge(dob) {
        if (!dob) return 'N/A';
        var birthDate = new Date(dob);
        var age = new Date().getFullYear() - birthDate.getFullYear();
        var m = new Date().getMonth() - birthDate.getMonth();
        if (m < 0 || (m === 0 && new Date().getDate() < birthDate.getDate())) {
            age--;
        }
        return age;
    }

    function calculateEligibilityDate(dob) {
        if (!dob) return 'N/A';
        var retirementAge = 60;
        var birthDate = new Date(dob);
        var eligibilityYear = birthDate.getFullYear() + retirementAge;
        var eligibilityDate = new Date(eligibilityYear, birthDate.getMonth(), birthDate.getDate());
        return eligibilityDate.toISOString().split('T')[0];
    }

    function refreshRemarks() {
        console.log("Refreshing remarks table...");
        populateRemarksTable('');
    }

    // ✅ Fix filtering issues with case sensitivity
    $('#retirableFilter').on('click', function () {
        console.log("Filtering Retirable Employees...");
        populateRemarksTable('retirable');
    });

    $('#retiredFilter').on('click', function () {
        console.log("Filtering Retired Employees...");
        populateRemarksTable('retired');
    });

    $('#nonRetirableFilter').on('click', function () {
        console.log("Filtering Non-Retirable Employees...");
        populateRemarksTable('non-retirable');
    });

    $('#remarksModal').on('show.bs.modal', function () {
        console.log("Remarks modal opened - updating table...");
        setTimeout(refreshRemarks, 500);
    });

    // ✅ Detect table updates (for dynamic changes)
    const observer = new MutationObserver(() => {
        console.log("Table Updated - Refreshing Remarks Automatically");
        refreshRemarks();
    });

    observer.observe(document.getElementById('employeeTable'), {
        childList: true,
        subtree: true
    });

    // ✅ Manual refresh button (if needed)
    $('#refreshRemarks').on('click', function () {
        console.log("Manual Refresh Triggered");
        refreshRemarks();
    });
});
