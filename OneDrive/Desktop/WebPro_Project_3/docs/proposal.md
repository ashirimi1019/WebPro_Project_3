# 🎄 Christmas Fifteen Puzzle - Project Proposal
## Web Programming Final Project #03 (Holiday Edition 2025)

**Student:** [Your Name]  
**Course:** Web Programming  
**Instructor:** [Instructor Name]  
**Due Date:** November 28, 2025  

---

## 📋 Executive Summary

This project implements **Santa's Workshop: The Adaptive Christmas Fifteen Puzzle**, a holiday-themed web application that combines the classic fifteen puzzle with modern adaptive gameplay mechanics. The system intelligently adjusts difficulty based on player performance, creating an engaging and personalized gaming experience while teaching spatial reasoning and problem-solving skills.

---

## 🎯 Project Selection

### Chosen Version: **Santa's Workshop (Adaptive Difficulty)**

**Rationale:**
- Provides a sophisticated single-player experience without multiplayer complexity
- Adaptive algorithms are academically interesting and demonstrate programming skill
- Easier to test and debug than multiplayer or AI-prediction systems
- Naturally fits the holiday theme with Santa's workshop progression
- Scalable difficulty system showcases database-driven dynamic content

---

## 🎨 Theme & User Experience

### Visual Theme: **Santa's Workshop**
The application features a fully immersive Christmas environment:
- **Tile Design:** Each puzzle piece displays festive imagery (ornaments, candy canes, reindeer, snowflakes, presents, etc.)
- **Background:** Animated snowfall with Santa's workshop backdrop
- **Audio:** Festive background music that intensifies as time runs low, with sound effects for tile movements and victory
- **Animations:** Smooth tile sliding transitions, confetti bursts on completion, glowing effects for strategic hints

### Adaptive Difficulty Progression
Players begin at an easy difficulty level. As they demonstrate skill through:
- Completing puzzles quickly
- Using fewer moves
- Winning consistently

The system automatically:
- Increases shuffle complexity (more randomization steps)
- Optionally expands grid size (4×4 → 5×5 → 6×6)
- Tracks a "Workshop Skill Level" stored in the database
- Provides feedback on performance trends

---

## 🔧 Core Technical Features

### 1. Adaptive Gameplay Experience ✅
- **Performance Tracking:** Real-time monitoring of moves, time, and success rate
- **Dynamic Difficulty Scaling:** Algorithm adjusts shuffle depth based on player's skill rating
- **Skill Progression System:** Players advance through "Workshop Levels" (Apprentice → Toymaker → Master Elf)
- **Intelligent Scaling:** System stores historical data to determine appropriate challenge level

### 2. Dynamic Puzzle Mechanics ✅
- **Solvable Shuffling:** Uses inversion parity algorithm to ensure every puzzle is winnable
- **Strategic Highlighting:** Visual indicators show which tiles are movable
- **Smooth Animations:** CSS transitions for professional tile sliding effects
- **Variable Grid Support:** Core 4×4 with expansion capability to 6×6 and 8×8

### 3. Immersive Audio-Visual Experience ✅
- **Dynamic Background Music:** Festive soundtrack that adjusts tempo based on remaining time
- **Sound Effects Library:**
  - Tile click sound
  - Move completion sound
  - Power-up activation sound
  - Victory jingle
- **Themed Imagery:** 15 unique Christmas-themed tile images (NO numbers)
- **Atmospheric Effects:** CSS-based snowfall animation, particle effects, glow overlays

### 4. Celebratory Completion System ✅
- **Victory Animation Sequence:**
  - Confetti particle explosion
  - Screen glow effect
  - Holiday jingle playback
  - "Workshop Complete!" overlay
- **Achievement Display:** Shows completion time, move count, and difficulty level
- **Reward Unlock:** Grants experience points and potential theme/power-up unlocks
- **Database Recording:** Permanently stores completion data for statistics

### 5. Comprehensive Progress Tracking ✅
**Tracked Metrics (All stored in database):**
- Total games played
- Win/loss ratio
- Average completion time
- Average move count
- Best time per difficulty level
- Hints used
- Power-ups consumed
- Current skill level
- Achievement progress

**Statistics Dashboard:**
- Profile page displays all metrics with visual charts
- Performance trend graphs
- Achievement showcase
- Leaderboard position (optional)

### 6. Strategic Assistance Features ✅
- **Hint System:**
  - Limited daily hints (3 per day, regenerates at midnight)
  - Shows preview of correct tile placement
  - Tracks hint usage in database
- **Power-ups:**
  - "Reveal Best Move" - Highlights optimal next tile
  - "Swap Two Tiles" - Corrects a mistake
  - "Freeze Timer" - Pauses countdown for 10 seconds
- **Solution Preview:** Shows solved board layout (consumes hint)

---

## 💾 Database Implementation

### Database Architecture
All database operations will be performed via **command-line MySQL** (no GUI tools), demonstrating proper SQL knowledge.

### Table Structures

#### **1. Users Table**
```sql
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    theme_preference ENUM('snowy', 'workshop', 'reindeer') DEFAULT 'workshop',
    skill_level INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);
```

