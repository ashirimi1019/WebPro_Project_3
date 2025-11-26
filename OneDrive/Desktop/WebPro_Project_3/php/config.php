<?php
/**
 * Database Configuration
 * Christmas Fifteen Puzzle - Web Pro Project #3
 * 
 * IMPORTANT: Update these values with your MySQL credentials
 */

// Database connection constants
define('DB_HOST', 'localhost');
define('DB_USER', 'aimran6');
define('DB_PASS', 'aimran6');
define('DB_NAME', 'aimran6');
define('DB_CHARSET', 'utf8mb4');

// Session configuration
define('SESSION_LIFETIME', 86400);  // 24 hours in seconds

// Security settings
define('BCRYPT_COST', 10);  // Password hashing cost
define('MAX_LOGIN_ATTEMPTS', 5);

// Application settings
define('DAILY_HINTS', 3);
define('STARTING_POWERUPS', [
    'reveal_move' => 3,
    'swap_tiles' => 2,
    'freeze_timer' => 5
]);

/**
 * Database connection class (Singleton pattern)
 */
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die(json_encode([
                'success' => false,
                'error' => 'Database connection failed. Please check configuration.'
            ]));
        }
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get PDO connection
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * Start secure session
 */
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0);  // Set to 1 if using HTTPS
        ini_set('session.cookie_samesite', 'Lax');
        session_start();
    }
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    startSecureSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    startSecureSession();
    return $_SESSION['user_id'] ?? null;
}

/**
 * Set JSON header
 */
function setJsonHeader() {
    header('Content-Type: application/json');
}

/**
 * Send JSON response
 */
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    setJsonHeader();
    echo json_encode($data);
    exit;
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => BCRYPT_COST]);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Log error to file
 */
function logError($message, $context = []) {
    $logMessage = date('[Y-m-d H:i:s] ') . $message;
    if (!empty($context)) {
        $logMessage .= ' | Context: ' . json_encode($context);
    }
    error_log($logMessage . PHP_EOL, 3, __DIR__ . '/error.log');
}

/**
 * Enable CORS (if needed for development)
 */
function enableCORS() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
}

// Uncomment for development with separate frontend
// enableCORS();
