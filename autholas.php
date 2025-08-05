<?php
/**
 * Autholas PHP Authentication Class
 * 
 * A comprehensive PHP implementation for Autholas authentication service
 * Optimized for web applications with no HWID restrictions
 */

class Autholas {
    
    // API Configuration
    private const API_KEY = 'YOUR_API_KEY_HERE';
    private const API_URL = 'https://autholas.nicholasdevs.xyz/api/auth/php';
    
    // Session properties
    public $sessionToken = '';
    public $sessionExpires = '';
    public $isAuthenticated = false;
    public $clientType = 'php';
    public $hwidLocked = false;
    
    /**
     * Handle authentication errors with user-friendly messages
     */
    private function handleAuthError($errorCode, $errorMessage) {
        $errorMessages = array(
            'INVALID_CREDENTIALS' => array(
                'title' => 'Login Failed',
                'message' => 'Username or password is incorrect.<br>Please double-check your credentials and try again.'
            ),
            'USER_BANNED' => array(
                'title' => 'Account Banned',
                'message' => 'Your account has been suspended.<br>Please contact support for assistance.'
            ),
            'SUBSCRIPTION_EXPIRED' => array(
                'title' => 'Subscription Expired',
                'message' => 'Your subscription has ended.<br>Please renew your subscription to continue.'
            ),
            'INVALID_API_KEY' => array(
                'title' => 'Service Error',
                'message' => 'Authentication service unavailable.<br>Please try again later or contact support.'
            ),
            'RATE_LIMIT_EXCEEDED' => array(
                'title' => 'Too Many Attempts',
                'message' => 'You have exceeded the maximum number of login attempts.<br>Please wait a few minutes before trying again.'
            ),
            'DEVELOPER_SUSPENDED' => array(
                'title' => 'Service Unavailable',
                'message' => 'Authentication service is temporarily unavailable.<br>Please contact support for assistance.'
            ),
            'SERVICE_ERROR' => array(
                'title' => 'Service Error',
                'message' => 'Authentication service is temporarily unavailable.<br>Please try again later or contact support.'
            ),
            'INVALID_CLIENT_TYPE' => array(
                'title' => 'Client Error',
                'message' => 'Invalid client configuration detected.<br>Please contact support for assistance.'
            ),
            'ACCOUNT_LOCKED' => array(
                'title' => 'Account Locked',
                'message' => 'Your account has been temporarily locked due to security reasons.<br>Please contact support to unlock your account.'
            ),
            'EMAIL_NOT_VERIFIED' => array(
                'title' => 'Email Verification Required',
                'message' => 'Please verify your email address before logging in.<br>Check your inbox for the verification link.'
            ),
            'MAINTENANCE_MODE' => array(
                'title' => 'System Maintenance',
                'message' => 'The system is currently under maintenance.<br>Please try again later.'
            ),
            'INVALID_TOKEN' => array(
                'title' => 'Session Expired',
                'message' => 'Your session has expired or is invalid.<br>Please log in again.'
            ),
            'ACCOUNT_INACTIVE' => array(
                'title' => 'Account Inactive',
                'message' => 'Your account is currently inactive.<br>Please contact support to activate your account.'
            ),
            'PASSWORD_EXPIRED' => array(
                'title' => 'Password Expired',
                'message' => 'Your password has expired.<br>Please reset your password to continue.'
            ),
            'GEOLOCATION_BLOCKED' => array(
                'title' => 'Location Restricted',
                'message' => 'Access from your current location is not permitted.<br>Please contact support if you believe this is an error.'
            ),
            'DEVICE_NOT_RECOGNIZED' => array(
                'title' => 'Unrecognized Device',
                'message' => 'Login from an unrecognized device detected.<br>Please verify your identity or use a trusted device.'
            ),
            'TWO_FACTOR_REQUIRED' => array(
                'title' => 'Two-Factor Authentication Required',
                'message' => 'Please complete two-factor authentication.<br>Enter the code from your authenticator app.'
            ),
            'NETWORK_ERROR' => array(
                'title' => 'Connection Error',
                'message' => 'Unable to connect to authentication server.<br>Please check your internet connection and try again.'
            ),
            'INVALID_REQUEST' => array(
                'title' => 'Invalid Request',
                'message' => 'The authentication request is malformed.<br>Please refresh the page and try again.'
            ),
            'SERVER_OVERLOADED' => array(
                'title' => 'Server Busy',
                'message' => 'The server is currently overloaded.<br>Please wait a moment and try again.'
            )
        );

        // Check if the error code exists in our predefined messages
        if (isset($errorMessages[$errorCode])) {
            $error = $errorMessages[$errorCode];
            return array(
                'title' => $error['title'],
                'message' => $error['message']
            );
        } else {
            // Fallback for unknown error codes
            return array(
                'title' => 'Authentication Error',
                'message' => !empty($errorMessage) ? htmlspecialchars($errorMessage) : 'An unknown error occurred.<br>Please try again or contact support.'
            );
        }
    }
    
