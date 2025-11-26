<?php
/**
 * Game Handler
 * Handles puzzle generation, game sessions, and gameplay mechanics
 */

require_once 'config.php';

class GameHandler {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Generate new puzzle based on user's skill level
     */
    public function generatePuzzle($userId, $size = 4) {
        try {
            // Get user's skill level and analytics
            $stmt = $this->db->prepare("
                SELECT u.skill_level, ua.total_wins, ua.average_moves, ua.average_time
                FROM users u
                LEFT JOIN user_analytics ua ON u.user_id = ua.user_id
                WHERE u.user_id = ?
            ");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch();
            
            if (!$userData) {
                return ['success' => false, 'error' => 'User not found'];
            }
            
            // Calculate difficulty based on skill level
            $difficulty = $this->calculateDifficulty($userData);
            
            // Calculate shuffle depth (more shuffles = harder puzzle)
            $shuffleDepth = $this->calculateShuffleDepth($difficulty, $size);
            
            // Generate solved state
            $solvedState = $this->generateSolvedState($size);
            
            // Shuffle to create initial state
            $initialState = $this->shufflePuzzle($solvedState, $shuffleDepth, $size);
            
            // Store puzzle in database
            $stmt = $this->db->prepare("
                INSERT INTO puzzles (size, difficulty_rating, shuffle_depth, initial_state, solution_state)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $size,
                $difficulty,
                $shuffleDepth,
                json_encode($initialState),
                json_encode($solvedState)
            ]);
            
            $puzzleId = $this->db->lastInsertId();
            
            return [
                'success' => true,
                'puzzle' => [
                    'puzzle_id' => $puzzleId,
                    'size' => $size,
                    'difficulty' => $difficulty,
                    'shuffle_depth' => $shuffleDepth,
                    'initial_state' => $initialState,
                    'solution_state' => $solvedState
                ]
            ];
            
        } catch (PDOException $e) {
            logError("Generate puzzle failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to generate puzzle'];
        }
    }
    
    /**
     * Calculate difficulty level (1-10) based on user performance
     */
    private function calculateDifficulty($userData) {
        $skillLevel = $userData['skill_level'] ?? 1;
        $totalWins = $userData['total_wins'] ?? 0;
        
        // Base difficulty on skill level
        $difficulty = min(10, floor($skillLevel / 2) + 1);
        
        // Adjust based on recent performance
        if ($totalWins >= 20) {
            $difficulty = min(10, $difficulty + 1);
        }
        if ($totalWins >= 50) {
            $difficulty = min(10, $difficulty + 1);
        }
        
        return max(1, $difficulty);
    }
    
    /**
     * Calculate shuffle depth based on difficulty
     */
    private function calculateShuffleDepth($difficulty, $size) {
        // Base shuffles: 10 for 4x4
        $baseShuffles = $size * $size * 2;
        
        // Multiply by difficulty factor
        $shuffleDepth = $baseShuffles * $difficulty;
        
        return max(20, min(500, $shuffleDepth));
    }
    
    /**
     * Generate solved state (0 represents empty tile)
     */
    private function generateSolvedState($size) {
        $state = [];
        for ($i = 1; $i < $size * $size; $i++) {
            $state[] = $i;
        }
        $state[] = 0;  // Empty tile at end
        return $state;
    }
    
    /**
     * Shuffle puzzle while maintaining solvability
     */
    private function shufflePuzzle($state, $shuffleDepth, $size) {
        $currentState = $state;
        $emptyPos = array_search(0, $currentState);
        
        for ($i = 0; $i < $shuffleDepth; $i++) {
            $validMoves = $this->getValidMoves($emptyPos, $size);
            $randomMove = $validMoves[array_rand($validMoves)];
            
            // Swap empty tile with random valid neighbor
            $temp = $currentState[$emptyPos];
            $currentState[$emptyPos] = $currentState[$randomMove];
            $currentState[$randomMove] = $temp;
            
            $emptyPos = $randomMove;
        }
        
        return $currentState;
    }
    
    /**
     * Get valid moves for empty tile position
     */
    private function getValidMoves($emptyPos, $size) {
        $row = floor($emptyPos / $size);
        $col = $emptyPos % $size;
        $validMoves = [];
        
        // Up
        if ($row > 0) {
            $validMoves[] = $emptyPos - $size;
        }
        // Down
        if ($row < $size - 1) {
            $validMoves[] = $emptyPos + $size;
        }
        // Left
        if ($col > 0) {
            $validMoves[] = $emptyPos - 1;
        }
        // Right
        if ($col < $size - 1) {
            $validMoves[] = $emptyPos + 1;
        }
        
        return $validMoves;
    }
    
    /**
     * Save game session
     */
    public function saveSession($userId, $puzzleId, $moves, $timeSeconds, $difficulty, $puzzleSize, $completed, $hintsUsed, $powerupsUsed) {
        try {
            // Calculate score
            $score = $this->calculateScore($moves, $timeSeconds, $difficulty, $completed, $hintsUsed, $powerupsUsed);
            
            // Call stored procedure to save session and update analytics
            $stmt = $this->db->prepare("
                CALL save_game_session(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $puzzleId,
                $moves,
                $timeSeconds,
                $difficulty,
                $puzzleSize,
                $completed ? 1 : 0,
                $hintsUsed,
                $powerupsUsed,
                $score
            ]);
            
            // Get updated user data
            $userData = $this->getUserData($userId);
            
            // Check for newly unlocked achievements
            $newAchievements = $this->getRecentAchievements($userId);
            
            // Check for newly unlocked story chapters
            $newStoryChapters = $this->getNewlyUnlockedChapters($userId);
            
            return [
                'success' => true,
                'message' => 'Session saved successfully',
                'score' => $score,
                'user_data' => $userData,
                'new_achievements' => $newAchievements,
                'new_story_chapters' => $newStoryChapters
            ];
            
        } catch (PDOException $e) {
            logError("Save session failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to save session'];
        }
    }
    
    /**
     * Calculate game score
     */
    private function calculateScore($moves, $timeSeconds, $difficulty, $completed, $hintsUsed, $powerupsUsed) {
        if (!$completed) {
            return 0;
        }
        
        // Base score
        $score = 1000 * $difficulty;
        
        // Time bonus (faster = more points)
        $timeBonus = max(0, 300 - $timeSeconds);
        $score += $timeBonus * 2;
        
        // Move efficiency bonus (fewer moves = more points)
        $optimalMoves = 20 * $difficulty;
        if ($moves <= $optimalMoves) {
            $score += 500;
        }
        
        // Hint penalty
        $score -= $hintsUsed * 100;
        
        // Power-up penalty
        $score -= $powerupsUsed * 50;
        
        return max(0, $score);
    }
    
    /**
     * Get user data after session
     */
    private function getUserData($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                u.skill_level,
                u.total_experience,
                ua.total_wins,
                ua.current_streak,
                ua.highest_difficulty_completed
            FROM users u
            LEFT JOIN user_analytics ua ON u.user_id = ua.user_id
            WHERE u.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    /**
     * Get recently unlocked achievements (last 5 minutes)
     */
    private function getRecentAchievements($userId) {
        $stmt = $this->db->prepare("
            SELECT achievement_type, unlocked_at
            FROM achievements
            WHERE user_id = ? AND unlocked_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
            ORDER BY unlocked_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get newly unlocked story chapters
     */
    private function getNewlyUnlockedChapters($userId) {
        $stmt = $this->db->prepare("
            SELECT sp.chapter_number, sc.title, sc.content, sc.image_url
            FROM story_progress sp
            JOIN story_chapters sc ON sp.chapter_number = sc.chapter_number
            WHERE sp.user_id = ? 
            AND sp.unlocked = TRUE 
            AND sp.viewed = FALSE
            ORDER BY sp.chapter_number
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Mark story chapter as viewed
     */
    public function markChapterViewed($userId, $chapterNumber) {
        try {
            $stmt = $this->db->prepare("
                UPDATE story_progress
                SET viewed = TRUE, viewed_at = NOW()
                WHERE user_id = ? AND chapter_number = ?
            ");
            $stmt->execute([$userId, $chapterNumber]);
            
            return ['success' => true, 'message' => 'Chapter marked as viewed'];
        } catch (PDOException $e) {
            logError("Mark chapter viewed failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to mark chapter'];
        }
    }
    
    /**
     * Get hint (if available)
     */
    public function getHint($userId, $currentState, $size) {
        try {
            // Check daily hints
            $stmt = $this->db->prepare("CALL reset_daily_hints(?)");
            $stmt->execute([$userId]);
            
            $stmt = $this->db->prepare("SELECT hints_remaining FROM daily_hints WHERE user_id = ?");
            $stmt->execute([$userId]);
            $hintsData = $stmt->fetch();
            
            if ($hintsData['hints_remaining'] <= 0) {
                return ['success' => false, 'error' => 'No hints remaining today'];
            }
            
            // Calculate hint (find best next move)
            $hint = $this->calculateBestMove($currentState, $size);
            
            // Decrement hints
            $stmt = $this->db->prepare("
                UPDATE daily_hints 
                SET hints_remaining = hints_remaining - 1 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            
            return [
                'success' => true,
                'hint' => $hint,
                'hints_remaining' => $hintsData['hints_remaining'] - 1
            ];
            
        } catch (PDOException $e) {
            logError("Get hint failed", ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Failed to get hint'];
        }
    }
    
    /**
     * Calculate best move (simple heuristic)
     */
    private function calculateBestMove($state, $size) {
        $emptyPos = array_search(0, $state);
        $validMoves = $this->getValidMoves($emptyPos, $size);
        
        // Simple heuristic: move tile that's furthest from its goal position
        $bestMove = $validMoves[0];
        $maxDistance = 0;
        
        foreach ($validMoves as $movePos) {
            $tileValue = $state[$movePos];
            if ($tileValue === 0) continue;
            
            $currentRow = floor($movePos / $size);
            $currentCol = $movePos % $size;
            $goalRow = floor(($tileValue - 1) / $size);
            $goalCol = ($tileValue - 1) % $size;
            
            $distance = abs($currentRow - $goalRow) + abs($currentCol - $goalCol);
            
            if ($distance > $maxDistance) {
                $maxDistance = $distance;
                $bestMove = $movePos;
            }
        }
        
        return $bestMove;
    }
}
