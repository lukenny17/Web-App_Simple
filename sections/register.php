<!-- register.php -->
<div id="registerForm" class="card mb-4" style="display:none;">
    <div class="card-header">Register</div>
    <div class="card-body">
        <form method="POST" action="index.php">
            <div class="form-group">
                <label for="registerName">Name</label>
                <input type="text" name="name" id="registerName" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="registerEmail">Email</label>
                <input type="email" name="email" id="registerEmail" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="registerPassword">Password</label>
                <input type="password" name="password" id="registerPassword" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="registerRole">Role</label>
                <select name="role" id="registerRole" class="form-control">
                    <option value="customer">Customer</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button type="submit" name="register" class="btn btn-secondary">Register</button>
        </form>
    </div>
</div>
