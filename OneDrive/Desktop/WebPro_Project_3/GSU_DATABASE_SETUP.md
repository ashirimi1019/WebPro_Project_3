# 🗄️ GSU Server Database Setup Guide

## For codd.cs.gsu.edu Server

---

## Step 1: Access MySQL on GSU Server

### Via SSH (Recommended)
```bash
# Connect to GSU server
ssh aimran6@codd.cs.gsu.edu

# Access MySQL
mysql -u aimran6 -p
# Enter your database password when prompted
```

### Via phpMyAdmin (Alternative)
- URL: https://codd.cs.gsu.edu/phpmyadmin/
- Username: aimran6
- Password: Your GSU database password

---

## Step 2: Create Database

```sql
-- Create database (if not exists)
CREATE DATABASE IF NOT EXISTS aimran6_christmas_puzzle;

-- Use the database
USE aimran6_christmas_puzzle;
```

**Note:** GSU usually prefixes database names with your username.

---

## Step 3: Import Schema

### Option A: Via Command Line (Fastest)
```bash
# Upload schema.sql to server first
scp sql/schema.sql aimran6@codd.cs.gsu.edu:~/

# Then on server
mysql -u aimran6 -p aimran6_christmas_puzzle < ~/schema.sql
```

### Option B: Via phpMyAdmin (Easiest)
1. Log into phpMyAdmin
2. Select `aimran6_christmas_puzzle` database
3. Click "Import" tab
4. Choose `schema.sql` file
5. Click "Go"

### Option C: Copy-Paste SQL (Manual)
1. Open `sql/schema.sql` locally
2. Copy entire contents
3. In MySQL command line or phpMyAdmin SQL tab
4. Paste and execute

---

## Step 4: Verify Tables Created

```sql
-- Check all tables exist
SHOW TABLES;

-- Should see 9 tables:
-- achievements
-- daily_hints
-- game_sessions
-- powerups_inventory
-- puzzles
-- story_chapters
-- story_progress
-- user_analytics
-- users

-- Verify table structure
DESCRIBE users;
DESCRIBE game_sessions;
```

---

## Step 5: Update PHP Config for GSU

Edit `php/config.php` on server:

```php
<?php
// Database connection constants
define('DB_HOST', 'localhost');  // Usually localhost on GSU
define('DB_USER', 'aimran6');    // Your GSU username
define('DB_PASS', 'YOUR_DB_PASSWORD_HERE');  // Your GSU database password
define('DB_NAME', 'aimran6_christmas_puzzle');  // Database name with your username prefix
define('DB_CHARSET', 'utf8mb4');
```

---

## Step 6: Test Database Connection

Create a test file: `test_db.php`

```php
<?php
require_once 'php/config.php';

try {
    $db = Database::getInstance();
    echo "✅ Database connection successful!<br>";
    
    // Test query
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📋 Tables found: " . count($tables) . "<br>";
    foreach ($tables as $table) {
        echo "  - $table<br>";
    }
    
    echo "<br>🎉 Database is ready!";
} catch (Exception $e) {
    echo "❌ Database connection failed:<br>";
    echo $e->getMessage();
}
?>
```

Upload to server and access:
`https://codd.cs.gsu.edu/~aimran6/WebPro_Project_3/test_db.php`

---

## Step 7: Set Correct File Permissions

```bash
# On GSU server via SSH
cd ~/public_html/WebPro_Project_3/

# Make PHP files readable
chmod 644 php/*.php

# Make directories accessible
chmod 755 php/
chmod 755 css/
chmod 755 js/
chmod 755 sql/
```

---

## Common GSU Server Issues & Solutions

### Issue 1: "Access denied for user"
**Solution:**
- Check username is `aimran6` (your GSU username)
- Verify password is correct
- Contact GSU IT if you don't know database password

### Issue 2: "Database does not exist"
**Solution:**
- Database name must be prefixed: `aimran6_christmas_puzzle`
- Create database first: `CREATE DATABASE aimran6_christmas_puzzle;`

### Issue 3: "Table already exists" errors
**Solution:**
```sql
-- Drop and recreate if needed
DROP DATABASE IF EXISTS aimran6_christmas_puzzle;
CREATE DATABASE aimran6_christmas_puzzle;
USE aimran6_christmas_puzzle;
SOURCE schema.sql;
```

### Issue 4: PHP can't connect to database
**Solution:**
- Verify `php/config.php` has correct credentials
- Check file permissions: `chmod 644 php/config.php`
- Ensure PDO MySQL extension is enabled (usually is on GSU)

---

## Quick Setup Commands (All-in-One)

```bash
# 1. Connect to server
ssh aimran6@codd.cs.gsu.edu

# 2. Navigate to project
cd ~/public_html/WebPro_Project_3/

# 3. Create and setup database
mysql -u aimran6 -p << EOF
CREATE DATABASE IF NOT EXISTS aimran6_christmas_puzzle;
USE aimran6_christmas_puzzle;
SOURCE sql/schema.sql;
SHOW TABLES;
EOF

# 4. Edit config file
nano php/config.php
# Update DB_USER, DB_PASS, DB_NAME
# Save with Ctrl+O, Exit with Ctrl+X

# 5. Test connection
php test_db.php

# Done!
```

---

## Verification Checklist

- [ ] Database created: `aimran6_christmas_puzzle`
- [ ] Schema imported (9 tables)
- [ ] `php/config.php` updated with GSU credentials
- [ ] File permissions set correctly (644/755)
- [ ] Test connection successful
- [ ] Can register new user via web interface
- [ ] Can login successfully
- [ ] Game loads without errors

---

## GSU-Specific Database Info

### Default Settings:
- **Host:** localhost
- **Username:** Your GSU username (aimran6)
- **Database Prefix:** Your username + underscore
- **Port:** 3306 (default MySQL)
- **Character Set:** utf8mb4

### Getting Help:
- **GSU IT Support:** https://technology.gsu.edu/
- **Database Issues:** Contact GSU systems admin
- **Web Hosting:** Check GSU web hosting documentation

---

## Testing the Full Application

After database setup:

1. **Visit:** https://codd.cs.gsu.edu/~aimran6/WebPro_Project_3/login.html

2. **Register Account:**
   - Click "Create an account"
   - Username: testuser
   - Email: test@example.com
   - Password: Test123!
   - Submit

3. **Verify in Database:**
```sql
SELECT * FROM users;
-- Should see your test user
```

4. **Play Game:**
   - Click "New Game"
   - Move tiles
   - Complete puzzle

5. **Check Stats:**
```sql
SELECT * FROM game_sessions;
SELECT * FROM user_analytics;
-- Should see your game data
```

---

## Quick Reference

| What | Value |
|------|-------|
| Server | codd.cs.gsu.edu |
| MySQL User | aimran6 |
| Database | aimran6_christmas_puzzle |
| Web Path | ~/public_html/WebPro_Project_3/ |
| URL | https://codd.cs.gsu.edu/~aimran6/WebPro_Project_3/ |

---

## Next Steps After Setup

1. ✅ Database configured
2. ✅ Test registration/login
3. ✅ Play complete game
4. 📸 Take screenshots for documentation
5. 📝 Complete proposal (due Nov 28!)
6. 🎉 Submit project

---

**Need more help?** Check the main README.md or contact your instructor!

**GSU Support:** https://technology.gsu.edu/support/
