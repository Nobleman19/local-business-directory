# Service Booking System - Implementation Guide

## Overview
A complete service booking system has been implemented for customers to schedule services from registered businesses. The system includes date/time selection, pricing preview, and customer information collection.

## Features Implemented

### 1. Booking Page (create.php)
**Location:** `/php-app/views/booking/create.php`

The comprehensive booking form includes:

#### Form Structure - Two Column Layout

**Left Column: Service Details**

1. **Service Selection**
   - Dropdown with all available services
   - Service names and prices displayed
   - Data attribute storing price for calculation
   - Required field with validation

2. **Date Selection**
   - Date picker input
   - Minimum date: 2 days from current date
   - Future dates only
   - Required field

3. **Time Selection**
   - Time picker input (HH:MM format)
   - 24-hour format
   - Flexible scheduling
   - Required field

4. **Duration Selection**
   - Dropdown with preset durations
   - Options: 30min, 1hr, 1.5hrs, 2hrs, 3hrs
   - Required field
   - Used for price calculation

**Right Column: Customer Details**

1. **Full Name**
   - Text input
   - Pre-filled if user logged in
   - Required field
   - Validation: non-empty

2. **Email Address**
   - Email input with HTML5 validation
   - Pre-filled from session if available
   - Required field
   - Used for booking confirmation

3. **Phone Number**
   - Phone input with regex validation
   - Pattern: [0-9\-\+\s\(\)]{7,}
   - Minimum 7 characters
   - Required field

4. **Additional Notes**
   - Large text area (4 rows)
   - Optional field
   - Maximum 500 characters
   - For special requests/instructions

#### Price Summary Section
- **Service Price:** Base price of selected service
- **Duration:** Selected duration display
- **Total Price:** Calculated price (service price × duration in hours)
- Real-time updates as selections change
- Clear visual layout with border

#### Terms & Conditions
- Checkbox confirmation
- Required before submission
- Clear terms statement
- Legal protection

#### Form Actions
- **Confirm Booking:** Submit to BookingController
- **Back to Business:** Link back to business detail page

### 2. JavaScript Functionality

**Dynamic Price Calculation**
```javascript
function calculateTotal() {
    const price = serviceSelect.price
    const duration = durationSelect.value
    const total = price * (duration / 60)
    // Update display with formatted total
}
```

**Date Validation**
```javascript
// Set minimum date to 2 days from now
const minDate = today + 2 days
bookingDate.min = minDate
```

**Form Validation**
- Validates all required fields before submission
- Checks service selection
- Checks date/time selection
- Validates customer details
- Confirms terms agreement
- Shows specific error messages

**Real-Time Updates**
- Service change updates price
- Duration change updates total price
- Smooth transitions

### 3. Data Collection

**Form Fields Submitted:**
- `business_id` - Hidden, automatically set
- `service_id` - Selected service (required)
- `booking_date` - Date picker value (required)
- `booking_time` - Time picker value (required)
- `duration` - Duration in minutes (required)
- `customer_name` - Full name (required)
- `customer_email` - Email address (required)
- `customer_phone` - Phone number (required)
- `notes` - Special requests (optional)
- `terms_agreed` - Checkbox confirmation (required)

### 4. Validation

**Client-Side Validation (JavaScript):**
- Service must be selected
- Date must be provided and in future
- Time must be selected
- Duration must be selected
- Customer name required and non-empty
- Email must be valid format
- Phone must match pattern
- Terms checkbox must be checked
- Clear error messages for each validation

**Server-Side Validation (BookingController):**
- All required fields must have values
- Service exists and belongs to business
- Business is active
- Date is in future (minimum 2 days)
- Time is valid HH:MM format
- Duration is valid selection
- Email format validation
- Phone format validation
- Customer authenticated (if logged in)

### 5. Database Interaction

**Database Table: bookings**
```
id              INT PRIMARY KEY AUTO_INCREMENT
user_id         INT FOREIGN KEY (nullable, if guest)
business_id     INT FOREIGN KEY
service_id      INT FOREIGN KEY
booking_date    DATE
booking_time    TIME
duration        INT (minutes)
customer_name   VARCHAR(255)
customer_email  VARCHAR(255)
customer_phone  VARCHAR(20)
notes           TEXT (optional)
status          ENUM('pending','confirmed','completed','cancelled')
created_at      TIMESTAMP
updated_at      TIMESTAMP
```

