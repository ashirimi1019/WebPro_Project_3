<?php
/**
 * Authentication Handler
 * Handles user registration, login, logout, and session management
 */

require_once 'config.php';

class AuthHandler {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Register new user
     */
    public function register($username, $email, $password) {
        try {
            // Validate inputs
            if (strlen($username) < 3 || strlen($username) > 50) {
                return ['success' => false, 'error' => 'Username must be 3-50 characters'];
            }
            
            if (!validateEmail($email)) {
                return ['success' => false, 'error' => 'Invalid email format'];
            }
            
            if (strlen($password) < 6) {
                return ['success' => false, 'error' => 'Password must be at least 6 characters'];
            }
            
            // Check if username exists
            $stmt = $this->db->prepare("SELECT user_id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                return ['success' => false, 'error' => 'Username already exists'];
            }
            
            // Check if email exists
            $stmt = $this->db->prepare("SELECT user_id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'error' => 'Email already registered'];
            }
            
            // Hash password
            $passwordHash = hashPassword($password);
            
            // Start transaction
            $this->db->beginTransaction();
            
            try {
                // Insert user
                $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $passwordHash]);
                $userId = $this->db->lastInsertId();
                
                // Initialize analytics
                $stmt = $this->db->prepare("INSERT INTO user_analytics (user_id) VALUES (?)");
                $stmt->execute([$userId]);
                
                // Initialize power-ups
                $stmt = $this->db->prepare("INSERT INTO powerups_inventory (user_id, powerup_type, quantity) VALUES (?, 'reveal_move', 3), (?, 'swap_tiles', 2), (?, 'freeze_timer', 5)");
                $stmt->execute([$userId, $userId, $userId]);
                
                // Initialize daily hints
                $stmt = $this->db->prepare("INSERT INTO daily_hints (user_id, last_reset) VALUES (?, CURDATE())");
                $stmt->execute([$userId]);
                
                // Initialize story progress (unlock chapter 1)
                $stmt = $this->db->prepare("INSERT INTO story_progress (user_id, chapter_number, unlocked, unlocked_at) VALUES (?, 1, TRUE, NOW())");
                $stmt->execute([$userId]);
                
                $this->db->commit();
                
                return [
                    'success' => true,
                    'message' => 'Registration successful',
                    'user_id' => $userId
                ];
                
            } catch (PDOException $e) {
                $this->db->rollBack();
                throw $e;
            }
            
        } catch (PDOException $e) {
            logError("Registration failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Login user
     */
    public function login($username, $password) {
        try {
            // Get user from database
            $stmt = $this->db->prepare("
                SELECT user_id, username, email, password_hash, skill_level, theme_preference
                FROM users 
                WHERE username = ? OR email = ?
            ");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'error' => 'Invalid credentials'];
            }
            
            // Verify password
            if (!verifyPassword($password, $user['password_hash'])) {
                return ['success' => false, 'error' => 'Invalid credentials'];
            }
            
            // Update last login
            $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
            $stmt->execute([$user['user_id']]);
            
            // Start session
            startSecureSession();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['skill_level'] = $user['skill_level'];
            $_SESSION['theme'] = $user['theme_preference'];
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'skill_level' => $user['skill_level'],
                    'theme' => $user['theme_preference']
                ]
            ];
            
        } catch (PDOException $e) {
            logError("Login failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Login failed'];
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        startSecureSession();
        session_unset();
        session_destroy();
        
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    /**
     * Check if user is authenticated
     */
    public function checkAuth() {
        if (!isAuthenticated()) {
            return ['success' => false, 'authenticated' => false];
        }
        
        return [
            'success' => true,
            'authenticated' => true,
            'user' => [
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'email' => $_SESSION['email'],
                'skill_level' => $_SESSION['skill_level'],
                'theme' => $_SESSION['theme']
            ]
        ];
    }
    
    /**
     * Update user theme preference
     */
    public function updateTheme($userId, $theme) {
        try {
            $validThemes = ['snowy', 'workshop', 'reindeer'];
            if (!in_array($theme, $validThemes)) {
                return ['success' => false, 'error' => 'Invalid theme'];
            }
            
            $stmt = $this->db->prepare("UPDATE users SET theme_preference = ? WHERE user_id = ?");
            $stmt->execute([$theme, $userId]);
            
            // Update session
            $_SESSION['theme'] = $theme;
            
            return ['success' => true, 'message' => 'Theme updated'];
        } catch (PDOException $e) {
            logError("Theme update failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Theme update failed'];
        }
    }
    
    /**
     * Get user profile
     */
    public function getProfile($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.user_id,
                    u.username,
                    u.email,
                    u.skill_level,
                    u.total_experience,
                    u.theme_preference,
                    u.created_at,
                    ua.total_games,
                    ua.total_wins,
                    ua.total_losses,
                    ua.best_time,
                    ua.best_moves,
                    ua.average_moves,
                    ua.average_time,
                    ua.current_streak,
                    ua.highest_streak,
                    ua.highest_difficulty_completed
                FROM users u
                LEFT JOIN user_analytics ua ON u.user_id = ua.user_id
                WHERE u.user_id = ?
            ");
            $stmt->execute([$userId]);
            $profile = $stmt->fetch();
            
            if (!$profile) {
                return ['success' => false, 'error' => 'User not found'];
            }
            
            return ['success' => true, 'profile' => $profile];
            
        } catch (PDOException $e) {
            logError("Get profile failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to fetch profile'];
        }
    }
}
