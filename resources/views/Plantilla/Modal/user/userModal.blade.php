<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm" action="{{ route('user.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="name" name="name" required>
                                <label for="name">Full Name</label>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="username" name="username" required>
                                        <label for="username">Username</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <label for="password">Password</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                        <label for="password_confirmation">Confirm Password</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <select class="form-select" id="role" name="role" required>
                                            <option value="">Select Role</option>
                                            <option value="superadmin">Super Admin</option>
                                            <option value="admin">Admin</option>
                                            <option value="user">User</option>
                                        </select>
                                        <label for="role">Role</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <h6 class="mb-3">Permissions</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="view" id="permissionView" name="permissions[]">
                                                <label class="form-check-label" for="permissionView">
                                                    View Records
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="create" id="permissionCreate" name="permissions[]">
                                                <label class="form-check-label" for="permissionCreate">
                                                    Create Records
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="edit" id="permissionEdit" name="permissions[]">
                                                <label class="form-check-label" for="permissionEdit">
                                                    Edit Records
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="approve" id="permissionApprove" name="permissions[]">
                                                <label class="form-check-label" for="permissionApprove">
                                                    Approve Requests
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        <script>
    $(document).ready(function() {
        // Password visibility toggle
        $('#add_togglePassword').on('click', function() {
            const passwordInput = $('#password');
            const icon = $(this).find('i');
            
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });

        // Confirm password visibility toggle
        $('#add_toggleConfirmPassword').on('click', function() {
            const passwordInput = $('#confirmPassword');
            const icon = $(this).find('i');
            
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            }
        });

        // Initialize modal
        // Initialize modal
        const addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
        const addUserForm = $('#addUserForm');
        
        if (!addUserForm.length) {
            console.error('Form not found!');
            return;
        }

        // Real-time validation
        addUserForm.find('input, select').on('keyup change', function() {
            const field = $(this);
            const fieldName = field.attr('name');
            const value = field.val();
            
            // Clear existing error messages
            field.removeClass('is-invalid');
            field.next('.invalid-feedback').remove();

            // Validate based on field type
            switch(fieldName) {
                case 'name':
                    if (!value) {
                        field.addClass('is-invalid');
                        field.after('<div class="invalid-feedback">Full name is required.</div>');
                    } else if (value.length > 100) {
                        field.addClass('is-invalid');
                        field.after('<div class="invalid-feedback">Maximum 100 characters allowed.</div>');
                    } else if (!/^[a-zA-Z\s\.\-']+/.test(value)) {
                        field.addClass('is-invalid');
                        field.after('<div class="invalid-feedback">Only letters, spaces, dots, hyphens, and apostrophes are allowed.</div>');
                    }
                    break;

                case 'username':
                    if (!value) {
                        field.addClass('is-invalid');
                        field.after('<div class="invalid-feedback">Username is required.</div>');
                    } else if (value.length > 50) {
                        field.addClass('is-invalid');
                        field.after('<div class="invalid-feedback">Maximum 50 characters allowed.</div>');
                    } else if (!/^[a-zA-Z0-9._-]+$/.test(value)) {
                        field.addClass('is-invalid');
                        field.after('<div class="invalid-feedback">Username can only contain letters, numbers, dots, underscores, and hyphens.</div>');
                    }
                    break;

                case 'password':
                    if (!value) {
                        field.parent().find('.invalid-feedback').remove();
                        field.parent().append('<div class="invalid-feedback">Password is required.</div>');
                        field.addClass('is-invalid');
                    } else if (value.length < 8) {
                        field.parent().find('.invalid-feedback').remove();
                        field.parent().append('<div class="invalid-feedback">Password must be at least 8 characters long.</div>');
                        field.addClass('is-invalid');
                    } else {
                        // Clear error if password is valid
                        field.parent().find('.invalid-feedback').remove();
                        field.removeClass('is-invalid');
                        // Store the password value in a data attribute
                        field.data('stored-value', value);
                    }
                    break;

                case 'confirmPassword':
                    if (!value) {
                        field.addClass('is-invalid');
                        field.after('<div class="invalid-feedback">Please confirm your password.</div>');
                    } else if (value !== $('#password').val()) {
                        field.addClass('is-invalid');
                        field.after('<div class="invalid-feedback">Password confirmation does not match.</div>');
                    }
                    break;

                case 'role':
                    if (!value) {
                        field.addClass('is-invalid');
                        field.after('<div class="invalid-feedback">Role is required.</div>');
                    } else if (!['superadmin', 'admin', 'user'].includes(value)) {
                        field.addClass('is-invalid');
                        field.after('<div class="invalid-feedback">Please select a valid role.</div>');
                    }
                    break;
            }

            // Validate permissions based on role
            const roleSelect = $('#role');
            const role = roleSelect.val();
            const permissions = [];
            
            // Get all selected permissions
            $('input[name="permissions[]"]:checked').each(function() {
                permissions.push($(this).val());
            });

            // Clear previous permission errors
            $('.permission-validation').remove();

            // Role-based permission validation
            if (role === 'superadmin') {
                // Superadmin can have any combination of permissions
            } else if (role === 'admin') {
                // Admin must have at least one permission
                if (permissions.length === 0) {
                    $('<div class="text-danger mb-3 permission-validation">Admin must have at least one permission selected.</div>').insertBefore('#permissionView');
                }
            } else if (role === 'user') {
                // User must have at least view permission
                if (!permissions.includes('view')) {
                    $('<div class="text-danger mb-3 permission-validation">Users must have at least the "View Records" permission.</div>').insertBefore('#permissionView');
                }
            }

            // Update form validity
            const hasPermissionErrors = $('.permission-validation').length > 0;
            if (hasPermissionErrors) {
                addUserForm[0].checkValidity();
            }
        });

            // Add password confirmation validation
            $('#password, #password_confirmation').on('input', function() {
                const password = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();
                const passwordGroup = $('#password_confirmation').closest('.form-floating');
                
                if (password && confirmPassword && password !== confirmPassword) {
                    passwordGroup.addClass('has-error');
                    passwordGroup.find('label').after('<div class="text-danger">Passwords do not match</div>');
                } else {
                    passwordGroup.removeClass('has-error');
                    passwordGroup.find('.text-danger').remove();
                }
            });

            // Form submission
            addUserForm.on('submit', function(e) {
                e.preventDefault();
                
                // Get all form fields
                const fields = addUserForm.find('input, select');
                let isValid = true;
                
                // Validate password confirmation
                const password = $('#password').val();
                const confirmPassword = $('#password_confirmation').val();
                if (password !== confirmPassword) {
                    isValid = false;
                    $('<div class="text-danger mb-3">Passwords do not match</div>').insertBefore('#password_confirmation');
                } else {
                    $('#password_confirmation').next('.text-danger').remove();
                }
            
            // Validate all fields before submission
            fields.each(function() {
                const field = $(this);
                const value = field.val();
                
                // Clear existing error messages
                field.removeClass('is-invalid');
                field.parent().find('.invalid-feedback').remove();
                
                // Validate based on field type
                switch(field.attr('name')) {
                    case 'name':
                        if (!value) {
                            field.addClass('is-invalid');
                            field.parent().append('<div class="invalid-feedback">Full name is required.</div>');
                            isValid = false;
                        } else if (value.length > 100) {
                            field.addClass('is-invalid');
                            field.parent().append('<div class="invalid-feedback">Maximum 100 characters allowed.</div>');
                            isValid = false;
                        } else if (!/^[a-zA-Z\s\.\-\']+/.test(value)) {
                            field.addClass('is-invalid');
                            field.parent().append('<div class="invalid-feedback">Only letters, spaces, dots, hyphens, and apostrophes are allowed.</div>');
                            isValid = false;
                        }
                        break;
                    
                    case 'username':
                        if (!value) {
                            field.addClass('is-invalid');
                            field.parent().append('<div class="invalid-feedback">Username is required.</div>');
                            isValid = false;
                        } else if (value.length > 50) {
                            field.addClass('is-invalid');
                            field.parent().append('<div class="invalid-feedback">Maximum 50 characters allowed.</div>');
                            isValid = false;
                        } else if (!/^[a-zA-Z0-9._-]+$/.test(value)) {
                            field.addClass('is-invalid');
                            field.parent().append('<div class="invalid-feedback">Username can only contain letters, numbers, dots, underscores, and hyphens.</div>');
                            isValid = false;
                        }
                        break;
                    
                    case 'password':
                        const passwordValue = field.val();
                        
                        if (!passwordValue) {
                            field.addClass('is-invalid');
                            field.parent().append('<div class="invalid-feedback">Password is required.</div>');
                            isValid = false;
                        } else if (passwordValue.length < 8) {
                            field.addClass('is-invalid');
                            field.parent().append('<div class="invalid-feedback">Password must be at least 8 characters long.</div>');
                            isValid = false;
                        }
                        break;
                    
                    case 'confirmPassword':
                        if (!value) {
                            field.addClass('is-invalid');
                            field.parent().append('<div class="invalid-feedback">Please confirm your password.</div>');
                            isValid = false;
                        } else if (value !== $('#password').val()) {
                            field.addClass('is-invalid');
                            field.parent().append('<div class="invalid-feedback">Password confirmation does not match.</div>');
                            isValid = false;
                        }
                        break;
                    
                    case 'role':
                        if (!value) {
                            field.addClass('is-invalid');
                            field.parent().append('<div class="invalid-feedback">Role is required.</div>');
                            isValid = false;
                        } else if (!['superadmin', 'admin', 'user'].includes(value)) {
                            field.addClass('is-invalid');
                            field.parent().append('<div class="invalid-feedback">Please select a valid role.</div>');
                            isValid = false;
                        }
                        break;
                }
            });
            
            // Only proceed with AJAX if all validations pass
            if (!isValid) {
                return;
            }
            
            const formData = new FormData(this);
            const url = $(this).attr('action');

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .done(function(data) {
                if (data.success) {
                    addUserModal.hide();
                    window.location.reload();
                } else {
                    // Handle validation errors
                    addUserForm.find('.alert-danger').remove();
                    
                    // Add error summary at the top
                    const errorSummary = $('<div class="alert alert-danger"></div>');
                    errorSummary.html('<ul>' + 
                        Object.values(data.errors).map(error => `<li>${error}</li>`).join('') + 
                        '</ul>');
                    addUserForm.prepend(errorSummary);

                    // Add error classes to fields
                    Object.keys(data.errors).forEach(field => {
                        const input = addUserForm.find(`[name="${field}"]`);
                        if (input.length) {
                            input.addClass('is-invalid');
                            const feedback = $('<div class="invalid-feedback"></div>');
                            feedback.text(data.errors[field]);
                            input.parent().append(feedback);
                        }
                    });
                }
            })
            .fail(function(xhr, status, error) {
                console.error('Error:', error);
                
                // Try to get the error message from the response
                let errorMessage = 'An error occurred while processing your request.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    } else if (response.errors) {
                        errorMessage = Object.values(response.errors).join('\n');
                    }
                } catch (e) {
                    console.error('Failed to parse response:', e);
                }
                
                // Show error message
                alert(errorMessage);
            });
        });
    });
</script>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUserForm" action="{{ route('user.update') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" id="editUserId" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="editName" name="name" required>
                                <label for="editName">Full Name</label>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="editUsername" name="username" required readonly>
                                        <label for="editUsername">Username</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="editPassword" name="password">
                                        <label for="editPassword">New Password (Leave blank to keep current)</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="password" class="form-control" id="editPasswordConfirmation" name="password_confirmation">
                                        <label for="editPasswordConfirmation">Confirm New Password</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <select class="form-select" id="editRole" name="role" required>
                                            <option value="">Select Role</option>
                                            <option value="superadmin">Super Admin</option>
                                            <option value="admin">Admin</option>
                                            <option value="user">User</option>
                                        </select>
                                        <label for="editRole">Role</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <h6 class="mb-3">Permissions</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="view" id="editPermissionView" name="permissions[]">
                                                <label class="form-check-label" for="editPermissionView">
                                                    View Records
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="create" id="editPermissionCreate" name="permissions[]">
                                                <label class="form-check-label" for="editPermissionCreate">
                                                    Create Records
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="edit" id="editPermissionEdit" name="permissions[]">
                                                <label class="form-check-label" for="editPermissionEdit">
                                                    Edit Records
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="approve" id="editPermissionApprove" name="permissions[]">
                                                <label class="form-check-label" for="editPermissionApprove">
                                                    Approve Requests
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Update User</button>
                </div>
        </div>
    </div>
</div>

<!-- Lock User Modal -->
<div class="modal fade" id="lockUserModal" tabindex="-1" aria-labelledby="lockUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="lockUserModalLabel">Lock User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to lock this user?</p>
                <p><strong>Username:</strong> <span id="lockUsername"></span></p>
                <p><strong>Name:</strong> <span id="lockName"></span></p>
                <p class="text-muted small">The user will be permanently locked until an admin unlocks them.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmLock">Lock User</button>
            </div>
        </div>
    </div>
</div>

<!-- Unlock User Modal -->
<div class="modal fade" id="unlockUserModal" tabindex="-1" aria-labelledby="unlockUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="unlockUserModalLabel">Unlock User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to unlock this user?</p>
                <p><strong>Username:</strong> <span id="unlockUsername"></span></p>
                <p><strong>Name:</strong> <span id="unlockName"></span></p>
                <p class="text-muted small">The user will be unlocked and able to access their account again.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmUnlock">Unlock User</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize modal when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modals
        const editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
        const lockUserModal = new bootstrap.Modal(document.getElementById('lockUserModal'));
        const blockUserModal = new bootstrap.Modal(document.getElementById('blockUserModal'));

        // Edit user button click handler
        document.querySelectorAll('.edit-user-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const username = this.dataset.username;
                const role = this.dataset.role;
                const permissions = JSON.parse(this.dataset.permissions);

                // Fill form with existing data
                document.getElementById('editUserId').value = id;
                document.getElementById('editName').value = name;
                document.getElementById('editUsername').value = username;
                document.getElementById('editRole').value = role;

                // Set permissions
                const permissionInputs = document.querySelectorAll('input[name="permissions[]"]');
                permissionInputs.forEach(input => {
                    input.checked = permissions.includes(input.value);
                });

                // Show modal
                editUserModal.show();
            });
        });

        // Handle edit form submission
        document.getElementById('editUserForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const url = this.action;

            fetch(url, {
                method: 'PUT',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    editUserModal.hide();
                    location.reload();

                    
                } else {
                    // Show error message in modal
                    const modalBody = document.querySelector('#editUserModal .modal-body');
                    // Remove any existing error messages
                    const existingError = modalBody.querySelector('.alert-danger');
                    if (existingError) {
                        existingError.remove();
                    }
                    
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger mb-3';
                    errorDiv.textContent = data.error || 'Failed to update user';
                    
                    // Add error message at the top of the modal body
                    modalBody.insertBefore(errorDiv, modalBody.firstChild);
                    
                    // Ensure the error message is visible
                    modalBody.scrollTop = 0;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const modalBody = document.querySelector('#editUserModal .modal-body');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mb-3';
                errorDiv.textContent = 'An error occurred while updating the user';
                modalBody.insertBefore(errorDiv, modalBody.firstChild);
            });
        });

        // Lock/unlock user button click handler
        document.querySelectorAll('.lock-user-btn, .unlock-user-btn').forEach(button => {
            button.addEventListener('click', function() {
                const username = this.dataset.username;
                const name = this.dataset.name;
                const userId = this.dataset.userId;
                const isLock = this.classList.contains('lock-user-btn');
                
                console.log('Button clicked:', {
                    username,
                    name,
                    userId,
                    isLock
                });
                
                // Get the appropriate modal IDs
                const modalId = isLock ? 'lockUserModal' : 'unlockUserModal';
                const usernameId = isLock ? 'lockUsername' : 'unlockUsername';
                const nameId = isLock ? 'lockName' : 'unlockName';
                const confirmId = isLock ? 'confirmLock' : 'confirmUnlock';
                const action = isLock ? 'block' : 'unblock';

                // Check if modal exists
                const modalElement = document.getElementById(modalId);
                if (!modalElement) {
                    console.error('Modal not found:', modalId);
                    return;
                }

                // Show appropriate modal
                const modal = new bootstrap.Modal(document.getElementById(modalId));
                
                // Check if modal is initialized
                if (!modal._element) {
                    console.error('Modal not initialized properly:', modalId);
                    return;
                }
                
                modal.show();

                // Update modal content after modal is shown
                modal._element.addEventListener('shown.bs.modal', function() {
                    console.log('Modal shown, updating content');
                    const usernameElement = document.getElementById(usernameId);
                    const nameElement = document.getElementById(nameId);
                    
                    if (usernameElement) {
                        usernameElement.textContent = username;
                    } else {
                        console.error('Username element not found:', usernameId);
                    }
                    
                    if (nameElement) {
                        nameElement.textContent = name;
                    } else {
                        console.error('Name element not found:', nameId);
                    }
                });

                // Handle confirmation
                document.getElementById(confirmId).addEventListener('click', function() {
                    fetch(`/users/${userId}/${action}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().catch(() => ({
                                error: 'An unexpected error occurred while processing your request.'
                            }));
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.error);
                        }
                        if (data.success) {
                            modal.hide();
                            location.reload();
                        } else {
                            throw new Error(`Failed to ${isLock ? 'lock' : 'unlock'} user`);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert(error.message || 'An unexpected error occurred while processing your request.');
                    });
                });
            });
        });

        // Block user button click handler
        document.querySelectorAll('.block-user-btn').forEach(button => {
            button.addEventListener('click', function() {
                const username = this.dataset.username;
                const name = this.dataset.name;

                // Update modal content
                document.getElementById('blockUsername').textContent = username;
                document.getElementById('blockName').textContent = name;

                // Show modal
                blockUserModal.show();

                // Handle block confirmation
                document.getElementById('confirmBlock').addEventListener('click', function() {
                    const formData = new FormData();
                    formData.append('username', username);

                    fetch('{{ route('auth.block') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            blockUserModal.hide();
                            location.reload();
                        } else {
                            alert('Failed to block user');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            });
        });

        // Close modals on cancel
        document.querySelectorAll('.btn-close').forEach(button => {
            button.addEventListener('click', function() {
                const modal = bootstrap.Modal.getInstance(this.closest('.modal'));
                if (modal) {
                    modal.hide();
                }
            });
        });

        // Close modals on cancel buttons
        document.querySelectorAll('.btn-secondary[data-bs-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', function() {
                const modal = bootstrap.Modal.getInstance(this.closest('.modal'));
                if (modal) {
                    modal.hide();
                }
            });
        });
        var addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));
        
        // Initialize form validation
        var addUserForm = document.getElementById('addUserForm');
        if (addUserForm) {
            addUserForm.addEventListener('submit', function(event) {
                if (!addUserForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                addUserForm.classList.add('was-validated');
            }, false);
        }
    });
</script>