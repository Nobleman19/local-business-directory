# Create Business Feature - Complete Implementation Guide

## Overview
The Create Business feature is fully implemented and ready for use. This guide covers the complete workflow, validation, and testing procedures.

## System Architecture

### Components Involved
1. **Router** (index.php) - Route handling
2. **Controller** (BusinessController.php) - Business logic and validation  
3. **Model** (Business.php) - Database operations
4. **View** (create.php) - User interface form
5. **Helper Classes** - Validation, file upload, authentication

---

## Feature Specifications

### Database Schema
```sql
CREATE TABLE businesses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    owner_id INT NOT NULL,
    business_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    website VARCHAR(255),
    working_hours TEXT,
    business_logo VARCHAR(255),
    rating DECIMAL(3,2) DEFAULT 0,
    is_verified BOOLEAN DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id),
    INDEX idx_owner (owner_id),
    INDEX idx_name (business_name),
    INDEX idx_location (location)
);
```

### Input Validation Rules

#### Business Name
- **Required:** Yes
- **Type:** Text (255 chars max)
- **Min Length:** 3 characters
- **Max Length:** 255 characters
- **Error Message:** "Business name must be between 3 and 255 characters"

#### Description
- **Required:** Yes
- **Type:** Long text (5000 chars max)
- **Min Length:** 20 characters
- **Max Length:** 5000 characters
- **Error Message:** "Description must be between 20 and 5000 characters"

#### Location/Address
- **Required:** Yes
- **Type:** Text (255 chars max)
- **Min Length:** 5 characters
- **Max Length:** 255 characters
- **Error Message:** "Complete address required (min 5 characters)"

#### Contact Number
- **Required:** Yes
- **Format:** +[1-15 digits] (e.g., +260123456789)
- **Validation:** Must start with + and contain 1-15 digits
- **Error Message:** "Format: +country code followed by phone number"

#### Email Address
- **Required:** Yes
- **Type:** Email
- **Format:** Valid email format (RFC 5322)
- **Error Message:** "Invalid email address format"

#### Website URL (Optional)
- **Required:** No
- **Format:** Valid HTTP/HTTPS URL
- **Examples:** https://www.example.com, http://business.com
- **Error Message:** "Enter a complete URL including http:// or https://"

#### Working Hours (Optional)
- **Required:** No
- **Max Length:** 500 characters
- **Example:** "Mon-Fri: 9AM-5PM, Sat: 10AM-2PM, Sun: Closed"

#### Business Logo (Optional)
- **Required:** No
- **Max File Size:** 5 MB
- **Allowed Formats:** JPG, PNG, GIF, WebP
- **Recommended Size:** 300x300px
- **Error Messages:**
  - "File size exceeds maximum allowed size"
  - "File type not allowed"

#### Categories
- **Required:** Yes
- **Type:** Multiple checkboxes
- **Minimum Selection:** At least 1 category
- **Error Message:** "Please select at least one business category"

---

## Validation Flow

### Client-Side Validation
1. HTML5 form validation (type, required, min/max length)
2. Pattern validation for contact number
3. JavaScript validation for category selection
4. Color-coded input validation (green for valid, red for invalid)

### Server-Side Validation
1. All inputs are re-validated on the server
2. Data is sanitized using `htmlspecialchars()`
3. Multiple validation rules per field
4. Enhanced error messages for user feedback
5. Transaction rollback on category addition failure

### Validation Process
```
POST /business/create
    ↓
Check Authentication (requireLogin)
    ↓
Check Authorization (requireRole('business_owner'))
    ↓
Validate All Fields (server-side)
    ↓
If errors: Return error messages to form
    ↓
If valid: Upload logo (if provided)
    ↓
Insert business into database
    ↓
Add selected categories (junction table)
    ↓
Redirect to dashboard with success message
```

---

## File Upload Handling

### Logo Upload Process
1. Check if file was uploaded successfully
2. Validate file type (image only)
3. Validate file size (max 5MB)
4. Generate unique filename: `uniqid() . '_' . originalname`
5. Create upload directory if needed
6. Move file to `/uploads/business_logos/`
7. Store filename in database
8. On edit: Delete old file before uploading new one

### Upload Error Handling
```php
// Possible errors:
- UPLOAD_ERR_NO_FILE → No file uploaded (acceptable, logo is optional)
- UPLOAD_ERR_OK → File uploaded successfully
- Other errors → Display error message
```

---

## API Endpoints

### Create Business
```
POST /FindItLocal/php-app/business/create
Content-Type: multipart/form-data

Parameters:
- business_name (required, string, 3-255 chars)
- description (required, string, 20-5000 chars)
- location (required, string, 5-255 chars)
- contact_number (required, string, format: +XXX)
- email (required, string, valid email)
- website (optional, string, valid URL)
- working_hours (optional, string, 0-500 chars)
- business_logo (optional, file, JPG/PNG/GIF/WebP, max 5MB)
- categories[] (required, array of integers, min 1)

Response (Success):
{
    "success": true,
    "message": "Business created successfully! You can now add services and images.",
    "id": 123
}

Response (Error):
{
    "error": "Field validation error message(s)"
}
```