    /**
     * Authenticate user with username and password
     * No HWID required for PHP client
     */
    public function authenticateUser($username, $password) {
        // Validate input
        if (empty($username) || empty($password)) {
            return array(
                'success' => false,
                'error' => 'Username and password are required',
                'error_code' => 'MISSING_CREDENTIALS',
                'title' => 'Missing Information',
                'message' => 'Please provide both username and password.'
            );
        }
        
        // PHP client payload - NO HWID required
        $payload = array(
            'api_key' => self::API_KEY,
            'username' => $username,
            'password' => $password,
            'client_type' => 'php' // Identify as PHP client
        );
        
        $ch = curl_init();
        
        curl_setopt_array($ch, array(
            CURLOPT_URL => self::API_URL,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'User-Agent: Autholas-PHP-Client/2.0',
                'Accept: application/json'
            ),
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 3
        ));
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        
        curl_close($ch);
        
        // Check for cURL errors
        if ($result === FALSE || !empty($curlError)) {
            return array(
                'success' => false, 
                'error' => 'Connection error: ' . $curlError,
                'error_code' => 'CONNECTION_ERROR',
                'title' => 'Connection Error',
                'message' => 'Unable to reach authentication server.\nPlease check your internet connection and try again.'
            );
        }
        
        // Check HTTP status code
        if ($httpCode >= 400) {
            $errorResponse = json_decode($result, true);
            $errorMessage = isset($errorResponse['error']) ? $errorResponse['error'] : 'HTTP Error ' . $httpCode;
            $errorCode = isset($errorResponse['error_code']) ? $errorResponse['error_code'] : 'HTTP_ERROR';
            
            $errorInfo = $this->handleAuthError($errorCode, $errorMessage);
            return array(
                'success' => false, 
                'error' => $errorMessage, 
                'error_code' => $errorCode,
                'title' => $errorInfo['title'],
                'message' => $errorInfo['message']
            );
        }
        
        $response = json_decode($result, true);
        
