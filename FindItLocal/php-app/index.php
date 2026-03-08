<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/classes/Auth.php';
require_once __DIR__ . '/classes/Validation.php';
require_once __DIR__ . '/classes/Helper.php';
require_once __DIR__ . '/classes/User.php';
require_once __DIR__ . '/classes/Category.php';
require_once __DIR__ . '/controllers/AdminController.php';

// Start session
Auth::startSession();

// Get request URI
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_uri = str_replace('/FindItLocal/php-app', '', $request_uri);
if (empty($request_uri)) $request_uri = '/';

// Check if user is logged in
$isLoggedIn = Auth::isLoggedIn();
$currentUser = Auth::getCurrentUser();
$firstName = $currentUser['first_name'] ?? '';
$userRole = $currentUser['role'] ?? '';

// Prepare for views
$view = null;
$pageTitle = 'NobleLink';
$message = null;
$error = null;

// Route handling
if ($request_uri === '/') {
    $pageTitle = 'Home';
    $categoryModel = new Category();
    $categories = $categoryModel->getAll();
    $view = __DIR__ . '/views/home.php';
} elseif ($request_uri === '/login') {
    if ($isLoggedIn) {
        Helper::redirect('/FindItLocal/php-app/');
    }
    $pageTitle = 'Login';
    require_once __DIR__ . '/controllers/AuthController.php';
    $authController = new AuthController();
    $result = $authController->login();
    if (isset($result['error'])) {
        $error = $result['error'];
    } elseif (isset($result['success'])) {
        $message = $result['message'];
        if ($result['role'] === 'business_owner') {
            Helper::redirect('/FindItLocal/php-app/dashboard');
        } else {
            Helper::redirect('/FindItLocal/php-app/');
        }
    }
    $view = __DIR__ . '/views/auth/login.php';
} elseif ($request_uri === '/register') {
    if ($isLoggedIn) {
        Helper::redirect('/FindItLocal/php-app/');
    }
    $pageTitle = 'Register';
    require_once __DIR__ . '/controllers/AuthController.php';
    $authController = new AuthController();
    $result = $authController->signup();
    if (isset($result['error'])) {
        $error = $result['error'];
    } elseif (isset($result['success'])) {
        $message = $result['message'];
        Helper::redirect('/FindItLocal/php-app/dashboard');
    }
    $view = __DIR__ . '/views/auth/register.php';
} elseif ($request_uri === '/logout') {
    Auth::logout();
    Helper::redirect('/FindItLocal/php-app/');
} elseif ($request_uri === '/businesses') {
    $pageTitle = 'Businesses';
    require_once __DIR__ . '/controllers/BusinessController.php';
    $businessController = new BusinessController();
    $data = $businessController->getAll();
    $businesses = $data['businesses'];
    $page = $data['page'];
    $view = __DIR__ . '/views/business/list.php';
} elseif (preg_match('/^\/category\/(\d+)/', $request_uri, $matches)) {
    $category_id = $matches[1];
    $pageTitle = 'Category Businesses';
    require_once __DIR__ . '/controllers/BusinessController.php';
    $businessController = new BusinessController();
    $_GET['category'] = $category_id;
    $_GET['page'] = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $data = $businessController->search();
    $businesses = $data['businesses'];
    $page = $data['page'];
    
    // Get category name for page title
    $categoryModel = new Category();
    $category = $categoryModel->getById($category_id);
    if ($category) {
        $pageTitle = 'Businesses in ' . htmlspecialchars($category['category_name']);
    }
    $view = __DIR__ . '/views/business/list.php';
} elseif (preg_match('/^\/business\/(\d+)/', $request_uri, $matches)) {
    $business_id = $matches[1];
    $pageTitle = 'Business Details';
    require_once __DIR__ . '/controllers/BusinessController.php';
    require_once __DIR__ . '/controllers/ReviewController.php';
    $businessController = new BusinessController();
    $_GET['id'] = $business_id;
    $data = $businessController->getById();
    $business = $data['business'];
    
    if ($business) {
        $pageTitle = htmlspecialchars($business['business_name']);
        
        // Get services
        require_once __DIR__ . '/classes/Service.php';
        $serviceModel = new \Service();
        $business['services'] = $serviceModel->getByBusinessId($business_id);
        
        // Get reviews
        $reviewController = new ReviewController();
        $_GET['business_id'] = $business_id;
        $_GET['page'] = 1;
        $_GET['limit'] = 10;
        $reviewData = $reviewController->getByBusiness();
        $reviews = $reviewData['reviews'];
    } else {
        $error = 'Business not found';
    }
    $view = __DIR__ . '/views/business/detail.php';
} elseif ($request_uri === '/search') {
    $pageTitle = 'Search Results';
    require_once __DIR__ . '/controllers/BusinessController.php';
    $businessController = new BusinessController();
    $data = $businessController->search();
    $businesses = $data['businesses'];
    $page = $data['page'];
    $query = $data['query'];
    $location = $data['location'];
    $view = __DIR__ . '/views/business/list.php';
} elseif (preg_match('/^\/booking\/create\/(\d+)/', $request_uri, $matches)) {
    Auth::requireLogin();
    $business_id = $matches[1];
    $pageTitle = 'Create Booking';
    
    try {
        require_once __DIR__ . '/controllers/BookingController.php';
        require_once __DIR__ . '/controllers/BusinessController.php';
        
        // Verify business exists
        $businessController = new BusinessController();
        $_GET['id'] = $business_id;
        $businessData = $businessController->getById();
        
        if (!$businessData['business']) {
            $error = 'Business not found';
            http_response_code(404);
            $view = __DIR__ . '/views/404.php';
        } else {
            $business = $businessData['business'];
            
            // Get services for this business
            require_once __DIR__ . '/classes/Service.php';
            $serviceModel = new \Service();
            $services = $serviceModel->getByBusinessId($business_id);
            $business['services'] = $services;
            
            // Handle booking creation
            $bookingController = new BookingController();
            $_GET['business_id'] = $business_id;
            $result = $bookingController->create();
            
            if (isset($result['error'])) {
                $error = $result['error'];
            } elseif (isset($result['success']) && $result['success']) {
                $message = $result['message'];
                // Store booking confirmation data in session
                $_SESSION['booking_id'] = $result['id'];
                $_SESSION['booking_confirmation'] = true;
                // Redirect to booking confirmation page
                Helper::redirect('/FindItLocal/php-app/booking/confirm/' . $result['id']);
            }
            
            $view = __DIR__ . '/views/booking/create.php';
        }
    } catch (Exception $e) {
        $error = 'An error occurred: ' . $e->getMessage();
        $view = __DIR__ . '/views/404.php';
    }
} elseif (preg_match('/^\/booking\/confirm\/(\d+)/', $request_uri, $matches)) {
    Auth::requireLogin();
    $booking_id = $matches[1];
    $pageTitle = 'Booking Confirmation';
    
    try {
        require_once __DIR__ . '/controllers/BookingController.php';
        require_once __DIR__ . '/classes/Booking.php';
        
        // Fetch the booking details
        $bookingModel = new Booking();
        $booking = $bookingModel->getById($booking_id);
        
        if (!$booking) {
            $error = 'Booking not found';
            http_response_code(404);
            $view = __DIR__ . '/views/404.php';
        } else {
            // Verify booking belongs to current user
            $user = Auth::getCurrentUser();
            if ($booking['user_id'] != $user['id']) {
                $error = 'You do not have permission to view this booking';
                http_response_code(403);
                $view = __DIR__ . '/views/404.php';
            } else {
                $view = __DIR__ . '/views/booking/confirm.php';
            }
        }
    } catch (Exception $e) {
        $error = 'An error occurred: ' . $e->getMessage();
        $view = __DIR__ . '/views/404.php';
    }
} elseif (preg_match('/^\/booking\/(\d+)(?!\/confirm)/', $request_uri, $matches)) {
    Auth::requireLogin();
    $booking_id = $matches[1];
    $pageTitle = 'Booking Details';
    
    try {
        require_once __DIR__ . '/classes/Booking.php';
        
        // Fetch the booking details
        $bookingModel = new Booking();
        $booking = $bookingModel->getById($booking_id);
        
        if (!$booking) {
            $error = 'Booking not found';
            http_response_code(404);
            $view = __DIR__ . '/views/404.php';
        } else {
            // Verify booking belongs to current user or is business owner
            $user = Auth::getCurrentUser();
            if ($booking['user_id'] != $user['id'] && $user['role'] !== 'admin') {
                $error = 'You do not have permission to view this booking';
                http_response_code(403);
                $view = __DIR__ . '/views/404.php';
            } else {
                $view = __DIR__ . '/views/booking/detail.php';
            }
        }
    } catch (Exception $e) {
        $error = 'An error occurred: ' . $e->getMessage();
        $view = __DIR__ . '/views/404.php';
    }
} elseif ($request_uri === '/bookings') {
    Auth::requireLogin();
    $pageTitle = 'My Bookings';
    require_once __DIR__ . '/controllers/BookingController.php';
    $bookingController = new BookingController();
    $data = $bookingController->getUserBookings();
    $bookings = $data['bookings'];
    $page = $data['page'];
    $view = __DIR__ . '/views/booking/list.php';
} elseif ($request_uri === '/profile') {
    Auth::requireLogin();
    $pageTitle = 'Profile';
    require_once __DIR__ . '/controllers/AuthController.php';
    $authController = new AuthController();
    $data = $authController->updateProfile();
    if (isset($data['error'])) {
        $error = $data['error'];
    } elseif (isset($data['success'])) {
        $message = $data['message'];
    }
    $user = $data['user'];
    $view = __DIR__ . '/views/dashboard/profile.php';
} elseif ($request_uri === '/dashboard') {
    Auth::requireLogin();
    $pageTitle = 'Dashboard';
    require_once __DIR__ . '/controllers/BookingController.php';
    require_once __DIR__ . '/controllers/BusinessController.php';
    require_once __DIR__ . '/classes/Business.php';
    
    $user = Auth::getCurrentUser();
    
    // Get user data
    $userModel = new User();
    $userData = $userModel->getById($user['id']);
    
    // Get user's bookings
    $bookingController = new BookingController();
    $_GET['page'] = 1;
    $_GET['limit'] = 10;
    $bookingData = $bookingController->getUserBookings();
    $bookings = $bookingData['bookings'] ?? [];
    
    // Get user's businesses if business owner
    $ownedBusinesses = [];
    if ($user['role'] === 'business_owner') {
        $businessModel = new Business();
        $ownedBusinesses = $businessModel->getByOwner($user['id']);
    }
    
    $view = __DIR__ . '/views/dashboard/index.php';
} elseif ($request_uri === '/contact') {
    $pageTitle = 'Contact Us';
    require_once __DIR__ . '/controllers/ContactController.php';
    $contactController = new ContactController();
    $result = $contactController->sendMessage();
    if (isset($result['error'])) {
        $error = $result['error'];
    } elseif (isset($result['success'])) {
        $message = $result['message'];
    }
    $view = __DIR__ . '/views/contact.php';
} elseif ($request_uri === '/business/create') {
    Auth::requireLogin();
    Auth::requireRole('business_owner');
    $pageTitle = 'Create Business';
    require_once __DIR__ . '/controllers/BusinessController.php';
    $businessController = new BusinessController();
    $data = $businessController->create();
    if (isset($data['error'])) {
        $error = $data['error'];
    } elseif (isset($data['success'])) {
        $message = $data['message'];
        Helper::redirect('/FindItLocal/php-app/dashboard');
    }
    $categories = $data['categories'] ?? [];
    $view = __DIR__ . '/views/business/create.php';
} elseif (preg_match('/^\/business\/edit\/(\d+)/', $request_uri, $matches)) {
    Auth::requireLogin();
    Auth::requireRole('business_owner');
    $business_id = $matches[1];
    $pageTitle = 'Edit Business';
    require_once __DIR__ . '/controllers/BusinessController.php';
    $businessController = new BusinessController();
    $_GET['id'] = $business_id;
    $data = $businessController->update();
    if (isset($data['error'])) {
        $error = $data['error'];
    } elseif (isset($data['success'])) {
        $message = $data['message'];
        Helper::redirect('/FindItLocal/php-app/dashboard');
    }
    $business = $data['business'] ?? null;
    $categories = $data['categories'] ?? [];
    $allCategories = $data['categories'] ?? [];
    if (!$business) {
        $error = 'Business not found or you do not have permission to edit it';
    }
    $view = __DIR__ . '/views/business/create.php';
} elseif (preg_match('/^\/business\/images\/(\d+)/', $request_uri, $matches)) {
    Auth::requireLogin();
    Auth::requireRole('business_owner');
    $business_id = $matches[1];
    $pageTitle = 'Business Images';
    require_once __DIR__ . '/controllers/BusinessImageController.php';
    $imageController = new BusinessImageController();
    $result = $imageController->upload();
    if (isset($result['error'])) {
        $error = $result['error'];
    } elseif (isset($result['success'])) {
        $message = $result['message'];
    }
    $_GET['business_id'] = $business_id;
    $data = $imageController->getImages();
    $images = $data['images'] ?? [];
    $view = __DIR__ . '/views/business/images.php';
} elseif ($request_uri === '/business/images/delete') {
    Auth::requireLogin();
    Auth::requireRole('business_owner');
    require_once __DIR__ . '/controllers/BusinessImageController.php';
    $imageController = new BusinessImageController();
    $result = $imageController->delete();
    if (isset($result['success']) && $result['success']) {
        Helper::redirect($_SERVER['HTTP_REFERER'] ?? '/FindItLocal/php-app/dashboard');
    } else {
        $error = $result['error'] ?? 'Failed to delete image';
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    Helper::redirect($_SERVER['HTTP_REFERER'] ?? '/FindItLocal/php-app/dashboard');
} elseif ($request_uri === '/business/images/set-primary') {
    Auth::requireLogin();
    Auth::requireRole('business_owner');
    require_once __DIR__ . '/controllers/BusinessImageController.php';
    $imageController = new BusinessImageController();
    $result = $imageController->setPrimary();
    if (isset($result['success']) && $result['success']) {
        Helper::redirect($_SERVER['HTTP_REFERER'] ?? '/FindItLocal/php-app/dashboard');
    } else {
        $error = $result['error'] ?? 'Failed to update primary image';
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
    Helper::redirect($_SERVER['HTTP_REFERER'] ?? '/FindItLocal/php-app/dashboard');
} elseif ($request_uri === '/owner/businesses') {
    Auth::requireLogin();
    Auth::requireRole('business_owner');
    $pageTitle = 'My Businesses';
    require_once __DIR__ . '/controllers/OwnerController.php';
    $ownerController = new OwnerController();
    $data = $ownerController->viewBusinesses();
    $businesses = $data['businesses'] ?? [];
    if (isset($data['error'])) {
        $error = $data['error'];
    }
    $view = __DIR__ . '/views/business/owner-businesses.php';
} elseif ($request_uri === '/owner/bookings') {
    Auth::requireLogin();
    Auth::requireRole('business_owner');
    $pageTitle = 'Service Bookings';
    require_once __DIR__ . '/controllers/OwnerController.php';
    $ownerController = new OwnerController();
    
    // Handle booking responses
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        $result = $ownerController->confirmBooking();
        if (isset($result['success'])) {
            $message = $result['message'];
        } elseif (isset($result['error'])) {
            $error = $result['error'];
        }
    }
    
    $business_id = isset($_GET['business_id']) ? (int)$_GET['business_id'] : 0;
    $_GET['business_id'] = $business_id;
    $data = $ownerController->viewBookings();
    $business = $data['business'] ?? null;
    $bookings = $data['bookings'] ?? [];
    if (isset($data['error'])) {
        $error = $data['error'];
    }
    $view = __DIR__ . '/views/owner/bookings.php';
} elseif ($request_uri === '/about') {
    $pageTitle = 'About Us';
    $view = __DIR__ . '/views/about.php';
} elseif ($request_uri === '/faq') {
    $pageTitle = 'FAQ';
    $view = __DIR__ . '/views/faq.php';
} elseif (preg_match('/^\/admin/', $request_uri)) {
    // Admin Routes
    Auth::requireLogin();
    Auth::requireRole('admin');
    
    $adminController = new AdminController();
    
    if ($request_uri === '/admin' || $request_uri === '/admin/') {
        $pageTitle = 'Admin Dashboard';
        $dashboardData = $adminController->dashboard();
        $data = is_array($dashboardData) ? $dashboardData : [
            'total_businesses' => 0,
            'total_users' => 0,
            'total_reviews' => 0,
            'pending_bookings' => 0,
            'unverified_businesses' => 0,
            'recent_reviews' => [],
            'recent_bookings' => []
        ];
        $view = __DIR__ . '/views/admin/index.php';
    } elseif ($request_uri === '/admin/businesses' || preg_match('/^\/admin\/businesses/', $request_uri)) {
        $pageTitle = 'Manage Businesses';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? Validation::sanitize($_GET['search']) : '';
        $status = isset($_GET['status']) ? Validation::sanitize($_GET['status']) : 'all';
        
        // Handle business actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                if ($_POST['action'] === 'update_status') {
                    $business_id = (int)$_POST['business_id'];
                    $is_verified = isset($_POST['is_verified']) ? 1 : 0;
                    $is_active = isset($_POST['is_active']) ? 1 : 0;
                    if ($adminController->updateBusinessStatus($business_id, $is_verified, $is_active)) {
                        $message = 'Business status updated successfully';
                    } else {
                        $error = 'Failed to update business status';
                    }
                } elseif ($_POST['action'] === 'delete') {
                    $business_id = (int)$_POST['business_id'];
                    if ($adminController->deleteBusiness($business_id)) {
                        $message = 'Business deleted successfully';
                    } else {
                        $error = 'Failed to delete business';
                    }
                }
            }
        }
        
        $result = $adminController->getBusinesses($page, $search, $status);
        $businesses = $result['businesses'];
        $total = $result['total'];
        $limit = $result['limit'];
        $total_pages = ceil($total / $limit);
        
        $view = __DIR__ . '/views/admin/businesses.php';
    } elseif ($request_uri === '/admin/users' || preg_match('/^\/admin\/users/', $request_uri)) {
        $pageTitle = 'Manage Users';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? Validation::sanitize($_GET['search']) : '';
        $role = isset($_GET['role']) ? Validation::sanitize($_GET['role']) : 'all';
        
        $result = $adminController->getUsers($page, $search, $role);
        $users = $result['users'];
        $total = $result['total'];
        $limit = $result['limit'];
        $total_pages = ceil($total / $limit);
        
        $view = __DIR__ . '/views/admin/users.php';
    } elseif ($request_uri === '/admin/reviews' || preg_match('/^\/admin\/reviews/', $request_uri)) {
        $pageTitle = 'Manage Reviews';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $status = isset($_GET['status']) ? Validation::sanitize($_GET['status']) : 'all';
        
        // Handle review actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                if ($_POST['action'] === 'approve_review') {
                    $review_id = (int)$_POST['review_id'];
                    if ($adminController->updateReviewStatus($review_id, true)) {
                        $message = 'Review approved successfully';
                    } else {
                        $error = 'Failed to approve review';
                    }
                } elseif ($_POST['action'] === 'delete_review') {
                    $review_id = (int)$_POST['review_id'];
                    if ($adminController->deleteReview($review_id)) {
                        $message = 'Review deleted successfully';
                    } else {
                        $error = 'Failed to delete review';
                    }
                }
            }
        }
        
        $result = $adminController->getReviews($page, $status);
        $reviews = $result['reviews'];
        $total = $result['total'];
        $limit = $result['limit'];
        $total_pages = ceil($total / $limit);
        
        $view = __DIR__ . '/views/admin/reviews.php';
    } elseif ($request_uri === '/admin/bookings' || preg_match('/^\/admin\/bookings/', $request_uri)) {
        $pageTitle = 'Manage Bookings';
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $status = isset($_GET['status']) ? Validation::sanitize($_GET['status']) : 'all';
        
        // Handle booking actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action']) && $_POST['action'] === 'update_booking_status') {
                $booking_id = (int)$_POST['booking_id'];
                $new_status = Validation::sanitize($_POST['status']);
                if ($adminController->updateBookingStatus($booking_id, $new_status)) {
                    $message = 'Booking status updated successfully';
                } else {
                    $error = 'Failed to update booking status';
                }
            }
        }
        
        $result = $adminController->getBookings($page, $status);
        $bookings = $result['bookings'];
        $total = $result['total'];
        $limit = $result['limit'];
        $total_pages = ceil($total / $limit);
        
        $view = __DIR__ . '/views/admin/bookings.php';
    } else {
        http_response_code(404);
        $error = 'Admin page not found';
        $view = __DIR__ . '/views/404.php';
    }
} else {
    http_response_code(404);
    $error = 'Page not found';
    $view = __DIR__ . '/views/404.php';
}

// Load layout and view
if ($view && file_exists($view)) {
    include __DIR__ . '/views/layout.php';
} else {
    echo '<h1>404 - Page Not Found</h1>';
}
?>