---

## Form Features

### User Interface Elements
1. **Section Headers** - Clear form organization
2. **Required Field Indicators** - Red asterisk (*)
3. **Helper Text** - Character limits and format hints
4. **Real-time Validation** - Visual feedback (green/red borders)
5. **Error Display** - Inline error messages
6. **Category Grid** - Visual category selection with icons
7. **Logo Preview** - Shows current logo when editing
8. **Responsive Design** - Mobile-optimized layout

### Form Layout
```
Business Information Section
├── Business Name (required)
├── Description (required)
├── Location (required, left column)
├── Contact Number (required, right column)
├── Email (required, left column)
├── Website (optional, right column)
├── Working Hours (optional)
└── Business Logo (optional)

Business Category Section
├── Category Selection Grid
│   └── Multiple checkboxes with icons
└── Error message (if no category selected)

Form Actions Section
├── Cancel Button (returns to dashboard)
└── Submit Button (Create/Update Business)
```

---

## Testing Checklist

### Pre-Requisites
- [ ] User is logged in
- [ ] User has 'business_owner' role
- [ ] Categories exist in database
- [ ] Upload directory permissions are correct (755)
- [ ] Database connection is working

### Test Case 1: Successful Business Creation
```
Steps:
1. Navigate to /business/create
2. Fill all required fields with valid data
3. Select at least one category
4. Submit form

Expected Result:
- Business is created in database
- Categories are linked in business_categories table
- User is redirected to dashboard
- Success message is displayed
```

### Test Case 2: Form Validation - Missing Required Fields
```
Steps:
1. Navigate to /business/create
2. Leave Business Name blank
3. Try to submit form

Expected Result:
- Form shows validation error
- "Business name is required" message appears
- Form is not submitted
```

### Test Case 3: Form Validation - Minimum Length
```
Steps:
1. Navigate to /business/create
2. Enter "Ab" in Business Name
3. Try to submit

Expected Result:
- "Business name must be at least 3 characters" error
- Form is not submitted
```

### Test Case 4: Invalid Contact Number
```
Steps:
1. Navigate to /business/create
2. Enter "123456789" in Contact Number
3. Try to submit

Expected Result:
- "Invalid contact number format" error
- Expected format: +260123456789
```

### Test Case 5: Invalid Email
```
Steps:
1. Navigate to /business/create
2. Enter "notanemail" in Email field
3. Try to submit

Expected Result:
- "Invalid email address format" error
```

### Test Case 6: Logo Upload
```
Steps:
1. Navigate to /business/create
2. Fill all required fields
3. Select a PNG image (300x300px)
4. Submit form

Expected Result:
- Logo is uploaded to /uploads/business_logos/
- Filename is stored in database
- Logo appears in business detail view
```

### Test Case 7: Logo Upload - Invalid Format
```
Steps:
1. Navigate to /business/create
2. Select a PDF or text file
3. Try to submit

Expected Result:
- "File type not allowed" error
- Only images accepted
```

### Test Case 8: Logo Upload - File Too Large
```
Steps:
1. Navigate to /business/create
2. Select an image larger than 5MB
3. Try to submit

Expected Result:
- "File size exceeds maximum allowed size" error
```

### Test Case 9: No Category Selected
```
Steps:
1. Navigate to /business/create
2. Fill all required fields
3. Leave all categories unchecked
4. Try to submit

Expected Result:
- Form validation error: "Please select at least one business category"
- Error message appears in red below category grid
```

### Test Case 10: Edit Business
```
Steps:
1. Navigate to /business/edit/[business_id]
2. Modify business name
3. Submit form

Expected Result:
- Business is updated
- Previous categories are cleared
- New categories are added
- Old logo is deleted if new one uploaded
- Success message displayed
```

### Test Case 11: Unauthorized Access
```
Steps:
1. Try to access /business/create without being logged in

Expected Result:
- Redirect to login page
```

### Test Case 12: Wrong Role Access
```
Steps:
1. Log in as a customer (not business_owner)
2. Try to access /business/create

Expected Result:
- "Access Denied" error (403)
- User cannot create business
```

---

## Database Queries

### Create Business
```sql
INSERT INTO businesses 
(owner_id, business_name, description, location, contact_number, email, website, working_hours, business_logo) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
```

### Link Categories
```sql
INSERT IGNORE INTO business_categories (business_id, category_id) VALUES (?, ?)
```

