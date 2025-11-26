# 🚀 QUICK FIX GUIDE - Tiles Not Showing & Database Setup

## Problem 1: Tiles Not Appearing ✅ FIXED!

### What Was Wrong:
The puzzle tiles were trying to load images that don't exist, causing them not to render.

### What I Fixed:
- ✅ Updated `js/puzzle.js` to show tile **numbers** by default
- ✅ Images now load as optional backgrounds (graceful fallback)
- ✅ Tiles display immediately with colorful gradient backgrounds from CSS

### Result:
**Tiles will now appear with numbers (1-15) and gradient backgrounds!**

---

## Problem 2: Database Connection 🔧 NEEDS YOUR INPUT

### Files Updated:
1. ✅ `php/config.php` - Updated with GSU server settings
2. ✅ `test_db.php` - Created database test page
3. ✅ `GSU_DATABASE_SETUP.md` - Complete setup guide

---

## 🎯 What YOU Need to Do Now:

### Step 1: Upload Fixed Files to GSU Server

Upload these modified files:
```bash
# From your computer, upload these files:
- js/puzzle.js (FIXED - tiles now show)
- php/config.php (Updated with GSU settings)
- test_db.php (NEW - test database connection)
- GSU_DATABASE_SETUP.md (NEW - full guide)
```

**Upload Command (from project directory):**
```bash
scp js/puzzle.js aimran6@codd.cs.gsu.edu:~/public_html/WebPro_Project_3/js/
scp php/config.php aimran6@codd.cs.gsu.edu:~/public_html/WebPro_Project_3/php/
scp test_db.php aimran6@codd.cs.gsu.edu:~/public_html/WebPro_Project_3/
scp GSU_DATABASE_SETUP.md aimran6@codd.cs.gsu.edu:~/public_html/WebPro_Project_3/
```

### Step 2: Add Your Database Password

Edit `php/config.php` on the server and add your GSU database password:

```php
define('DB_PASS', 'YOUR_PASSWORD_HERE');  // Replace with your actual password
```

**Via SSH:**
```bash
ssh aimran6@codd.cs.gsu.edu
cd ~/public_html/WebPro_Project_3/
nano php/config.php
# Edit line 12, add your password
# Save: Ctrl+O, Enter, Exit: Ctrl+X
```

### Step 3: Setup Database

**Option A: Via SSH (Recommended)**
```bash
ssh aimran6@codd.cs.gsu.edu

# Create database
mysql -u aimran6 -p
# Enter password when prompted

# Then in MySQL:
CREATE DATABASE IF NOT EXISTS aimran6_christmas_puzzle;
USE aimran6_christmas_puzzle;
SOURCE ~/public_html/WebPro_Project_3/sql/schema.sql;
SHOW TABLES;  -- Should show 9 tables
EXIT;
```

**Option B: Via phpMyAdmin**
1. Go to: https://codd.cs.gsu.edu/phpmyadmin/
2. Login with your credentials
3. Create database: `aimran6_christmas_puzzle`
4. Import `sql/schema.sql` file

### Step 4: Test Database Connection

Visit this URL in your browser:
```
https://codd.cs.gsu.edu/~aimran6/WebPro_Project_3/test_db.php
```

**You should see:**
- ✅ Green "Database Connection Successful!"
- ✅ List of 9 tables
- ✅ 5 stored procedures

**If you see errors:**
- Check database password in `php/config.php`
- Verify database name: `aimran6_christmas_puzzle`
- Make sure schema.sql was imported

### Step 5: Test the Game!

Once database test passes, refresh your game:
```
https://codd.cs.gsu.edu/~aimran6/WebPro_Project_3/index.html
```

**You should now see:**
- ✅ Tiles appearing with numbers 1-15
- ✅ Colorful gradient backgrounds
- ✅ Tiles are clickable
- ✅ Can move tiles around

---

## 🎮 Quick Test Checklist

After uploading files and setting up database:

### Test Tiles:
- [ ] Visit game page
- [ ] Click "New Game"
- [ ] See 4×4 grid with numbers 1-15
- [ ] Tiles have gradient backgrounds
- [ ] Can click and move tiles

### Test Database:
- [ ] Visit `test_db.php`
- [ ] See green success message
- [ ] See 9 tables listed
- [ ] All tables have 0 or more rows

### Test Full Game:
- [ ] Go to `login.html`
- [ ] Register new account
- [ ] Login successfully
- [ ] Play and complete a puzzle
- [ ] Check stats on profile page

---

## 📋 Summary of Changes

### What's Fixed:
1. ✅ **Tiles now display** - Show numbers with gradient backgrounds
2. ✅ **Config updated** - GSU server database settings
3. ✅ **Test page added** - Easy database verification
4. ✅ **Setup guide** - Complete GSU database instructions

### What You Need to Do:
1. 📤 Upload 4 files to server
2. 🔐 Add database password to config.php
3. 🗄️ Setup database (create + import schema)
4. ✅ Test database connection
5. 🎮 Test game with tiles!

---

## 🆘 Troubleshooting

### "Tiles still not showing"
- Clear browser cache (Ctrl+Shift+Del)
- Hard refresh (Ctrl+Shift+R)
- Check browser console (F12) for errors
- Verify `js/puzzle.js` was uploaded correctly

### "Database connection failed"
- Check password in `php/config.php` (line 12)
- Verify database exists: `aimran6_christmas_puzzle`
- Make sure MySQL is running on GSU server
- Contact GSU IT if can't access database

### "Tables not found"
- Import `sql/schema.sql` file
- Check database name is correct
- Verify you have permissions to create tables

---

## 📞 Need Help?

1. **Check:** `GSU_DATABASE_SETUP.md` for detailed instructions
2. **Test:** Visit `test_db.php` to diagnose issues
3. **Contact:** GSU IT Support for database access issues
4. **Ask:** Your instructor if you're stuck

---

## ✅ Expected Result

After completing all steps:

✅ Tiles appear with numbers and gradients  
✅ Database connection successful  
✅ Can register and login  
✅ Can play and complete puzzles  
✅ Stats save to database  
✅ Profile page shows your progress  

**Your game will be fully functional! 🎄✨**

---

## 🎯 Files You Need to Upload

```
1. js/puzzle.js           (MODIFIED - tiles fix)
2. php/config.php         (MODIFIED - GSU settings)
3. test_db.php            (NEW - database test)
4. GSU_DATABASE_SETUP.md  (NEW - setup guide)
```

**Upload these 4 files and follow Step 2-5 above!**

---

Good luck! Your game is almost there! 🚀
