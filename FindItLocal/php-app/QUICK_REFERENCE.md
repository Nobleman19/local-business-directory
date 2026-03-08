# PHP APPLICATION - QUICK REFERENCE

## File Locations

```
📁 Core Files
  └─ c:\xampp\htdocs\FindItLocal\php-app\
     ├─ index.php ..................... Main router/entry point
     ├─ .htaccess ..................... URL rewriting
     └─ Database_Setup.sql ............ Database schema

📁 Configuration (config/)
  ├─ config.php ...................... App settings & DB credentials
  └─ Database.php .................... MySQL connection class

📁 Classes (classes/)
  ├─ Auth.php ........................ Authentication & sessions
  ├─ Validation.php .................. Input validation
  ├─ Helper.php ...................... Utility functions
  ├─ User.php ........................ User model
  ├─ Business.php .................... Business model
  ├─ Service.php ..................... Service model
  ├─ Booking.php ..................... Booking model
  ├─ Review.php ...................... Review model
  ├─ Category.php .................... Category model
  ├─ Payment.php ..................... Payment model
  ├─ Discount.php .................... Discount model
  ├─ Contact.php ..................... Contact model
  └─ Ticket.php ...................... Support ticket model

📁 Controllers (controllers/)
  ├─ AuthController.php .............. Login/register/profile
  ├─ BusinessController.php .......... Business operations
  ├─ ServiceController.php ........... Service operations
  ├─ BookingController.php ........... Booking operations
  ├─ ReviewController.php ............ Review operations
  ├─ CategoryController.php .......... Category operations
  ├─ PaymentController.php ........... Payment operations
  └─ ContactController.php ........... Contact operations

📁 Views (views/)
  ├─ layout.php ...................... Master template
  ├─ home.php ........................ Homepage
  ├─ about.php ....................... About page
  ├─ contact.php ..................... Contact form
  ├─ faq.php ......................... FAQ page
  ├─ 404.php ......................... Error page
  ├─ auth/
  │  ├─ login.php
  │  └─ register.php
  ├─ business/
  │  ├─ list.php ..................... All businesses
  │  └─ detail.php ................... Single business
  ├─ booking/
  │  └─ list.php ..................... User bookings
  └─ dashboard/
     └─ profile.php .................. User profile

📁 Assets (assets/)
  ├─ css/
  │  └─ style.css ................... Main stylesheet
  └─ js/
     └─ main.js ..................... JavaScript utilities

📁 Uploads (uploads/)
  └─ business_logos/ ................ Business images
```

## Database Connection

```php
// In any class:
$db = Database::getInstance();
$conn = $db->getConnection();

// Execute query
$result = $db->query("SELECT * FROM users");

// Prepared statement
$stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
```

## Authentication Usage

```php
// Start session
Auth::startSession();

// Check if logged in
if (Auth::isLoggedIn()) {
    $user = Auth::getCurrentUser();
    // $user has: id, email, role, first_name, last_name
}

// Require login for page
Auth::requireLogin();

// Require specific role
Auth::requireRole('business_owner');

// Check role
if (Auth::isBusinessOwner()) { ... }
if (Auth::isCustomer()) { ... }
if (Auth::isAdmin()) { ... }

// Login user
Auth::login($userData);

// Logout
Auth::logout();
```

## Creating Models

```php
// In classes/MyModel.php:
class MyModel {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    public function create($data) {
        $sql = "INSERT INTO table (col1, col2) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $data['col1'], $data['col2']);
        
        if ($stmt->execute()) {
            return ['success' => true, 'id' => $this->conn->insert_id];
        }
        return ['success' => false, 'error' => $stmt->error];
    }
}
```

## Creating Controllers

```php
// In controllers/MyController.php:
require_once __DIR__ . '/../classes/MyModel.php';

class MyController {
    private $model;

    public function __construct() {
        $this->model = new MyModel();
    }

    public function action() {
        Auth::requireLogin();
        $result = $this->model->getAll();
        return ['data' => $result];
    }
}
```

## Validation Examples

```php
// Email
if (!Validation::validateEmail($email)) {
    $error = 'Invalid email';
}

// Phone (10 digits)
if (!Validation::validatePhone($phone)) {
    $error = 'Invalid phone';
}

// Password (min 6 chars)
if (!Validation::validatePassword($pass)) {
    $error = 'Password too short';
}

// File upload
$validation = Validation::validateFileUpload($_FILES['file']);
if (!$validation['valid']) {
    $errors = $validation['errors'];
}

// Sanitize input
$safe_data = Validation::sanitize($user_input);
```

## Common Helper Functions

