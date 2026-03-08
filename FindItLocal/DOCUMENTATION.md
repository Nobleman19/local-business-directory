# FindItLocal - Complete Software Engineering Documentation

## Table of Contents

1. [Project Overview](#project-overview)
2. [System Architecture](#system-architecture)
3. [Technology Stack](#technology-stack)
4. [Database Schema](#database-schema)
5. [Directory Structure](#directory-structure)
6. [Installation & Setup](#installation--setup)
7. [Configuration](#configuration)
8. [Core Classes Documentation](#core-classes-documentation)
9. [Controllers Documentation](#controllers-documentation)
10. [Routes & API Endpoints](#routes--api-endpoints)
11. [Views & Frontend](#views--frontend)
12. [Authentication & Authorization](#authentication--authorization)
13. [File Upload System](#file-upload-system)
14. [Error Handling](#error-handling)
15. [Security Considerations](#security-considerations)
16. [Development Guidelines](#development-guidelines)
17. [Troubleshooting](#troubleshooting)

---

## Project Overview

### Purpose
FindItLocal is a comprehensive business directory application designed to connect customers with local service providers. The platform enables businesses to list their services, manage their profiles, upload images, and accept bookings. Customers can browse, search, and book services from verified local businesses.

### Key Features
- **Multi-role User System**: Customer, Business Owner, and Admin roles
- **Business Management**: Create, edit, and manage business profiles
- **Image Gallery**: Upload and manage multiple business images
- **Service Listing**: Business owners can list and manage their services
- **Booking System**: Customers can book services and manage bookings
- **Review & Rating System**: Customer feedback and business ratings
- **Search & Filter**: Find businesses by category, location, and name
- **Payment Processing**: Secure payment integration
- **User Dashboard**: Role-specific dashboards for customers and business owners
- **Contact System**: Customer inquiries and support messaging

### Business Goals
- Provide a platform for local businesses to reach customers
- Enable service discovery and booking convenience
- Build trust through reviews and ratings
- Facilitate transactions and payments

---

## System Architecture

### Architectural Pattern
The application follows the **Model-View-Controller (MVC)** architectural pattern:

```
Request → Router (index.php) → Controller → Model/Class → Database
                                      ↓
                                    View (HTML)
                                      ↓
                                   Response
```

### Layers

#### 1. **Presentation Layer (Views)**
- HTML templates with Bootstrap styling
- Client-side validation using JavaScript
- Responsive design for mobile and desktop
- Located in `/views` directory

#### 2. **Business Logic Layer (Controllers & Classes)**
- Controllers handle request processing
- Business logic in dedicated classes
- Data validation and sanitization
- Located in `/controllers` and `/classes` directories

#### 3. **Data Access Layer (Database)**
- MySQLi for database operations
- Prepared statements for security
- Transaction support for complex operations
- Located in `/config/Database.php`

#### 4. **Routing Layer**
- Single entry point at `index.php`
- URL routing and request dispatching
- Authentication and authorization checks
- Session management

### Data Flow Diagram

```
User Input (Form/API)
    ↓
index.php (Router)
    ↓
Validation (Input sanitization)
    ↓
Controller (Business Logic)
    ↓
Model Classes (Database Operations)
    ↓
Database (MySQLi Connection)
    ↓
Response (JSON/HTML)
    ↓
View (Rendered to User)
```

---

## Technology Stack

### Backend
- **Language**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Database Driver**: MySQLi (Object-Oriented)
- **Session Management**: PHP Native Sessions

### Frontend
- **Markup**: HTML5
- **Styling**: CSS3, Bootstrap 5
- **Client-side Logic**: JavaScript (Vanilla)
- **Icons**: Font Awesome

### Tools & Libraries
- **Code Organization**: MVC Pattern
- **File Uploads**: Server-side file handling
- **Security**: Password hashing, prepared statements
- **Utilities**: Helper functions for common operations

### Server Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache with mod_rewrite (optional, for .htaccess)
- GD Library (for image processing)

---

## Database Schema

### Database Name
`finditlocal_db`

### Tables Overview

#### 1. **users** - User Account Information
```sql
Columns:
- id (INT, PK, AUTO_INCREMENT)
- first_name (VARCHAR 100)
- last_name (VARCHAR 100)
- email (VARCHAR 255, UNIQUE)
- password (VARCHAR 255, hashed)
- phone_number (VARCHAR 20)
- address_line_1 (VARCHAR 255)
- address_line_2 (VARCHAR 255, nullable)
- city (VARCHAR 100)
- state (VARCHAR 100)
- postal_code (VARCHAR 10)
- role (ENUM: 'customer', 'business_owner', 'admin')
- profile_image (VARCHAR 255, nullable)
- date_joined (TIMESTAMP)
- last_login (TIMESTAMP, nullable)
- is_active (BOOLEAN)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

Indexes:
- PRIMARY KEY (id)
- UNIQUE (email)
- INDEX (role)
```

#### 2. **categories** - Service Categories
```sql
Columns:
- id (INT, PK, AUTO_INCREMENT)
- category_name (VARCHAR 100, UNIQUE)
- description (TEXT, nullable)
- icon (VARCHAR 255, nullable)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

Indexes:
- PRIMARY KEY (id)
- INDEX (category_name)
```

#### 3. **businesses** - Business Profiles
```sql
Columns:
- id (INT, PK, AUTO_INCREMENT)
- owner_id (INT, FK → users.id)
- business_name (VARCHAR 255)
- description (TEXT)
- location (VARCHAR 255)
- contact_number (VARCHAR 20)
- email (VARCHAR 255)
- website (VARCHAR 255, nullable)
- working_hours (TEXT, nullable)
- business_logo (VARCHAR 255, nullable)
- rating (DECIMAL 3,2)
- is_verified (BOOLEAN)
- is_active (BOOLEAN)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

Foreign Keys:
- owner_id → users(id) ON DELETE CASCADE

Indexes:
- PRIMARY KEY (id)
- INDEX (owner_id)
- INDEX (business_name)
- INDEX (location)
```

#### 4. **business_categories** - Business-Category Junction
```sql
Columns:
- id (INT, PK, AUTO_INCREMENT)
- business_id (INT, FK → businesses.id)
- category_id (INT, FK → categories.id)

Indexes:
- PRIMARY KEY (id)
- UNIQUE (business_id, category_id)
```

#### 5. **business_images** - Business Gallery Images
```sql
Columns:
- id (INT, PK, AUTO_INCREMENT)
- business_id (INT, FK → businesses.id)
- image_url (VARCHAR 255)
- is_primary (BOOLEAN)
- uploaded_at (TIMESTAMP)

Foreign Keys:
- business_id → businesses(id) ON DELETE CASCADE

Indexes:
- PRIMARY KEY (id)
- INDEX (business_id)
```

#### 6. **services** - Business Services/Offerings
```sql
Columns:
- id (INT, PK, AUTO_INCREMENT)
- business_id (INT, FK → businesses.id)
- service_name (VARCHAR 255)
- description (TEXT)
- price (DECIMAL 10,2)
- duration (INT, in minutes)
- availability_status (ENUM: 'available', 'unavailable')
- discount_percentage (DECIMAL 5,2)
- service_category (ENUM: 'standard', 'premium', 'exclusive')
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

Foreign Keys:
- business_id → businesses(id) ON DELETE CASCADE

Indexes:
- PRIMARY KEY (id)
- INDEX (business_id)
```

#### 7. **bookings** - Service Bookings
```sql
Columns:
- id (INT, PK, AUTO_INCREMENT)
- customer_id (INT, FK → users.id)
- service_id (INT, FK → services.id)
- business_id (INT, FK → businesses.id)
- booking_date (DATETIME)
- booking_status (ENUM: 'pending', 'confirmed', 'completed', 'cancelled')
- payment_status (ENUM: 'pending', 'completed', 'failed')
- notes (TEXT, nullable)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

Foreign Keys:
- customer_id → users(id) ON DELETE CASCADE
- service_id → services(id) ON DELETE CASCADE
- business_id → businesses(id) ON DELETE CASCADE
```

#### 8. **reviews** - Customer Reviews
```sql
Columns:
- id (INT, PK, AUTO_INCREMENT)
- customer_id (INT, FK → users.id)
- business_id (INT, FK → businesses.id)
- rating (INT, 1-5)
- review_text (TEXT)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)

Foreign Keys:
- customer_id → users(id) ON DELETE CASCADE
- business_id → businesses(id) ON DELETE CASCADE
```

#### 9. **contacts** - Contact Form Submissions
```sql
Columns:
- id (INT, PK, AUTO_INCREMENT)
- name (VARCHAR 255)
- email (VARCHAR 255)
- phone (VARCHAR 20, nullable)
- subject (VARCHAR 255)
- message (TEXT)
- status (ENUM: 'unread', 'read', 'replied')
- created_at (TIMESTAMP)
```

#### 10. **payments** - Payment Records
```sql
Columns:
- id (INT, PK, AUTO_INCREMENT)
- booking_id (INT, FK → bookings.id)
- user_id (INT, FK → users.id)
- amount (DECIMAL 10,2)
- currency (VARCHAR 10, default: 'ZMW')
- payment_method (VARCHAR 50)
- transaction_id (VARCHAR 255, UNIQUE)
- payment_status (ENUM: 'pending', 'completed', 'failed')
- created_at (TIMESTAMP)

Foreign Keys:
- booking_id → bookings(id)
- user_id → users(id)
```

#### 11. **discounts** - Promotional Discounts
```sql
Columns:
- id (INT, PK, AUTO_INCREMENT)
- code (VARCHAR 50, UNIQUE)
- description (TEXT)
- discount_percentage (DECIMAL 5,2)
- start_date (DATETIME)
- end_date (DATETIME)
- max_uses (INT, nullable)
- current_uses (INT)
- is_active (BOOLEAN)
- created_at (TIMESTAMP)
```

### Relationships Overview

```
users (1) ──→ (M) businesses
           ──→ (M) bookings (as customer)
           ──→ (M) reviews

businesses (1) ──→ (M) services
             ──→ (M) business_images
             ──→ (M) business_categories
             ──→ (M) bookings
             ──→ (M) reviews

categories (1) ──→ (M) business_categories

services (1) ──→ (M) bookings

payments (1) ──→ (1) bookings
```

---

## Directory Structure

```
FindItLocal/
├── php-app/                          # Main application directory
│   ├── index.php                     # Single entry point (Router)
│   ├── config/
│   │   ├── config.php                # Configuration constants
│   │   └── Database.php              # MySQLi connection class
│   ├── classes/                      # Business logic & models
│   │   ├── Auth.php                  # Authentication & session management
│   │   ├── User.php                  # User model (registration, profile)
│   │   ├── Business.php              # Business model (CRUD operations)
│   │   ├── BusinessImage.php         # Image gallery management
│   │   ├── Category.php              # Category management
│   │   ├── Service.php               # Service model
│   │   ├── Booking.php               # Booking model
│   │   ├── Review.php                # Review model
│   │   ├── Payment.php               # Payment processing
│   │   ├── Discount.php              # Discount management
│   │   ├── Contact.php               # Contact form handling
│   │   ├── Ticket.php                # Support tickets (if applicable)
│   │   ├── Validation.php            # Input validation & sanitization
│   │   └── Helper.php                # Utility functions
│   ├── controllers/                  # Request handlers
│   │   ├── AuthController.php        # Auth routes (login, register, logout)
│   │   ├── BusinessController.php    # Business routes (create, edit, delete)
│   │   ├── BusinessImageController.php # Image management routes
│   │   ├── CategoryController.php    # Category routes
│   │   ├── ServiceController.php     # Service routes
│   │   ├── BookingController.php     # Booking routes
│   │   ├── ReviewController.php      # Review routes
│   │   ├── PaymentController.php     # Payment routes
│   │   └── ContactController.php     # Contact form routes
│   ├── views/                        # HTML templates
│   │   ├── layout.php                # Master layout template
│   │   ├── home.php                  # Homepage
│   │   ├── about.php                 # About page
│   │   ├── contact.php               # Contact form page
│   │   ├── faq.php                   # FAQ page
│   │   ├── 404.php                   # 404 error page
│   │   ├── auth/
│   │   │   ├── login.php             # Login form
│   │   │   └── register.php          # Registration form
│   │   ├── business/
│   │   │   ├── list.php              # Business directory listing
│   │   │   ├── detail.php            # Business detail page
│   │   │   ├── create.php            # Create/Edit business form
│   │   │   └── images.php            # Business image gallery
│   │   ├── booking/
│   │   │   └── list.php              # User bookings list
│   │   └── dashboard/
│   │       ├── index.php             # Main dashboard
│   │       └── profile.php           # User profile edit
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css             # Global styles
│   │   └── js/
│   │       └── main.js               # Global JavaScript
│   ├── uploads/                      # User uploaded files
│   │   ├── business_logos/           # Business logos
│   │   └── business_images/          # Gallery images
│   ├── logs/                         # Application logs
│   │   └── error.log                 # Error log file
│   ├── Database_Setup.sql            # Database initialization script
│   ├── README.md                     # Basic readme
│   ├── SETUP_GUIDE.md                # Setup instructions
│   ├── QUICK_REFERENCE.md            # Quick reference
│   ├── PROJECT_SUMMARY.md            # Project summary
│   └── FILE_VERIFICATION.md          # File verification notes
├── DOCUMENTATION.md                  # This file
└── gitignore.txt                     # Git ignore rules
```

---

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server (or equivalent)
- GD Library for PHP

### Step-by-Step Installation

#### 1. Clone/Download the Project
```bash
# Extract the FindItLocal project to your web server directory
# For XAMPP: C:\xampp\htdocs\FindItLocal
```

#### 2. Create Database
```bash
# Log into MySQL
mysql -u root -p

# Execute the database setup script
mysql -u root -p < php-app/Database_Setup.sql
```

#### 3. Configure Application
- Update `/config/config.php` with your database credentials
- Verify upload directories are created:
  - `/uploads/business_logos/`
  - `/uploads/business_images/`
  - `/logs/`

#### 4. Set File Permissions
```bash
# Make directories writable (Linux/Mac)
chmod -R 755 uploads/
chmod -R 755 logs/
```

#### 5. Start Application
- Access the application at `http://localhost:8080` (or your configured URL)
- First user to register will have admin access (modify in code if needed)

### Initial Data

#### Sample Categories (Insert into database)
```sql
INSERT INTO categories (category_name, description, icon) VALUES
('Restaurants', 'Food and dining services', 'utensils'),
('Salons & Spas', 'Personal care services', 'scissors'),
('Plumbing', 'Plumbing services', 'wrench'),
('Electricians', 'Electrical services', 'plug'),
('Auto Repair', 'Vehicle repair and maintenance', 'car'),
('Home Cleaning', 'Cleaning services', 'broom');
```

---

## Configuration

### config.php Settings

```php
// Database Configuration
DB_HOST       - Server hostname (default: localhost)
DB_USER       - Database username (default: root)
DB_PASSWORD   - Database password
DB_NAME       - Database name (default: finditlocal_db)
DB_PORT       - Database port (default: 3306)

// Application Configuration
APP_NAME      - Application title display
APP_URL       - Base application URL for links/redirects
JWT_SECRET    - Secret for JWT tokens (change in production)
SESSION_TIMEOUT - Session expiry time in seconds (default: 3600)

// File Upload Configuration
UPLOAD_DIR    - Base upload directory (default: /uploads/)
MAX_FILE_SIZE - Maximum file size in bytes (default: 5242880 = 5MB)
ALLOWED_EXTENSIONS - Array of allowed file extensions

// Error Reporting
display_errors, log_errors - Control error display and logging
error_log      - Path to error log file
```

### Environment Variables (Production)
For production deployment, consider using environment variables:

```php
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASSWORD') ?: '';
```

---

## Core Classes Documentation

### 1. Auth.php - Authentication & Session Management

**Purpose**: Manage user authentication, session handling, and authorization

**Key Methods**:

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `login()` | $email, $password | bool | Authenticate user and create session |
| `logout()` | - | bool | Destroy session and logout |
| `isLoggedIn()` | - | bool | Check if user is logged in |
| `getCurrentUser()` | - | array\|null | Get current session user data |
| `getCurrentUserId()` | - | int\|null | Get logged-in user ID |
| `hasRole()` | $role | bool | Check if user has specific role |
| `generateToken()` | - | string | Generate CSRF token |
| `verifyToken()` | $token | bool | Verify CSRF token |
| `checkPermission()` | $required_role | void | Redirect if insufficient permission |

**Usage Example**:
```php
require_once 'classes/Auth.php';
$auth = new Auth();

if ($auth->isLoggedIn()) {
    $user = $auth->getCurrentUser();
    echo "Welcome, " . $user['first_name'];
}

if ($auth->hasRole('business_owner')) {
    // Display business owner features
}
```

### 2. User.php - User Model

**Purpose**: Handle user registration, profile management, and retrieval

**Key Methods**:

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `register()` | $data (array) | bool | Create new user account |
| `getByEmail()` | $email | array\|null | Fetch user by email |
| `getById()` | $id | array\|null | Fetch user by ID |
| `update()` | $user_id, $data | bool | Update user profile |
| `verifyPassword()` | $password, $hash | bool | Verify password against hash |
| `updateLastLogin()` | $user_id | bool | Update last login timestamp |
| `getAll()` | - | array | Get all users |
| `delete()` | $user_id | bool | Soft delete user |

**Data Structure**:
```php
$user_data = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john@example.com',
    'password' => 'SecurePassword123',
    'phone_number' => '0960000000',
    'address_line_1' => '123 Main Street',
    'address_line_2' => 'Apt 4',
    'city' => 'Lusaka',
    'state' => 'Lusaka',
    'postal_code' => '000000',
    'role' => 'customer' // or 'business_owner'
];
```

### 3. Business.php - Business Model

**Purpose**: Handle business CRUD operations and business profile management

**Key Methods**:

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `create()` | $data (array) | int\|bool | Create new business, returns ID |
| `getAll()` | $limit, $offset | array | Get all businesses with pagination |
| `getById()` | $id | array\|null | Get business detail by ID |
| `getByOwner()` | $owner_id | array | Get all businesses by owner |
| `update()` | $business_id, $data | bool | Update business information |
| `delete()` | $business_id | bool | Delete business |
| `searchByName()` | $name | array | Search businesses by name |
| `searchByLocation()` | $location | array | Search businesses by location |
| `searchByCategory()` | $category_id | array | Search businesses by category |
| `addCategory()` | $business_id, $category_id | bool | Assign category to business |
| `removeCategory()` | $business_id, $category_id | bool | Remove category from business |
| `getCategories()` | $business_id | array | Get all categories of business |

**Data Structure**:
```php
$business_data = [
    'business_name' => 'John\'s Restaurant',
    'description' => 'Fine dining restaurant',
    'location' => '123 Main Street, Lusaka',
    'contact_number' => '0960000000',
    'email' => 'info@johnsrestaurant.com',
    'website' => 'https://johnsrestaurant.com',
    'working_hours' => 'Mon-Fri: 10AM-10PM, Sat-Sun: 12PM-11PM',
    'business_logo' => 'logo.png'
];
```

### 4. BusinessImage.php - Image Gallery Management

**Purpose**: Handle business image uploads, deletion, and gallery management

**Key Methods**:

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `upload()` | $business_id, $image_url, $is_primary | bool | Upload/save image URL |
| `getByBusinessId()` | $business_id | array | Get all images for business |
| `getPrimaryImage()` | $business_id | array\|null | Get primary image |
| `delete()` | $image_id | bool | Delete image from database & filesystem |
| `setPrimary()` | $image_id, $business_id | bool | Set image as primary |

**Image Storage**:
- Images stored in `/uploads/business_images/`
- Filename format: `{business_id}_{timestamp}_{random}.{extension}`
- Supported formats: jpg, jpeg, png, gif
- Max file size: 5MB

### 5. Validation.php - Input Validation & Sanitization

**Purpose**: Validate and sanitize user input data

**Key Methods**:

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `validateEmail()` | $email | bool | Validate email format |
| `validatePassword()` | $password | bool | Check password >= 6 characters |
| `validatePhone()` | $phone | bool | Validate 10-digit phone number |
| `validatePostalCode()` | $code | bool | Validate 6-digit postal code |
| `validateContactNumber()` | $number | bool | Validate +{1-15 digits} format |
| `validateUrl()` | $url | bool | Validate URL format |
| `validateFileUpload()` | $file | bool | Validate uploaded file |
| `sanitize()` | $data | array\|string | Clean input data |

**Usage Example**:
```php
$validator = new Validation();

if (!$validator->validateEmail($email)) {
    throw new Exception('Invalid email format');
}

$clean_data = $validator->sanitize($_POST);
```

### 6. Helper.php - Utility Functions

**Purpose**: Provide common utility functions

**Key Methods**:

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `formatCurrency()` | $amount | string | Format amount as ZMW X,XXX.XX |
| `redirect()` | $url | void | Redirect to URL |
| `uploadFile()` | $file, $directory | string\|bool | Save uploaded file |
| `formatDate()` | $date, $format | string | Format date string |
| `generateSlug()` | $text | string | Generate URL-friendly slug |
| `generateTransactionId()` | - | string | Generate unique transaction ID |
| `calculateDiscount()` | $price, $percent | float | Calculate discount amount |

**Example - Currency Formatting**:
```php
echo Helper::formatCurrency(15450.50); // Output: ZMW 15,450.50
echo Helper::formatCurrency(1000);     // Output: ZMW 1,000.00
```

### 7. Contact.php - Contact Form

**Purpose**: Handle contact form submissions

**Key Methods**:

| Method | Parameters | Returns | Description |
|--------|-----------|---------|-------------|
| `sendMessage()` | $data (array) | bool | Save contact message |
| `getAllMessages()` | - | array | Retrieve all messages |
| `getMessageById()` | $id | array\|null | Get specific message |
| `markAsRead()` | $message_id | bool | Mark message as read |
| `markAsReplied()` | $message_id | bool | Mark message as replied |
| `deleteMessage()` | $message_id | bool | Delete message |

---

## Controllers Documentation

### Request Flow

```
index.php (Router)
    ↓
Parse URL and get action
    ↓
Check authentication/authorization
    ↓
Call appropriate Controller method
    ↓
Return response (view or JSON)
```

### 1. AuthController.php - Authentication Routes

**Routes**:

| Route | Method | Description |
|-------|--------|-------------|
| `/login` | GET | Display login form |
| `/login` | POST | Process login |
| `/register` | GET | Display registration form |
| `/register` | POST | Process registration |
| `/logout` | GET | Logout user |

**Key Methods**:

```php
public function login()
// Validate email & password, create session

public function signup()
// Validate registration data, create user account, auto-login

public function logout()
// Destroy session, redirect to home

public function profile()
// Display user profile (protected)

public function updateProfile()
// Update user information (protected, POST)
```

### 2. BusinessController.php - Business Management Routes

**Routes**:

| Route | Method | Description |
|-------|--------|-------------|
| `/business` | GET | List all businesses |
| `/business/{id}` | GET | Show business detail |
| `/business/create` | GET | Display create form (protected) |
| `/business/create` | POST | Save new business (protected) |
| `/business/edit/{id}` | GET | Display edit form (protected) |
| `/business/edit/{id}` | POST | Update business (protected) |
| `/business/delete/{id}` | POST | Delete business (protected) |

**Key Methods**:

```php
public function getAll()
// Return paginated list of businesses

public function getById($business_id)
// Return business detail with categories and images

public function create()
// Handle POST from create form
// Validate data, save to database, handle logo upload

public function update($business_id)
// Handle POST from edit form
// Verify ownership, validate, update database and files

public function search()
// Search by name, location, category
// Support multiple filters
```

### 3. BusinessImageController.php - Image Management Routes

**Routes**:

| Route | Method | Description |
|-------|--------|-------------|
| `/business/images/{id}` | GET | Display image gallery |
| `/business/images/upload` | POST | Upload new image (protected) |
| `/business/images/delete` | POST | Delete image (protected) |
| `/business/images/set-primary` | POST | Set primary image (protected) |

**Key Methods**:

```php
public function upload($business_id)
// Handle file upload, save to disk and database
// Validate ownership

public function delete($image_id, $business_id)
// Delete from database and filesystem
// Reassign primary if needed

public function setPrimary($image_id, $business_id)
// Update is_primary field
// Reset other images' primary status
```

---

## Routes & API Endpoints

### Authentication Routes

#### User Registration
```
POST /register
Content-Type: application/x-www-form-urlencoded

Parameters:
- first_name: string (required)
- last_name: string (required)
- email: string (required, valid email)
- password: string (required, min 6 chars)
- phone_number: string (required, 10 digits)
- address_line_1: string (required)
- address_line_2: string (optional)
- city: string (required)
- state: string (required)
- postal_code: string (required, 6 digits)
- role: string (required, 'customer' or 'business_owner')

Response:
- Success: Redirect to /dashboard with session
- Failure: Return to /register with error message
```

#### User Login
```
POST /login
Content-Type: application/x-www-form-urlencoded

Parameters:
- email: string (required)
- password: string (required)

Response:
- Success: Redirect to dashboard
- Failure: Return error message
```

#### Logout
```
GET /logout
Authentication: Required (session)

Response: Redirect to /home
```

### Business Routes

#### Create Business
```
POST /business/create
Authentication: Required (business_owner role)
Content-Type: multipart/form-data

Parameters:
- business_name: string (required)
- description: string (required)
- location: string (required)
- contact_number: string (required)
- email: string (required)
- website: string (optional)
- working_hours: string (optional)
- business_logo: file (optional, image)
- categories: array (optional, category IDs)

Response:
- Success: Redirect to /business/{id} with success message
- Failure: Return to form with error details
```

#### Edit Business
```
POST /business/edit/{id}
Authentication: Required (business owner)
Content-Type: multipart/form-data

Parameters: Same as create
Additional check: User must be business owner

Response: Same as create
```

#### Get Business Detail
```
GET /business/{id}
Authentication: Optional

Response JSON:
{
    "business": {
        "id": int,
        "business_name": string,
        "description": string,
        "location": string,
        "contact_number": string,
        "email": string,
        "website": string,
        "working_hours": string,
        "business_logo": string (URL),
        "rating": decimal,
        "is_verified": boolean,
        "created_at": timestamp
    },
    "images": [
        {
            "id": int,
            "image_url": string (URL),
            "is_primary": boolean,
            "uploaded_at": timestamp
        }
    ],
    "categories": [
        {
            "id": int,
            "category_name": string
        }
    ],
    "services": [
        {
            "id": int,
            "service_name": string,
            "price": decimal (ZMW),
            "duration": int
        }
    ]
}
```

### Business Image Routes

#### Upload Image
```
POST /business/images/upload
Authentication: Required (business owner)
Content-Type: multipart/form-data

Parameters:
- business_id: int (required)
- image: file (required, image format)
- is_primary: boolean (optional)

Response:
- Success: JSON { success: true, image_id: int, message: string }
- Failure: JSON { success: false, error: string }
```

#### Delete Image
```
POST /business/images/delete
Authentication: Required
Content-Type: application/x-www-form-urlencoded

Parameters:
- image_id: int (required)

Response:
- Success: JSON { success: true, message: string }
- Failure: JSON { success: false, error: string }
```

#### Set Primary Image
```
POST /business/images/set-primary
Authentication: Required
Content-Type: application/x-www-form-urlencoded

Parameters:
- image_id: int (required)
- business_id: int (required)

Response:
- Success: JSON { success: true, message: string }
- Failure: JSON { success: false, error: string }
```

### Dashboard Routes

#### User Dashboard
```
GET /dashboard
Authentication: Required

Response: 
- For customers: Recent bookings, account summary
- For business_owners: Owned businesses, recent bookings, 
  statistics, links to manage businesses
```

---

## Views & Frontend

### View Hierarchy

```
layout.php (Master Template)
├── header with navigation
├── sidebar navigation (if logged in)
├── main content area
│   └── [specific page view]
└── footer
```

### Key Views

#### 1. `auth/register.php` - User Registration
**Features**:
- First/Last name fields
- Email input with validation
- Password input with strength indicator
- Phone number (10 digits)
- Complete address (4 fields)
- **Role Selection**: Dropdown to select 'Customer' or 'Business Owner'
- Form validation with client-side JS
- Submit button with loading state
- Link to login page

#### 2. `business/create.php` - Create/Edit Business Form
**Dual-Purpose View**:
- Title changes based on action (Create vs. Edit)
- Form submits to `/business/create` or `/business/edit/{id}`
- **Create Mode**:
  - All fields empty
  - No logo preview
  - Category selector (checkboxes)
  
- **Edit Mode**:
  - Pre-filled from database
  - Logo preview with current logo
  - Pre-selected categories
  - Current value validation

**Form Fields**:
- Business name (text, required)
- Description (textarea, required)
- Location (text, required)
- Contact number (tel, required)
- Email (email, required)
- Website (url, optional)
- Working hours (textarea, optional)
- Business logo (file, optional, image only)
- Categories (multi-checkbox, optional)

#### 3. `business/detail.php` - Business Profile
**Displays**:
- Business logo with fallback icon
- Business name and rating
- Contact information
- Working hours
- Gallery with image carousel
- Categories as badges
- Services list
- Customer reviews
- Booking CTA button

#### 4. `business/images.php` - Image Gallery Management
**Features**:
- Upload form for new images (drag & drop)
- Image grid display
- Primary image badge
- Upload date
- Delete button per image
- Set as primary action
- File validation messages
- Empty state if no images

#### 5. `dashboard/index.php` - User Dashboard
**Dual-View Dashboard**:

**For Customers**:
- Account summary card
- Recent bookings list
- Quick links to browse businesses
- Booking status indicators

**For Business Owners**:
- Account summary card
- **Owned Businesses Grid**:
  - Business logo
  - Business name
  - Rating display
  - Categories badges
  - Quick action buttons:
    - View business
    - Edit business
    - Manage images
    - View analytics
- Recent bookings from customers
- Quick stats (total businesses, total bookings)

### Frontend Technologies

#### CSS Framework
- Bootstrap 5 for responsive design
- Custom CSS in `assets/css/style.css`
- Responsive grid system
- Pre-built components (cards, buttons, modals)

#### Icons
- Font Awesome for icons
- Used in navigation, buttons, status indicators

#### JavaScript
- Vanilla JavaScript in `assets/js/main.js`
- Form validation before submission
- Dynamic element manipulation
- File preview for image uploads
- Modal dialogs for confirmations

#### Responsive Design
- Mobile-first approach
- Breakpoints: xs, sm, md, lg, xl
- Touch-friendly button sizes
- Mobile navigation menu

---

## Authentication & Authorization

### Authentication Flow

```
1. User fills login form
2. POST to /login
3. AuthController.login() validates credentials
4. If valid:
   - Hash and match password against database
   - Create $_SESSION['user_id'] and user data
   - Redirect to dashboard
5. If invalid:
   - Display error message
   - Return to login form
```

### Session Management

**Session Data Structure**:
```php
$_SESSION = [
    'user_id' => 1,
    'user_email' => 'user@example.com',
    'user_role' => 'customer', // 'customer', 'business_owner', 'admin'
    'first_name' => 'John',
    'last_name' => 'Doe'
];
```

**Session Timeout**: 3600 seconds (1 hour)

### Authorization

#### Role-Based Access Control (RBAC)

**Roles**:
1. **Customer** - Can browse businesses, book services, leave reviews
2. **Business Owner** - Can manage own business, services, accept bookings
3. **Admin** - Full access to all features and user management

**Protected Routes**:

| Route | Required Role | Method |
|-------|--------------|--------|
| `/dashboard` | customer, business_owner, admin | GET |
| `/business/create` | business_owner, admin | GET/POST |
| `/business/edit/{id}` | business_owner (owner of business) | GET/POST |
| `/business/images/{id}` | business_owner (owner of business) | GET |
| `/booking/create` | customer, business_owner | POST |

**Authorization Checking**:
```php
// In index.php router
if ($auth->isLoggedIn()) {
    $user = $auth->getCurrentUser();
    
    if ($action === 'business/edit' && $user['role'] !== 'business_owner') {
        // Check if user owns this business
        if (!$business->isOwner($business_id, $user['user_id'])) {
            die("Unauthorized");
        }
    }
}
```

---

## File Upload System

### Upload Directory Structure

```
uploads/
├── business_logos/        # Business profile logos
│   ├── 1_1699564800_ab12cd34.png
│   ├── 2_1699565000_ef56gh78.jpg
│   └── ...
├── business_images/       # Business gallery images
│   ├── 1_1699564850_xyz789.png
│   ├── 1_1699564900_abc123.jpg
│   ├── 2_1699565050_def456.png
│   └── ...
└── [other uploads as needed]
```

### Upload Process

#### 1. File Upload & Validation
```php
// In controller or BusinessImageController
$file = $_FILES['image'];

// Validation
if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    throw new Exception("Upload failed");
}

if ($file['size'] > MAX_FILE_SIZE) {
    throw new Exception("File too large");
}

$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ALLOWED_EXTENSIONS)) {
    throw new Exception("File type not allowed");
}
```

#### 2. File Storage
```php
// Generate unique filename
$filename = $business_id . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
$file_path = UPLOAD_DIR . 'business_images/' . $filename;

// Move uploaded file
if (!move_uploaded_file($file['tmp_name'], $file_path)) {
    throw new Exception("File save failed");
}

// Save to database
$image_url = '/uploads/business_images/' . $filename;
```

#### 3. File Deletion
```php
// Get file path from database
$image = $database->query("SELECT image_url FROM business_images WHERE id = ?");

// Delete physical file
if (file_exists($image['image_url'])) {
    unlink($image['image_url']);
}

// Delete database record
// ... database delete
```

### Supported File Types

| Type | Extensions | Max Size |
|------|-----------|----------|
| Images | jpg, jpeg, png, gif | 5MB |
| Documents | pdf | 5MB |

### Directory Auto-Creation

In `config.php`, directories are created automatically:
```php
if (!is_dir(UPLOAD_DIR . 'business_images')) {
    mkdir(UPLOAD_DIR . 'business_images', 0755, true);
}
```

---

## Error Handling

### Error Categories

#### 1. Validation Errors
```php
try {
    if (!$validator->validateEmail($email)) {
        throw new Exception("Invalid email format");
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: /register");
}
```

#### 2. Database Errors
```php
try {
    $result = $database->query($sql);
    if (!$result) {
        throw new Exception("Database error: " . $database->error);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $_SESSION['error'] = "An error occurred. Please try again.";
}
```

#### 3. Authentication Errors
```php
if (!Auth::isLoggedIn()) {
    $_SESSION['error'] = "Please log in first";
    header("Location: /login");
    exit;
}
```

#### 4. Authorization Errors
```php
if (!Auth::hasRole('business_owner')) {
    $_SESSION['error'] = "You don't have permission to do this";
    header("Location: /dashboard");
    exit;
}
```

### Error Logging

**Error Log Location**: `/logs/error.log`

**Log Format**:
```
[2026-03-01 10:30:45] ERROR: Database connection failed
[2026-03-01 10:31:15] WARNING: Invalid email format from user@example
[2026-03-01 10:32:00] ERROR: File upload failed - Permission denied
```

**Configuration** (in `config.php`):
```php
error_reporting(E_ALL);
ini_set('display_errors', '0');        // hide in production
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/error.log');
```

### Error Messages for Users

**Best Practices**:
- Don't expose technical details to users
- Use friendly, actionable error messages
- Provide guidance on how to fix the issue

**Examples**:
```php
// Bad: "MySQLi error #1062: Duplicate entry"
// Good: "This email is already registered. Please log in instead."

// Bad: "File permission denied"
// Good: "Unable to save file. Please try again or contact support."
```

---

## Security Considerations

### 1. Password Security

#### Password Hashing
```php
// When saving password
$password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// When verifying
$is_valid = password_verify($input_password, $password_hash);
```

**Specifications**:
- Algorithm: BCRYPT
- Cost factor: 12 (computation difficulty)
- Never store plain text passwords

### 2. SQL Injection Prevention

#### Prepared Statements
```php
// Always use prepared statements
$stmt = $database->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

// Never use concatenation
// BAD: "SELECT * FROM users WHERE email = '$email'"
```

#### Parameter Binding
```php
// Correct - bind parameters by reference
$user_id = 5;
$name = "John";
$stmt->bind_param("is", $user_id, $name);

// Use variables, not expressions
// BAD: $stmt->bind_param("s", $_POST['email'])
// GOOD:
$email = $_POST['email'];
$stmt->bind_param("s", $email);
```

### 3. CSRF Protection

#### Token Generation & Verification
```php
// Generate token
$token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $token;

// Include in forms
echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';

// Verify on submission
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    throw new Exception("Invalid request");
}
```

### 4. Input Validation & Sanitization

#### Whitelist Approach
```php
// Always validate input
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if ($email === false) {
    throw new Exception("Invalid email");
}

// Sanitize strings
$name = htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8');

// Validate data types
$user_id = (int) $_POST['id'];
```

### 5. Authentication Security

#### Session Security
```php
// Regenerate session ID after login
session_regenerate_id(true);

// Set secure session cookie settings
session_set_cookie_params([
    'httponly' => true,    // Prevent JS access
    'secure' => true,      // Only HTTPS
    'samesite' => 'Strict' // CSRF prevention
]);
```

#### Password Requirements
- Minimum 6 characters
- Should include numbers and special characters
- Don't store plain text

### 6. File Upload Security

#### Whitelist Extensions
```php
$allowed = ['jpg', 'jpeg', 'png', 'gif'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

if (!in_array($ext, $allowed)) {
    throw new Exception("File not allowed");
}
```

#### Validate MIME Type
```php
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);

if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
    throw new Exception("Invalid file type");
}
```

#### Store Outside Web Root
```php
// Store files outside /uploads if possible
// Use absolute paths, not web-accessible
define('STORAGE_PATH', '/secure/path/uploads/');
```

### 7. Access Control

#### Authorization Checks
```php
// Always verify ownership before allowing edit
$business = Business::getById($id);
if ($business['owner_id'] !== Auth::getCurrentUserId()) {
    die("Unauthorized");
}

// Check role for sensitive operations
Auth::checkPermission('business_owner');
```

### 8. Protection Against Common Attacks

| Attack Type | Prevention |
|-------------|-----------|
| SQL Injection | Prepared statements, parameter binding |
| XSS | htmlspecialchars(), input validation |
| CSRF | CSRF tokens, SameSite cookies |
| Session Hijacking | Secure cookies, HTTPS only |
| Brute Force | Rate limiting, account lockout |
| File Upload | Whitelist extensions, validate MIME, scan for malware |

---

## Development Guidelines

### Code Standards

#### PHP Coding Standards (PSR-12)
```php
// Class naming: PascalCase
class BusinessController { }

// Method naming: camelCase
public function createBusiness() { }

// Constant naming: UPPER_CASE
define('DB_HOST', 'localhost');

// Variable naming: camelCase
$user_id = 5;
$emailAddress = "user@example.com";
```

#### File Organization
```php
// 1. Declarations (namespace, use statements)
<?php
namespace App;

// 2. Class declaration
class MyClass {
    // 3. Properties
    private $property;
    
    // 4. Constructor
    public function __construct() { }
    
    // 5. Public methods
    public function publicMethod() { }
    
    // 6. Protected methods
    protected function protectedMethod() { }
    
    // 7. Private methods
    private function privateMethod() { }
}
```

### Code Comments

#### Method Documentation
```php
/**
 * Register a new user account
 *
 * @param array $data User registration data
 *        - first_name: User's first name
 *        - last_name: User's last name
 *        - email: User's email address
 *        - password: User's password (will be hashed)
 * @return int|bool User ID on success, false on failure
 * @throws Exception If validation fails
 */
public function register($data) {
    // Implementation
}
```

#### Complex Logic Comments
```php
// Don't over-comment obvious code
$name = "John"; // BAD: Assigning name

// DO comment WHY, not WHAT
// Regenerate session ID to prevent session fixation attacks
session_regenerate_id(true);
```

### Testing Guidelines

#### Manual Testing Checklist
- [ ] User registration with all roles
- [ ] User login with correct/incorrect credentials
- [ ] Role-specific dashboard display
- [ ] Business creation by business owner
- [ ] Business editing (verify ownership)
- [ ] Image upload (validate file types)
- [ ] Image deletion (verify primary image handling)
- [ ] Business search functionality
- [ ] Category filtering
- [ ] Error messages display correctly

#### SQL Testing
```sql
-- Test user registration
SELECT * FROM users WHERE email = 'test@example.com';

-- Test business creation
SELECT * FROM businesses WHERE owner_id = 1;

-- Test business categories
SELECT bc.*, c.category_name FROM business_categories bc
JOIN categories c ON bc.category_id = c.id
WHERE bc.business_id = 1;

-- Test image upload
SELECT * FROM business_images WHERE business_id = 1;
```

### Database Backup

#### Regular Backups
```bash
# Backup entire database
mysqldump -u root -p finditlocal_db > backup_finditlocal_db.sql

# Backup specific table
mysqldump -u root -p finditlocal_db users > backup_users.sql

# Restore from backup
mysql -u root -p finditlocal_db < backup_finditlocal_db.sql
```

#### Backup Schedule
- Daily backups for production
- After major updates
- After database migrations

### Version Control

#### Commit Messages
```
[FEATURE] Add business image gallery management
- Implement upload functionality
- Add delete operation
- Add set primary image feature

[BUGFIX] Fix bind_param reference error in User.php
- Extract optional values to variables before binding
- Prevent mysqli error when binding expressions

[DOCS] Update API documentation for business routes
```

#### Branch Strategy
```
main/master          - Production ready code
├── develop          - Development branch
│   ├── feature/...  - New features
│   └── bugfix/...   - Bug fixes
└── hotfix/...       - Production hotfixes
```

---

## Troubleshooting

### Common Issues & Solutions

#### 1. "Cannot access the specified file" (Database Connection)

**Symptoms**: Cannot connect to database, blank page

**Solutions**:
```bash
# Check MySQL is running
sudo service mysql start

# Verify credentials in config.php
# Test connection manually
mysql -u root -p -h localhost finditlocal_db

# Check error log
tail -f /logs/error.log
```

#### 2. "Warning: move_uploaded_file()" (File Upload Fails)

**Symptoms**: Upload fails silently or with error

**Solutions**:
```bash
# Check directory permissions
chmod -R 755 uploads/
chmod -R 755 uploads/business_images/

# Verify upload_max_filesize in php.ini
upload_max_filesize = 50M
post_max_size = 50M

# Clear temp directory
rm -rf /tmp/php*
```

#### 3. "Headers already sent" (Session Issues)

**Symptoms**: White page, error about headers

**Solutions**:
```php
// Remove any output before session_start()
<?php  // Start immediately, no whitespace before
session_start();
// ... rest of code

// Check for BOM in files
// Remove BOM from PHP files if present
```

#### 4. "mysqli_stmt::bind_param(): Number of variables doesn't match number of placeholders"

**Symptoms**: Error when executing prepared statement

**Solutions**:
```php
// WRONG: Using expressions directly
$stmt->bind_param("s", $data['email']); // ERROR

// CORRECT: Extract to variable first
$email = $data['email'];
$stmt->bind_param("s", $email);

// Check number of placeholders (?) matches parameters
$stmt->bind_param("sss", $param1, $param2, $param3); // 3 placeholders, 3 params
```

#### 5. "Access Denied for user" (MySQLi Permission)

**Symptoms**: Cannot execute queries

**Solutions**:
```sql
-- Check user permissions
SELECT user, host FROM mysql.user WHERE user = 'root';

-- Grant permissions
GRANT ALL PRIVILEGES ON finditlocal_db.* TO 'root'@'localhost';
FLUSH PRIVILEGES;

-- Check current permissions
SHOW GRANTS FOR 'root'@'localhost';
```

#### 6. "Undefined variable/function" (Code Errors)

**Symptoms**: PHP warnings/notices

**Solutions**:
```php
// Always check if variable exists
if (isset($_POST['email'])) {
    $email = $_POST['email'];
}

// Check if function exists before calling
if (function_exists('mycustom_function')) {
    mycustom_function();
}

// Use null coalescing operator
$value = $_GET['param'] ?? 'default';
```

#### 7. "Permission Denied" (Authorization)

**Symptoms**: User can't access business they own to edit

**Solutions**:
```php
// Verify ownership check
$business = Business::getById($id);
if ($business['owner_id'] != Auth::getCurrentUserId()) {
    die("You don't own this business");
}

// Check role in AuthController
if ($user['role'] !== 'business_owner') {
    die("Only business owners can create businesses");
}
```

### Debug Mode

#### Enable Detailed Errors
```php
// In config.php for development only
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/error.log');
```

#### Log Database Queries
```php
// Add to Database.php to log all queries
public function query($sql) {
    error_log("QUERY: " . $sql);
    // ... execute query
    if ($this->error) {
        error_log("ERROR: " . $this->error);
    }
}
```

#### Check Session Data
```php
// Debug session contents
echo '<pre>';
print_r($_SESSION);
echo '</pre>';
```

### Performance Optimization

#### Database Indexing
```sql
-- Check indexes
SHOW INDEX FROM users;

-- Add missing indexes (already in schema)
ALTER TABLE businesses ADD INDEX idx_owner (owner_id);
ALTER TABLE business_images ADD INDEX idx_business (business_id);
```

#### Query Optimization
```php
// Use LIMIT for pagination
SELECT * FROM businesses LIMIT 0, 20;

// Use JOINs instead of multiple queries
SELECT b.*, bc.category_id FROM businesses b
LEFT JOIN business_categories bc ON b.id = bc.business_id
WHERE b.owner_id = 5;

// Avoid SELECT * if not needed
SELECT id, business_name, location FROM businesses;
```

#### Caching Strategies
```php
// Simple caching
$cache_key = 'categories_list';
if (!isset($_SESSION[$cache_key])) {
    $_SESSION[$cache_key] = Category::getAll();
    $_SESSION[$cache_key . '_time'] = time();
}
```

---

## Maintenance & Monitoring

### Regular Maintenance Tasks

#### Daily
- [ ] Check error logs for issues
- [ ] Monitor database size
- [ ] Backup database

#### Weekly
- [ ] Review user registrations for spam
- [ ] Check contact messages
- [ ] Verify upload directory sizes

#### Monthly
- [ ] Review and optimize slow queries
- [ ] Update security rules if needed
- [ ] Archive old data

### Monitoring Checklist

```bash
# Check error log size
ls -lh /logs/error.log

# Monitor disk space
df -h

# Check MySQL database size
SELECT table_schema, ROUND(SUM(data_length + index_length)/1024/1024,2) AS size_mb
FROM information_schema.tables
GROUP BY table_schema;

# Check active connections
SHOW PROCESSLIST;
```

---

## Future Enhancements

### Phase 2 Features
- Advanced analytics dashboard
- Business approval workflow
- Payment gateway integration
- Email notifications
- SMS alerts
- Mobile app
- Advanced search filters
- Booking calendar
- Business subscription tiers
- API for third-party integration

### Planned Improvements
- TypeScript migration
- Vue.js/React frontend
- GraphQL API
- Redis caching
- Elasticsearch integration
- Docker containerization
- Kubernetes deployment

---

## Support & Contact

### For Issues
1. Check this documentation
2. Review error logs at `/logs/error.log`
3. Check troubleshooting section
4. Contact development team

### Documentation Locations
- **Setup**: [SETUP_GUIDE.md](php-app/SETUP_GUIDE.md)
- **Quick Reference**: [QUICK_REFERENCE.md](php-app/QUICK_REFERENCE.md)
- **Project Summary**: [PROJECT_SUMMARY.md](php-app/PROJECT_SUMMARY.md)
- **File Verification**: [FILE_VERIFICATION.md](php-app/FILE_VERIFICATION.md)

---

## Changelog

### Version 1.0.0 (2026-03-01)
- Initial release
- Multi-role user system (customer, business_owner, admin)
- Business management (CRUD operations)
- Business image gallery with primary image support
- Currency system (ZMW Zambian Kwacha)
- User dashboard with role-specific views
- Contact form system
- Category management
- Service listing structure
- Booking system foundation
- Review and rating system foundation
- Payment processing foundation
- Comprehensive error handling
- Security implementations (password hashing, prepared statements, CSRF tokens)

---

This documentation is a living document and should be updated as the project evolves. Last updated: March 1, 2026
