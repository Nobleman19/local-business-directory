# FindItLocal - Technical Reference Guide

Quick reference for developers working on the FindItLocal project.

## Database Connection

```php
require_once 'config/Database.php';
$db = new Database();
$connection = $db->getConnection();
```

## Common Database Operations

### Create User
```php
$user_data = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@example.com',
    'password' => 'password123',
    'phone_number' => '0960000000',
    'address_line_1' => '123 Main St',
    'city' => 'Lusaka',
    'state' => 'Lusaka',
    'postal_code' => '000000',
    'role' => 'customer'
];
$user = new User();
$user->register($user_data);
```

### Create Business
```php
$business_data = [
    'owner_id' => 1,
    'business_name' => 'Restaurant Name',
    'description' => 'Description',
    'location' => 'Location',
    'contact_number' => '0960000000',
    'email' => 'business@example.com',
    'website' => 'https://example.com',
    'working_hours' => 'Mon-Fri: 10AM-10PM',
    'business_logo' => 'logo.png'
];
$business = new Business();
$business_id = $business->create($business_data);
```

### Upload Business Image
```php
$image = new BusinessImage();
$image->upload($business_id, $image_path, $is_primary = false);
```

## Authentication

```php
$auth = new Auth();

// Check if logged in
if (!$auth->isLoggedIn()) {
    header("Location: /login");
}

// Get current user
$user = $auth->getCurrentUser();

// Check role
if (!$auth->hasRole('business_owner')) {
    die("Unauthorized");
}

// Logout
$auth->logout();
```

## Input Validation

```php
$validator = new Validation();

// Validate email
if (!$validator->validateEmail($_POST['email'])) {
    throw new Exception("Invalid email");
}

// Validate password
if (!$validator->validatePassword($_POST['password'])) {
    throw new Exception("Password must be at least 6 characters");
}

// Validate phone
if (!$validator->validatePhone($_POST['phone'])) {
    throw new Exception("Phone must be 10 digits");
}

// Sanitize all input
$clean_data = $validator->sanitize($_POST);
```

## Currency Formatting

```php
echo Helper::formatCurrency(1500);    // ZMW 1,500.00
echo Helper::formatCurrency(99.99);   // ZMW 99.99
```

## File Upload

```php
$uploaded_file = $_FILES['image'];

// Validate
if ($uploaded_file['size'] > MAX_FILE_SIZE) {
    throw new Exception("File too large");
}

$ext = strtolower(pathinfo($uploaded_file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ALLOWED_EXTENSIONS)) {
    throw new Exception("File type not allowed");
}

// Save
$path = Helper::uploadFile($uploaded_file, 'business_images');
```

## Database Queries

### Prepared Statement
```php
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
```

### Insert
```php
$sql = "INSERT INTO businesses (owner_id, business_name, location) VALUES (?, ?, ?)";
$stmt = $connection->prepare($sql);
$stmt->bind_param("iss", $owner_id, $business_name, $location);
$stmt->execute();
$business_id = $connection->insert_id;
```

### Update
```php
$sql = "UPDATE users SET first_name = ?, last_name = ? WHERE id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("ssi", $first_name, $last_name, $user_id);
$stmt->execute();
```

### Delete
```php
$sql = "DELETE FROM business_images WHERE id = ? AND business_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("ii", $image_id, $business_id);
$stmt->execute();
```

## Error Handling

```php
try {
    // Database operation
    $user = User::getById(1);
    if (!$user) {
        throw new Exception("User not found");
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $_SESSION['error'] = "An error occurred. Please try again later.";
    header("Location: /");
}
```

## Session Management

```php
// Set session variable
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_role'] = $user['role'];

// Get session variable
$user_id = $_SESSION['user_id'] ?? null;

// Unset session variable
unset($_SESSION['user_id']);

// Destroy entire session
session_destroy();
```

## Password Hashing

```php
// Hash password
$hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Verify password
if (password_verify($input_password, $hashed)) {
    // Password is correct
}
```

## CSRF Protection

```php
// Generate token
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;

// In form
<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

// Verify
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    throw new Exception("Invalid request");
}
```

## Common Routes

### Authentication
- `GET /login` - Login form
- `POST /login` - Process login
- `GET /register` - Registration form
- `POST /register` - Process registration
- `GET /logout` - Logout

### Business Management
- `GET /business` - List all businesses
- `GET /business/{id}` - View business
- `GET /business/create` - Create form (protected)
- `POST /business/create` - Save business (protected)
- `GET /business/edit/{id}` - Edit form (protected)
- `POST /business/edit/{id}` - Update business (protected)

