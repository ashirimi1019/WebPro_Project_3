# 🎄 Santa's Workshop - Christmas Fifteen Puzzle
## Web Programming Final Project #03 (Holiday Edition 2025)

> An adaptive, holiday-themed puzzle game with progressive difficulty, achievements, power-ups, and story mode.

---

## 📖 Table of Contents
- [Overview](#overview)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Development](#development)
- [Testing](#testing)
- [Credits](#credits)

---

## 🎯 Overview

**Santa's Workshop** is a comprehensive web application that reimagines the classic fifteen puzzle with festive Christmas theming and intelligent adaptive difficulty. As players solve puzzles, the game dynamically adjusts challenge levels, unlocks achievements, reveals story chapters, and rewards strategic gameplay with power-ups.

### Key Highlights
- ✨ **Adaptive Difficulty** - Dynamically scales based on performance
- 🏆 **8 Unique Achievements** - Unlock rewards through skillful play
- 🎁 **3 Strategic Power-ups** - Enhance gameplay with special abilities
- 📖 **7-Chapter Story Mode** - Progressive narrative unlocks
- 🎨 **3 Festive Themes** - Customizable visual experiences
- 📊 **Comprehensive Statistics** - Track every aspect of your journey

---

## ✨ Features

### Core Gameplay
- **4×4 Puzzle Grid** with Christmas-themed tile imagery
- **Solvable Shuffle Algorithm** ensuring every puzzle is winnable
- **Real-time Move Counter** and timer
- **Strategic Tile Highlighting** showing valid moves
- **Smooth CSS Animations** for professional feel

### Adaptive System
- **Performance Tracking** across all sessions
- **Skill Level Progression** (1-10 scale)
- **Dynamic Shuffle Depth** increases with skill
- **Intelligent Difficulty Scaling** based on win rate and speed

### Achievement System
- 🏆 **Quick Solver** - Complete under 60 seconds
- 💎 **Perfect Run** - Win without hints/power-ups
- 🎅 **Santa's Apprentice** - 3-win streak
- 🧝 **Master Elf** - 20 total completions
- 🔥 **Speed Demon** - Sub-30-second completion
- 🏃 **Marathon Runner** - 50 games played
- ✨ **Perfectionist** - Minimum moves completion
- 👑 **Untouchable** - 10-win streak

### Power-up System
- **🔍 Reveal Move** - Highlights optimal next tile (3 starting)
- **🔄 Swap Tiles** - Corrects mistakes instantly (2 starting)
- **❄️ Freeze Timer** - 10-second pause (5 starting)
- Earn more through achievements and milestones!

### Story Mode
- **7 Progressive Chapters** unlock with wins
- Rich narrative following your journey
- Chapter-specific imagery and rewards
- Replay unlocked chapters anytime

### Theme System
- **❄️ Snowy** - Winter wonderland aesthetic
- **🎅 Workshop** - Santa's cozy workspace
- **🦌 Reindeer** - Rustic stable vibes
- Themes unlock through achievements

### Audio-Visual Polish
- Background festive music
- Tile click sound effects
- Victory celebration jingle
- Animated confetti on completion
- Continuous snowfall effect

---

## 🛠️ Technology Stack

### Frontend
- **HTML5** - Semantic markup
- **CSS3** - Grid, Flexbox, Animations, Custom Properties
- **JavaScript (ES6+)** - Vanilla JS, no frameworks

### Backend
- **PHP 7.4+** - Server-side logic
- **MySQL 8.0+** - Database management

### Development Tools
- **Command-Line MySQL** - Database administration
- **VS Code** - Recommended editor
- **XAMPP/WAMP/MAMP** - Local server environment

---

## 📦 Installation

### Prerequisites
1. **Web Server** with PHP 7.4+ support (Apache/Nginx)
2. **MySQL 8.0+** installed and running
3. **Command-line access** to MySQL

### Step-by-Step Setup

#### 1. Clone/Download Project
```bash
cd C:\Users\ashir\OneDrive\Desktop\
# Project is already in WebPro_Project_3 folder
```

#### 2. Database Setup
```bash
# Open Command Prompt/PowerShell
mysql -u root -p

# Inside MySQL prompt:
SOURCE C:/Users/ashir/OneDrive/Desktop/WebPro_Project_3/sql/schema.sql;
```

Verify installation:
```sql
USE christmas_puzzle;
SHOW TABLES;
SELECT * FROM story_chapters;
```

#### 3. Configure PHP Backend
Edit `php/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');          // Your MySQL user
define('DB_PASS', 'your_password'); // Your MySQL password
define('DB_NAME', 'christmas_puzzle');
```

#### 4. Setup Web Server

**Option A: XAMPP**
```bash
# Copy project to XAMPP htdocs
copy WebPro_Project_3 C:\xampp\htdocs\

# Start Apache and MySQL from XAMPP Control Panel
# Access: http://localhost/WebPro_Project_3/
```

**Option B: PHP Built-in Server**
```bash
cd C:\Users\ashir\OneDrive\Desktop\WebPro_Project_3
php -S localhost:8000
# Access: http://localhost:8000/login.html
```

#### 5. Create Asset Folders (Optional)
```bash
# Create placeholder images
mkdir assets\images\tiles
mkdir assets\audio

# You'll need to add:
# - 15 tile images (tile-1.jpg through tile-15.jpg)
# - Audio files (bgm.mp3, tile-click.mp3, etc.)
```

---

## ⚙️ Configuration

### Database User (Recommended)
Create dedicated user for security:
```sql
CREATE USER 'puzzle_app'@'localhost' IDENTIFIED BY 'SecurePassword123!';
GRANT SELECT, INSERT, UPDATE, DELETE ON christmas_puzzle.* TO 'puzzle_app'@'localhost';
FLUSH PRIVILEGES;
```

Update `php/config.php` accordingly.

### Audio Files
Place audio files in `assets/audio/`:
- `bgm.mp3` - Background music (loop)
- `tile-click.mp3` - Tile click sound
- `move.mp3` - Move completion
- `victory.mp3` - Win celebration
- `powerup.mp3` - Power-up activation

### Tile Images
Place tile images in `assets/images/tiles/`:
- `tile-1.jpg` through `tile-15.jpg`
- Each should be 200x200px
- Christmas-themed imagery (ornaments, candy canes, snowflakes, etc.)

---

## 🎮 Usage

### First Time Setup
1. Navigate to `http://localhost/WebPro_Project_3/login.html`
2. Click "Create an account"
3. Fill in username, email, password
4. Click "Create Account"
5. You'll be auto-logged in

### Playing the Game
1. Click tiles adjacent to the empty space to move them
2. Arrange tiles in numerical order (1-15)
3. Use hints (3 per day) for guidance
4. Activate power-ups strategically
5. Complete puzzles to:
   - Increase skill level
   - Unlock achievements
   - Reveal story chapters
   - Earn more power-ups

### Viewing Stats
- Click "📊 Stats" in navigation
- View comprehensive analytics
- Check leaderboard rankings
- Review game history
- Read unlocked story chapters

### Changing Themes
1. Click "🎨 Change Theme" button
2. Select from available themes
3. Theme is saved to your profile

---

## 📁 Project Structure

```
WebPro_Project_3/
│
├── index.html              # Main game page
├── login.html              # Authentication page
├── profile.html            # Stats dashboard
│
├── css/
│   ├── main.css           # Core styles
│   ├── game.css           # Game board styles
│   ├── animations.css     # Effects & animations
│   └── themes.css         # Theme variations
│
├── js/
│   ├── api.js             # API communication
│   ├── auth.js            # Authentication logic
│   ├── game.js            # Main game controller
│   ├── puzzle.js          # Puzzle mechanics
│   ├── adaptive.js        # Difficulty scaling
│   ├── powerups.js        # Power-up management
│   ├── audio.js           # Sound system
│   └── animations.js      # Visual effects
│
├── php/
│   ├── config.php         # Database config
│   ├── api.php            # API router
│   ├── auth.php           # Auth handlers
│   ├── game.php           # Game logic
│   └── stats.php          # Statistics handlers
│
├── sql/
│   ├── schema.sql         # Database schema
│   └── setup.txt          # Setup instructions
│
├── assets/
│   ├── images/tiles/      # Tile imagery
│   └── audio/             # Sound files
│
├── docs/
│   ├── proposal.md        # Project proposal
│   ├── development-plan.md
│   └── testing-guide.md
│
└── README.md
```

---

## 🔧 Development

### Adding New Features

#### New Achievement
1. Add to `sql/schema.sql` achievements ENUM
2. Update `php/game.php` check_achievements procedure
3. Add display in `js/stats.js`

#### New Power-up
1. Add to powerups_inventory table
2. Implement effect in `js/powerups.js`
3. Add UI button in `index.html`

#### New Theme
1. Create CSS in `css/themes.css`
2. Add option in theme modal
3. Update database ENUM in users table

### Code Style
- **PHP**: PSR-12 standard
- **JavaScript**: ES6+ with camelCase
- **CSS**: BEM methodology
- **SQL**: UPPERCASE keywords

### Git Workflow (If Using)
```bash
git add .
git commit -m "feat: descriptive message"
git push origin main
```

---

## 🧪 Testing

### Manual Testing Checklist
- [ ] User registration works
- [ ] Login/logout functional
- [ ] Puzzle generates and displays
- [ ] Tiles move correctly
- [ ] Win detection accurate
- [ ] Stats save properly
- [ ] Achievements unlock
- [ ] Power-ups consume correctly
- [ ] Hints work (3 per day limit)
- [ ] Story chapters unlock
- [ ] Themes change properly
- [ ] Audio plays correctly

### Database Testing
```sql
-- Test user registration
SELECT * FROM users ORDER BY created_at DESC LIMIT 5;

-- Test game sessions
SELECT * FROM game_sessions ORDER BY created_at DESC LIMIT 10;

-- Test achievements
SELECT u.username, a.achievement_type 
FROM achievements a 
JOIN users u ON a.user_id = u.user_id;

-- Test leaderboard view
SELECT * FROM leaderboard LIMIT 10;
```

### Performance Testing
- Test with 100+ game sessions
- Monitor query execution times
- Check for memory leaks (browser DevTools)
- Verify responsive design on mobile

---

## 🐛 Troubleshooting

### Database Connection Failed
```php
// Check config.php credentials
// Verify MySQL is running:
mysql -u root -p
```

### Tiles Not Displaying
```
// Check assets/images/tiles/ folder exists
// Verify image files are named correctly (tile-1.jpg, etc.)
// Check browser console for 404 errors
```

### Session Not Persisting
```php
// Ensure session_start() is called
// Check browser cookies are enabled
// Verify php.ini session settings
```

### Audio Not Playing
```
// Check audio files exist in assets/audio/
// Verify browser autoplay policy
// Check file formats (MP3 recommended)
```

---

## 📚 API Documentation

### Authentication Endpoints
- `POST /api.php?action=register` - Create account
- `POST /api.php?action=login` - User login
- `POST /api.php?action=logout` - User logout
- `GET /api.php?action=check_auth` - Verify session

### Game Endpoints
- `POST /api.php?action=generate_puzzle` - New puzzle
- `POST /api.php?action=save_session` - Save completion
- `POST /api.php?action=get_hint` - Request hint
- `POST /api.php?action=mark_chapter_viewed` - Mark story read

### Statistics Endpoints
- `GET /api.php?action=get_stats` - User statistics
- `GET /api.php?action=get_achievements` - Achievement list
- `GET /api.php?action=get_powerups` - Power-up inventory
- `POST /api.php?action=use_powerup` - Consume power-up
- `GET /api.php?action=get_leaderboard` - Top players
- `GET /api.php?action=get_game_history` - Recent games
- `GET /api.php?action=get_story_progress` - Story status

---

## 🎓 Academic Notes

This project demonstrates:
- ✅ Full-stack web development
- ✅ Database design with normalization
- ✅ RESTful API architecture
- ✅ Secure authentication (password hashing, prepared statements)
- ✅ Algorithm implementation (solvable shuffle, difficulty scaling)
- ✅ Object-oriented JavaScript
- ✅ Responsive CSS design
- ✅ Command-line database administration

---

## 📝 License

This is an academic project for Web Programming coursework.

---

## 👤 Author

**[Your Name]**  
Web Programming Final Project #03  
November 2025

---

## 🙏 Acknowledgments

- Instructor: [Instructor Name]
- Course: Web Programming
- Semester: Fall 2025
- Institution: [Your University]

---

## 📞 Support

For issues or questions:
1. Check `docs/testing-guide.md`
2. Review `sql/setup.txt`
3. Contact instructor during office hours

---

**Last Updated:** November 25, 2025  
**Version:** 1.0.0  
**Status:** ✅ Complete & Ready for Submission
