/**
 * Adaptive Difficulty Module
 * Manages difficulty scaling based on player performance
 */

class AdaptiveDifficulty {
    constructor() {
        this.skillLevel = 1;
        this.recentPerformance = [];
        this.maxHistorySize = 10;
    }
    
    /**
     * Record game performance
     */
    recordPerformance(moves, timeSeconds, difficulty, completed) {
        const performance = {
            moves,
            timeSeconds,
            difficulty,
            completed,
            timestamp: Date.now()
        };
        
        this.recentPerformance.push(performance);
        
        // Keep only recent games
        if (this.recentPerformance.length > this.maxHistorySize) {
            this.recentPerformance.shift();
        }
    }
    
    /**
     * Calculate recommended difficulty for next puzzle
     */
    getRecommendedDifficulty() {
        if (this.recentPerformance.length === 0) {
            return 1; // Start easy
        }
        
        const recentGames = this.recentPerformance.slice(-5);
        const completedGames = recentGames.filter(g => g.completed);
        
        // If player hasn't won recently, decrease difficulty
        if (completedGames.length === 0) {
            return Math.max(1, this.skillLevel - 1);
        }
        
        // Calculate average performance
        const avgMoves = completedGames.reduce((sum, g) => sum + g.moves, 0) / completedGames.length;
        const avgTime = completedGames.reduce((sum, g) => sum + g.timeSeconds, 0) / completedGames.length;
        
        // If player is doing well, increase difficulty
        if (completedGames.length >= 3 && avgTime < 120 && avgMoves < 50) {
            return Math.min(10, this.skillLevel + 1);
        }
        
        // If player is struggling, maintain or decrease
        if (avgTime > 300 || avgMoves > 100) {
            return Math.max(1, this.skillLevel - 1);
        }
        
        return this.skillLevel;
    }
    
    /**
     * Get performance feedback message
     */
    getPerformanceFeedback() {
        const completedGames = this.recentPerformance.filter(g => g.completed);
        const winRate = completedGames.length / this.recentPerformance.length;
        
        if (winRate >= 0.8) {
            return "🌟 Excellent! You're a natural!";
        } else if (winRate >= 0.6) {
            return "👍 Great progress! Keep it up!";
        } else if (winRate >= 0.4) {
            return "💪 You're improving! Don't give up!";
        } else {
            return "🎯 Practice makes perfect! You've got this!";
        }
    }
    
    /**
     * Check if player should level up
     */
    shouldLevelUp() {
        const recentGames = this.recentPerformance.slice(-5);
        const allCompleted = recentGames.every(g => g.completed);
        return allCompleted && recentGames.length >= 5;
    }
    
    /**
     * Update skill level
     */
    updateSkillLevel(newLevel) {
        this.skillLevel = newLevel;
    }
}

// Export for use in game.js
window.AdaptiveDifficulty = AdaptiveDifficulty;
