-- ============================================
-- SANTA'S WORKSHOP DATABASE SCHEMA
-- Tables only (no database creation)
-- ============================================

USE aimran6;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS daily_hints;
DROP TABLE IF EXISTS story_progress;
DROP TABLE IF EXISTS powerups_inventory;
DROP TABLE IF EXISTS achievements;
DROP TABLE IF EXISTS game_sessions;
DROP TABLE IF EXISTS user_analytics;
DROP TABLE IF EXISTS puzzles;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS story_chapters;

-- ============================================
-- TABLE 1: USERS
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
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_skill_level (skill_level)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 2: PUZZLES
-- ============================================
CREATE TABLE puzzles (
    puzzle_id INT PRIMARY KEY AUTO_INCREMENT,
    size INT NOT NULL DEFAULT 4,
    difficulty_rating INT NOT NULL,
    shuffle_depth INT NOT NULL,
    initial_state JSON NOT NULL,
    solution_state JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_difficulty (difficulty_rating),
    INDEX idx_size (size)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 3: GAME SESSIONS
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
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (puzzle_id) REFERENCES puzzles(puzzle_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_completed (completed),
    INDEX idx_created_at (created_at),
    INDEX idx_user_completed (user_id, completed),
    INDEX idx_score (score)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 4: USER ANALYTICS
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
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_total_wins (total_wins)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 5: ACHIEVEMENTS
-- ============================================
CREATE TABLE achievements (
    achievement_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    achievement_type ENUM(
        'quick_solver',
        'perfect_run',
        'santa_apprentice',
        'master_elf',
        'speed_demon',
        'marathon_runner',
        'perfectionist',
        'untouchable'
    ) NOT NULL,
    unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_achievement (user_id, achievement_type),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 6: POWER-UPS INVENTORY
-- ============================================
CREATE TABLE powerups_inventory (
    inventory_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    powerup_type ENUM('reveal_move', 'swap_tiles', 'freeze_timer') NOT NULL,
    quantity INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_powerup (user_id, powerup_type),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 7: STORY PROGRESS
-- ============================================
CREATE TABLE story_progress (
    progress_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    chapter_number INT NOT NULL,
    unlocked BOOLEAN DEFAULT FALSE,
    unlocked_at TIMESTAMP NULL,
    viewed BOOLEAN DEFAULT FALSE,
    viewed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_chapter (user_id, chapter_number),
    INDEX idx_user_id (user_id),
    INDEX idx_chapter (chapter_number)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 8: STORY CHAPTERS
-- ============================================
CREATE TABLE story_chapters (
    chapter_id INT PRIMARY KEY AUTO_INCREMENT,
    chapter_number INT UNIQUE NOT NULL,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    unlock_requirement INT NOT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_chapter_number (chapter_number)
) ENGINE=InnoDB;

-- ============================================
-- TABLE 9: DAILY HINTS
-- ============================================
CREATE TABLE daily_hints (
    hint_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    hints_remaining INT DEFAULT 3,
    last_reset DATE NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
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

-- Additional indexes
CREATE INDEX idx_sessions_user_date ON game_sessions(user_id, created_at DESC);
CREATE INDEX idx_sessions_completed_score ON game_sessions(completed, score DESC);

-- Show created tables
SHOW TABLES;