### Get Business Details
```sql
SELECT b.*, u.first_name, u.last_name, COUNT(r.id) as review_count, AVG(r.rating) as rating
FROM businesses b
LEFT JOIN users u ON b.owner_id = u.id
LEFT JOIN reviews r ON b.id = r.business_id
WHERE b.id = ? AND b.is_active = 1
```

### Get Business Categories
```sql
SELECT c.* FROM categories c
JOIN business_categories bc ON c.id = bc.category_id
WHERE bc.business_id = ?
```

---

## Error Handling

### Common Errors and Solutions

| Error | Cause | Solution |
|-------|-------|----------|
| "All required fields must be filled" | Missing data | Complete all required fields |
| "Invalid contact number format" | Wrong format | Use +[country code][number] |
| "Invalid email address format" | Invalid email | Use valid email format |
| "File type not allowed" | Wrong file type | Upload JPG, PNG, GIF, or WebP |
| "File size exceeds maximum" | File too large | Use image smaller than 5MB |
| "Please select at least one category" | No categories | Check at least one category |
| "Failed to create business" | DB error | Check database connection |
| "Access Denied" | Wrong role | Log in with business_owner role |

---

## Security Features

### Authentication & Authorization
- ✅ Session-based authentication
- ✅ Role-based access control (business_owner only)
- ✅ Password hashing (bcrypt)
- ✅ CSRF protection via sessionToken

### Data Protection
- ✅ Input sanitization (htmlspecialchars)
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection (output encoding)
- ✅ File upload validation
- ✅ File type verification

### File Security
- ✅ File size limits (5MB max)
- ✅ File type validation
- ✅ Unique filenames (uniqid)
- ✅ Secure upload directory

---

## Code Structure

### Controller Method Flow
```php
BusinessController::create()
├── Check Authentication (Auth::requireRole)
├── Get Current User
├── If POST Request:
│   ├── Validate Each Field
│   ├── Handle File Upload
│   ├── Create Business Record
│   ├── Link Categories
│   └── Return Success/Error
└── Get Categories for Form
└── Return Categories Array
```

### Validation Method Structure
```php
Field Validation Pattern:
├── Check if empty
├── Trim whitespace
├── Check length constraints
├── Apply format validation
├── Sanitize if valid
└── Add error if invalid
```

---

## Performance Optimizations

### Database Queries
- ✅ Indexed fields: owner_id, business_name, location
- ✅ Foreign key relationships for referential integrity
- ✅ Prepared statements for security and performance
- ✅ Single SELECT to get business with owner info

### File Operations
- ✅ Unique filenames prevent conflicts
- ✅ Directory creation on demand
- ✅ Old file deletion on update

### Form Optimization
- ✅ Client-side validation reduces server load
- ✅ Lazy loading for images
- ✅ CSS-based grid layout
- ✅ Minimal JavaScript overhead

---

## Future Enhancements

### Planned Features
1. **Image Cropping** - Allow users to crop logo before upload
2. **Bulk Category Import** - Import categories from file
3. **Business Verification** - Admin approval workflow
4. **Auto-Geocoding** - Convert address to coordinates
5. **Business Hours Picker** - Visual time selector
6. **Template System** - Pre-filled business descriptions
7. **Analytics Dashboard** - View business performance
8. **Social Media Links** - Add multiple social profiles

---

## Support & Troubleshooting

### Common Issues

**Issue: Form submission goes to blank page**
- Solution: Check browser console for errors
- Check PHP error logs in /logs/ directory

**Issue: Logo upload fails silently**
- Solution: Check /uploads/business_logos/ permissions
- Ensure directory exists and is writable (chmod 755)

**Issue: Categories don't appear on form**
- Solution: Check database has categories
- Run Database_Setup.sql to populate sample categories

**Issue: Redirect to dashboard fails**
- Solution: Check that dashboard route exists
- Verify 'business_owner' role is set for user

---

## Deployment Checklist

Before deploying to production:

- [ ] Database migrations are complete
- [ ] Upload directories exist and have correct permissions
- [ ] Error logging is configured
- [ ] HTTPS is enabled
- [ ] File upload security is tested
- [ ] Input validation is working
- [ ] Categories are populated in database
- [ ] Test with both Firefox and Chrome
- [ ] Test on mobile devices
- [ ] Backup database before deploying
- [ ] Monitor error logs after deployment

---

## Support Resources

### Key Files
- Route Handler: [index.php](index.php#L259)
- Controller: [BusinessController.php](php-app/controllers/BusinessController.php)
- Model: [Business.php](php-app/classes/Business.php)
- View: [create.php](php-app/views/business/create.php)
- Database: [Database_Setup.sql](php-app/Database_Setup.sql)

### Related Documentation
- [API Routes](API_ROUTES.md)
- [Technical Reference](TECHNICAL_REFERENCE.md)
- [Database Setup](php-app/Database_Setup.md)

---

**Last Updated:** March 2, 2026
**Status:** ✅ Fully Implemented and Tested
**Version:** 1.0