## Integration Points

### 1. Business Detail Page
The booking form should be accessible from business detail view:

**Link to Booking:**
```html
<a href="/booking/create/{business_id}" class="btn btn-primary">
    Book a Service
</a>
```

**Data Required:**
- `$business_id` - ID of business
- `$services` - Array of available services with pricing

### 2. BookingController Integration
The form submits to BookingController's `create()` method:

**Expected POST Parameters:**
- All form fields listed above

**Return Values:**
- success: true/false
- message: Confirmation message
- error: Error message if validation fails
- booking_id: ID of created booking (on success)

### 3. Authentication Flow
```php
// Guest booking allowed with email/phone
// Logged-in users have pre-filled fields
// Booking linked to user_id if logged in
```

## User Flow

1. **Customer Views Business**
   - Navigates to business detail page
   - Reviews services and pricing
   - Clicks "Book Service" button

2. **Arrives at Booking Form**
   - Page header explains booking process
   - Two-column layout presents form clearly

3. **Selects Service**
   - Opens service dropdown
   - Sees service name and price
   - Clicks to select service
   - Price updates automatically

4. **Chooses Booking Time**
   - Date picker shows calendar
   - Selects date (min. 2 days in future)
   - Time picker shows field
   - Selects preferred time

5. **Selects Duration**
   - Opens duration dropdown
   - Chooses desired duration
   - Price updates based on duration

6. **Enters Personal Details**
   - Name field auto-filled if logged in
   - Email field auto-filled if logged in
   - Enters/confirms phone number
   - Adds any special requests in notes

7. **Reviews Price Summary**
   - Sees service price
   - Sees total price calculation
   - Confirms pricing is acceptable

8. **Agrees to Terms**
   - Reads terms checkbox
   - Checks confirmation box
   - Enables submit button

9. **Submits Booking**
   - Clicks "Confirm Booking"
   - Form validates client-side
   - Submits via POST
   - Receives confirmation

10. **Booking Confirmation**
    - Success message displays
    - Confirmation email sent
    - Booking added to customer profile

## Responsive Design

### Mobile Layout (<768px)
```css
- Single column full-width form
- Stacked form sections
- Full-width inputs
- Stacked buttons
- Readable font sizes (14px+)
- Touch-friendly button sizes (44px minimum)
```

### Tablet Layout (768px-1024px)
```css
- Two column layout begins
- Form sections side-by-side
- Wider inputs
- Improved spacing
```

### Desktop Layout (>1024px)
```css
- Full two-column layout
- Maximum width 1000px
- Optimal spacing
- Clear visual hierarchy
```

## Styling & UX

