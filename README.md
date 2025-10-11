# Autholas PHP Authentication System

A comprehensive PHP implementation for Autholas authentication service optimized for web applications with no hardware ID restrictions.

## Features

- Web-based user authentication via Autholas API
- No hardware ID (HWID) restrictions - perfect for web applications
- Session management with PHP sessions
- Persistent login across page refreshes
- Multi-device support (login from any browser/device)
- Comprehensive error handling with user-friendly messages
- Responsive web interface
- Cross-platform compatibility

## Prerequisites

- **PHP 7.4+** (PHP 8.0+ recommended)
- **Web server** (Apache, Nginx, or built-in PHP server)
- **cURL extension** enabled in PHP
- **JSON extension** enabled in PHP (usually enabled by default)
- **Sessions support** enabled in PHP
- Internet connection for API communication

## Installation Guide

### Method 1: XAMPP/WAMP/MAMP (Recommended for Development)

#### Step 1: Install Local Server

**Windows - XAMPP:**
1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install and start Apache service
3. Navigate to `C:\xampp\htdocs\`

**Windows - WAMP:**
1. Download WAMP from [http://www.wampserver.com/](http://www.wampserver.com/)
2. Install and start services
3. Navigate to `C:\wamp64\www\`

**macOS - MAMP:**
1. Download MAMP from [https://www.mamp.info/](https://www.mamp.info/)
2. Install and start servers
3. Navigate to `/Applications/MAMP/htdocs/`

#### Step 2: Setup Project

```bash
# Navigate to web root directory
cd /path/to/webroot

# Create project directory
mkdir autholas-php
cd autholas-php

# Copy the source files
# - autholas.php
# - index.php
```

#### Step 3: Verify PHP Configuration

Create a `phpinfo.php` file:
```php
<?php phpinfo(); ?>
```

Visit `http://localhost/autholas-php/phpinfo.php` and verify:
- PHP version 7.4+
- cURL extension enabled
- JSON extension enabled
- Sessions enabled

### Method 2: Linux Server (Ubuntu/Debian)

#### Step 1: Install PHP and Dependencies

```bash
# Update package list
sudo apt update

# Install PHP and required extensions
sudo apt install php8.1-fpm php8.1-curl php8.1-json php8.1-session php8.1-mbstring

# Install web server (choose one)
# Apache:
sudo apt install apache2 libapache2-mod-php8.1

# Nginx:
sudo apt install nginx
```

#### Step 2: Configure Web Server

**Apache Configuration:**
```bash
# Enable PHP module
sudo a2enmod php8.1

# Create virtual host (optional)
sudo nano /etc/apache2/sites-available/autholas.conf

# Add virtual host configuration:
<VirtualHost *:80>
    ServerName autholas.local
    DocumentRoot /var/www/html/autholas-php
    <Directory /var/www/html/autholas-php>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Enable site and restart Apache
sudo a2ensite autholas.conf
sudo systemctl restart apache2
```

**Nginx Configuration:**
```bash
# Create server block
sudo nano /etc/nginx/sites-available/autholas

# Add configuration:
server {
    listen 80;
    server_name autholas.local;
    root /var/www/html/autholas-php;
    index index.php index.html;

    location / {
        try_files $uri $uri/ =404;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }
}

# Enable site and restart Nginx
sudo ln -s /etc/nginx/sites-available/autholas /etc/nginx/sites-enabled/
sudo systemctl restart nginx
```

#### Step 3: Setup Project Files

```bash
# Create project directory
sudo mkdir -p /var/www/html/autholas-php
cd /var/www/html/autholas-php

# Set proper permissions
sudo chown -R www-data:www-data /var/www/html/autholas-php
sudo chmod -R 755 /var/www/html/autholas-php

# Copy your source files here
```

### Method 3: Shared Hosting / cPanel

#### Step 1: Verify PHP Requirements

1. Login to cPanel
2. Go to **PHP Selector** or **MultiPHP Manager**
3. Ensure PHP 7.4+ is selected
4. Check **PHP Extensions** - enable cURL and JSON if not enabled

#### Step 2: Upload Files

1. Use **File Manager** or FTP client
2. Navigate to `public_html` directory
3. Create folder `autholas-php`
4. Upload `autholas.php` and `index.php`

#### Step 3: Set Permissions

```bash
# Via File Manager or FTP client
# Set directories to 755
# Set PHP files to 644
```

### Method 4: Docker (Advanced)

#### Step 1: Create Dockerfile

```dockerfile
FROM php:8.1-apache

# Install required extensions
RUN docker-php-ext-install curl json session

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy source files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
```

#### Step 2: Create docker-compose.yml

```yaml
version: '3.8'
services:
  autholas-php:
    build: .
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html
    environment:
      - PHP_INI_SCAN_DIR=/usr/local/etc/php/conf.d:/etc/php/conf.d
```

#### Step 3: Run Container

