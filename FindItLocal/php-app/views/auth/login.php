<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <h1>Login</h1>
            <p>Welcome back! Please login to your account</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="/FindItLocal/php-app/login" method="POST" class="auth-form">
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

                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>

            <p class="auth-footer">
                Don't have an account? <a href="/FindItLocal/php-app/register">Sign up here</a>
            </p>
        </div>
    </div>
</div>