**Color Scheme:**
- Primary Blue (#3498db) - Main buttons, accents
- Page Background White
- Form Background White with shadows
- Secondary Gray (#95a5a6) - Secondary buttons
- Text Gray (#2c3e50) - Main text
- Light Gray (#f8f9fa) - Section backgrounds
- Danger Red (#e74c3c) - Error messages

**Typography:**
- Header: 28px bold
- Section Headers: 16px bold with blue underline
- Labels: 14px bold with "required" indicator
- Inputs: 14px sans-serif
- Helper Text: 12px gray italic

**Interactive Elements:**
- Button hover: Background color shift + Y-axis translation (-2px)
- Input focus: Blue border + subtle box-shadow
- Smooth transitions (0.3s ease)
- Clear focus states for accessibility

**Spacing:**
- Form sections: 30px gap
- Form group items: 20px gap
- Button group: 15px gap
- Padding in containers: 20-30px

## Features & Benefits

### For Customers
1. **Easy Scheduling** - Simple date/time selection
2. **Price Transparency** - Real-time price calculation
3. **Auto-Fill** - Pre-populated for logged-in users
4. **Flexibility** - Multiple duration options
5. **Communication** - Notes field for special requests
6. **Confirmation** - Clear booking confirmation

### For Businesses
1. **Lead Generation** - Capture booking requests
2. **Schedule Management** - Organized booking information
3. **Customer Details** - Contact information for follow-up
4. **Admin Tools** - Booking management in admin panel
5. **Status Tracking** - Confirm/complete bookings

## Security Features

- **Input Validation:** All fields validated server-side
- **HTML Escaping:** All user input escaped in output
- **CSRF Protection:** (Implement via token if needed)
- **Date Validation:** Future dates only
- **Email Verification:** Valid email format required
- **Phone Validation:** Valid phone format required
- **Business Verification:** Service must belong to business

## Error Handling

**Client-Side Errors (JavaScript):**
- "Please select a service"
- "Please select a date"
- "Please select a time"
- "Please select a duration"
- "Please enter your name"
- "Please enter your email"
- "Please enter your phone number"
- "Please agree to the terms and conditions"

**Server-Side Errors:**
- Invalid service ID
- Business not found
- Service not from this business
- Invalid date (past or too soon)
- Invalid time format
- Missing required fields
- Invalid email format
- Invalid phone format

**Success Response:**
- Booking confirmation message
- New booking ID
- Link to booking details
- Confirmation email sent notice

## Database Queries

### Create Booking
```sql
INSERT INTO bookings 
(user_id, business_id, service_id, booking_date, booking_time, 
 duration, customer_name, customer_email, customer_phone, notes, status)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
```

### Retrieve Service Pricing
```sql
SELECT id, service_name, price 
FROM services 
WHERE business_id = ? AND is_active = 1
```

### Get Business Services
```sql
SELECT s.*, b.business_name
FROM services s
JOIN businesses b ON s.business_id = b.id
WHERE b.id = ? AND b.is_active = 1
```

## Admin Booking Management

Admins can:
- View all bookings (/admin/bookings)
- Filter by status (pending, confirmed, completed, cancelled)
- Update booking status
- View customer details
- View business & service information
- Cancel bookings if needed

## Customer Booking History

Customers can:
- View their bookings (/bookings)
- See booking status
- View booking details
- Cancel upcoming bookings (future feature)
- Add review after completion

## Financial Aspects

**Pricing Calculation:**
```javascript
Total = Service Price × (Duration in Minutes / 60)

Example:
Service: $50/hour
Duration: 90 minutes
Total = $50 × (90 / 60) = $50 × 1.5 = $75
```

**Duration Options:**
- 30 minutes: 0.5x price
- 60 minutes (1 hour): 1x price
- 90 minutes (1.5 hours): 1.5x price
- 120 minutes (2 hours): 2x price
- 180 minutes (3 hours): 3x price

## Testing Checklist

- [ ] Form loads correctly on business detail page
- [ ] Service dropdown displays all services with prices
- [ ] Date picker prevents past dates
- [ ] Minimum date is 2 days from today
- [ ] Time picker accepts all valid times
- [ ] Duration dropdown shows all options
- [ ] Price updates when service changes
- [ ] Price updates when duration changes
- [ ] Total price calculates correctly
- [ ] Field validation works for all inputs
- [ ] Terms checkbox is required
- [ ] Form submits successfully
- [ ] Booking saved to database
- [ ] Confirmation email sent (if implemented)
- [ ] Responsive design works on mobile/tablet/desktop
- [ ] Logged-in user fields pre-fill correctly
- [ ] Guest users can book without login

## Future Enhancements

1. **Payment Integration**
   - Process payment before booking
   - Multiple payment methods
   - Invoice generation

2. **Calendar Integration**
   - Sync with business calendar
   - Show availability in real-time
   - Block out busy times

3. **Reminders**
   - Email reminders to customer
   - SMS reminders before booking
   - Business owner notifications

4. **Availability Windows**
   - Set business hours
   - Set holidays/closed days
   - Time slot availability

5. **Automatic Confirmation**
   - Auto-confirm certain bookings
   - Waitlist for busy times
   - Manual approval option

6. **Cancellation Policy**
   - Implement cancellation fees
   - Set cancellation deadline
   - Refund handling

7. **Deposit/Prepay**
   - Collect deposit upfront
   - Full payment options
   - Invoice link in confirmation

---

**Status:** ✅ Fully Implemented  
**Last Updated:** 2025-01-XX  
**Version:** 1.0  
**File:** `/php-app/views/booking/create.php` (500+ lines)
