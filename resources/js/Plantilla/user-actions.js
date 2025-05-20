document.addEventListener('DOMContentLoaded', function() {
    // Edit User
    document.querySelectorAll('.edit-user-btn').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const username = this.getAttribute('data-username');
            const role = this.getAttribute('data-role');
            const permissions = JSON.parse(this.getAttribute('data-permissions') || '[]');

            // Populate edit form
            document.getElementById('editUserId').value = id;
            document.getElementById('editName').value = name;
            document.getElementById('editUsername').value = username;
            document.getElementById('editRole').value = role;

            // Set permissions
            const permissionCheckboxes = document.querySelectorAll('input[name="permissions[]"]');
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = permissions.includes(checkbox.value);
            });
        });
    });

    // Lock User
    document.querySelectorAll('.lock-user-btn').forEach(button => {
        button.addEventListener('click', function() {
            const username = this.getAttribute('data-username');
            const name = this.getAttribute('data-name');

            // Populate lock modal
            document.getElementById('lockUsername').value = username;
            document.getElementById('lockName').textContent = name;
        });
    });

    // Block User
    document.querySelectorAll('.block-user-btn').forEach(button => {
        button.addEventListener('click', function() {
            const username = this.getAttribute('data-username');
            const name = this.getAttribute('data-name');

            // Populate block modal
            document.getElementById('blockUsername').value = username;
            document.getElementById('blockName').textContent = name;
        });
    });

    // Handle lock user form submission
    document.getElementById('lockUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const username = document.getElementById('lockUsername').value;
        const reason = document.getElementById('lockReason').value;

        fetch('{{ route("users.block") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                id: username,
                reason: reason
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('lockUserModal'));
                modal.hide();
                // Refresh the user list
                refreshUserList();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to lock user'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while locking the user'
            });
        });
    });

    // Handle block user form submission
    document.getElementById('blockUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const username = document.getElementById('blockUsername').value;
        const reason = document.getElementById('blockReason').value;

        fetch('{{ route("users.block") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                id: username,
                reason: reason,
                block: true
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                });
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('blockUserModal'));
                modal.hide();
                // Refresh the user list
                refreshUserList();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to block user'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred while blocking the user'
            });
        });
    });

    // Function to refresh the user list
    function refreshUserList() {
        const table = document.querySelector('.table-responsive table');
        if (table) {
            // Clear existing table
            table.innerHTML = '';
            // Fetch new data
            fetch('{{ route("Plantilla.Pages.Accounts") }}')
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTable = doc.querySelector('.table-responsive table');
                    if (newTable) {
                        table.innerHTML = newTable.innerHTML;
                    }
                });
        }
    }
});