```bash
# Build and run
docker-compose up -d

# Access at http://localhost:8080
```

## Project Structure

```
autholas-php/
├── autholas.php           # Main authentication class
├── index.php             # Example web application
├── .htaccess            # Apache configuration (optional)
├── composer.json        # Composer dependencies (optional)
└── README.md            # This documentation
```

## Configuration

### 1. Set Your API Key

Edit `autholas.php` and replace the API key:

```php
private const API_KEY = 'your_actual_api_key_here';
```

### 2. Optional: Create .htaccess for Clean URLs

```apache
RewriteEngine On

# Redirect to HTTPS (optional)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Security headers
<IfModule mod_headers.c>
    Header always set X-Frame-Options DENY
    Header always set X-Content-Type-Options nosniff
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>

# Hide PHP version
ServerTokens Prod
```

### 3. Optional: Composer Setup

Create `composer.json`:
```json
{
    "name": "your-name/autholas-php",
    "description": "Autholas PHP Authentication System",
    "type": "project",
    "require": {
        "php": ">=7.4"
    },
    "autoload": {
        "classmap": ["autholas.php"]
    }
}
```

Then run:
```bash
composer install
```

## Usage

### Basic Usage

1. **Start your web server**
2. **Navigate to your application** (e.g., `http://localhost/autholas-php/`)
3. **Enter credentials** and login
4. **Session persists** across page refreshes

### Example Implementation

```php
<?php
require_once 'autholas.php';

// Start session
session_start();

// Initialize Autholas
$autholas = new Autholas();

// Check if user is logged in
if ($autholas->loadFromSession() && $autholas->isSessionValid()) {
    // User is authenticated
    echo "Welcome! Your session token: " . $autholas->getSessionToken();
} else {
    // Show login form
    if ($_POST['username'] && $_POST['password']) {
        $result = $autholas->authenticateUser($_POST['username'], $_POST['password']);
        if ($result['success']) {
            $autholas->storeInSession();
            echo "Login successful!";
        } else {
            echo "Login failed: " . $result['message'];
        }
    }
}
?>
```

### Session Management

```php
// Check session status
if ($autholas->isSessionValid()) {
    echo "Session is valid";
} else {
    echo "Session expired or invalid";
}

// Get session information
$token = $autholas->getSessionToken();
$expires = $autholas->getSessionExpires();
$clientType = $autholas->getClientType();

// Logout
$autholas->logout(); // Clears both object and PHP session data
```

## Advanced Configuration

### Custom Error Handling

```php
class CustomAutholas extends Autholas {
    public function customErrorHandler($errorCode, $errorMessage) {
        // Custom logging
        error_log("Auth Error: $errorCode - $errorMessage");
        
        // Send to monitoring service
        // $this->sendToMonitoring($errorCode, $errorMessage);
        
        return $this->handleAuthError($errorCode, $errorMessage);
    }
}
```

### Database Session Storage

```php
// Instead of PHP sessions, store in database
class DatabaseAutholas extends Autholas {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function storeInDatabase($userId, $sessionData) {
        $stmt = $this->pdo->prepare("
            INSERT INTO auth_sessions (user_id, session_token, expires_at, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$userId, $sessionData['session_token'], $sessionData['expires_at']]);
    }
}
```

### API Response Caching

```php
// Add caching for user info
class CachedAutholas extends Autholas {
    private $cache = [];
    
    public function authenticateUserWithCache($username, $password) {
        $cacheKey = md5($username . $password);
        
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }
        
        $result = $this->authenticateUser($username, $password);
        $this->cache[$cacheKey] = $result;
        
        return $result;
    }
}
```

## Troubleshooting

### Common Issues and Solutions

#### 1. cURL Not Working

**Error:** "cURL extension not found"
```bash
# Ubuntu/Debian
sudo apt install php-curl
sudo systemctl restart apache2

# CentOS/RHEL
sudo yum install php-curl
sudo systemctl restart httpd

# Windows (XAMPP)
# Uncomment in php.ini: extension=curl
```

#### 2. Session Issues

**Sessions not persisting:**
```php
// Check session configuration
echo "Session save path: " . session_save_path();
echo "Session ID: " . session_id();

// Ensure session_start() is called before any output
session_start();
```

**Session permission errors:**
```bash
# Fix session directory permissions
sudo chmod -R 777 /var/lib/php/sessions
# or
sudo chown -R www-data:www-data /var/lib/php/sessions
```

#### 3. SSL/HTTPS Issues

**SSL certificate problems:**
```php
// In autholas.php, modify cURL settings (development only!)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
```

**For production, use proper SSL:**
```php
curl_setopt($ch, CURLOPT_CAINFO, '/path/to/cacert.pem');
```

#### 4. API Connection Issues

**Test API connectivity:**
```php
// Test script
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://autholas.nicholasdevs.my.id/api/auth/php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $result\n";
```

#### 5. PHP Version Issues

