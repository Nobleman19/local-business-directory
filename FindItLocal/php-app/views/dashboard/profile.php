<div class="container">
    <div class="page-header">
        <h1>Profile</h1>
    </div>

    <div class="profile-container">
        <div class="profile-sidebar"></div>

        <div class="profile-content">
            <h2>Personal Information</h2>

            <?php if (isset($message)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="/FindItLocal/php-app/profile" method="POST" class="profile-form">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address (Read Only)</label>
                    <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="tel" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                </div>

                <h3>Address</h3>

                <div class="form-group">
                    <label for="address_line_1">Address Line 1</label>
                    <input type="text" id="address_line_1" name="address_line_1" value="<?php echo htmlspecialchars($user['address_line_1']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="address_line_2">Address Line 2</label>
                    <input type="text" id="address_line_2" name="address_line_2" value="<?php echo htmlspecialchars($user['address_line_2'] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City</label>
                        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="state">State</label>
                        <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($user['state']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code']); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>