```php
// Format currency
echo Helper::formatCurrency(1234.56); // Rs. 1,234.56

// Format date
echo Helper::formatDate('2024-02-27'); // Feb 27, 2024

// Time ago
echo Helper::getTimeAgo('2024-02-27 10:00:00'); // 2 hours ago

// Upload file
$result = Helper::uploadFile($_FILES['image'], $uploadDir);
if ($result['success']) {
    $filename = $result['filename'];
}

// Generate transaction ID
$txn_id = Helper::generateTransactionId();

// Redirect
Helper::redirect('/FindItLocal/php-app/');

// JSON response
Helper::json(['success' => true], 200);

// Generate slug
$slug = Helper::generateSlug('Hello World'); // hello-world
```

## Adding New Routes

In `index.php`, add to the route handling section:

```php
} elseif ($request_uri === '/my-new-page') {
    $pageTitle = 'My Page';
    require_once __DIR__ . '/controllers/MyController.php';
    $controller = new MyController();
    $data = $controller->myAction();
    // Extract variables from $data if needed
    $view = __DIR__ . '/views/my-page.php';
```

## Session Variables

```php
// Available after login:
$_SESSION['user_id']      // User ID (int)
$_SESSION['email']        // Email (string)
$_SESSION['role']         // Role (customer/business_owner/admin)
$_SESSION['first_name']   // First name
$_SESSION['last_name']    // Last name
$_SESSION['login_time']   // Login timestamp

// Access via Auth class (recommended):
$user = Auth::getCurrentUser();
echo $user['first_name'];
```

## Database Tables Quick Reference

```
users
├─ id, first_name, last_name, email, password
├─ phone_number, address_line_1, address_line_2
├─ city, state, postal_code, role
└─ date_joined, last_login, is_active

businesses
├─ id, owner_id (FK), business_name, description
├─ location, contact_number, email, website
├─ working_hours, business_logo, rating
└─ is_verified, is_active

services
├─ id, business_id (FK), service_name, description
├─ price, duration, availability_status
├─ discount_percentage, service_category

bookings
├─ id, user_id (FK), service_id (FK), business_id (FK)
├─ booking_date, scheduled_date, status
├─ notes, total_amount, payment_status, discount_code

reviews
├─ id, user_id (FK), business_id (FK), service_id (FK)
├─ rating, review_text, review_date, is_verified_purchase

payments
├─ id, booking_id (FK), user_id (FK), amount, payment_date
├─ payment_method, transaction_id, payment_status
```

## URL Patterns

```
/                            Home
/login                       Login page
/register                    Register page
/logout                      Logout (redirects to home)
/businesses                  All businesses
/business/{id}               Business details
/search?q=text              Search by name
/search?location=text       Search by location
/search?category={id}       Filter by category
/bookings                    My bookings
/profile                     User profile
/contact                     Contact form
/about                       About us
/faq                         FAQ
```

## Common Patterns

### Get & Display Data
```php
$model = new Business();
$data = $model->getAll(20, 0); // limit, offset

$view = __DIR__ . '/views/business/list.php';
// In view: foreach($data as $item) { ... }
```

### Update Data
```php
$model = new User();
$result = $model->update($id, [
    'first_name' => 'John',
    'last_name' => 'Doe'
]);

if ($result['success']) {
    $message = 'Updated successfully';
} else {
    $error = $result['error'];
}
```

### Handle Form Submit
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = Validation::sanitize($_POST);
    
    if (empty($data['field'])) {
        $error = 'Field required';
    } else {
        $result = $model->create($data);
        if ($result['success']) {
            $message = 'Created successfully';
        }
    }
}
```

## Debugging Tips

```php
// Check what's in a variable
var_dump($variable);
print_r($array);

// Log to file
error_log("Debug message");
error_log(print_r($data, true));

// Check session
var_dump($_SESSION);

// View error log
// C:\xampp\apache\logs\error.log
```

## Production Checklist

- [ ] Change database password in config.php
- [ ] Set display_errors to 0
- [ ] Enable error logging
- [ ] Set up HTTPS
- [ ] Implement rate limiting
- [ ] Add CSRF tokens
- [ ] Update JWT_SECRET
- [ ] Configure backup system
- [ ] Test all features
- [ ] Optimize images
- [ ] Minify CSS/JS
- [ ] Set up monitoring
- [ ] Configure mail server

## Performance Optimization

```php
// Use indexes on frequently queried columns
// Already done in Database_Setup.sql

// Limit results
$limit = 20;
$offset = ($page - 1) * $limit;

// Use prepared statements (prevents SQL injection + faster)
$stmt = $conn->prepare($sql);

// Cache static data
// TODO: Add Redis caching

// Optimize queries
// SELECT only needed columns
// Use JOINs instead of multiple queries
```

---

**Quick Reference Version:** 2.0
**Last Updated:** February 27, 2024
**PHP Version:** 7.4+
**MySQL Version:** 5.7+
