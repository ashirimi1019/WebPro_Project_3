<?php
/**
 * Main API Router
 * Christmas Fifteen Puzzle - Web Pro Project #3
 * 
 * Handles all API requests and routes to appropriate handlers
 */

require_once 'config.php';
require_once 'auth.php';
require_once 'game.php';
require_once 'stats.php';

// Enable CORS for GSU server
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Start session
startSecureSession();

// Set JSON header
setJsonHeader();

// Get request method and action
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Parse JSON input for POST requests
$input = [];
if ($method === 'POST') {
    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true) ?? [];
}

// Initialize response
$response = ['success' => false, 'error' => 'Invalid action'];

try {
    // ============================================
    // AUTHENTICATION ROUTES
    // ============================================
    if ($action === 'register') {
        $auth = new AuthHandler();
        $username = sanitizeInput($input['username'] ?? '');
        $email = sanitizeInput($input['email'] ?? '');
        $password = $input['password'] ?? '';
        
        $response = $auth->register($username, $email, $password);
    }
    
    elseif ($action === 'login') {
        $auth = new AuthHandler();
        $username = sanitizeInput($input['username'] ?? '');
        $password = $input['password'] ?? '';
        
        $response = $auth->login($username, $password);
    }
    
    elseif ($action === 'logout') {
        $auth = new AuthHandler();
        $response = $auth->logout();
    }
    
    elseif ($action === 'check_auth') {
        $auth = new AuthHandler();
        $response = $auth->checkAuth();
    }
    
    elseif ($action === 'update_theme') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $auth = new AuthHandler();
            $theme = sanitizeInput($input['theme'] ?? '');
            $response = $auth->updateTheme(getCurrentUserId(), $theme);
        }
    }
    
    elseif ($action === 'get_profile') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $auth = new AuthHandler();
            $response = $auth->getProfile(getCurrentUserId());
        }
    }
    
    // ============================================
    // GAME ROUTES
    // ============================================
    elseif ($action === 'generate_puzzle') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $game = new GameHandler();
            $size = intval($input['size'] ?? 4);
            $response = $game->generatePuzzle(getCurrentUserId(), $size);
        }
    }
    
    elseif ($action === 'save_session') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $game = new GameHandler();
            $response = $game->saveSession(
                getCurrentUserId(),
                intval($input['puzzle_id'] ?? 0),
                intval($input['moves'] ?? 0),
                intval($input['time_seconds'] ?? 0),
                intval($input['difficulty'] ?? 1),
                intval($input['puzzle_size'] ?? 4),
                boolval($input['completed'] ?? false),
                intval($input['hints_used'] ?? 0),
                intval($input['powerups_used'] ?? 0)
            );
        }
    }
    
    elseif ($action === 'get_hint') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $game = new GameHandler();
            $currentState = $input['current_state'] ?? [];
            $size = intval($input['size'] ?? 4);
            $response = $game->getHint(getCurrentUserId(), $currentState, $size);
        }
    }
    
    elseif ($action === 'mark_chapter_viewed') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $game = new GameHandler();
            $chapterNumber = intval($input['chapter_number'] ?? 0);
            $response = $game->markChapterViewed(getCurrentUserId(), $chapterNumber);
        }
    }
    
    // ============================================
    // STATISTICS ROUTES
    // ============================================
    elseif ($action === 'get_stats') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $stats = new StatsHandler();
            $response = $stats->getUserStats(getCurrentUserId());
        }
    }
    
    elseif ($action === 'get_achievements') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $stats = new StatsHandler();
            $response = $stats->getUserAchievements(getCurrentUserId());
        }
    }
    
    elseif ($action === 'get_powerups') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $stats = new StatsHandler();
            $response = $stats->getPowerups(getCurrentUserId());
        }
    }
    
    elseif ($action === 'use_powerup') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $stats = new StatsHandler();
            $powerupType = sanitizeInput($input['powerup_type'] ?? '');
            $response = $stats->usePowerup(getCurrentUserId(), $powerupType);
        }
    }
    
    elseif ($action === 'get_leaderboard') {
        $stats = new StatsHandler();
        $limit = intval($_GET['limit'] ?? 10);
        $response = $stats->getLeaderboard($limit);
    }
    
    elseif ($action === 'get_game_history') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $stats = new StatsHandler();
            $limit = intval($_GET['limit'] ?? 20);
            $response = $stats->getGameHistory(getCurrentUserId(), $limit);
        }
    }
    
    elseif ($action === 'get_story_progress') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $stats = new StatsHandler();
            $response = $stats->getStoryProgress(getCurrentUserId());
        }
    }
    
    elseif ($action === 'get_daily_hints') {
        if (!isAuthenticated()) {
            $response = ['success' => false, 'error' => 'Not authenticated'];
        } else {
            $stats = new StatsHandler();
            $response = $stats->getDailyHints(getCurrentUserId());
        }
    }
    
    // ============================================
    // HEALTH CHECK
    // ============================================
    elseif ($action === 'health') {
        $response = [
            'success' => true,
            'message' => 'API is running',
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
} catch (Exception $e) {
    logError("API Error", [
        'action' => $action,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    $response = [
        'success' => false,
        'error' => 'An error occurred processing your request',
        'debug' => [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ];
}

// Send response
echo json_encode($response);