#### **2. Puzzles Table**
```sql
CREATE TABLE puzzles (
    puzzle_id INT PRIMARY KEY AUTO_INCREMENT,
    size INT NOT NULL DEFAULT 4,
    difficulty_rating INT NOT NULL,
    shuffle_depth INT NOT NULL,
    initial_state JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_difficulty (difficulty_rating)
);
```

#### **3. Game Sessions Table**
```sql
CREATE TABLE game_sessions (
    session_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    puzzle_id INT NOT NULL,
    moves INT NOT NULL,
    time_seconds INT NOT NULL,
    difficulty INT NOT NULL,
    completed BOOLEAN DEFAULT FALSE,
    hints_used INT DEFAULT 0,
    powerups_used INT DEFAULT 0,
    score INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (puzzle_id) REFERENCES puzzles(puzzle_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_completed (completed),
    INDEX idx_created_at (created_at)
);
```

#### **4. User Analytics Table**
```sql
CREATE TABLE user_analytics (
    analytics_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_games INT DEFAULT 0,
    total_wins INT DEFAULT 0,
    total_losses INT DEFAULT 0,
    best_time INT,
    average_moves DECIMAL(10,2),
    average_time DECIMAL(10,2),
    current_streak INT DEFAULT 0,
    highest_difficulty_completed INT DEFAULT 1,
    last_played TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);
```

#### **5. Achievements Table**
```sql
CREATE TABLE achievements (
    achievement_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    achievement_type ENUM('quick_solver', 'perfect_run', 'santa_apprentice', 'master_elf', 'speed_demon') NOT NULL,
    unlocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_achievement (user_id, achievement_type),
    INDEX idx_user_id (user_id)
);
```

#### **6. Power-ups Inventory Table**
```sql
CREATE TABLE powerups_inventory (
    inventory_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    powerup_type ENUM('reveal_move', 'swap_tiles', 'freeze_timer') NOT NULL,
    quantity INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_powerup (user_id, powerup_type),
    INDEX idx_user_id (user_id)
);
```

#### **7. Story Progress Table**
```sql
CREATE TABLE story_progress (
    progress_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    chapter_number INT NOT NULL,
    unlocked BOOLEAN DEFAULT FALSE,
    unlocked_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_chapter (user_id, chapter_number),
    INDEX idx_user_id (user_id)
);
```

### Security Measures
- **Password Hashing:** All passwords stored using PHP's `password_hash()` with bcrypt
- **Prepared Statements:** All SQL queries use parameterized statements to prevent SQL injection
- **Session Management:** Secure session tokens with httpOnly and secure flags
- **Input Validation:** Server-side validation of all user inputs
- **XSS Protection:** Output escaping for all user-generated content

### Performance Optimizations
- **Indexing:** Strategic indexes on frequently queried columns (user_id, session timestamps, difficulty)
- **Transactions:** Game session saves wrapped in transactions for data integrity
- **Connection Pooling:** Reusable database connections
- **Query Optimization:** Efficient JOIN operations and aggregate queries for analytics

---

## 🌟 Custom Undergraduate Features (4 Required)

### Feature #1: **Festive Theme System** ✅
**Description:** Dynamic theme changes based on player progression and time
- **Three Theme Variants:**
  1. **Snowy Theme:** White/blue color palette, gentle snowfall
  2. **Santa's Workshop:** Red/green/gold palette, workshop interior background
  3. **Reindeer Stable:** Brown/forest green palette, stable imagery

- **Automatic Theme Switching:**
  - Time-based: Different themes for morning/afternoon/evening
  - Performance-based: Unlock themes after completing 5/10/15 puzzles
  - Player preference: Manual selection saved in database

- **Technical Implementation:**
  - CSS custom properties for theme switching
  - Database stores theme preference per user
  - JavaScript applies theme dynamically on page load

### Feature #2: **Gift & Reward System** ✅
**Description:** Achievement-based rewards that unlock cosmetic enhancements

**Achievements:**
- **"Quick Solver"** - Complete any puzzle in under 60 seconds
- **"Perfect Run"** - Win without using any hints
- **"Santa's Apprentice"** - Win 3 puzzles in a row
- **"Master Elf"** - Complete 20 total puzzles
- **"Speed Demon"** - Complete 4×4 puzzle in under 30 seconds

**Rewards:**
- **Tile Skins:** Alternative visual styles for puzzle tiles
- **Music Packs:** Additional background music tracks
- **Glow Effects:** Animated border effects for tiles
- **Gift Box Animation:** Visual reward presentation

**Technical Implementation:**
- Achievement triggers checked after each game completion
- Stored in `achievements` table
- Unlocked rewards displayed in profile
- Can be equipped from inventory system

### Feature #3: **Holiday Magic Power-ups** ✅
**Description:** Strategic items that assist with puzzle solving

**Power-up Types:**
1. **"Reveal Best Move"** (3 starting, earn 1 per 5 wins)
   - Highlights the optimal tile to move next
   - Uses simple distance-to-solution heuristic

2. **"Swap Two Tiles"** (2 starting, earn 1 per 10 wins)
   - Allows player to swap any two tiles instantly
   - Useful for correcting major mistakes

