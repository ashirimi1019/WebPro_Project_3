# ✅ DEPLOYMENT CHECKLIST

## Pre-Deployment Verification

### Environment Check
- [ ] PHP 7.4+ installed (`php -v`)
- [ ] MySQL 8.0+ installed and running
- [ ] Command-line access to MySQL
- [ ] Text editor for config changes
- [ ] Web browser (Chrome, Firefox, Edge)

### File Verification
- [ ] All 33 project files present
- [ ] No missing folders
- [ ] SQL files readable
- [ ] PHP files have correct extension
- [ ] HTML files have correct extension

---

## Database Setup (5 min)

### Step 1: Create Database
```sql
-- Connect to MySQL
mysql -u root -p

-- Create database
CREATE DATABASE christmas_puzzle;

-- Select database
USE christmas_puzzle;
```
- [ ] Database created successfully
- [ ] No error messages

### Step 2: Import Schema
```sql
-- Import schema (adjust path if needed)
SOURCE C:/Users/ashir/OneDrive/Desktop/WebPro_Project_3/sql/schema.sql;
```
- [ ] Schema imported without errors
- [ ] Tables created (check with `SHOW TABLES;`)
- [ ] Should see 9 tables

### Step 3: Verify Tables
```sql
-- Check all tables exist
SHOW TABLES;

-- Should see:
-- achievements
-- daily_hints
-- game_sessions
-- powerups_inventory
-- puzzles
-- story_chapters
-- story_progress
-- user_analytics
-- users
```
- [ ] All 9 tables present
- [ ] No error messages

### Step 4: Verify Procedures
```sql
-- Check stored procedures
SHOW PROCEDURE STATUS WHERE Db = 'christmas_puzzle';

-- Should see:
-- register_user
-- save_game_session
-- check_achievements
-- check_story_unlocks
-- reset_daily_hints
```
- [ ] All 5 procedures present

### Step 5: Verify Views
```sql
-- Check views
SHOW FULL TABLES WHERE TABLE_TYPE LIKE 'VIEW';

-- Should see:
-- leaderboard
-- recent_activity
```
- [ ] Both views present

### Step 6: Exit MySQL
```sql
EXIT;
```
- [ ] Exited successfully

---

## PHP Configuration (2 min)

### Step 1: Open Config File
- [ ] Navigate to `php/config.php`
- [ ] Open in text editor

### Step 2: Update Database Credentials
```php
// Line 7-10 in php/config.php
private $host = 'localhost';          // Usually localhost
private $dbname = 'christmas_puzzle'; // Keep this
private $username = 'root';           // YOUR MySQL username
private $password = '';               // YOUR MySQL password
```
- [ ] Username updated (if not 'root')
- [ ] Password updated (if you have one)
- [ ] File saved

### Step 3: Verify PHP Syntax
```bash
php -l php/config.php
# Should output: No syntax errors detected
```
- [ ] No syntax errors

---

## Web Server Setup (1 min)

### Step 1: Navigate to Project Directory
```bash
cd C:\Users\ashir\OneDrive\Desktop\WebPro_Project_3
```
- [ ] Directory exists
- [ ] In correct location

### Step 2: Start PHP Built-in Server
```bash
php -S localhost:8000
```
- [ ] Server started
- [ ] Shows: "Development Server (http://localhost:8000) started"
- [ ] No error messages

### Step 3: Keep Terminal Open
- [ ] Terminal window kept open
- [ ] Server running in background

---

## Application Testing (10 min)

### Step 1: Access Login Page
- [ ] Open browser
- [ ] Navigate to: http://localhost:8000/login.html
- [ ] Page loads successfully
- [ ] Snowfall animation visible
- [ ] No console errors (press F12 to check)

### Step 2: Register New Account
- [ ] Click "Create an account"
- [ ] Fill in username (e.g., "testuser")
- [ ] Fill in email (e.g., "test@example.com")
- [ ] Fill in password (e.g., "password123")
- [ ] Click "Create Account"
- [ ] Success message appears
- [ ] Redirected to game page

