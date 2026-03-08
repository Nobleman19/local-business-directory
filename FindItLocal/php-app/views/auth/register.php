<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Create Account</h1>
            <p>Join NobleLink today</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="/FindItLocal/php-app/register" method="POST" class="auth-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input-group">
                        <input type="password" id="password" name="password" required class="password-input">
                        <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password')" title="Show/Hide Password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number (10 digits)</label>
                    <input type="tel" id="phone_number" name="phone_number" required pattern="\d{10}">
                </div>

                <div class="form-group">
                    <label for="address_line_1">Address Line 1</label>
                    <input type="text" id="address_line_1" name="address_line_1" required>
                </div>

                <div class="form-group">
                    <label for="address_line_2">Address Line 2 (Optional)</label>
                    <input type="text" id="address_line_2" name="address_line_2">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" id="state" name="state" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="postal_code">Postal Code (6 digits)</label>
                    <input type="text" id="postal_code" name="postal_code" required pattern="\d{6}">
                </div>

                <div class="form-group">
                    <label for="role">Account Type</label>
                    <select id="role" name="role" required>
                        <option value="customer">Customer (Browse & Book Services)</option>
                        <option value="business_owner">Business Owner (Manage Business & Services)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Create Account</button>
            </form>

            <p class="auth-footer">
                Already have an account? <a href="/FindItLocal/php-app/login">Login here</a>
            </p>
        </div>
    </div>
</div>
