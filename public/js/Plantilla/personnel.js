document.addEventListener("DOMContentLoaded", function () {
    const addPersonnelForm = document.getElementById("addPersonnelForm");
    const tableBody = document.getElementById("employeeTable");
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let isLoading = false;

    if (!csrfToken) {
        console.error("CSRF token not found!");
        return;
    }

    // Add loading state
    function setLoading(state) {
        isLoading = state;
        const submitBtn = addPersonnelForm.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = state;
            submitBtn.textContent = state ? "Saving..." : "Save";
        }
    }

    // Validate form data
    function validateFormData(formData) {
        const requiredFields = ['itemNo', 'position', 'salaryGrade', 'authorizedSalary', 'actualSalary', 'step', 'code', 'level', 'lastName', 'firstName', 'status'];
        for (const field of requiredFields) {
            if (!formData[field]) {
                throw new Error(`Please fill in ${field}`);
            }
        }
    }

    // Helper function to escape HTML
    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // 1. Load Existing Personnel from Database
    async function loadPersonnel() {
        try {
            const response = await fetch("/personnels");
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            
            const data = await response.json();
            tableBody.innerHTML = ""; // Clear table

            if (data.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="16" class="text-center">No personnel found.</td></tr>`;
                return;
            }

            data.forEach(personnel => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${escapeHtml(personnel.itemNo)}</td>
                    <td>${escapeHtml(personnel.position)}</td>
                    <td>${escapeHtml(personnel.salaryGrade)}</td>
                    <td>${escapeHtml(personnel.authorizedSalary)}</td>
                    <td>${escapeHtml(personnel.actualSalary)}</td>
                    <td>${escapeHtml(personnel.step)}</td>
                    <td>${escapeHtml(personnel.code)}</td>
                    <td>${escapeHtml(personnel.type)}</td>
                    <td>${escapeHtml(personnel.level)}</td>
                    <td>${escapeHtml(personnel.lastName)}</td>
                    <td>${escapeHtml(personnel.firstName)}</td>
                    <td>${escapeHtml(personnel.middleName || '')}</td>
                    <td>${personnel.dob ? new Date(personnel.dob).toISOString().split('T')[0] : ''}</td>
                    <td>${personnel.originalAppointment ? new Date(personnel.originalAppointment).toISOString().split('T')[0] : ''}</td>
                    <td>${personnel.lastPromotion ? new Date(personnel.lastPromotion).toISOString().split('T')[0] : ''}</td>
                    <td>${escapeHtml(personnel.status)}</td>
                `;
                tableBody.appendChild(row);
            });
        } catch (error) {
            console.error("Error loading personnel:", error);
            alert("Error loading personnel data. Please try again.");
        }
    }

    // 2. Add Personnel and Save to Database
    addPersonnelForm.addEventListener("submit", async function (event) {
        event.preventDefault();
        
        if (isLoading) return;
        setLoading(true);

        try {
            const formData = {
                itemNo: document.getElementById("itemNo").value,
                position: document.getElementById("position").value,
                salaryGrade: document.getElementById("salaryGrade").value,
                authorizedSalary: document.getElementById("authorizedSalary").value,
                actualSalary: document.getElementById("actualSalary").value,
                step: document.getElementById("step").value,
                code: document.getElementById("code").value,
                type: "M", // Always "M"
                level: document.getElementById("level").value,
                lastName: document.getElementById("lastName").value,
                firstName: document.getElementById("firstName").value,
                middleName: document.getElementById("middleName").value || "N/A",
                dob: document.getElementById("dob").value,
                originalAppointment: document.getElementById("originalAppointment").value,
                lastPromotion: document.getElementById("lastPromotion").value || null,
                status: document.getElementById("status").value,
            };

            validateFormData(formData);

            const response = await fetch("/add-personnel", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

            const data = await response.json();
            
            if (data.success) {
                // Refresh personnel list
                loadPersonnel();

                // Close modal
                const modal = document.getElementById("addPersonnelModal");
                modal.classList.remove("show");
                modal.style.display = "none";

                // Reset form
                addPersonnelForm.reset();

                // Show success message
                alert("Personnel added successfully!");

                // Redirect to index page after 500ms
                setTimeout(() => {
                    window.location.href = PlantillaRoute;
                }, 500);
            } else {
                throw new Error(data.message || "Unable to save data");
            }
        } catch (error) {
            console.error("Error:", error);
            alert(error.message || "An error occurred while saving.");
        } finally {
            setLoading(false);
        }
    });

    // 3. Reset Form on Modal Close
    const modal = document.getElementById("addPersonnelModal");
    if (modal) {
        modal.addEventListener("hidden.bs.modal", function () {
            addPersonnelForm.reset();
            setLoading(false);
        });
    }

    // remove aria
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('shown.bs.modal', function () {
            const firstInput = modal.querySelector('input, select, textarea, button');
            if (firstInput) firstInput.focus();
        });
    });

    // Handle remarks modal
    const remarksModal = document.getElementById('remarksModal');
    if (remarksModal) {
        remarksModal.addEventListener('shown.bs.modal', function () {
            const retirableFilter = remarksModal.querySelector('#retirableFilter');
            if (retirableFilter) retirableFilter.focus();
        });
    }

    // Initial load
    loadPersonnel();
});