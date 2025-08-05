<?php
/**
 * Autholas PHP Authentication Example
 * 
 * This file demonstrates how to use the Autholas PHP class
 * for web-based authentication with session management
 */

// Start session at the very beginning
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include the Autholas class
require_once 'autholas.php';

// Initialize Autholas
$autholas = new Autholas();

// Check if user is already logged in from previous session
if ($autholas->loadFromSession() && $autholas->isSessionValid()) {
    $isLoggedIn = true;
    $authResult = array(
        'success' => true,
        'session_token' => $autholas->getSessionToken(),
        'client_type' => $autholas->getClientType(),
        'hwid_locked' => $autholas->isHwidLocked(),
        'expires_at' => $autholas->getSessionExpires()
    );
} else {
    $isLoggedIn = false;
    $authResult = null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'login':
                if (isset($_POST['username']) && isset($_POST['password'])) {
                    $username = trim($_POST['username']);
                    $password = trim($_POST['password']);
                    
                    if (!empty($username) && !empty($password)) {
                        $authResult = $autholas->authenticateUser($username, $password);
                        if ($authResult['success']) {
                            $autholas->storeInSession();
                            $isLoggedIn = true;
                        }
                    }
                }
                break;
                
            case 'logout':
                $autholas->logout();
                $isLoggedIn = false;
                $authResult = null;
                break;
        }
    }
}