3. **"Freeze Timer"** (5 starting, earn 1 per 3 wins)
   - Pauses the game timer for 10 seconds
   - Gives breathing room for strategic thinking

**Technical Implementation:**
- Power-up inventory stored in database per user
- JavaScript enables/disables buttons based on quantity
- Usage tracked in game sessions
- Earned through gameplay milestones

### Feature #4: **Christmas Story Mode** ✅
**Description:** Narrative progression that unlocks as player completes puzzles

**Story Structure:**
- **7 Chapters Total:** Each unlocks after completing certain puzzle milestones
  - Chapter 1: "Welcome to the Workshop" (unlocked at start)
  - Chapter 2: "The Mysterious Order" (after 3 wins)
  - Chapter 3: "The Enchanted Tiles" (after 7 wins)
  - Chapter 4: "The Great Toy Shortage" (after 12 wins)
  - Chapter 5: "The Reindeer's Secret" (after 18 wins)
  - Chapter 6: "The Magic Restored" (after 25 wins)
  - Chapter 7: "Christmas is Saved!" (after 30 wins)

**Features:**
- Story pages display between game sessions
- Illustrated with festive graphics
- Chapter text stored in database
- Progress tracked per user
- Optional replay of unlocked chapters

**Technical Implementation:**
- `story_progress` table tracks unlocked chapters
- Modal overlay displays story content
- Player can skip or read
- Chapter unlock notifications appear after milestone wins

---

## 📊 Deliverables

### Code Deliverables
1. ✅ Complete HTML pages (index.html, login.html, profile.html)
2. ✅ Comprehensive CSS styling (main.css, game.css, animations.css, themes.css)
3. ✅ Full JavaScript game logic (all .js files in js/ folder)
4. ✅ PHP backend with API endpoints (all .php files in php/ folder)
5. ✅ MySQL database schema (schema.sql with command-line setup instructions)
6. ✅ All game assets (tile images, audio files)

### Documentation Deliverables
1. ✅ This project proposal (proposal.md)
2. ✅ Development checklist (development-plan.md)
3. ✅ Testing guide (testing-guide.md)
4. ✅ README with setup instructions

### Functional Requirements Met
- ✅ Adaptive difficulty based on player skill
- ✅ Database-driven gameplay with command-line SQL setup
- ✅ Complete authentication system
- ✅ Comprehensive progress tracking and analytics
- ✅ Strategic hint and power-up system
- ✅ Fully themed visual experience (no numbers on tiles)
- ✅ Audio integration with dynamic music
- ✅ Victory animations and celebrations
- ✅ Four custom undergraduate features

---

## 🚀 Development Timeline

| Phase | Tasks | Estimated Time |
|-------|-------|----------------|
| **Phase 1: Setup** | Database creation, file structure, configuration | 2 hours |
| **Phase 2: Authentication** | Login/register system, session management | 3 hours |
| **Phase 3: Core Puzzle** | Tile movement, shuffle algorithm, win detection | 4 hours |
| **Phase 4: Adaptive System** | Difficulty scaling, skill tracking | 3 hours |
| **Phase 5: Custom Features** | Themes, rewards, power-ups, story mode | 5 hours |
| **Phase 6: Audio/Visual** | Animations, music, sound effects | 3 hours |
| **Phase 7: Testing** | Bug fixes, compatibility testing | 3 hours |
| **Phase 8: Polish** | Documentation, final touches | 2 hours |
| **TOTAL** | | **25 hours** |

---

## 🎓 Academic Justification

This project demonstrates mastery of:
- **Database Design:** Normalized schema with proper relationships and indexing
- **Server-Side Programming:** RESTful API design, authentication, session management
- **Client-Side Programming:** Complex JavaScript logic for game mechanics
- **Algorithm Implementation:** Shuffle algorithms, solvability checking, adaptive difficulty
- **UI/UX Design:** Responsive, themed interface with animations
- **Security:** Password hashing, SQL injection prevention, input validation
- **Project Management:** Structured development with documentation

The adaptive difficulty system provides academic depth by implementing:
- Statistical performance tracking
- Dynamic content generation based on user data
- Algorithmic complexity scaling
- Machine learning-inspired feedback loops

---

## 🏆 Success Metrics

The project will be considered successful when:
1. ✅ All core requirements are fully functional
2. ✅ Database is properly implemented with command-line SQL
3. ✅ All 4 custom features work correctly
4. ✅ Application is fully themed with no placeholder content
5. ✅ Code is well-commented and documented
6. ✅ Testing reveals no critical bugs
7. ✅ Application is deployed and accessible

---

## 📝 Conclusion

The **Santa's Workshop Adaptive Christmas Fifteen Puzzle** represents a comprehensive full-stack web application that meets all project requirements while providing an engaging user experience. The adaptive difficulty system demonstrates sophisticated programming concepts, while the four custom features add depth and replayability. The project is scoped appropriately for completion within the given timeline and showcases proficiency in HTML, CSS, JavaScript, PHP, and MySQL.

**Ready for approval and implementation.**

---

**Signature:** ____________________  
**Date:** November 28, 2025
