# 🚀 QUICK START GUIDE
## Get Santa's Workshop Running in 10 Minutes!

---

## ⚡ Prerequisites Check

Before starting, ensure you have:
- ✅ MySQL 8.0+ installed and running
- ✅ PHP 7.4+ installed
- ✅ Web server (Apache/XAMPP/WAMP) OR use PHP built-in server
- ✅ Command-line access

---

## 📋 5-Step Setup

### Step 1: Database Setup (3 minutes)

Open Command Prompt/PowerShell:

```powershell
# Login to MySQL
mysql -u root -p
# Enter your password when prompted
```

Inside MySQL:
```sql
# Execute the schema
SOURCE C:/Users/ashir/OneDrive/Desktop/WebPro_Project_3/sql/schema.sql;

# Verify it worked
SHOW DATABASES;
USE christmas_puzzle;
SHOW TABLES;

# Exit MySQL
EXIT;
```

✅ **Success Check:** You should see 9 tables and 7 story chapters.

---

### Step 2: Configure Database Connection (1 minute)

Edit `php/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // ← Your MySQL username
define('DB_PASS', 'YOUR_PASSWORD');  // ← Your MySQL password
define('DB_NAME', 'christmas_puzzle');
```

Save the file.

---

### Step 3: Start Web Server (1 minute)

**Option A: Using PHP Built-in Server (Easiest)**
```powershell
cd C:\Users\ashir\OneDrive\Desktop\WebPro_Project_3
php -S localhost:8000
```

**Option B: Using XAMPP**
```powershell
# Copy project to htdocs folder
copy WebPro_Project_3 C:\xampp\htdocs\

# Start Apache and MySQL from XAMPP Control Panel
```

---

### Step 4: Test API Connection (1 minute)

Open browser and visit:
```
http://localhost:8000/php/api.php?action=health
```

Expected response:
```json
{"success":true,"message":"API is running","timestamp":"2025-11-25 12:00:00"}
```

✅ **Success Check:** If you see this JSON, your backend is working!

---

### Step 5: Create Account & Play (4 minutes)

1. Visit: `http://localhost:8000/login.html`
2. Click "Create an account"
3. Fill in:
   - Username: `testplayer`
   - Email: `test@example.com`
   - Password: `password123`
4. Click "Create Account"
5. You'll auto-login and see the game board!
6. Click tiles to move them
7. Solve the puzzle!

---

## 🎯 What to Do Next

### Immediate Next Steps:
1. ✅ Complete at least 3 puzzles to test difficulty scaling
2. ✅ Check your profile page: `http://localhost:8000/profile.html`
3. ✅ Test power-ups and hints
4. ✅ Try changing themes

### Before Submission:
1. 📖 Read `docs/development-plan.md` for full checklist
2. 🧪 Follow `docs/testing-guide.md` to test everything
3. 📝 Review `docs/proposal.md` (due Nov 28!)
4. 🌟 Consider `docs/extra-credit.md` for bonus points

---

## 🐛 Troubleshooting

### "Database connection failed"
```php
// Check credentials in php/config.php
// Verify MySQL is running: mysql -u root -p
```

### "Cannot find schema.sql"
```powershell
# Use full absolute path
SOURCE C:/Users/ashir/OneDrive/Desktop/WebPro_Project_3/sql/schema.sql;
# Note: Use forward slashes /
```

### "php is not recognized"
```powershell
# Use full path to PHP
"C:\xampp\php\php.exe" -S localhost:8000
# Or add PHP to your PATH
```

### Tiles not showing images
```
# Create placeholder images or comment out image loading temporarily
# The game will work without images (numbers will show)
```

### "No hints remaining"
```sql
-- Reset hints manually in MySQL
UPDATE daily_hints SET hints_remaining = 3 WHERE user_id = 1;
```

---

## 📁 Project Structure (Quick Reference)

```
WebPro_Project_3/
├── index.html          ← Main game page
├── login.html          ← Start here!
├── profile.html        ← Stats page
├── php/
│   ├── config.php      ← Edit this for DB connection
│   └── api.php         ← Test this endpoint
├── sql/
│   └── schema.sql      ← Run this in MySQL
└── docs/
    ├── proposal.md     ← Due Nov 28
    ├── development-plan.md
    ├── testing-guide.md
    └── extra-credit.md
```

---

## ✅ Verification Checklist

After setup, verify:
- [ ] Can access login.html in browser
- [ ] Can create new account
- [ ] Login redirects to game page
- [ ] Puzzle board displays
- [ ] Tiles move when clicked
- [ ] Move counter increases
- [ ] Timer runs
- [ ] Profile page loads

If all checked ✅, you're ready to develop!

---

## 💡 Pro Tips

1. **Keep MySQL open** - Easier to test queries
2. **Use browser DevTools** - Check Console for errors
3. **Test frequently** - Don't wait until the end
4. **Commit often** - If using Git (optional)
5. **Read error messages** - They usually tell you what's wrong

---

## 📞 Need Help?

1. Check `README.md` for detailed info
2. Review `docs/testing-guide.md` for debugging
3. Look at `sql/setup.txt` for database help
4. Check browser Console for JavaScript errors
5. Check MySQL error log for database issues

---

## 🎓 Academic Notes

**Remember:**
- This is YOUR project - understand every line of code
- Test thoroughly before submission
- Document any issues and how you solved them
- Keep backups of your database
- Save your work frequently!

---

## 🎉 You're All Set!

Your project is now running. Complete the core functionality following `docs/development-plan.md`, then add extra features from `docs/extra-credit.md` if you have time!

**Good luck! 🎄**

---

**Last Updated:** November 25, 2025  
**Estimated Setup Time:** 10 minutes  
**Next Step:** Play the game and test all features!