function startApplication($authResult) {
    echo "<div class='success-box'>";
    echo "<h2>🎉 Authentication Successful!</h2>";
    echo "<div class='session-info'>";
    echo "<p><strong>Session Token:</strong> <code>" . htmlspecialchars(substr($authResult['session_token'], 0, 20)) . "...</code></p>";
    echo "<p><strong>Client Type:</strong> <span class='badge php-badge'>📱 " . strtoupper($authResult['client_type']) . "</span></p>";
    echo "<p><strong>HWID Protection:</strong> <span class='badge " . ($authResult['hwid_locked'] ? 'locked' : 'unlocked') . "'>";
    echo ($authResult['hwid_locked'] ? '🔒 Enabled' : '🔓 Disabled for PHP') . "</span></p>";
    echo "<p><strong>Status:</strong> <span class='badge success'>✅ Authenticated</span></p>";
    if (!empty($authResult['expires_at'])) {
        echo "<p><strong>Session Expires:</strong> " . htmlspecialchars($authResult['expires_at']) . "</p>";
    }
    echo "</div>";
    echo "<p class='description'>Your PHP application is now running with simplified authentication (no device restrictions).</p>";
    echo "</div>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autholas PHP Authentication System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            line-height: 1.6;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        
        .header {
            margin-bottom: 30px;
        }
        
        .header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .php-badge {
            display: inline-block;
            background: #8892B0;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn {
            padding: 12px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
.error-container {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
    padding: 12px;
    margin: 10px 0;
}

.error-title {
    font-weight: bold;
    color: #721c24;
    margin-bottom: 5px;
}

.error-message {
    color: #721c24;
    line-height: 1.4;
}

        .btn:active {
            transform: translateY(0);
        }
        
        .btn-full {
            width: 100%;
        }
        
        .btn-logout {
            background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
        }
        
        .error-box {
            background: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #c62828;
            text-align: left;
        }
        
        .error-box h3 {
            margin-bottom: 5px;
            font-size: 16px;
        }
        
        .error-box p {
            font-size: 14px;
            line-height: 1.4;
            white-space: pre-line;
        }

        
        
        .success-box {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #4caf50;
            text-align: left;
        }
        
        .success-box h2 {
            margin-bottom: 15px;
            color: #2e7d32;
            text-align: center;
        }
        
        .session-info {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        
        .session-info p {
            margin-bottom: 8px;
        }
        
        .description {
            font-style: italic;
            margin-top: 15px;
            text-align: center;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge.php-badge {
            background: #8892B0;
            color: white;
        }
        
        .badge.locked {
            background: #f44336;
            color: white;
        }
        
        .badge.unlocked {
            background: #4caf50;
            color: white;
        }
        
        .badge.success {
            background: #4caf50;
            color: white;
        }
        
        .info-box {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 13px;
            color: #666;
            text-align: left;
        }
        
        .info-box h4 {
            margin-bottom: 10px;
            color: #333;
        }
        
        .info-box ul {
            margin-left: 20px;
        }
        
        .info-box li {
            margin-bottom: 5px;
        }
        
        .php-features {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin-top: 15px;
            border-radius: 8px;
            text-align: left;
        }
        
        .php-features h4 {
            color: #1976d2;
            margin-bottom: 10px;
        }
        
        .php-features ul {
            color: #424242;
            font-size: 13px;
            margin-left: 20px;
        }
        
        .php-features li {
            margin-bottom: 5px;
        }
        
        .application-menu {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            text-align: left;
        }
        
        .application-menu h3 {
            color: #333;
            margin-bottom: 15px;
            text-align: center;
        }
        
        .menu-item {
            padding: 10px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .menu-item:hover {
            background-color: #e9ecef;
        }
        
        .menu-item strong {
            color: #495057;
        }
        
        code {
            background: #f8f9fa;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Autholas PHP</h1>
            <p>Secure Web Authentication System</p>
            <div class="php-badge">PHP CLIENT - NO HWID LOCK</div>
        </div>
        
        <?php if ($isLoggedIn && $authResult && $authResult['success']): ?>
            <!-- User is logged in - Show application -->
            <?php startApplication($authResult); ?>
            
            <div class="application-menu">
                <h3>🚀 Application Menu</h3>
                <div class="menu-item">
                    <strong>Session Status:</strong> 
                    <?php echo $autholas->isSessionValid() ? '<span class="badge success">✅ Valid</span>' : '<span class="badge locked">❌ Expired</span>'; ?>
                </div>
                <div class="menu-item">
                    <strong>Client Type:</strong> <?php echo strtoupper($autholas->getClientType()); ?>
                </div>
                <div class="menu-item">
                    <strong>HWID Locked:</strong> <?php echo $autholas->isHwidLocked() ? 'Yes' : 'No'; ?>
                </div>
                <div class="menu-item">
                    <strong>Session Token:</strong> <code><?php echo htmlspecialchars(substr($autholas->getSessionToken(), 0, 30)); ?>...</code>
                </div>
            </div>
            
            <form method="POST" action="" style="margin-top: 20px;">
                <input type="hidden" name="action" value="logout">
                <button type="submit" class="btn btn-logout btn-full">
                    🚪 Logout
                </button>
            </form>
            
        <?php else: ?>
            <!-- User is not logged in - Show login form -->
            
            <?php if ($authResult && !$authResult['success']): ?>
                <div class="error-box">
                    <h3><?php echo htmlspecialchars($authResult['title']); ?></h3>
                    <p><?php echo htmlspecialchars($authResult['message']); ?></p>
                </div>
                
                <?php if (isset($authResult['error_code'])): ?>
                    <div class="info-box">
                        <h4>💡 Troubleshooting Tips:</h4>
                        <ul>
                            <?php foreach ($autholas->getTroubleshootingTips($authResult['error_code']) as $tip): ?>
                                <li><?php echo htmlspecialchars($tip); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <form method="POST" action="" id="loginForm">
                <input type="hidden" name="action" value="login">
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           placeholder="Enter your username">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required 
                           placeholder="Enter your password">
                </div>
                
                <button type="submit" class="btn btn-full" id="loginBtn">
                    🚀 Login to PHP Application
                </button>
            </form>
        <?php endif; ?>
        
        <div class="php-features">
            <h4>📱 PHP Client Features:</h4>
            <ul>
                <li><strong>No Device Restrictions:</strong> Login from any device/browser</li>
                <li><strong>Session Management:</strong> Secure token-based authentication</li>
                <li><strong>Persistent Login:</strong> Stay logged in across page refreshes</li>
                <li><strong>Multi-Device Support:</strong> Use across multiple browsers/devices</li>
                <li><strong>Web Optimized:</strong> Perfect for web applications</li>
            </ul>
        </div>
        
        <div class="info-box">
            <h4>ℹ️ About This System:</h4>
            <ul>
                <li><strong>PHP-Specific Endpoint:</strong> Optimized for web applications</li>
                <li><strong>No HWID Lock:</strong> Unlike C#/C++/Python/Node.js clients</li>
                <li><strong>Session Security:</strong> Server-side session management</li>
                <li><strong>Error Handling:</strong> Comprehensive error feedback</li>
                <li><strong>Cross-Platform:</strong> Works on any web server with PHP</li>
            </ul>
        </div>
    </div>
    
    <script>
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.innerHTML = '⏳ Authenticating...';
            btn.disabled = true;
        });
        
        // Auto-focus username field
        document.getElementById('username')?.focus();
    </script>
</body>
</html>