### Images
- `GET /business/images/{id}` - Image gallery (protected)
- `POST /business/images/upload` - Upload image (protected)
- `POST /business/images/delete` - Delete image (protected)
- `POST /business/images/set-primary` - Set primary (protected)

### Dashboard
- `GET /dashboard` - User dashboard (protected)

## Role Checks

```php
// Check if customer
if ($auth->hasRole('customer')) { }

// Check if business owner
if ($auth->hasRole('business_owner')) { }

// Check if admin
if ($auth->hasRole('admin')) { }

// Check ownership
if ($business['owner_id'] != $auth->getCurrentUserId()) {
    die("Unauthorized");
}
```

## Object Instantiation

```php
// User operations
$user = new User();
$user->register($data);
$user->getByEmail($email);
$user->update($user_id, $data);

// Business operations
$business = new Business();
$business->create($data);
$business->getById($id);
$business->getByOwner($owner_id);
$business->update($business_id, $data);

// Image operations
$image = new BusinessImage();
$image->upload($business_id, $image_url, $is_primary);
$image->delete($image_id);
$image->setPrimary($image_id, $business_id);

// Validation
$validator = new Validation();
$validator->validateEmail($email);
$validator->sanitize($_POST);

// Helper functions
Helper::formatCurrency($amount);
Helper::redirect($url);
Helper::uploadFile($file, $directory);
Helper::formatDate($date);

// Categories
$category = new Category();
$category->getAll();
$category->getById($id);

// Contact
$contact = new Contact();
$contact->sendMessage($data);
$contact->getAllMessages();
```

## Configuration Constants

```php
// Database
DB_HOST              // localhost
DB_USER              // root
DB_PASSWORD          // (empty)
DB_NAME              // finditlocal_db
DB_PORT              // 3306

// Application
APP_NAME             // Find It Local - Business Directory
APP_URL              // http://localhost:8080
JWT_SECRET           // Secret key for tokens
SESSION_TIMEOUT      // 3600 seconds

// File Upload
UPLOAD_DIR           // /uploads/
MAX_FILE_SIZE        // 5242880 bytes (5MB)
ALLOWED_EXTENSIONS   // ['jpg', 'jpeg', 'png', 'gif', 'pdf']
```

## Data Binding Parameters

| Param | Type |
|-------|------|
| i | Integer |
| d | Double/Float |
| s | String |
| b | Blob |

```php
// Multiple parameters
$stmt->bind_param("isds", $id, $name, $amount, $date);
```

## Debugging

```php
// Log message
error_log("Debug message: " . print_r($data, true));

// Check error log
tail -f /logs/error.log

// Print variable
echo '<pre>';
print_r($variable);
echo '</pre>';

// Check session
var_dump($_SESSION);

// Database error
echo "Error: " . $connection->error;
```

## File Paths

| Path | Purpose |
|------|---------|
|/config/config.php | Configuration |
|/config/Database.php | Database class |
|/classes/ | Model/Business logic classes |
|/controllers/ | Request handlers |
|/views/ | HTML templates |
|/assets/css/ | Stylesheets |
|/assets/js/ | JavaScript files |
|/uploads/ | User uploaded files |
|/logs/ | Error and application logs |

## Useful SQL Queries

### Get user with role
```sql
SELECT * FROM users WHERE email = 'user@example.com' AND role = 'business_owner';
```

### Get all businesses by owner
```sql
SELECT * FROM businesses WHERE owner_id = 1;
```

### Get business with categories
```sql
SELECT b.*, GROUP_CONCAT(c.category_name) as categories
FROM businesses b
LEFT JOIN business_categories bc ON b.id = bc.business_id
LEFT JOIN categories c ON bc.category_id = c.id
WHERE b.id = 1
GROUP BY b.id;
```

### Get business with images
```sql
SELECT * FROM business_images 
WHERE business_id = 1 
ORDER BY is_primary DESC, uploaded_at DESC;
```

### Get average rating
```sql
SELECT AVG(rating) as avg_rating FROM reviews WHERE business_id = 1;
```

### Get recent bookings
```sql
SELECT b.*, s.service_name, u.first_name, u.last_name
FROM bookings b
JOIN services s ON b.service_id = s.id
JOIN users u ON b.customer_id = u.id
WHERE b.business_id = 1
ORDER BY b.created_at DESC
LIMIT 10;
```

---

**Last Updated**: March 1, 2026
