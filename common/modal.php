<!-- Dashboard Modal -->
<div class="modal fade" id="dashboardModal" tabindex="-1" aria-labelledby="dashboardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dashboardModalLabel">Dashboard</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="dashboardTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Login</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Register</button>
                    </li>
                </ul>
                <div class="tab-content" id="dashboardTabContent">
                    <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                        <!-- Login form here -->
                        <form method="POST">
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="login" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                        <!-- Register form here -->
                        <form method="POST">
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="role">Role:</label>
                                <select name="role" class="form-control" id="role">
                                    <option value="customer">Customer</option>
                                    <option value="staff">Staff</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <div class="form-group" id="accessCodeField" style="display: none">
                                <label for="access_code">Access Code (Required for Staff/Admin):</label>
                                <input type="text" id="access_code" name="access_code" class="form-control">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="register" class="btn btn-secondary">Register</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Shows 'Access Code' field if role selected as staff/admin
    document.addEventListener('DOMContentLoaded', function() {
        var roleSelect = document.getElementById('role');
        var accessCodeField = document.getElementById('accessCodeField');

        roleSelect.addEventListener('change', function() {
            if (this.value === 'staff' || this.value === 'admin') {
                accessCodeField.style.display = 'block';
            } else {
                accessCodeField.style.display = 'none';
            }
        });
    });
</script>