**Check PHP version:**
```bash
php -v
```

**Update PHP (Ubuntu):**
```bash
sudo add-apt-repository ppa:ondrej/php
sudo apt update
sudo apt install php8.1
```

### Security Considerations

#### 1. Input Validation

```php
// Always validate and sanitize input
function validateInput($input) {
    return filter_var(trim($input), FILTER_SANITIZE_STRING);
}

$username = validateInput($_POST['username'] ?? '');
$password = validateInput($_POST['password'] ?? '');
```

#### 2. CSRF Protection

```php
// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Validate CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
```

#### 3. Rate Limiting

```php
// Simple rate limiting
class RateLimiter {
    public static function checkRateLimit($ip, $maxAttempts = 5, $timeWindow = 300) {
        $key = "rate_limit_$ip";
        $attempts = $_SESSION[$key] ?? 0;
        
        if ($attempts >= $maxAttempts) {
            return false; // Rate limited
        }
        
        $_SESSION[$key] = $attempts + 1;
        return true; // Allowed
    }
}
```

## Error Handling

The system handles various authentication scenarios:

### Authentication Errors
- `INVALID_CREDENTIALS` - Wrong username/password
- `USER_BANNED` - Account suspended
- `SUBSCRIPTION_EXPIRED` - Subscription ended
- `RATE_LIMIT_EXCEEDED` - Too many authentication attempts
- `DEVELOPER_SUSPENDED` - API developer account suspended
- `INVALID_CLIENT_TYPE` - Client configuration error

### Network/System Errors
- `CONNECTION_ERROR` - Network connectivity issues
- `INVALID_RESPONSE` - Server response parsing error
- `HTTP_ERROR` - HTTP status code errors
- `MISSING_CREDENTIALS` - Required fields not provided

## Performance Optimization

### 1. Enable OPcache

```ini
; In php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
```

### 2. Session Optimization

```php
// Configure session for better performance
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1); // HTTPS only
ini_set('session.gc_maxlifetime', 3600); // 1 hour
```

### 3. HTTP Keep-Alive

```php
// Reuse cURL handle for multiple requests
class OptimizedAutholas extends Autholas {
    private static $curlHandle;
    
    private function getCurlHandle() {
        if (!self::$curlHandle) {
            self::$curlHandle = curl_init();
            // Set persistent options
        }
        return self::$curlHandle;
    }
}
```

## Testing

### Manual Testing

1. **Test login with valid credentials**
2. **Test login with invalid credentials**
3. **Test session persistence**
4. **Test logout functionality**
5. **Test error handling**

### Unit Testing with PHPUnit

```bash
# Install PHPUnit
composer require --dev phpunit/phpunit

# Create test
mkdir tests
touch tests/AutholasTest.php
```

Example test:
```php
<?php
use PHPUnit\Framework\TestCase;

class AutholasTest extends TestCase {
    public function testAuthenticateWithValidCredentials() {
        $autholas = new Autholas();
        $result = $autholas->authenticateUser('testuser', 'testpass');
        $this->assertArrayHasKey('success', $result);
    }
}
```

## Deployment

### Production Checklist

- [ ] Set secure API key
- [ ] Enable HTTPS
- [ ] Configure proper error reporting
- [ ] Set up monitoring
- [ ] Configure backups
- [ ] Test all functionality
- [ ] Set proper file permissions
- [ ] Enable security headers

### Environment Variables

```php
// Use environment variables for sensitive data
$apiKey = getenv('AUTHOLAS_API_KEY') ?: 'default_key';
$apiUrl = getenv('AUTHOLAS_API_URL') ?: 'https://autholas.nicholasdevs.my.id/api/auth/php';
```

## Dependencies

- **PHP 7.4+**: Core runtime
- **cURL extension**: HTTP requests to API
- **JSON extension**: Response parsing
- **Session extension**: Session management
- **OpenSSL extension**: HTTPS support

## Compatibility

- **PHP Versions**: 7.4, 8.0, 8.1, 8.2+
- **Web Servers**: Apache, Nginx, IIS, Built-in PHP server
- **Operating Systems**: Linux, Windows, macOS
- **Hosting**: Shared hosting, VPS, dedicated servers, cloud platforms

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Follow PSR-12 coding standards
4. Add tests for new functionality
5. Commit your changes (`git commit -m 'Add amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## License

This project is provided as-is for educational and development purposes. Please respect the terms of service of the Autholas API.

## Support

For issues related to:
- **Autholas API**: Contact Autholas support
- **PHP-specific issues**: Check [PHP Documentation](https://www.php.net/docs.php)
- **Code issues**: Create an issue in this repository

## Changelog

### v1.0.0
- Initial PHP implementation
- Web-based authentication
- Session management
- No HWID restrictions
- Comprehensive error handling
- Responsive web interface

---


**Note**: This PHP client is specifically designed for web applications and does not enforce hardware ID restrictions, making it perfect for multi-device web access.
