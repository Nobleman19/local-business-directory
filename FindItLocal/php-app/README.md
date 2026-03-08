# Find It Local - PHP Version

## Project Overview
This is a complete conversion of the Node.js/React Business Directory application to PHP/MySQL with XAMPP.

## Project Structure

```
php-app/
├── config/
│   ├── config.php              # Configuration settings
│   └── Database.php            # Database connection class
├── classes/
│   ├── Auth.php                # Authentication
│   ├── Validation.php          # Input validation
│   ├── Helper.php              # Helper functions
│   ├── User.php                # User model
│   ├── Business.php            # Business model
│   ├── Service.php             # Service model
│   ├── Booking.php             # Booking model
│   ├── Review.php              # Review model
│   ├── Category.php            # Category model
│   ├── Payment.php             # Payment model
│   ├── Discount.php            # Discount model
│   ├── Contact.php             # Contact model
│   └── Ticket.php              # Support ticket model
├── controllers/
│   ├── AuthController.php
│   ├── BusinessController.php
│   ├── ServiceController.php
│   ├── BookingController.php
│   ├── ReviewController.php
│   ├── CategoryController.php
│   ├── PaymentController.php
│   └── ContactController.php
├── views/
│   ├── layout.php              # Main layout template
│   ├── home.php                # Home page
│   ├── about.php               # About page
│   ├── contact.php             # Contact page
│   ├── faq.php                 # FAQ page
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── business/
│   │   ├── list.php
│   │   └── detail.php
│   ├── booking/
│   │   └── list.php
│   └── dashboard/
│       └── profile.php
├── assets/
│   ├── css/
│   │   └── style.css           # Main stylesheet
│   ├── js/
│   │   └── main.js             # Main JavaScript
│   └── images/
├── uploads/                    # User uploads directory
├── index.php                   # Main router
├── Database_Setup.sql          # SQL schema
└── README.md

```

## Installation & Setup

### Prerequisites
- XAMPP (Apache, PHP, MySQL)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Step-by-Step Setup

1. **Place files in XAMPP directory:**
   ```
   C:\xampp\htdocs\FindItLocal\php-app\
   ```

2. **Create MySQL Database:**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Import the SQL file: `Database_Setup.sql`
   - This creates all necessary tables

3. **Update Database Configuration (if needed):**
   - Edit `config/config.php`
   - Update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASSWORD', '');
   define('DB_NAME', 'business_directory');
   ```

4. **Create Uploads Directory:**
   - Ensure `uploads/` folder exists and is writable
   ```
   mkdir uploads
   chmod 755 uploads
   ```

5. **Start XAMPP:**
   - Start Apache and MySQL services
   - Access the application: `http://localhost/FindItLocal/php-app`

## Database Schema

The application uses the following main tables:
- **users** - User accounts and profiles
- **businesses** - Business listings
- **business_categories** - Business-Category relationships
- **services** - Services offered by businesses
- **bookings** - Service bookings
- **payments** - Payment transactions
- **reviews** - Customer reviews and ratings
- **categories** - Service categories
- **discounts** - Promotional discount codes
- **contact_messages** - Contact form submissions
- **support_tickets** - Customer support tickets
- **availability** - Service availability schedules

## Features Implemented

### User Management
- User registration (Customer/Business Owner)
- User login/logout
- Profile management
- Password hashing with bcrypt

### Business Management
- Create and manage businesses
- Business categories
- Business details, contact info, working hours
- Logo uploads

### Services
- Create and manage services
- Service pricing and duration
- Availability management
- Service categorization

### Bookings
- Create service bookings
- View and manage bookings
- Booking status tracking
- Booking cancellation

### Payments
- Payment processing
- Transaction tracking
- Multiple payment methods
- Payment status management

### Reviews & Ratings
- Customer reviews and ratings (1-5 stars)
- Review display and management
- Rating calculation

### Categories
- Category management
- Business-category associations
- Category browsing

### Search & Filter
- Search businesses by name
- Search by location
- Filter by category
- Pagination support

### Additional Features
- Contact form for inquiries
- FAQ section
- Discount code management
- Support ticket system
- Session-based authentication

## Security Features

- SQL injection prevention (prepared statements)
- Password hashing with bcrypt
- Session-based authentication
- Input validation and sanitization
- CSRF protection ready
- XSS prevention (htmlspecialchars)
- File upload validation
- Error logging

## User Roles

1. **Customer** - Can browse, search, book services, and leave reviews
2. **Business Owner** - Can manage their business, services, and view bookings
3. **Admin** - Full system access (can be extended)

## API Endpoints (Main Routes)

- `/` - Home page
- `/login` - Login page
- `/register` - Registration page
- `/logout` - Logout
- `/businesses` - Browse all businesses
- `/business/{id}` - Business details
- `/search` - Search functionality
- `/bookings` - User bookings
- `/profile` - User profile
- `/contact` - Contact form
- `/about` - About page
- `/faq` - FAQ page

## Configuration

Edit `config/config.php` to customize:
- Database credentials
- JWT secret
- Session timeout
- File upload limits
- Maximum file size
- Allowed file extensions

## Functionality Mapping

### From Node.js to PHP

| Node.js/Express | PHP Equivalent |
|---|---|
| mongoose models | Custom Database + prepared statements |
| JWT tokens | Session-based auth |
| express routes | index.php router |
| React components | PHP views + HTML templates |
| bcryt | password_hash() / password_verify() |
| nodemailer | mail() or PHPMailer (can be added) |
| middleware | Auth class |

## Next Steps for Expansion

1. **Email Notifications**
   - Add PHPMailer for email confirmations
   - Set up email templates

2. **Payment Gateway Integration**
   - Integrate with Stripe, PayPal, or local payment providers
   - Implement webhook handling

3. **Image Management**
   - Implement image upload for profile pictures
   - Add image gallery for businesses

4. **Analytics**
   - Add booking statistics
   - Business performance metrics

5. **Admin Dashboard**
   - Create admin panel
   - User and business management
   - System statistics

6. **API Version**
   - Convert to RESTful API
   - Add JWT authentication
   - Mobile app support

## Troubleshooting

### Database Connection Error
- Ensure MySQL is running
- Check database credentials in `config.php`
- Verify database exists and tables are created

### File Upload Issues
- Check `uploads/` directory permissions
- Verify PHP `max_upload_size` in php.ini
- Check file extension is allowed

### Session Issues
- Ensure cookies are enabled
- Check PHP `session.save_path` is writable
- Clear browser cookies if needed

## Support

For issues or questions, use the Contact Us page within the application or refer to this README.

## License

This is a commercial project. All rights reserved.

## Version History

- **v1.0** (2024) - Initial PHP conversion from Node.js/React

---

**Last Updated:** February 27, 2024
**Compatibility:** PHP 7.4+, MySQL 5.7+, XAMPP
