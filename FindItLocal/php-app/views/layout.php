<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Find It Local'; ?> - Business Directory</title>
    <link rel="stylesheet" href="/FindItLocal/php-app/assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="/FindItLocal/php-app/">
                    <h1><i class="fas fa-map-location-dot"></i> NobleLink</h1>
                </a>
            </div>

            <ul class="navbar-menu">
                <li><a href="/FindItLocal/php-app/">Home</a></li>
                <li><a href="/FindItLocal/php-app/businesses">Browse Businesses</a></li>
                <li><a href="/FindItLocal/php-app/about">About Us</a></li>
                <li><a href="/FindItLocal/php-app/contact">Contact Us</a></li>

                <?php if ($isLoggedIn): ?>
                    <li><a href="/FindItLocal/php-app/dashboard">Dashboard</a></li>
                    <?php if ($userRole === 'business_owner'): ?>
                        <li><a href="/FindItLocal/php-app/owner/businesses">My Businesses</a></li>
                        <li><a href="/FindItLocal/php-app/owner/bookings">Service Bookings</a></li>
                        <li><a href="/FindItLocal/php-app/business/create">Create Business</a></li>
                    <?php else: ?>
                        <li><a href="/FindItLocal/php-app/bookings">My Bookings</a></li>
                    <?php endif; ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle"><?php echo htmlspecialchars($firstName); ?> <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-menu">
                            <li><a href="/FindItLocal/php-app/profile">Profile</a></li>
                            <li><a href="/FindItLocal/php-app/logout">Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li><a href="/FindItLocal/php-app/login" class="btn btn-primary">Login</a></li>
                    <li><a href="/FindItLocal/php-app/register" class="btn btn-secondary">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Messages -->
    <?php if (isset($message)): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="main-content">
        <?php include $view; ?>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>About NobleLink</h3>
                    <p>Your trusted business directory connecting trusted local professionals.</p>
                </div>

                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="/FindItLocal/php-app/">Home</a></li>
                        <li><a href="/FindItLocal/php-app/businesses">Businesses</a></li>
                        <li><a href="/FindItLocal/php-app/about">About</a></li>
                        <li><a href="/FindItLocal/php-app/contact">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-section">
                    <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2026 NobleLink. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="/FindItLocal/php-app/assets/js/main.js"></script>
    
    <script>
        // Password visibility toggle function
        function togglePasswordVisibility(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const toggleButton = event.currentTarget;
            const icon = toggleButton.querySelector('i');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Header hide/show on scroll functionality
        const navbar = document.querySelector('.navbar');
        let lastScrollTop = 0;
        let scrollDirection = 'up';
        
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            
            // Determine scroll direction
            if (currentScroll > lastScrollTop) {
                scrollDirection = 'down';
            } else {
                scrollDirection = 'up';
            }
            
            // Show/hide navbar based on scroll
            if (scrollDirection === 'down' && currentScroll > 100) {
                // Scrolling down - hide navbar
                navbar.classList.add('navbar-hidden');
            } else {
                // Scrolling up or near top - show navbar
                navbar.classList.remove('navbar-hidden');
            }
            
            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // For Mobile or negative scrolling
        });
    </script>
</body>

</html>