### Step 3: Game Page Verification
- [ ] Puzzle board visible (4×4 grid)
- [ ] 15 tiles with gradients/colors
- [ ] Empty space visible
- [ ] Left sidebar shows stats
- [ ] Right sidebar shows achievements
- [ ] Timer shows "00:00"
- [ ] Moves counter shows "0"
- [ ] Power-up buttons visible

### Step 4: Play a Game
- [ ] Click on movable tile (should glow)
- [ ] Tile moves to empty space
- [ ] Move counter increases
- [ ] Timer starts
- [ ] Can move multiple tiles
- [ ] Tiles slide smoothly

### Step 5: Complete a Puzzle
- [ ] Solve the puzzle (arrange 1-15 in order)
- [ ] Victory modal appears
- [ ] Confetti animation plays
- [ ] Score displayed
- [ ] Stats updated

### Step 6: Profile Page
- [ ] Click "Profile" in navigation
- [ ] Profile page loads
- [ ] Username displayed
- [ ] Stats visible (games played, wins, etc.)
- [ ] Achievements grid shown
- [ ] Game history table visible
- [ ] Leaderboard table visible

### Step 7: Theme Switching
- [ ] Click "Change Theme" button
- [ ] Theme modal appears
- [ ] Click different theme
- [ ] Colors change
- [ ] Theme persists after refresh

### Step 8: Logout and Login
- [ ] Click "Logout"
- [ ] Redirected to login page
- [ ] Enter same credentials
- [ ] Click "Login"
- [ ] Successfully logged in
- [ ] Previous stats preserved

---

## Database Verification (5 min)

### Verify Data Saved
```sql
mysql -u root -p
USE christmas_puzzle;

-- Check user was created
SELECT * FROM users;
-- Should see your test user

-- Check game session saved
SELECT * FROM game_sessions;
-- Should see your game(s)

-- Check stats updated
SELECT * FROM user_analytics;
-- Should see your stats

-- Check leaderboard view
SELECT * FROM leaderboard LIMIT 5;
-- Should see your ranking

EXIT;
```
- [ ] User record exists
- [ ] Game sessions recorded
- [ ] Stats calculated correctly
- [ ] Leaderboard shows user

---

## Browser Compatibility (5 min)

### Test in Multiple Browsers
- [ ] Chrome - All features work
- [ ] Firefox - All features work
- [ ] Edge - All features work
- [ ] Safari (if Mac) - All features work

### Test Responsive Design
- [ ] Desktop (1920×1080) - Layout correct
- [ ] Laptop (1366×768) - Layout correct
- [ ] Tablet (768×1024) - Layout adapts
- [ ] Mobile (375×667) - Layout stacks vertically

**How to test:**
- Press F12 in browser
- Click device toolbar icon
- Select different device sizes

---

## Console Error Check (2 min)

### Check for Errors
1. Press F12 in browser
2. Go to "Console" tab
3. Look for red error messages

**Expected:**
- [ ] No JavaScript errors
- [ ] No CSS loading errors
- [ ] No 404 file not found errors
- [ ] API calls return 200 OK

**Common Issues:**
- If 404 errors: Check file paths
- If API errors: Check PHP config
- If database errors: Check MySQL connection

---

## Performance Check (2 min)

### Speed Tests
- [ ] Page loads in < 3 seconds
- [ ] Tiles move smoothly (no lag)
- [ ] Animations are smooth (60 FPS)
- [ ] No memory leaks (check Task Manager)

### Network Check
1. Press F12
2. Go to "Network" tab
3. Refresh page
4. Check:
   - [ ] All resources load (green 200 status)
   - [ ] Total load time < 3s
   - [ ] No failed requests

---

## Security Verification (3 min)

