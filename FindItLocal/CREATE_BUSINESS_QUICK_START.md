# Create Business Feature - Quick Start Guide

## Table of Contents
- [For Users](#for-users)
- [For Developers](#for-developers)
- [Troubleshooting](#troubleshooting)

---

## For Users

### Getting Started

#### Prerequisites
- You must be logged in as a **Business Owner**
- If you're a customer, you'll need to register as a business owner first

#### Steps to Create a Business

1. **Log In**
   - Go to the login page
   - Use your business owner account credentials

2. **Navigate to Create Business**
   - Click on your name → Dashboard
   - Click "Create Business" in the sidebar menu
   - Or go directly to: `/FindItLocal/php-app/business/create`

3. **Fill in Business Information**
   ```
   Business Name          → Your company name (3-255 characters)
   Description            → Tell customers about your business (20-5000 characters)
   Location/Address       → Complete street address (5-255 characters)
   Contact Number         → Format: +country code + number (e.g., +260123456789)
   Email Address          → Business email
   Website URL (optional) → Your website (e.g., https://www.example.com)
   Working Hours (optional) → When you're open (e.g., Mon-Fri: 9AM-5PM)
   Business Logo (optional) → Upload a logo (JPG, PNG, GIF, WebP, max 5MB)
   ```

4. **Select Categories**
   - Choose at least ONE category that describes your business
   - You can select multiple categories
   - Categories help customers find you

5. **Submit**
   - Click "Create Business" button
   - If successful, you'll be redirected to your dashboard
   - You'll see a success message

### Form Tips

**✓ DO:**
- Use a professional business name
- Write a clear, detailed description
- Use a complete physical address
- Use the correct phone number format (+260XXXXXXXXX for Zambia)
- Upload a logo if you have one
- Select ALL relevant categories

**✗ DON'T:**
- Leave required fields blank
- Use special characters in business name (except apostrophes)
- Upload files larger than 5MB
- Forget to select at least one category
- Use a personal phone number instead of business number

### After Creating Your Business

Once your business is created, you can:
1. **Edit Business Information** - Click "Edit" in your dashboard
2. **Add Services** - Create services offered by your business
3. **Upload Images** - Add photos of your business
4. **View Your Business** - See how it appears in search results
5. **Manage Bookings** - View customer bookings
6. **View Reviews** - See customer feedback

---

## For Developers

### Quick Implementation Check

#### 1. Verify Route is Working
```bash
# Test the route is accessible
curl http://localhost:8000/FindItLocal/php-app/business/create
# Should return 200 (with login redirect if not authenticated)
```

#### 2. Verify Database Structure
```sql
-- Check if tables exist
SHOW TABLES LIKE 'businesses';
SHOW TABLES LIKE 'business_categories';
SHOW TABLES LIKE 'categories';

-- Check business table structure
DESCRIBE businesses;

-- Check if categories exist
SELECT * FROM categories;
```

#### 3. Verify Upload Directories
```bash
# Check upload directory permissions
ls -la /c/xampp/htdocs/FindItLocal/php-app/uploads/business_logos/
# Should have 755 permissions (drwxr-xr-x)

# Create if missing
mkdir -p /c/xampp/htdocs/FindItLocal/php-app/uploads/business_logos/
chmod 755 /c/xampp/htdocs/FindItLocal/php-app/uploads/business_logos/
```

#### 4. Verify Authentication
```php
// In your test script:
Auth::startSession();
if (Auth::isBusinessOwner()) {
    echo "Business owner authenticated";
}
```

### API Usage Example

#### Create Business with cURL
```bash
curl -X POST http://localhost:8000/FindItLocal/php-app/business/create \
  -H "Content-Type: multipart/form-data" \
  -F "business_name=My Awesome Business" \
  -F "description=We provide excellent services in our industry with 10+ years of experience" \
  -F "location=123 Main Street, Lusaka, Zambia" \
  -F "contact_number=+260123456789" \
  -F "email=business@example.com" \
  -F "website=https://www.example.com" \
  -F "working_hours=Mon-Fri: 9AM-5PM, Sat: 10AM-2PM" \
  -F "categories[]=1" \
  -F "categories[]=3" \
  -F "business_logo=@logo.png" \
  -b "PHPSESSID=your_session_id"
```

#### Create Business with PHP
```php
<?php
// Assuming you're in the application context

$_POST['business_name'] = 'My Awesome Business';
$_POST['description'] = 'We provide excellent services...';
$_POST['location'] = '123 Main Street, Lusaka';
$_POST['contact_number'] = '+260123456789';
$_POST['email'] = 'business@example.com';
$_POST['website'] = 'https://www.example.com';
$_POST['working_hours'] = 'Mon-Fri: 9AM-5PM';
$_POST['categories'] = ['1', '3'];

// Simulate file upload
$_FILES['business_logo'] = [
    'name' => 'logo.png',
    'type' => 'image/png',
    'tmp_name' => '/tmp/upload_file',
    'error' => UPLOAD_ERR_OK,
    'size' => 15000
];

require_once 'controllers/BusinessController.php';
$controller = new BusinessController();
$result = $controller->create();

if ($result['success']) {
    echo "Business created with ID: " . $result['id'];
} else {
    echo "Error: " . $result['error'];
}
?>
```

### Code Extension Examples

#### Add Custom Validation
```php
// In BusinessController.php create method:
if (strlen($data['business_name']) > 0 && strpos($data['business_name'], '<') !== false) {
    $errors[] = 'Business name cannot contain HTML tags';
}
```

#### Add Email Verification
```php
// Send verification email after business creation
$verification_token = Auth::generateToken(['business_id' => $result['id']]);
// Send email to $data['email'] with verification link
```

#### Add Business Logo Image Optimization
```php
// In Helper.php uploadFile method:
if (function_exists('imagecreatefromstring')) {
    $image = imagecreatefromstring(file_get_contents($filepath));
    // Resize if larger than 300x300
    // Save as WebP for better compression
}
```

### Integration Points

#### With Dashboard
```php
// In dashboard/index.php:
// Get owner's businesses
$businessModel = new Business();
$ownedBusinesses = $businessModel->getByOwner($user['id']);

// Display "Create Business" button for business owners
if ($user['role'] === 'business_owner' && empty($ownedBusinesses)) {
    echo '<a href="/business/create" class="btn btn-primary">Create Your First Business</a>';
}
```

#### With Services
```php
// Each business can have services
// Create service after business creation:
$_POST['business_id'] = $result['id'];
$serviceController->create();
```

#### With Reviews
```php
// Reviews are tied to businesses
// After business is created, reviews can be added by customers
$_POST['business_id'] = $business_id;
$reviewController->create();
```

### Testing Guide

#### Unit Tests
```php
// Test: Valid business creation
function testCreateValidBusiness() {
    $controller = new BusinessController();
    $_POST = [
        'business_name' => 'Test Business',
        'description' => 'This is a longer description that meets the minimum character requirement',
        'location' => '123 Test Street, Lusaka',
        'contact_number' => '+260123456789',
        'email' => 'test@example.com',
        'categories' => ['1']
    ];
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    $result = $controller->create();
    assert($result['success'] === true);
    assert(isset($result['id']));
}

// Test: Missing required field
function testMissingRequiredField() {
    $controller = new BusinessController();
    $_POST = [
        'business_name' => 'Test',
        // Missing description
        'location' => '123 Test Street',
        'contact_number' => '+260123456789',
        'email' => 'test@example.com',
        'categories' => ['1']
    ];
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    $result = $controller->create();
    assert($result['error'] !== null);
}
```

#### Integration Tests
```bash
# Test 1: Access without login
curl -i http://localhost/FindItLocal/php-app/business/create
# Expected: 302 redirect to login

# Test 2: Access as customer
# Auth as customer, then access
curl -i -b "PHPSESSID=customer_session" http://localhost/FindItLocal/php-app/business/create
# Expected: 403 Forbidden

# Test 3: Create with valid data
curl -X POST -b "PHPSESSID=owner_session" http://localhost/FindItLocal/php-app/business/create \
  -F "business_name=Test Business" \
  -F "description=Valid longer description for testing purposes" \
  -F "location=Test Location" \
  -F "contact_number=+260123456789" \
  -F "email=test@example.com" \
  -F "categories[]=1"
# Expected: Redirect to dashboard with success
```

---

## Troubleshooting

### Problem: "Access Denied" Error

**Cause:** You don't have the correct role
**Solution:** 
- Verify your account is set as "business_owner" in the users table
- Log out and log back in
- Contact administrator if role is not correct

```sql
-- Check your role
SELECT email, role FROM users WHERE email = 'your@email.com';

-- Update role if needed (admin only)
UPDATE users SET role = 'business_owner' WHERE email = 'your@email.com';
```

### Problem: Form Won't Submit

**Cause:** Validation error
**Solution:**
- Check browser console for JavaScript errors
- Verify all required fields are filled
- Ensure contact number format is correct (+260...)
- Make sure at least one category is selected

### Problem: Logo Won't Upload

**Cause:** File size or type issue
**Solution:**
- Check file is JPG, PNG, GIF, or WebP
- Verify file size is under 5MB
- Try a different image
- Check directory permissions: `chmod 755 uploads/business_logos/`

```bash
# Fix permissions
chmod 755 /c/xampp/htdocs/FindItLocal/php-app/uploads/business_logos/
chmod 755 /c/xampp/htdocs/FindItLocal/php-app/uploads/
```

### Problem: "No categories available" Message

**Cause:** Categories table is empty
**Solution:**
- Run the TEST_DATA.sql to populate categories
- Or manually insert categories:

```sql
INSERT INTO categories (category_name, description, icon) VALUES
('Restaurants', 'Food and dining', 'fas fa-utensils'),
('Healthcare', 'Medical services', 'fas fa-heartbeat'),
('Retail', 'Shops and stores', 'fas fa-shopping-bag');
```

### Problem: Can't Login

**If you don't have a business owner account:**

1. Register as new user at `/login`
2. Contact administrator to change role to 'business_owner'
3. Or use test account:
   - Email: businessowner@test.com
   - Password: Test@1234

**Run TEST_DATA.sql first to create test account**

### Problem: Business Not Appearing After Creation

**Cause:** Business is inactive or verification needed
**Solution:**
- Check database: `SELECT * FROM businesses WHERE owner_id = YOUR_ID;`
- Check if `is_active = 1`
- Business may need admin verification before appearing in public listings

### Problem: Redirect Loop After Submit

**Cause:** Dashboard route not accessible
**Solution:**
- Verify route exists: Check index.php for `/dashboard` route
- Check user role is set correctly
- Clear browser cache and cookies
- Try accessing dashboard directly: `/FindItLocal/php-app/dashboard`

### Problem: Database Error

**Cause:** Table structure mismatch
**Solution:**
- Run migrations: `php-app/Database_Setup.sql`
- Check error logs: `php-app/logs/error.log`
- Verify connection: `php-app/config/config.php`

---

## Support

### Getting Help

1. **Check the Logs**
   ```bash
   tail -f /c/xampp/htdocs/FindItLocal/php-app/logs/error.log
   ```

2. **Review Documentation**
   - See [BUSINESS_CREATE_GUIDE.md](BUSINESS_CREATE_GUIDE.md) for detailed info

3. **Check Browser Console**
   - Press F12 → Console tab
   - Look for JavaScript errors

4. **Database Check**
   ```sql
   -- Verify tables exist
   SHOW TABLES;
   
   -- Check your business
   SELECT * FROM businesses WHERE owner_id = YOUR_ID;
   ```

---

**Quick Links:**
- [Full Implementation Guide](BUSINESS_CREATE_GUIDE.md)
- [API Routes](API_ROUTES.md)
- [Technical Reference](TECHNICAL_REFERENCE.md)
- [Database Setup](php-app/Database_Setup.md)

**Last Updated:** March 2, 2026
