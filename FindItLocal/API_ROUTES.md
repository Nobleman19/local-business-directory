# FindItLocal - API & Routes Documentation

Complete reference for all routes, endpoints, and their parameters.

## Table of Contents
1. [Authentication Routes](#authentication-routes)
2. [Business Routes](#business-routes)
3. [Image Management Routes](#image-management-routes)
4. [Dashboard Routes](#dashboard-routes)
5. [Contact Routes](#contact-routes)
6. [Error Responses](#error-responses)

---

## Authentication Routes

### User Registration

**Endpoint**: `POST /register`

**Description**: Register a new user account

**Authentication**: Not required

**Content-Type**: `application/x-www-form-urlencoded`

**Parameters**:
| Parameter | Type | Required | Validation | Notes |
|-----------|------|----------|-----------|-------|
| first_name | string | Yes | Max 100 chars | |
| last_name | string | Yes | Max 100 chars | |
| email | string | Yes | Valid email format | Must be unique |
| password | string | Yes | Min 6 characters | Will be hashed |
| phone_number | string | Yes | Exactly 10 digits | Numeric only |
| address_line_1 | string | Yes | Max 255 chars | Street address |
| address_line_2 | string | No | Max 255 chars | Apartment/Suite |
| city | string | Yes | Max 100 chars | |
| state | string | Yes | Max 100 chars | Province/Region |
| postal_code | string | Yes | Exactly 6 digits | Numeric only |
| role | string | Yes | `customer` or `business_owner` | Determines user access |

**Request Example**:
```
POST /register HTTP/1.1
Content-Type: application/x-www-form-urlencoded

first_name=John&last_name=Doe&email=john@example.com&password=SecurePass123&phone_number=0960000000&address_line_1=123%20Main%20St&city=Lusaka&state=Lusaka&postal_code=000000&role=business_owner
```

**Success Response**: 
- Status: 302 Redirect to `/dashboard`
- Session created with user data

**Failure Response**:
- Status: 302 Redirect to `/register`
- `$_SESSION['error']` contains error message

**Error Messages**:
- "Invalid email format"
- "Email already registered"
- "Password must be at least 6 characters"
- "Phone number must be 10 digits"
- "Postal code must be 6 digits"
- "Please select a valid role"

---

### User Login

**Endpoint**: `POST /login`

**Description**: Authenticate user and create session

**Authentication**: Not required

**Content-Type**: `application/x-www-form-urlencoded`

**Parameters**:
| Parameter | Type | Required | Validation |
|-----------|------|----------|-----------|
| email | string | Yes | Valid email |
| password | string | Yes | 6+ characters |

**Request Example**:
```
POST /login HTTP/1.1
Content-Type: application/x-www-form-urlencoded

email=john@example.com&password=SecurePass123
```

**Success Response**:
- Status: 302 Redirect to `/dashboard`
- Session variables set:
  - `user_id`: int
  - `user_email`: string
  - `user_role`: string ('customer', 'business_owner', 'admin')
  - `first_name`: string
  - `last_name`: string

**Failure Response**:
- Status: 302 Redirect to `/login`
- `$_SESSION['error']` = "Invalid email or password"

---

### User Logout

**Endpoint**: `GET /logout`

**Description**: Destroy session and logout user

**Authentication**: Required (logged-in user)

**Response**:
- Status: 302 Redirect to `/home`
- Session destroyed

---

### Display Login Form

**Endpoint**: `GET /login`

**Description**: Display user login form

**Response**: HTML form with email and password fields

---

### Display Registration Form

**Endpoint**: `GET /register`

**Description**: Display user registration form

**Response**: HTML form with all registration fields

---

## Business Routes

### List All Businesses

**Endpoint**: `GET /business`

**Description**: Display paginated list of all businesses

**Authentication**: Not required

**Query Parameters**:
| Parameter | Type | Required | Default | Notes |
|-----------|------|----------|---------|-------|
| page | int | No | 1 | Page number |
| category | int | No | - | Filter by category ID |
| search | string | No | - | Search by name |
| location | string | No | - | Filter by location |

**Request Example**:
```
GET /business?category=1&search=restaurant&page=1
```

**Response**: HTML page with business cards/grid

**Display Elements**:
- Business logo
- Business name
- Rating
- Location
- Categories
- Contact button

---

### View Business Detail

**Endpoint**: `GET /business/{id}`

**Description**: Display detailed business profile

**Authentication**: Not required

**Path Parameters**:
| Parameter | Type | Required |
|-----------|------|----------|
| id | int | Yes |

**Request Example**:
```
GET /business/5
```

**Response**: HTML page with:
- Business logo
- Name and rating
- Description
- Contact information
- Working hours
- Categories
- Image gallery
- Services list
- Customer reviews
- Booking button

**Error Response**:
- If business not found: 404 page

---

### Create Business Form

**Endpoint**: `GET /business/create`

**Description**: Display form to create new business

**Authentication**: Required (business_owner role)

**Request Example**:
```
GET /business/create
```

**Response**: HTML form with fields for:
- Business name
- Description
- Location
- Contact number
- Email
- Website
- Working hours
- Logo upload
- Category selection

**Error Response**:
- Status: 302 Redirect to `/dashboard`
- Message: "You must be a business owner to create a business"

---

### Create Business

**Endpoint**: `POST /business/create`

**Description**: Create new business and save to database

**Authentication**: Required (business_owner role)

**Content-Type**: `multipart/form-data`

**Parameters**:
| Parameter | Type | Required | Validation | Notes |
|-----------|------|----------|-----------|-------|
| business_name | string | Yes | Max 255 chars | |
| description | string | Yes | Max 65,535 chars | |
| location | string | Yes | Max 255 chars | |
| contact_number | string | Yes | Max 20 chars | |
| email | string | Yes | Valid email | |
| website | string | No | Valid URL if provided | Optional |
| working_hours | string | No | Max 1000 chars | Optional |
| business_logo | file | No | Image file | Max 5MB |
| categories | array | No | Valid category IDs | Can select multiple |

**Request Example**:
```
POST /business/create HTTP/1.1
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="business_name"

John's Restaurant
------WebKitFormBoundary
Content-Disposition: form-data; name="description"

Fine dining restaurant
------WebKitFormBoundary
Content-Disposition: form-data; name="location"

123 Main Street, Lusaka
------WebKitFormBoundary
Content-Disposition: form-data; name="categories"

1
------WebKitFormBoundary
Content-Disposition: form-data; name="categories"

3
------WebKitFormBoundary
Content-Disposition: form-data; name="business_logo"; filename="logo.png"
Content-Type: image/png

[binary data]
------WebKitFormBoundary--
```

**Success Response**:
- Status: 302 Redirect to `/business/{id}`
- `$_SESSION['success']` = "Business created successfully"

**Failure Response**:
- Status: 302 Redirect to `/business/create`
- `$_SESSION['error']` contains specific error message

**Error Messages**:
- "Business name is required"
- "Invalid email format"
- "Logo file is too large (max 5MB)"
- "Invalid file type"
- "Database error occurred"

---

### Edit Business Form

**Endpoint**: `GET /business/edit/{id}`

**Description**: Display form to edit business

**Authentication**: Required (business owner of this business)

**Path Parameters**:
| Parameter | Type | Required |
|-----------|------|----------|
| id | int | Yes |

**Request Example**:
```
GET /business/edit/5
```

**Response**: HTML form pre-filled with:
- Current business values
- Current logo preview
- Pre-selected categories
- Modified form action URI

**Error Responses**:
- If not authenticated: Redirect to `/login`
- If not business owner: Redirect to `/dashboard` with error
- If business not found: 404 page

---

### Update Business

**Endpoint**: `POST /business/edit/{id}`

**Description**: Update existing business

**Authentication**: Required (owner of business)

**Content-Type**: `multipart/form-data`

**Path Parameters**:
| Parameter | Type | Required |
|-----------|------|----------|
| id | int | Yes |

**Parameters**: Same as POST /business/create

**Request Example**:
```
POST /business/edit/5 HTTP/1.1
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

[same format as create]
```

**Success Response**:
- Status: 302 Redirect to `/business/{id}`
- `$_SESSION['success']` = "Business updated successfully"

**Failure Response**:
- Status: 302 Redirect back to edit form
- `$_SESSION['error']` contains error message

**Authorization Check**:
- Verifies `business->owner_id == current_user_id`
- Returns "Unauthorized" if mismatch

---

### Delete Business

**Endpoint**: `GET /business/delete/{id}`

**Description**: Delete business and associated data

**Authentication**: Required (owner of business)

**Path Parameters**:
| Parameter | Type | Required |
|-----------|------|----------|
| id | int | Yes |

**Request Example**:
```
GET /business/delete/5
```

**Success Response**:
- Status: 302 Redirect to `/dashboard`
- `$_SESSION['success']` = "Business deleted successfully"

**Side Effects**:
- Deletes business record
- Deletes associated categories
- Deletes associated images
- Deletes associated services
- Cascades to bookings

---

## Image Management Routes

### View Image Gallery

**Endpoint**: `GET /business/images/{id}`

**Description**: Display image gallery for business

**Authentication**: Required (business owner of this business)

**Path Parameters**:
| Parameter | Type | Required |
|-----------|------|----------|
| id | int | Yes |

**Request Example**:
```
GET /business/images/5
```

**Response**: HTML page with:
- Image upload form (drag & drop)
- Image grid
- Primary image badge
- Delete button per image
- Upload date
- Validation messages

**Error Response**:
- If not business owner: 403 Unauthorized

---

### Upload Business Image

**Endpoint**: `POST /business/images/upload`

**Description**: Upload new image for business

**Authentication**: Required (business owner)

**Content-Type**: `multipart/form-data`

**Parameters**:
| Parameter | Type | Required | Validation |
|-----------|------|----------|-----------|
| business_id | int | Yes | Owned by current user |
| image | file | Yes | Image file, max 5MB |
| is_primary | boolean | No | Default: false |

**Request Example**:
```
POST /business/images/upload HTTP/1.1
Content-Type: multipart/form-data; boundary=----WebKitFormBoundary

------WebKitFormBoundary
Content-Disposition: form-data; name="business_id"

5
------WebKitFormBoundary
Content-Disposition: form-data; name="is_primary"

true
------WebKitFormBoundary
Content-Disposition: form-data; name="image"; filename="restaurant.jpg"
Content-Type: image/jpeg

[binary data]
------WebKitFormBoundary--
```

**Success Response**:
```json
{
    "success": true,
    "image_id": 42,
    "image_url": "/uploads/business_images/5_1699564850_abc123.jpg",
    "message": "Image uploaded successfully"
}
```

**Failure Response**:
```json
{
    "success": false,
    "error": "File size exceeds 5MB limit"
}
```

**Error Messages**:
- "File is required"
- "File size exceeds 5MB limit"
- "Invalid file type"
- "Business not found"
- "Unauthorized"
- "Upload failed"

---

### Delete Image

**Endpoint**: `POST /business/images/delete`

**Description**: Delete image from gallery

**Authentication**: Required (business owner)

**Content-Type**: `application/x-www-form-urlencoded`

**Parameters**:
| Parameter | Type | Required |
|-----------|------|----------|
| image_id | int | Yes |

**Request Example**:
```
POST /business/images/delete HTTP/1.1
Content-Type: application/x-www-form-urlencoded

image_id=42
```

**Success Response**:
```json
{
    "success": true,
    "message": "Image deleted successfully"
}
```

**Failure Response**:
```json
{
    "success": false,
    "error": "Image not found"
}
```

**Side Effects**:
- Deletes database record
- Deletes physical file from server
- If was primary, reassigns primary to oldest image

---

### Set Primary Image

**Endpoint**: `POST /business/images/set-primary`

**Description**: Set an image as primary/featured

**Authentication**: Required (business owner)

**Content-Type**: `application/x-www-form-urlencoded`

**Parameters**:
| Parameter | Type | Required |
|-----------|------|----------|
| image_id | int | Yes |
| business_id | int | Yes |

**Request Example**:
```
POST /business/images/set-primary HTTP/1.1
Content-Type: application/x-www-form-urlencoded

image_id=42&business_id=5
```

**Success Response**:
```json
{
    "success": true,
    "message": "Primary image updated"
}
```

**Failure Response**:
```json
{
    "success": false,
    "error": "Unauthorized"
}
```

**Side Effects**:
- Sets image.is_primary = 1
- Sets all other images for business to is_primary = 0

---

## Dashboard Routes

### User Dashboard

**Endpoint**: `GET /dashboard`

**Description**: Display user-specific dashboard

**Authentication**: Required (any logged-in user)

**Query Parameters**: None

**Request Example**:
```
GET /dashboard
```

**Response**: HTML dashboard

**Customer Dashboard Shows**:
- Account summary
- Recent bookings
- Quick links to browse businesses
- Booking status

**Business Owner Dashboard Shows**:
- Account summary
- Owned businesses grid with:
  - Logo
  - Name
  - Rating
  - Categories
  - Quick action buttons
- Recent customer bookings
- Quick stats
  - Total businesses owned
  - Total bookings received
  - Average rating

**Error Response**:
- If not authenticated: 302 Redirect to `/login`

---

## Contact Routes

### Contact Form Page

**Endpoint**: `GET /contact`

**Description**: Display contact form

**Authentication**: Not required

**Response**: HTML contact form with fields for:
- Name
- Email
- Phone (optional)
- Subject
- Message

---

### Submit Contact Message

**Endpoint**: `POST /contact/send`

**Description**: Submit contact form message

**Authentication**: Not required

**Content-Type**: `application/x-www-form-urlencoded`

**Parameters**:
| Parameter | Type | Required | Validation |
|-----------|------|----------|-----------|
| name | string | Yes | Max 255 chars |
| email | string | Yes | Valid email |
| phone | string | No | 10 digits if provided |
| subject | string | Yes | Max 255 chars |
| message | string | Yes | Max 5000 chars |

**Request Example**:
```
POST /contact/send HTTP/1.1
Content-Type: application/x-www-form-urlencoded

name=John%20Doe&email=john@example.com&phone=0960000000&subject=Inquiry&message=I%20have%20a%20question
```

**Success Response**:
- Status: 302 Redirect to `/contact`
- `$_SESSION['success']` = "Message sent successfully"

**Failure Response**:
- Status: 302 Redirect to `/contact`
- `$_SESSION['error']` contains error message

**Error Messages**:
- "Name is required"
- "Invalid email format"
- "Invalid phone format"
- "Message is required"

---

## Error Responses

### HTTP Status Codes Used

| Status | Meaning | Example |
|--------|---------|---------|
| 200 | OK | Successful GET request |
| 302 | Redirect | After successful POST |
| 400 | Bad Request | Invalid parameters |
| 401 | Unauthorized | Not authenticated |
| 403 | Forbidden | Insufficient permissions |
| 404 | Not Found | Business/page not found |
| 500 | Server Error | Database error |

### Standard Error Response (HTML)

When error occurs with session handling:
```php
$_SESSION['error'] = "Error message here";
header("Location: /previous-page");
```

User sees error message displayed on redirected page.

### Standard Success Response (HTML)

```php
$_SESSION['success'] = "Operation completed successfully";
header("Location: /success-page");
```

---

## Common Request/Response Patterns

### HTML Form Submission

**Request**:
```html
<form method="POST" action="/business/create" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="...">
    <input type="text" name="business_name" required>
    <input type="file" name="business_logo">
    <button type="submit">Create</button>
</form>
```

**Response**: 302 Redirect with session message

### AJAX JSON Response

**Request**:
```javascript
fetch('/business/images/upload', {
    method: 'POST',
    body: formData
});
```

**Response**:
```json
{
    "success": true,
    "image_id": 42,
    "image_url": "...",
    "message": "..."
}
```

---

## Authentication Using Routes

**Login Required Routes**:
- `/dashboard` - Any logged-in user
- `/business/create` - business_owner role
- `/business/edit/{id}` - Owner of business
- `/business/images/{id}` - Owner of business
- `/business/images/upload` - Owner of business
- `/business/images/delete` - Owner of business
- `/business/images/set-primary` - Owner of business

**Login Not Required**:
- `/` (home)
- `/login`
- `/register`
- `/business` (list)
- `/business/{id}` (detail)
- `/contact`
- `/about`
- `/faq`

---

## Rate Limiting

Currently no rate limiting is implemented. Consider adding for production:
- 5 login attempts per 15 minutes per IP
- 10 registration attempts per hour per IP
- 100 requests per minute per user

---

## CORS Headers

No CORS headers are set. If building a separate frontend:
1. Add CORS headers in config or controller
2. Allow specific origin domains
3. Include credentials if needed

---

## File Naming Conventions

### Uploaded Files

**Business Logo Format**:
```
{business_id}_{timestamp}_{random_hex}.{extension}
Example: 5_1699564800_a1b2c3d4.png
```

**Business Image Format**:
```
{business_id}_{timestamp}_{random_hex}.{extension}
Example: 5_1699564850_xyz789.jpg
```

---

**Last Updated**: March 1, 2026
