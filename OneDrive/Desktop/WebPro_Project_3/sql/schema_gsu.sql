-- ============================================
-- SANTA'S WORKSHOP DATABASE SCHEMA
-- Christmas Fifteen Puzzle - GSU Server Setup
-- ============================================
-- Modified for GSU server (aimran6)

-- Drop existing database if it exists (CAUTION: This deletes all data)
DROP DATABASE IF EXISTS aimran6_christmas_puzzle;

-- Create the database
CREATE DATABASE aimran6_christmas_puzzle CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE aimran6_christmas_puzzle;

-- ============================================
-- TABLE 1: USERS
-- Stores user authentication and preferences
-- ============================================
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    theme_preference ENUM('snowy', 'workshop', 'reindeer') DEFAULT 'workshop',
    skill_level INT DEFAULT 1,
    total_experience INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_skill_level (skill_level)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 2: PUZZLES
-- Stores puzzle configurations
-- ============================================
CREATE TABLE puzzles (
    puzzle_id INT PRIMARY KEY AUTO_INCREMENT,
    size INT NOT NULL DEFAULT 4,
    difficulty_rating INT NOT NULL,
    shuffle_depth INT NOT NULL,
    initial_state JSON NOT NULL,
    solution_state JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_difficulty (difficulty_rating),
    INDEX idx_size (size)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 3: GAME SESSIONS
-- Tracks every gameplay session
-- ============================================
CREATE TABLE game_sessions (
    session_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    puzzle_id INT NOT NULL,
    moves INT NOT NULL,
    time_seconds INT NOT NULL,
    difficulty INT NOT NULL,
    puzzle_size INT DEFAULT 4,
    completed BOOLEAN DEFAULT FALSE,
    hints_used INT DEFAULT 0,
    powerups_used INT DEFAULT 0,
    score INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign keys
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (puzzle_id) REFERENCES puzzles(puzzle_id) ON DELETE CASCADE,
    
    -- Indexes for analytics queries
    INDEX idx_user_id (user_id),
    INDEX idx_completed (completed),
    INDEX idx_created_at (created_at),
    INDEX idx_user_completed (user_id, completed),
    INDEX idx_score (score)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 4: USER ANALYTICS
-- Aggregate statistics per user
-- ============================================
CREATE TABLE user_analytics (
    analytics_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_games INT DEFAULT 0,
    total_wins INT DEFAULT 0,
    total_losses INT DEFAULT 0,
    best_time INT,
    best_moves INT,
    average_moves DECIMAL(10,2),
    average_time DECIMAL(10,2),
    current_streak INT DEFAULT 0,
    highest_streak INT DEFAULT 0,
    highest_difficulty_completed INT DEFAULT 1,
    last_played TIMESTAMP,
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    
    -- Index
    INDEX idx_user_id (user_id),
    INDEX idx_total_wins (total_wins)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 5: ACHIEVEMENTS
-- Tracks unlocked achievements per user
-- ============================================
CREATE TABLE achievements (
    achievement_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    achievement_type ENUM(
        'quick_solver',      -- Complete puzzle under 60 seconds
        'perfect_run',       -- Win without hints
        'santa_apprentice',  -- 3 wins in a row
        'master_elf',        -- Complete 20 puzzles
        'speed_demon',       -- 4x4 under 30 seconds
        'marathon_runner',   -- Play 50 games
        'perfectionist',     -- Complete with minimum moves
        'untouchable'        -- 10 win streak
    ) NOT NULL,
    unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    
    -- Ensure one achievement type per user
    UNIQUE KEY unique_achievement (user_id, achievement_type),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 6: POWER-UPS INVENTORY
-- Tracks power-up quantities per user
-- ============================================
CREATE TABLE powerups_inventory (
    inventory_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    powerup_type ENUM('reveal_move', 'swap_tiles', 'freeze_timer') NOT NULL,
    quantity INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    
    -- Ensure one row per user per power-up type
    UNIQUE KEY unique_powerup (user_id, powerup_type),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 7: STORY PROGRESS
-- Tracks story chapter unlocks
-- ============================================
CREATE TABLE story_progress (
    progress_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    chapter_number INT NOT NULL,
    unlocked BOOLEAN DEFAULT FALSE,
    unlocked_at TIMESTAMP NULL,
    viewed BOOLEAN DEFAULT FALSE,
    viewed_at TIMESTAMP NULL,
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    
    -- Ensure one chapter per user
    UNIQUE KEY unique_chapter (user_id, chapter_number),
    INDEX idx_user_id (user_id),
    INDEX idx_chapter (chapter_number)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 8: STORY CHAPTERS
-- Stores story chapter content
-- ============================================
CREATE TABLE story_chapters (
    chapter_id INT PRIMARY KEY AUTO_INCREMENT,
    chapter_number INT UNIQUE NOT NULL,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    unlock_requirement INT NOT NULL,  -- Number of wins required
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_chapter_number (chapter_number)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 9: DAILY HINTS
-- Tracks daily hint usage (3 per day)
-- ============================================
CREATE TABLE daily_hints (
    hint_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    hints_remaining INT DEFAULT 3,
    last_reset DATE NOT NULL,
    
    -- Foreign key
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    
    -- One record per user
    UNIQUE KEY unique_user (user_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- ============================================
-- SEED DATA: STORY CHAPTERS
-- ============================================
INSERT INTO story_chapters (chapter_number, title, content, unlock_requirement, image_url) VALUES
(1, 'Welcome to the Workshop', 
'Welcome, new apprentice! Santa has noticed your potential and invited you to join the elite team of puzzle-solving elves. Your mission: master the enchanted tile puzzles to unlock the magic of Christmas preparation. As you progress, the puzzles will become more challenging, but so will your skills!',
0, 'images/story/chapter1.jpg'),

(2, 'The Mysterious Order',
'Strange news from the North Pole! An enormous toy order has arrived, but the warehouse is in disarray. The magical tiles that organize everything have been scrambled. Only by solving these puzzles can you restore order and help Santa prepare for Christmas Eve. The workshop is counting on you!',
3, 'images/story/chapter2.jpg'),

(3, 'The Enchanted Tiles',
'You discover that these aren''t ordinary puzzle tiles – they''re enchanted with ancient Christmas magic! Each time you solve a puzzle, the magic grows stronger. The head elf reveals that mastering these puzzles will unlock special powers to help you work faster and more efficiently.',
7, 'images/story/chapter3.jpg'),

(4, 'The Great Toy Shortage',
'Disaster strikes! A snowstorm has blocked the supply routes, and the workshop is running low on materials. The only way to unlock the emergency supplies is by solving increasingly difficult puzzles. Time is running out, and Christmas hangs in the balance. Can you rise to the challenge?',
12, 'images/story/chapter4.jpg'),

(5, 'The Reindeer''s Secret',
'Rudolph approaches you with a secret: the reindeer have been watching your progress, and they''re impressed. They reveal that solving puzzles at their stable unlocks a special reindeer theme and powerful new abilities. The reindeer magic will help you solve even the most challenging arrangements!',
18, 'images/story/chapter5.jpg'),

(6, 'The Magic Restored',
'Your dedication has paid off! The workshop is now running at full capacity, and the Christmas magic is stronger than ever. Santa himself congratulates you and grants you the title of Master Puzzle Solver. But there''s one final challenge that awaits...',
25, 'images/story/chapter6.jpg'),

(7, 'Christmas is Saved!',
'You''ve done it! Through skill, determination, and puzzle-solving prowess, you''ve saved Christmas. The workshop is perfectly organized, all toys are ready for delivery, and Santa''s sleigh is loaded. As the Northern Lights dance overhead, you''ve earned your place among the legendary elves of the North Pole. Merry Christmas, Master Elf!',
30, 'images/story/chapter7.jpg');

-- ============================================
-- CREATE VIEWS FOR EASY QUERYING
-- ============================================

-- View: User leaderboard
CREATE VIEW leaderboard AS
SELECT 
    u.user_id,
    u.username,
    u.skill_level,
    ua.total_wins,
    ua.best_time,
    ua.best_moves,
    ua.highest_streak,
    ua.highest_difficulty_completed
FROM users u
LEFT JOIN user_analytics ua ON u.user_id = ua.user_id
ORDER BY u.skill_level DESC, ua.total_wins DESC, ua.best_time ASC;

-- View: Recent game activity
CREATE VIEW recent_activity AS
SELECT 
    gs.session_id,
    u.username,
    gs.puzzle_size,
    gs.difficulty,
    gs.moves,
    gs.time_seconds,
    gs.completed,
    gs.score,
    gs.created_at
FROM game_sessions gs
JOIN users u ON gs.user_id = u.user_id
ORDER BY gs.created_at DESC
LIMIT 50;

-- ============================================
-- STORED PROCEDURES
-- ============================================

-- Procedure: Register new user with initial data
DELIMITER //
CREATE PROCEDURE register_user(
    IN p_username VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_password_hash VARCHAR(255)
)
BEGIN
    DECLARE new_user_id INT;
    
    -- Start transaction
    START TRANSACTION;
    
    -- Insert user
    INSERT INTO users (username, email, password_hash)
    VALUES (p_username, p_email, p_password_hash);
    
    SET new_user_id = LAST_INSERT_ID();
    
    -- Initialize analytics
    INSERT INTO user_analytics (user_id)
    VALUES (new_user_id);
    
    -- Initialize power-ups (starting quantities)
    INSERT INTO powerups_inventory (user_id, powerup_type, quantity) VALUES
    (new_user_id, 'reveal_move', 3),
    (new_user_id, 'swap_tiles', 2),
    (new_user_id, 'freeze_timer', 5);
    
    -- Initialize daily hints
    INSERT INTO daily_hints (user_id, last_reset)
    VALUES (new_user_id, CURDATE());
    
    -- Initialize story progress (unlock chapter 1)
    INSERT INTO story_progress (user_id, chapter_number, unlocked, unlocked_at)
    VALUES (new_user_id, 1, TRUE, NOW());
    
    -- Commit transaction
    COMMIT;
    
    SELECT new_user_id AS user_id;
END //
DELIMITER ;

-- Procedure: Save game session and update analytics
DELIMITER //
CREATE PROCEDURE save_game_session(
    IN p_user_id INT,
    IN p_puzzle_id INT,
    IN p_moves INT,
    IN p_time_seconds INT,
    IN p_difficulty INT,
    IN p_puzzle_size INT,
    IN p_completed BOOLEAN,
    IN p_hints_used INT,
    IN p_powerups_used INT,
    IN p_score INT
)
BEGIN
    DECLARE current_streak INT DEFAULT 0;
    DECLARE total_wins_count INT DEFAULT 0;
    
    -- Start transaction
    START TRANSACTION;
    
    -- Insert game session
    INSERT INTO game_sessions (
        user_id, puzzle_id, moves, time_seconds, difficulty, 
        puzzle_size, completed, hints_used, powerups_used, score
    ) VALUES (
        p_user_id, p_puzzle_id, p_moves, p_time_seconds, p_difficulty,
        p_puzzle_size, p_completed, p_hints_used, p_powerups_used, p_score
    );
    
    -- Update user analytics
    IF p_completed THEN
        -- Update wins and streak
        UPDATE user_analytics 
        SET 
            total_games = total_games + 1,
            total_wins = total_wins + 1,
            current_streak = current_streak + 1,
            highest_streak = GREATEST(highest_streak, current_streak + 1),
            best_time = CASE 
                WHEN best_time IS NULL THEN p_time_seconds
                ELSE LEAST(best_time, p_time_seconds)
            END,
            best_moves = CASE 
                WHEN best_moves IS NULL THEN p_moves
                ELSE LEAST(best_moves, p_moves)
            END,
            highest_difficulty_completed = GREATEST(highest_difficulty_completed, p_difficulty),
            last_played = NOW()
        WHERE user_id = p_user_id;
        
        -- Get total wins for skill level calculation
        SELECT total_wins INTO total_wins_count 
        FROM user_analytics 
        WHERE user_id = p_user_id;
        
        -- Update user skill level (1 level per 5 wins)
        UPDATE users 
        SET skill_level = 1 + FLOOR(total_wins_count / 5),
            total_experience = total_wins_count * 100
        WHERE user_id = p_user_id;
        
        -- Check for achievements
        CALL check_achievements(p_user_id);
        
        -- Check for story unlocks
        CALL check_story_unlocks(p_user_id, total_wins_count);
        
    ELSE
        -- Update losses and reset streak
        UPDATE user_analytics 
        SET 
            total_games = total_games + 1,
            total_losses = total_losses + 1,
            current_streak = 0,
            last_played = NOW()
        WHERE user_id = p_user_id;
    END IF;
    
    -- Update average stats
    UPDATE user_analytics ua
    SET 
        average_moves = (
            SELECT AVG(moves) 
            FROM game_sessions 
            WHERE user_id = p_user_id AND completed = TRUE
        ),
        average_time = (
            SELECT AVG(time_seconds) 
            FROM game_sessions 
            WHERE user_id = p_user_id AND completed = TRUE
        )
    WHERE ua.user_id = p_user_id;
    
    -- Commit transaction
    COMMIT;
END //
DELIMITER ;

-- Procedure: Check and award achievements
DELIMITER //
CREATE PROCEDURE check_achievements(IN p_user_id INT)
BEGIN
    DECLARE v_total_wins INT;
    DECLARE v_best_time INT;
    DECLARE v_highest_streak INT;
    DECLARE v_total_games INT;
    DECLARE v_best_moves INT;
    
    -- Get user stats
    SELECT total_wins, best_time, highest_streak, total_games, best_moves
    INTO v_total_wins, v_best_time, v_highest_streak, v_total_games, v_best_moves
    FROM user_analytics
    WHERE user_id = p_user_id;
    
    -- Quick Solver: under 60 seconds
    IF v_best_time IS NOT NULL AND v_best_time < 60 THEN
        INSERT IGNORE INTO achievements (user_id, achievement_type)
        VALUES (p_user_id, 'quick_solver');
    END IF;
    
    -- Speed Demon: 4x4 under 30 seconds
    IF v_best_time IS NOT NULL AND v_best_time < 30 THEN
        INSERT IGNORE INTO achievements (user_id, achievement_type)
        VALUES (p_user_id, 'speed_demon');
    END IF;
    
    -- Santa's Apprentice: 3 wins in a row
    IF v_highest_streak >= 3 THEN
        INSERT IGNORE INTO achievements (user_id, achievement_type)
        VALUES (p_user_id, 'santa_apprentice');
    END IF;
    
    -- Untouchable: 10 win streak
    IF v_highest_streak >= 10 THEN
        INSERT IGNORE INTO achievements (user_id, achievement_type)
        VALUES (p_user_id, 'untouchable');
    END IF;
    
    -- Master Elf: 20 total wins
    IF v_total_wins >= 20 THEN
        INSERT IGNORE INTO achievements (user_id, achievement_type)
        VALUES (p_user_id, 'master_elf');
    END IF;
    
    -- Marathon Runner: 50 total games
    IF v_total_games >= 50 THEN
        INSERT IGNORE INTO achievements (user_id, achievement_type)
        VALUES (p_user_id, 'marathon_runner');
    END IF;
    
    -- Check for Perfect Run (no hints in last session)
    IF EXISTS (
        SELECT 1 FROM game_sessions 
        WHERE user_id = p_user_id 
        AND completed = TRUE 
        AND hints_used = 0 
        AND powerups_used = 0
        ORDER BY created_at DESC LIMIT 1
    ) THEN
        INSERT IGNORE INTO achievements (user_id, achievement_type)
        VALUES (p_user_id, 'perfect_run');
    END IF;
END //
DELIMITER ;

-- Procedure: Check and unlock story chapters
DELIMITER //
CREATE PROCEDURE check_story_unlocks(IN p_user_id INT, IN p_total_wins INT)
BEGIN
    -- Unlock chapters based on win count
    UPDATE story_progress sp
    JOIN story_chapters sc ON sp.chapter_number = sc.chapter_number
    SET sp.unlocked = TRUE, sp.unlocked_at = NOW()
    WHERE sp.user_id = p_user_id 
    AND sp.unlocked = FALSE 
    AND sc.unlock_requirement <= p_total_wins;
END //
DELIMITER ;

-- Procedure: Reset daily hints
DELIMITER //
CREATE PROCEDURE reset_daily_hints(IN p_user_id INT)
BEGIN
    UPDATE daily_hints
    SET hints_remaining = 3, last_reset = CURDATE()
    WHERE user_id = p_user_id AND last_reset < CURDATE();
END //
DELIMITER ;

-- ============================================
-- TRIGGERS
-- ============================================

-- Trigger: Award power-ups on achievement unlock
DELIMITER //
CREATE TRIGGER award_powerup_on_achievement
AFTER INSERT ON achievements
FOR EACH ROW
BEGIN
    -- Award 1 of each power-up when achievement is unlocked
    UPDATE powerups_inventory
    SET quantity = quantity + 1
    WHERE user_id = NEW.user_id;
END //
DELIMITER ;

-- ============================================
-- INDEXES FOR PERFORMANCE
-- ============================================
-- Additional composite indexes for common queries

CREATE INDEX idx_sessions_user_date ON game_sessions(user_id, created_at DESC);
CREATE INDEX idx_sessions_completed_score ON game_sessions(completed, score DESC);

-- ============================================
-- VERIFICATION QUERIES
-- ============================================
-- Run these to verify the database is set up correctly

-- Show all tables
SHOW TABLES;

-- Show table structures
DESCRIBE users;
DESCRIBE puzzles;
DESCRIBE game_sessions;
DESCRIBE user_analytics;
DESCRIBE achievements;
DESCRIBE powerups_inventory;
DESCRIBE story_progress;
DESCRIBE story_chapters;
DESCRIBE daily_hints;

-- Show stored procedures
SHOW PROCEDURE STATUS WHERE Db = 'aimran6_christmas_puzzle';

-- Show triggers
SHOW TRIGGERS;

-- Show views
SHOW FULL TABLES WHERE Table_type = 'VIEW';

-- ============================================
-- END OF SCHEMA
-- ============================================