        // Check for JSON decode errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array(
                'success' => false,
                'error' => 'Invalid server response format',
                'error_code' => 'INVALID_RESPONSE',
                'title' => 'Server Error',
                'message' => 'Server returned invalid response. Please try again later.'
            );
        }
        
        if (isset($response['success']) && $response['success']) {
            // Store authentication data
            $this->sessionToken = $response['session_token'] ?? '';
            $this->sessionExpires = $response['expires_at'] ?? '';
            $this->isAuthenticated = true;
            $this->clientType = $response['client_type'] ?? 'php';
            $this->hwidLocked = $response['hwid_locked'] ?? false;
            
            return array(
                'success' => true, 
                'session_token' => $this->sessionToken,
                'user' => $response['user'] ?? array(),
                'expires_at' => $this->sessionExpires,
                'client_type' => $this->clientType,
                'hwid_locked' => $this->hwidLocked,
                'message' => $response['message'] ?? 'Authentication successful'
            );
        } else {
            $errorCode = isset($response['error_code']) ? $response['error_code'] : 'UNKNOWN';
            $errorMessage = isset($response['error']) ? $response['error'] : 'Unknown error';
            
            $errorInfo = $this->handleAuthError($errorCode, $errorMessage);
            return array(
                'success' => false, 
                'error' => $errorMessage, 
                'error_code' => $errorCode,
                'title' => $errorInfo['title'],
                'message' => $errorInfo['message']
            );
        }
    }
    
    /**
     * Check if current session is still valid
     */
    public function isSessionValid() {
        if (!$this->isAuthenticated || empty($this->sessionToken)) {
            return false;
        }
        
        if (empty($this->sessionExpires)) {
            return true; // No expiration set
        }
        
        $expireTime = strtotime($this->sessionExpires);
        return $expireTime !== false && time() < $expireTime;
    }
    
    /**
     * Get current session token
     */
    public function getSessionToken() {
        return $this->sessionToken;
    }
    
    /**
     * Get session expiration time
     */
    public function getSessionExpires() {
        return $this->sessionExpires;
    }
    
    /**
     * Check if user is authenticated
     */
    public function isAuthenticated() {
        return $this->isAuthenticated;
    }
    
    /**
     * Get client type
     */
    public function getClientType() {
        return $this->clientType;
    }
    
    /**
     * Check if HWID locking is enabled
     */
    public function isHwidLocked() {
        return $this->hwidLocked;
    }
    
    /**
     * Logout and clear session data
     */
    public function logout() {
        $this->sessionToken = '';
        $this->sessionExpires = '';
        $this->isAuthenticated = false;
        $this->clientType = 'php';
        $this->hwidLocked = false;
        
        // Clear PHP session data if session is active
        if (session_status() === PHP_SESSION_ACTIVE) {
            unset($_SESSION['autholas_token']);
            unset($_SESSION['autholas_authenticated']);
            unset($_SESSION['autholas_expires']);
            unset($_SESSION['autholas_client_type']);
            unset($_SESSION['autholas_hwid_locked']);
        }
    }
    
    /**
     * Store session data in PHP session
     */
    public function storeInSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $_SESSION['autholas_token'] = $this->sessionToken;
        $_SESSION['autholas_authenticated'] = $this->isAuthenticated;
        $_SESSION['autholas_expires'] = $this->sessionExpires;
        $_SESSION['autholas_client_type'] = $this->clientType;
        $_SESSION['autholas_hwid_locked'] = $this->hwidLocked;
    }
    
    /**
     * Load session data from PHP session
     */
    public function loadFromSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['autholas_authenticated']) && $_SESSION['autholas_authenticated']) {
            $this->sessionToken = $_SESSION['autholas_token'] ?? '';
            $this->isAuthenticated = $_SESSION['autholas_authenticated'] ?? false;
            $this->sessionExpires = $_SESSION['autholas_expires'] ?? '';
            $this->clientType = $_SESSION['autholas_client_type'] ?? 'php';
            $this->hwidLocked = $_SESSION['autholas_hwid_locked'] ?? false;
            
            return $this->isSessionValid();
        }
        
        return false;
    }
    
    /**
     * Get troubleshooting tips for specific error codes
     */
    public function getTroubleshootingTips($errorCode) {
        $tips = array(
            'INVALID_CREDENTIALS' => array(
                'Double-check your username and password spelling',
                'Make sure Caps Lock is not enabled',
                'Contact your administrator if you\'ve forgotten your credentials'
            ),
            'USER_BANNED' => array(
                'Contact support to appeal your ban',
                'Check if your subscription is still active'
            ),
            'SUBSCRIPTION_EXPIRED' => array(
                'Renew your subscription to regain access',
                'Contact billing support for payment issues'
            ),
            'RATE_LIMIT_EXCEEDED' => array(
                'Wait a few minutes before trying again',
                'Too many failed login attempts detected'
            ),
            'INVALID_API_KEY' => array(
                'Check if the API key is correct',
                'Contact support for API issues'
            ),
            'DEVELOPER_SUSPENDED' => array(
                'Contact support for account issues'
            ),
            'INVALID_CLIENT_TYPE' => array(
                'Client configuration error',
                'Contact support for technical assistance'
            ),
            'CONNECTION_ERROR' => array(
                'Check your internet connection',
                'Verify the server is accessible',
                'Check firewall settings'
            )
        );
        
        return isset($tips[$errorCode]) ? $tips[$errorCode] : array(
            'Check your internet connection',
            'Verify your credentials are correct',
            'Contact support if the problem persists'
        );
    }
}
?>