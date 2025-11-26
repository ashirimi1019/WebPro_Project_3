<?php
/**
 * Statistics Handler
 * Handles user stats, achievements, leaderboards, and power-ups
 */

require_once 'config.php';

class StatsHandler {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get user statistics
     */
    public function getUserStats($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.username,
                    u.skill_level,
                    u.total_experience,
                    u.theme_preference,
                    ua.*
                FROM user_analytics ua
                JOIN users u ON ua.user_id = u.user_id
                WHERE ua.user_id = ?
            ");
            $stmt->execute([$userId]);
            $stats = $stmt->fetch();
            
            if (!$stats) {
                return ['success' => false, 'error' => 'Stats not found'];
            }
            
            return ['success' => true, 'stats' => $stats];
            
        } catch (PDOException $e) {
            logError("Get user stats failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to fetch stats'];
        }
    }
    
    /**
     * Get user achievements
     */
    public function getUserAchievements($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT achievement_type, unlocked_at
                FROM achievements
                WHERE user_id = ?
                ORDER BY unlocked_at DESC
            ");
            $stmt->execute([$userId]);
            $achievements = $stmt->fetchAll();
            
            // Get all possible achievements
            $allAchievements = [
                'quick_solver' => [
                    'name' => 'Quick Solver',
                    'description' => 'Complete a puzzle in under 60 seconds',
                    'icon' => '⚡'
                ],
                'perfect_run' => [
                    'name' => 'Perfect Run',
                    'description' => 'Win without using hints or power-ups',
                    'icon' => '💎'
                ],
                'santa_apprentice' => [
                    'name' => 'Santa\'s Apprentice',
                    'description' => 'Win 3 puzzles in a row',
                    'icon' => '🎅'
                ],
                'master_elf' => [
                    'name' => 'Master Elf',
                    'description' => 'Complete 20 total puzzles',
                    'icon' => '🧝'
                ],
                'speed_demon' => [
                    'name' => 'Speed Demon',
                    'description' => 'Complete 4×4 puzzle in under 30 seconds',
                    'icon' => '🔥'
                ],
                'marathon_runner' => [
                    'name' => 'Marathon Runner',
                    'description' => 'Play 50 games',
                    'icon' => '🏃'
                ],
                'perfectionist' => [
                    'name' => 'Perfectionist',
                    'description' => 'Complete with minimum moves',
                    'icon' => '✨'
                ],
                'untouchable' => [
                    'name' => 'Untouchable',
                    'description' => 'Win 10 puzzles in a row',
                    'icon' => '👑'
                ]
            ];
            
            // Mark unlocked achievements
            $unlockedTypes = array_column($achievements, 'achievement_type');
            foreach ($allAchievements as $type => &$achievement) {
                $achievement['unlocked'] = in_array($type, $unlockedTypes);
                $achievement['unlocked_at'] = null;
                
                foreach ($achievements as $unlocked) {
                    if ($unlocked['achievement_type'] === $type) {
                        $achievement['unlocked_at'] = $unlocked['unlocked_at'];
                        break;
                    }
                }
            }
            
            return [
                'success' => true,
                'achievements' => $allAchievements,
                'total_unlocked' => count($unlockedTypes),
                'total_possible' => count($allAchievements)
            ];
            
        } catch (PDOException $e) {
            logError("Get achievements failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to fetch achievements'];
        }
    }
    
    /**
     * Get power-ups inventory
     */
    public function getPowerups($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT powerup_type, quantity
                FROM powerups_inventory
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $powerups = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            
            // Ensure all power-up types exist
            $allPowerups = [
                'reveal_move' => $powerups['reveal_move'] ?? 0,
                'swap_tiles' => $powerups['swap_tiles'] ?? 0,
                'freeze_timer' => $powerups['freeze_timer'] ?? 0
            ];
            
            return ['success' => true, 'powerups' => $allPowerups];
            
        } catch (PDOException $e) {
            logError("Get powerups failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to fetch power-ups'];
        }
    }
    