### Test Security Features
```sql
-- Try SQL injection in login form
Username: ' OR '1'='1
Password: anything

-- Should NOT log in (protected by prepared statements)
```
- [ ] SQL injection blocked

### Check Password Security
```sql
-- Check password is hashed (not plain text)
SELECT username, password FROM users LIMIT 1;

-- Password should look like: $2y$10$...random characters...
```
- [ ] Password is hashed (not readable)

---

## Final Verification (2 min)

### Complete Feature Test
- [ ] User registration works
- [ ] User login works
- [ ] Puzzle generates correctly
- [ ] Tiles move properly
- [ ] Win detection works
- [ ] Stats save to database
- [ ] Achievements unlock
- [ ] Power-ups work
- [ ] Themes switch
- [ ] Leaderboard updates
- [ ] Profile displays correctly
- [ ] Logout works

---

## Documentation Check (2 min)

### Verify Files Present
- [ ] README.md exists and is complete
- [ ] QUICKSTART.md exists
- [ ] PROJECT_COMPLETE.md exists
- [ ] FINAL_SUMMARY.md exists
- [ ] docs/proposal.md exists
- [ ] docs/testing-guide.md exists

---

## Common Issues & Solutions

### Issue: "Can't connect to database"
**Solution:**
1. Check MySQL is running
2. Verify credentials in `php/config.php`
3. Ensure database exists: `SHOW DATABASES;`

### Issue: "Page not found"
**Solution:**
1. Check server is running: `php -S localhost:8000`
2. Use correct URL: http://localhost:8000/login.html
3. Check file exists in project directory

### Issue: "Tiles don't move"
**Solution:**
1. Check browser console for JS errors (F12)
2. Verify all JS files loaded (Network tab)
3. Clear browser cache (Ctrl+Shift+Del)

### Issue: "Stats don't save"
**Solution:**
1. Check database connection
2. Verify tables exist: `SHOW TABLES;`
3. Check PHP error log

### Issue: "Images don't show"
**Solution:**
1. Images are optional! Gradients used by default
2. If adding images, check filenames: `tile-1.jpg`
3. Check path: `assets/images/tiles/`

---

## Production Deployment (Optional)

If deploying to real web server:

### Additional Steps
- [ ] Update `php/config.php` with production DB credentials
- [ ] Set `session.cookie_secure = true` for HTTPS
- [ ] Enable error logging (disable display_errors)
- [ ] Set up HTTPS/SSL certificate
- [ ] Configure proper Apache/Nginx vhost
- [ ] Set up automated backups
- [ ] Configure firewall rules
- [ ] Test from external network

---

## Deployment Complete! ✅

### Success Criteria
- ✅ Database set up correctly
- ✅ PHP configured properly
- ✅ Server running without errors
- ✅ Can register and login
- ✅ Game is playable
- ✅ Stats save correctly
- ✅ All features functional
- ✅ No console errors
- ✅ Performance is good

### You're Ready To:
- ✅ Submit proposal (Nov 28)
- ✅ Present project to class
- ✅ Submit final project
- ✅ Add to portfolio
- ✅ Show to friends!

---

## Post-Deployment

### Keep Server Running
```bash
# Terminal 1: Keep this open while testing
php -S localhost:8000
```

### Stop Server (When Done)
```
Press Ctrl+C in terminal
```

### Restart Server
```bash
cd C:\Users\ashir\OneDrive\Desktop\WebPro_Project_3
php -S localhost:8000
```

---

## Backup Recommendation

### Before Final Submission
1. Copy entire project folder
2. Export database:
   ```sql
   mysqldump -u root -p christmas_puzzle > backup.sql
   ```
3. Zip everything:
   ```bash
   # Right-click folder → Send to → Compressed (zipped) folder
   ```
4. Keep backup safe!

---

**Status:** 🎉 **DEPLOYMENT COMPLETE**  
**Next:** Submit proposal by Nov 28!  
**Enjoy:** Your amazing Christmas Puzzle game! 🎄✨
