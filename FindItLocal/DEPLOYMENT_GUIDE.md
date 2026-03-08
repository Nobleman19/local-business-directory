# FindItLocal - Deployment & Architecture Guide

Complete guide for deploying and scaling the FindItLocal application.

## Table of Contents
1. [System Architecture](#system-architecture)
2. [Development Environment](#development-environment)
3. [Staging Environment](#staging-environment)
4. [Production Environment](#production-environment)
5. [Deployment Checklist](#deployment-checklist)
6. [Scaling Strategies](#scaling-strategies)
7. [Monitoring & Maintenance](#monitoring--maintenance)
8. [Disaster Recovery](#disaster-recovery)

---

## System Architecture

### High-Level Architecture

```
User Browser
    ↓
WiFi/Internet
    ↓
Web Server (Apache/Nginx)
    ↓
PHP Application
    ├── config/ (Database config)
    ├── classes/ (Business logic)
    ├── controllers/ (Request handlers)
    └── views/ (HTML templates)
    ↓
MySQL Database
    ├── users table
    ├── businesses table
    ├── categories table
    └── [other tables]
    ↓
File System
    ├── uploads/business_logos/
    ├── uploads/business_images/
    └── logs/
```

### Component Diagram

```
┌─────────────────────────────────────────────────────────┐
│                   Client Layer                          │
│  ┌────────────────────────────────────────────────────┐ │
│  │  Browser (HTML, CSS, JavaScript)                  │ │
│  └────────────────────────────────────────────────────┘ │
└──────────────────────────┬──────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────┐
│                Web Server (Apache/Nginx)                │
│  ┌────────────────────────────────────────────────────┐ │
│  │  Static Files (CSS, JS, Images)                   │ │
│  │  PHP Engine                                        │ │
│  │  SSL/TLS Encryption                              │ │
│  └────────────────────────────────────────────────────┘ │
└──────────────────────────┬──────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────┐
│              Application Layer (PHP)                    │
│  ┌────────────────────────────────────────────────────┐ │
│  │  index.php (Router)                               │ │
│  │  ├── Controllers (Request Handlers)               │ │
│  │  ├── Classes (Business Logic)                     │ │
│  │  └── Views (Response Templates)                   │ │
│  └────────────────────────────────────────────────────┘ │
└──────────────────────────┬──────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────┐
│               Data Layer (MySQL)                        │
│  ┌────────────────────────────────────────────────────┐ │
│  │  Database: finditlocal_db                         │ │
│  │  Tables: users, businesses, categories, etc.      │ │
│  │  Backup & Replication                            │ │
│  └────────────────────────────────────────────────────┘ │
└──────────────────────────┬──────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────┐
│             File Storage (Local Disk)                   │
│  ┌────────────────────────────────────────────────────┐ │
│  │  uploads/business_logos/                          │ │
│  │  uploads/business_images/                         │ │
│  │  logs/error.log                                   │ │
│  └────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────┘
```

### Technology Stack by Layer

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Presentation** | HTML5, CSS3, Bootstrap 5 | User Interface |
| **Client-Side** | JavaScript (Vanilla) | Interactivity |
| **Server** | Apache/Nginx | Web Server |
| **Application** | PHP 7.4+ | Backend Logic |
| **Database** | MySQL 5.7+ | Data Storage |
| **Session** | PHP Sessions | User State |
| **Storage** | File System | Upload Storage |
| **Security** | SSL/TLS, Bcrypt | Encryption |

---

## Development Environment

### Local Setup (Windows with XAMPP)

#### System Requirements
- Windows 7 or later
- XAMPP 7.4+ (PHP 7.4, MySQL, Apache)
- 2GB RAM minimum
- 500MB disk space for development

#### Installation Steps

1. **Install XAMPP**
   ```
   Download from: https://www.apachefriends.org/
   Install to: C:\xampp
   ```

2. **Configure XAMPP**
   ```
   Start Apache: XAMPP Control Panel → Start Apache
   Start MySQL: XAMPP Control Panel → Start MySQL
   ```

3. **Clone Project**
   ```
   Extract FindItLocal to: C:\xampp\htdocs\FindItLocal
   ```

4. **Create Database**
   ```
   Open browser: http://localhost/phpmyadmin
   Create database: finditlocal_db
   Import: php-app/Database_Setup.sql
   ```

5. **Configure Application**
   ```
   Edit: php-app/config/config.php
   Update DB credentials if needed
   ```

6. **Access Application**
   ```
   Open browser: http://localhost:8080/FindItLocal/php-app
   ```

#### Development Tools

**Recommended IDE**: Visual Studio Code
```
Extensions:
- PHP Intelephense
- MySQL
- MySQL Syntax
- PHP Extension Pack
```

**Database Management**: phpMyAdmin (included with XAMPP)

**Testing Tools**:
- Postman (API testing)
- Chrome DevTools (frontend debugging)
- PHP Debug (if xdebug configured)

---

## Staging Environment

### Staging Server Setup

#### Specifications
- Linux Server (Ubuntu 20.04 LTS recommended)
- PHP 7.4+
- MySQL 5.7+
- Apache 2.4+
- SSL Certificate
- 8GB RAM minimum
- 20GB disk space

#### Installation Steps

1. **Update System**
   ```bash
   sudo apt-get update
   sudo apt-get upgrade
   ```

2. **Install Web Server Stack**
   ```bash
   sudo apt-get install apache2
   sudo apt-get install php7.4 php7.4-mysql php7.4-gd
   sudo apt-get install mysql-server
   ```

3. **Enable Required Modules**
   ```bash
   sudo a2enmod rewrite
   sudo a2enmod ssl
   sudo systemctl restart apache2
   ```

4. **Configure MySQL**
   ```bash
   sudo mysql_secure_installation
   sudo systemctl start mysql
   sudo systemctl enable mysql
   ```

5. **Create Database**
   ```bash
   mysql -u root -p < Database_Setup.sql
   ```

6. **Deploy Application**
   ```bash
   cd /var/www/html
   git clone [repository-url] finditlocal
   cd finditlocal/php-app
   chmod -R 755 uploads/
   chmod -R 755 logs/
   ```

7. **Configure Web Server**
   ```apache
   <VirtualHost *:80>
       ServerName staging.finditlocal.local
       DocumentRoot /var/www/html/finditlocal/php-app
       
       <Directory /var/www/html/finditlocal/php-app>
           AllowOverride All
           Require all granted
       </Directory>
       
       ErrorLog ${APACHE_LOG_DIR}/finditlocal-error.log
       CustomLog ${APACHE_LOG_DIR}/finditlocal-access.log combined
   </VirtualHost>
   ```

8. **Enable SSL**
   ```bash
   sudo certbot certonly --apache -d staging.finditlocal.local
   # Configure SSL in VirtualHost
   ```

#### Staging Configuration

Create `config/staging.php`:
```php
<?php
// Staging Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'finditlocal_staging');
define('DB_PASSWORD', 'staging_password_123');
define('DB_NAME', 'finditlocal_staging_db');

define('APP_URL', 'https://staging.finditlocal.local');
define('JWT_SECRET', 'staging-secret-key-change-this');

// Enable detailed logging for testing
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', '/var/log/finditlocal/staging.log');

// Allow file uploads
define('UPLOAD_DIR', '/var/uploads/finditlocal/');
define('MAX_FILE_SIZE', 10485760); // 10MB for testing

// Session configuration
define('SESSION_TIMEOUT', 7200); // 2 hours for testing
?>
```

---

## Production Environment

### Production Server Requirements

#### Hardware
- **CPU**: 4 cores minimum (8+ recommended)
- **RAM**: 16GB minimum (32GB+ for high traffic)
- **Disk**: 100GB SSD minimum
- **Bandwidth**: Unlimited or 10Mbps+ dedicated

#### Software Stack
- **OS**: Ubuntu 20.04 LTS or later
- **Web Server**: Nginx 1.18+ (or Apache 2.4+)
- **PHP**: 8.0+ with opcache
- **Database**: MySQL 8.0 or MariaDB 10.5+
- **Cache**: Redis 6.0+
- **Search**: Elasticsearch (optional, for advanced search)

#### Security
- SSL/TLS Certificate (Let's Encrypt or commercial)
- Firewall rules configured
- Fail2ban for intrusion prevention
- Regular security updates

### Production Deployment

#### Step 1: Server Preparation

```bash
# Update system
sudo apt-get update && sudo apt-get upgrade -y

# Install dependencies
sudo apt-get install -y nginx php8.0-fpm mysql-server redis-server git curl

# Setup firewall
sudo ufw enable
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS

# Create application directory
sudo mkdir -p /var/www/finditlocal
sudo chown -R www-data:www-data /var/www/finditlocal
```

#### Step 2: Database Setup

```bash
# Create database and user
mysql -u root -p << EOF
CREATE DATABASE finditlocal_prod;
CREATE USER 'finditlocal_prod'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON finditlocal_prod.* TO 'finditlocal_prod'@'localhost';
FLUSH PRIVILEGES;
EOF

# Import schema
mysql -u finditlocal_prod -p finditlocal_prod < Database_Setup.sql

# Configure MySQL for production
# Edit /etc/mysql/mysql.conf.d/mysqld.cnf
# - max_connections = 500
# - innodb_buffer_pool_size = 8G
# - query_cache_type = 1
# - slow_query_log = ON

sudo systemctl restart mysql
```

#### Step 3: Application Deployment

```bash
# Deploy application
cd /var/www/finditlocal
git clone [repository-url] .
git checkout main  # Production branch

# Set permissions
chmod -R 755 php-app/uploads/
chmod -R 755 php-app/logs/
sudo chown -R www-data:www-data php-app/

# Create directories
mkdir -p php-app/logs
mkdir -p php-app/uploads/business_logos
mkdir -p php-app/uploads/business_images
sudo chown -R www-data:www-data php-app/logs
sudo chown -R www-data:www-data php-app/uploads
```

#### Step 4: Web Server Configuration (Nginx)

Create `/etc/nginx/sites-available/finditlocal`:
```nginx
upstream php {
    server unix:/run/php/php8.0-fpm.sock;
}

server {
    listen 80;
    server_name finditlocal.com www.finditlocal.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name finditlocal.com www.finditlocal.com;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/finditlocal.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/finditlocal.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Logging
    access_log /var/log/nginx/finditlocal-access.log;
    error_log /var/log/nginx/finditlocal-error.log;

    # Root directory
    root /var/www/finditlocal/php-app;
    
    # Upload directory size limit
    client_max_body_size 50M;

    # Compression
    gzip on;
    gzip_vary on;
    gzip_types text/plain text/css text/js text/xml text/javascript application/json application/javascript application/xml+rss;

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 7d;
        add_header Cache-Control "public, immutable";
    }

    # Disable access to sensitive files
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    location ~ ~$ {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Route all requests to index.php
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM Configuration
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass php;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Security
        fastcgi_param HTTP_PROXY "";
        fastcgi_intercept_errors on;
    }

    # Deny access to logs and uploads management
    location ~ ^/logs/ {
        deny all;
    }

    location ~ ^/config/ {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/finditlocal /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx
```

#### Step 5: SSL Certificate

```bash
# Install Certbot
sudo apt-get install -y certbot python3-certbot-nginx

# Obtain certificate
sudo certbot certonly --standalone -d finditlocal.com -d www.finditlocal.com

# Setup auto-renewal
sudo systemctl enable certbot.timer
sudo systemctl start certbot.timer
```

#### Step 6: Production Configuration

Create `php-app/config/production.php`:
```php
<?php
// Production Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'finditlocal_prod');
define('DB_PASSWORD', getenv('DB_PASSWORD')); // Use environment variables
define('DB_NAME', 'finditlocal_prod');

define('APP_URL', 'https://finditlocal.com');
define('APP_NAME', 'Find It Local - Business Directory');

// Security - Use environment variables or secure vault
define('JWT_SECRET', getenv('JWT_SECRET'));

// Error Reporting - Disabled in production
error_reporting(0);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', '/var/log/finditlocal/error.log');

// File Uploads
define('UPLOAD_DIR', '/var/uploads/finditlocal/');
define('MAX_FILE_SIZE', 5242880); // 5MB strict limit

// Session Security
define('SESSION_TIMEOUT', 3600);
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => 'finditlocal.com',
    'secure' => true,      // HTTPS only
    'httponly' => true,    // JS cannot access
    'samesite' => 'Strict' // CSRF protection
]);

// Database connection timeout
define('DB_CONNECT_TIMEOUT', 5);

// Rate limiting
define('RATE_LIMIT_ENABLED', true);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_DURATION', 900); // 15 minutes

// Monitoring
define('SENTRY_DSN', getenv('SENTRY_DSN')); // Error tracking
define('LOG_LEVEL', 'warning'); // Only warnings and errors
?>
```

### Performance Optimization

#### PHP Configuration

Edit `/etc/php/8.0/fpm/php.ini`:
```ini
; Memory and Execution
memory_limit = 512M
max_execution_time = 60
max_input_time = 60
upload_max_filesize = 50M
post_max_size = 50M

; OPcache (Essential for performance)
opcache.enable = 1
opcache.memory_consumption = 256
opcache.max_accelerated_files = 10000
opcache.validate_timestamps = 0
opcache.revalidate_freq = 0

; Session
session.gc_maxlifetime = 3600
session.cookie_secure = 1
session.cookie_httponly = 1
session.cookie_samesite = "Strict"

; Error Reporting
error_reporting = E_ALL
display_errors = 0
log_errors = 1
error_log = /var/log/php-error.log
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.0-fpm
```

---

## Deployment Checklist

### Pre-Deployment (48 hours before)

- [ ] Code review completed
- [ ] All tests passed
- [ ] Database backup taken
- [ ] Backup of current production files
- [ ] Rollback plan documented
- [ ] Notify team of deployment window
- [ ] Disable monitoring alerts (optional)

### Deployment Day

- [ ] Pull latest code from repository
- [ ] Verify all files are in place
- [ ] Run database migrations if needed
- [ ] Update configuration files
- [ ] Clear any caches
- [ ] Verify all permissions correct
- [ ] Test critical user flows
- [ ] Monitor error logs

### Post-Deployment (2 hours after)

- [ ] Monitor error logs for issues
- [ ] Verify database connections
- [ ] Test file uploads
- [ ] Verify business creation workflow
- [ ] Check image gallery functionality
- [ ] Monitor server performance
- [ ] Check SSL certificate validity
- [ ] Verify backups completed

### Rollback Plan

If deployment causes issues:

```bash
# 1. Revert code
cd /var/www/finditlocal
git reset --hard [previous-commit-hash]

# 2. Restore database (if schema changed)
mysql -u finditlocal_prod -p finditlocal_prod < backup.sql

# 3. Clear caches
redis-cli FLUSHALL

# 4. Restart services
sudo systemctl restart nginx php8.0-fpm

# 5. Verify application
# Visit https://finditlocal.com and test
```

---

## Scaling Strategies

### Horizontal Scaling (Multiple Servers)

#### Load Balancer Setup
```nginx
upstream backend {
    least_conn;
    server app1.internal:80;
    server app2.internal:80;
    server app3.internal:80;
    
    # Health check
    check interval=3000 rise=2 fall=5 timeout=1000;
}

server {
    listen 80;
    location / {
        proxy_pass http://backend;
    }
}
```

#### Session Storage (Shared Redis)

Modify `config/Database.php`:
```php
// Use Redis for sessions instead of file storage
ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://redis-server:6379');
```

#### Database Replication
```sql
-- Primary server backup
SHOW MASTER STATUS;

-- Secondary server replication
CHANGE MASTER TO
    MASTER_HOST = '192.168.1.100',
    MASTER_USER = 'repl_user',
    MASTER_PASSWORD = 'repl_password',
    MASTER_LOG_FILE = 'mysql-bin.000001',
    MASTER_LOG_POS = 154;

START SLAVE;
```

### Vertical Scaling (Bigger Servers)

- Upgrade CPU cores
- Increase RAM (optimize MySQL buffer pools)
- Use SSD storage
- Increase bandwidth

### Database Optimization

#### Indexing Strategy
```sql
-- Add indexes for frequently queried columns
ALTER TABLE businesses ADD INDEX idx_owner_id (owner_id);
ALTER TABLE business_images ADD INDEX idx_business_id (business_id);
ALTER TABLE business_categories ADD INDEX idx_category_id (category_id);
ALTER TABLE reviews ADD INDEX idx_business_id (business_id);
```

#### Query Optimization
```sql
-- Use EXPLAIN to analyze queries
EXPLAIN SELECT * FROM businesses WHERE owner_id = 1;

-- Add limiting and pagination
SELECT * FROM businesses LIMIT 20 OFFSET 40;

-- Use appropriate JOINs
SELECT b.*, COUNT(r.id) as review_count
FROM businesses b
LEFT JOIN reviews r ON b.id = r.business_id
GROUP BY b.id;
```

---

## Monitoring & Maintenance

### Application Monitoring

#### Log Monitoring
```bash
# Real-time error log monitoring
tail -f /var/log/finditlocal/error.log

# Search for specific errors
grep "ERROR" /var/log/finditlocal/error.log | tail -20

# Archive logs
gzip /var/log/finditlocal/error.log.*
```

#### Performance Monitoring
```bash
# Monitor CPU and Memory
top -u www-data

# Monitor disk usage
df -h
du -sh /var/www/finditlocal/*
du -sh /var/uploads/finditlocal/*

# Monitor network
netstat -tulpn | grep nginx
```

### Database Maintenance

#### Daily Tasks
```bash
# Backup database
mysqldump -u finditlocal_prod -p finditlocal_prod > /backups/finditlocal_$(date +%Y%m%d).sql

# Check for errors
mysql -u finditlocal_prod -p -e "SHOW ENGINE INNODB STATUS;" finditlocal_prod | grep ERROR

# Monitor slow queries
tail -f /var/log/mysql/slow-query.log
```

#### Weekly Tasks
```sql
-- Optimize tables
OPTIMIZE TABLE users;
OPTIMIZE TABLE businesses;
OPTIMIZE TABLE business_images;

-- Check table status
CHECK TABLE users;
CHECK TABLE businesses;

-- Analyze tables
ANALYZE TABLE users;
ANALYZE TABLE businesses;
```

#### Monthly Tasks
```sql
-- Archive old data (older than 6 months)
DELETE FROM bookings WHERE created_at < DATE_SUB(NOW(), INTERVAL 6 MONTH);

-- Rebuild indexes
REBUILD TABLE users;
REBUILD TABLE businesses;
```

### Security Monitoring

```bash
# Monitor failed logins
grep "failed_login" /var/log/finditlocal/*.log

# Check for brute force attempts
grep "Invalid email" /var/log/finditlocal/error.log | wc -l

# Monitor file integrity
aide --config=/etc/aide/aide.conf --check

# Check open ports
netstat -tulpn

# Monitor user accounts
w    # Currently logged in
who  # Login history
lastlog
```

### Automated Monitoring Setup

Create `/usr/local/bin/finditlocal-monitor.sh`:
```bash
#!/bin/bash

# Check if services are running
systemctl is-active --quiet nginx || systemctl restart nginx
systemctl is-active --quiet mysql || systemctl restart mysql
systemctl is-active --quiet php8.0-fpm || systemctl restart php8.0-fpm

# Check disk space
DISK_USAGE=$(df /var | awk 'NR==2 {print $5}' | cut -d% -f1)
if [ $DISK_USAGE -gt 90 ]; then
    # Send alert
    mail -s "Disk Usage Alert: $DISK_USAGE%" admin@finditlocal.com
fi

# Verify application is responding
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://finditlocal.com)
if [ $HTTP_STATUS -ne 200 ]; then
    mail -s "Application Error: HTTP $HTTP_STATUS" admin@finditlocal.com
fi

# Backup database
mysqldump -u finditlocal_prod -p$DB_PASSWORD finditlocal_prod | gzip > /backups/daily_$(date +%Y%m%d).sql.gz
```

Add to crontab:
```bash
crontab -e
# Run every hour
0 * * * * /usr/local/bin/finditlocal-monitor.sh
```

---

## Disaster Recovery

### Backup Strategy

#### 3-2-1 Backup Rule
- **3** copies of data
- **2** different storage media
- **1** offsite location

#### Backup Schedule

```
Daily:    Database dump (12 AM)
Weekly:   Full application + database (Sunday 2 AM)
Monthly:  Archive backup (1st of month, 3 AM)
Offsite:  Weekly copy to cloud storage
```

### Backup Implementation

```bash
#!/bin/bash
# /usr/local/bin/backup-finditlocal.sh

BACKUP_DIR="/backups/finditlocal"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=30

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump --user=finditlocal_prod --password=$DB_PASSWORD \
    finditlocal_prod > $BACKUP_DIR/database_$DATE.sql

# Compress database
gzip $BACKUP_DIR/database_$DATE.sql

# Application files backup (excluding uploads)
tar --exclude='uploads' --exclude='logs' \
    -czf $BACKUP_DIR/app_$DATE.tar.gz /var/www/finditlocal

# Upload files backup
tar -czf $BACKUP_DIR/uploads_$DATE.tar.gz /var/uploads/finditlocal

# Upload to cloud storage (AWS S3)
aws s3 cp $BACKUP_DIR/database_$DATE.sql.gz s3://finditlocal-backups/
aws s3 cp $BACKUP_DIR/app_$DATE.tar.gz s3://finditlocal-backups/
aws s3 cp $BACKUP_DIR/uploads_$DATE.tar.gz s3://finditlocal-backups/

# Delete old backups (older than retention period)
find $BACKUP_DIR -type f -mtime +$RETENTION_DAYS -delete

echo "Backup completed: $DATE"
```

### Restore Procedures

#### Restore from Database Backup

```bash
# 1. Stop application
sudo systemctl stop nginx php8.0-fpm

# 2. Restore database
mysql -u finditlocal_prod -p finditlocal_prod < backup_database.sql

# 3. Start application
sudo systemctl start nginx php8.0-fpm

# 4. Verify
# Visit application and test core functionality
```

#### Restore from Full Backup

```bash
# 1. Extract application
tar -xzf app_backup.tar.gz -C /var/www/

# 2. Extract uploads
tar -xzf uploads_backup.tar.gz -C /var/

# 3. Fix permissions
sudo chown -R www-data:www-data /var/www/finditlocal
sudo chown -R www-data:www-data /var/uploads/finditlocal

# 4. Restore database
mysql -u finditlocal_prod -p finditlocal_prod < database_backup.sql

# 5. Restart services
sudo systemctl restart nginx php8.0-fpm mysql

# 6. Verify restoration
# Test application thoroughly
```

### Disaster Recovery Plan (DRP)

#### RTO & RPO

| Metric | Target | Method |
|--------|--------|--------|
| RTO (Recovery Time Objective) | 4 hours | Automated failover to backup server |
| RPO (Recovery Point Objective) | 1 hour | Hourly incremental backups |

#### Failover Procedure

```
1. Detect outage (monitoring alerts)
2. Activate backup server
3. Update DNS to point to backup
4. Restore latest backup
5. Verify application functionality
6. Monitor until issue resolved
7. Switch back to primary
```

---

## Documentation Requirements

Each deployment should be documented:

### Deployment Log Template

```markdown
# Deployment Log - [DATE]

**Version**: [Git commit hash]
**Environment**: production
**Deployed by**: [Name]

## Changes
- [Change 1]
- [Change 2]

## Pre-Deployment Checks
- [ ] Code reviewed
- [ ] Tests passed
- [ ] Backup taken
- [ ] Rollback plan ready

## Deployment Steps
1. [Step 1] - [HH:MM]
2. [Step 2] - [HH:MM]

## Post-Deployment Verification
- [ ] Application loads
- [ ] Database accessible
- [ ] File uploads work
- [ ] Error logs clean

## Issues Encountered
[Any issues and resolutions]

## Performance Impact
[Expected vs actual]

## Rollback Decision
[Rolled back/Deployed successfully]
```

---

**Last Updated**: March 1, 2026

For questions or issues, contact: devops@finditlocal.com