    /**
     * Use power-up
     */
    public function usePowerup($userId, $powerupType) {
        try {
            $validTypes = ['reveal_move', 'swap_tiles', 'freeze_timer'];
            if (!in_array($powerupType, $validTypes)) {
                return ['success' => false, 'error' => 'Invalid power-up type'];
            }
            
            // Check if user has this power-up
            $stmt = $this->db->prepare("
                SELECT quantity FROM powerups_inventory
                WHERE user_id = ? AND powerup_type = ?
            ");
            $stmt->execute([$userId, $powerupType]);
            $powerup = $stmt->fetch();
            
            if (!$powerup || $powerup['quantity'] <= 0) {
                return ['success' => false, 'error' => 'No power-ups remaining'];
            }
            
            // Decrement quantity
            $stmt = $this->db->prepare("
                UPDATE powerups_inventory
                SET quantity = quantity - 1
                WHERE user_id = ? AND powerup_type = ?
            ");
            $stmt->execute([$userId, $powerupType]);
            
            return [
                'success' => true,
                'message' => 'Power-up used',
                'remaining' => $powerup['quantity'] - 1
            ];
            
        } catch (PDOException $e) {
            logError("Use powerup failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to use power-up'];
        }
    }
    
    /**
     * Get leaderboard
     */
    public function getLeaderboard($limit = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM leaderboard LIMIT ?
            ");
            $stmt->execute([$limit]);
            $leaderboard = $stmt->fetchAll();
            
            return ['success' => true, 'leaderboard' => $leaderboard];
            
        } catch (PDOException $e) {
            logError("Get leaderboard failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to fetch leaderboard'];
        }
    }
    
    /**
     * Get recent game history
     */
    public function getGameHistory($userId, $limit = 20) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    session_id,
                    puzzle_size,
                    difficulty,
                    moves,
                    time_seconds,
                    completed,
                    score,
                    hints_used,
                    powerups_used,
                    created_at
                FROM game_sessions
                WHERE user_id = ?
                ORDER BY created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$userId, $limit]);
            $history = $stmt->fetchAll();
            
            return ['success' => true, 'history' => $history];
            
        } catch (PDOException $e) {
            logError("Get game history failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to fetch history'];
        }
    }
    
    /**
     * Get story progress
     */
    public function getStoryProgress($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    sp.chapter_number,
                    sc.title,
                    sc.content,
                    sc.image_url,
                    sc.unlock_requirement,
                    sp.unlocked,
                    sp.unlocked_at,
                    sp.viewed
                FROM story_progress sp
                JOIN story_chapters sc ON sp.chapter_number = sc.chapter_number
                WHERE sp.user_id = ?
                ORDER BY sp.chapter_number
            ");
            $stmt->execute([$userId]);
            $progress = $stmt->fetchAll();
            
            // Get all chapters (including not yet unlocked)
            $stmt = $this->db->prepare("SELECT * FROM story_chapters ORDER BY chapter_number");
            $stmt->execute();
            $allChapters = $stmt->fetchAll();
            
            // Get current wins to show progress
            $stmt = $this->db->prepare("
                SELECT total_wins FROM user_analytics WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $stats = $stmt->fetch();
            $currentWins = $stats['total_wins'] ?? 0;
            
            return [
                'success' => true,
                'progress' => $progress,
                'all_chapters' => $allChapters,
                'current_wins' => $currentWins
            ];
            
        } catch (PDOException $e) {
            logError("Get story progress failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to fetch story progress'];
        }
    }
    
    /**
     * Get daily hints remaining
     */
    public function getDailyHints($userId) {
        try {
            // Reset hints if needed
            $stmt = $this->db->prepare("CALL reset_daily_hints(?)");
            $stmt->execute([$userId]);
            
            $stmt = $this->db->prepare("
                SELECT hints_remaining, last_reset 
                FROM daily_hints 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $hints = $stmt->fetch();
            
            return [
                'success' => true,
                'hints_remaining' => $hints['hints_remaining'] ?? DAILY_HINTS,
                'last_reset' => $hints['last_reset'] ?? date('Y-m-d')
            ];
            
        } catch (PDOException $e) {
            logError("Get daily hints failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to fetch hints'];
        }
    }
}
