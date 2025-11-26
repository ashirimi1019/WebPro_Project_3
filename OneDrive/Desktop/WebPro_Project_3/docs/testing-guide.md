# 🧪 TESTING GUIDE
## Santa's Workshop - Comprehensive Testing Procedures

---

## 🎯 Testing Overview

This guide covers all testing procedures to ensure the Christmas Fifteen Puzzle meets all requirements and functions correctly.

---

## ✅ Unit Testing Checklist

### Database Layer Tests

#### Test 1: User Registration
```sql
-- Create test user
CALL register_user('testuser1', 'test1@example.com', '$2y$10$examplehash');

-- Verify user created
SELECT * FROM users WHERE username = 'testuser1';
-- Expected: 1 row with user data

-- Verify analytics initialized
SELECT * FROM user_analytics WHERE user_id = LAST_INSERT_ID();
-- Expected: 1 row with default values (total_games=0, etc.)

-- Verify power-ups initialized
SELECT * FROM powerups_inventory WHERE user_id = LAST_INSERT_ID();
-- Expected: 3 rows (reveal_move=3, swap_tiles=2, freeze_timer=5)

-- Verify hints initialized
SELECT * FROM daily_hints WHERE user_id = LAST_INSERT_ID();
-- Expected: 1 row with hints_remaining=3

-- Verify story initialized
SELECT * FROM story_progress WHERE user_id = LAST_INSERT_ID();
-- Expected: 1 row for chapter 1 with unlocked=TRUE
```

**Pass Criteria:** All tables populated correctly with initial data.

#### Test 2: Save Game Session
```sql
-- Simulate completed game
CALL save_game_session(1, 1, 45, 120, 3, 4, TRUE, 0, 0, 5000);

-- Verify session saved
SELECT * FROM game_sessions WHERE user_id = 1 ORDER BY created_at DESC LIMIT 1;
-- Expected: 1 row with moves=45, time_seconds=120, completed=TRUE

-- Verify analytics updated
SELECT total_games, total_wins, current_streak 
FROM user_analytics WHERE user_id = 1;
-- Expected: total_games=1, total_wins=1, current_streak=1

-- Verify skill level updated (after 5 wins)
-- Play 5 games, then check:
SELECT skill_level FROM users WHERE user_id = 1;
-- Expected: skill_level=2 (after 5 wins)
```

**Pass Criteria:** Session saves correctly and analytics update.

#### Test 3: Achievement Unlocking
```sql
-- Create fast completion (under 60 seconds)
CALL save_game_session(1, 2, 30, 45, 2, 4, TRUE, 0, 0, 8000);

-- Check achievement unlocked
SELECT * FROM achievements 
WHERE user_id = 1 AND achievement_type = 'quick_solver';
-- Expected: 1 row with unlocked achievement

-- Verify trigger awarded power-ups
SELECT SUM(quantity) FROM powerups_inventory WHERE user_id = 1;
-- Expected: Total quantity increased by 3 (1 per power-up type)
```

**Pass Criteria:** Achievement unlocks automatically and rewards granted.

---

## 🌐 API Endpoint Tests

### Authentication APIs

#### Test 1: POST /api.php?action=register
```javascript
// Test payload
{
  "username": "newuser",
  "email": "new@example.com",
  "password": "securepass123"
}

// Expected response
{
  "success": true,
  "message": "Registration successful",
  "user_id": 5
}

// Error cases to test:
// - Username too short (< 3 chars) -> error
// - Invalid email format -> error
// - Password too short (< 6 chars) -> error
// - Duplicate username -> error
// - Duplicate email -> error
```

#### Test 2: POST /api.php?action=login
```javascript
// Test payload
{
  "username": "testuser",
  "password": "password123"
}

// Expected response
{
  "success": true,
  "message": "Login successful",
  "user": {
    "user_id": 1,
    "username": "testuser",
    "email": "test@example.com",
    "skill_level": 3,
    "theme": "workshop"
  }
}

// Error cases:
// - Wrong password -> {"success": false, "error": "Invalid credentials"}
// - Non-existent user -> {"success": false, "error": "Invalid credentials"}
```

### Game APIs

#### Test 3: POST /api.php?action=generate_puzzle
```javascript
// Test payload
{
  "size": 4
}

// Expected response
{
  "success": true,
  "puzzle": {
    "puzzle_id": 123,
    "size": 4,
    "difficulty": 3,
    "shuffle_depth": 180,
    "initial_state": [5,1,3,4,9,2,7,8,6,10,11,12,13,14,15,0],
    "solution_state": [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,0]
  }
}

// Validation:
// - Initial state must be solvable (check inversion parity)
// - Solution state must be [1..15, 0]
// - Shuffle depth appropriate for difficulty
```

#### Test 4: POST /api.php?action=get_hint
```javascript
// Test payload
{
  "current_state": [1,2,3,4,5,6,7,8,9,10,11,0,13,14,15,12],
  "size": 4
}

// Expected response
{
  "success": true,
  "hint": 15,  // Position of best move
  "hints_remaining": 2
}

// Error case:
// - No hints remaining -> {"success": false, "error": "No hints remaining today"}
```

---

## 🎮 Functional Testing

### Game Flow Tests

#### Test 1: Complete Game Flow
1. Open browser to login.html
2. Register new account
3. Verify redirect to index.html
4. Verify puzzle auto-generates
5. Solve puzzle completely
6. Verify victory modal appears
7. Check database for saved session
8. Click "Play Again"
9. Verify new puzzle generates

**Pass Criteria:** Entire flow works without errors.

#### Test 2: Tile Movement Logic
```
Initial state: [1,2,3,4, 5,6,7,8, 9,10,11,0, 13,14,15,12]

Test moves:
1. Click tile 12 -> Should move to position 11
2. Click tile 15 -> Should move to position 15
3. Click tile 14 -> Should NOT move (not adjacent)
4. Click tile 11 -> Should move

Verify:
- Move counter increments correctly
- Timer runs
- Invalid moves don't count
- Board updates after each move
```

**Pass Criteria:** Only adjacent tiles move, counter accurate.

#### Test 3: Win Detection
```
Create near-solved state: [1,2,3,4, 5,6,7,8, 9,10,11,12, 13,14,0,15]
Move tile 15 to position 14
Move tile 0 to position 15

Expected:
- Victory modal appears immediately
- Confetti animation triggers
- Victory sound plays
- Session saves to database
- Move count and time accurate
```

**Pass Criteria:** Win detected instantly, all effects trigger.

---

## ⚡ Performance Testing

### Load Time Tests
```
Target: < 3 seconds full page load

Test procedure:
1. Open DevTools Network tab
2. Hard refresh page (Ctrl+Shift+R)
3. Wait for all resources to load
4. Check "Load" time in bottom status bar

Pass criteria: Load time < 3000ms on average connection
```

### Database Query Performance
```sql
-- Test query speed
SET profiling = 1;

SELECT * FROM game_sessions WHERE user_id = 1;
SHOW PROFILES;
-- Target: < 0.01 seconds

SELECT * FROM leaderboard LIMIT 10;
SHOW PROFILES;
-- Target: < 0.05 seconds

-- Check index usage
EXPLAIN SELECT * FROM game_sessions WHERE user_id = 1;
-- Should use idx_user_id index
```

**Pass Criteria:** All queries under target times, indexes used.

### Memory Leak Test
```
Test procedure:
1. Open Chrome DevTools > Memory tab
2. Take heap snapshot
3. Play 50 games in a row
4. Take another heap snapshot
5. Compare memory usage

Pass criteria: Memory usage increases < 20MB over 50 games
```

---

## 🔒 Security Testing

### SQL Injection Tests
```
Test 1: Login form
Username: admin' OR '1'='1
Password: anything

Expected: Login fails, no SQL executed

Test 2: Registration
Username: '; DROP TABLE users; --
Email: test@test.com
Password: password

Expected: Username sanitized, no SQL executed
```

**Pass Criteria:** All SQL injection attempts blocked.

### XSS Tests
```
Test 1: Username field
Input: <script>alert('XSS')</script>

Expected: HTML escaped, displays as text, no alert

Test 2: Email field
Input: <img src=x onerror=alert('XSS')>

Expected: Tags stripped or escaped
```

**Pass Criteria:** No JavaScript execution from user input.

### Password Security Test
```sql
-- Check password hashing
SELECT password_hash FROM users WHERE username = 'testuser';

Expected format: $2y$10$...  (bcrypt hash, 60 chars)
Pass criteria: All passwords hashed, never plain text
```

---

## 📱 Responsive Design Testing

### Viewport Tests

#### Mobile (375px width)
- [ ] Navbar stacks vertically
- [ ] Puzzle board fits screen
- [ ] Tiles clickable on touch
- [ ] Modals scroll correctly
- [ ] Text readable without zoom

#### Tablet (768px width)
- [ ] Sidebar hides or stacks
- [ ] Grid maintains aspect ratio
- [ ] Buttons appropriately sized

#### Desktop (1920px width)
- [ ] Layout uses full width effectively
- [ ] No excessive empty space
- [ ] All elements visible without scroll

**Test browsers:**
- Chrome mobile emulator
- Firefox responsive design mode
- Real device testing (if available)

---

## ♿ Accessibility Testing

### Keyboard Navigation
- [ ] Tab through all interactive elements
- [ ] Enter key submits forms
- [ ] Escape closes modals
- [ ] Arrow keys navigate (optional)

### Screen Reader Test
- [ ] All images have alt text
- [ ] Form labels associated correctly
- [ ] ARIA labels on buttons
- [ ] Focus indicators visible

### Color Contrast
- [ ] Text meets WCAG AA standards (4.5:1 ratio)
- [ ] Buttons distinguishable
- [ ] Links clearly visible

---

## 🐛 Bug Tracking

### Critical Bugs (Must Fix)
- [ ] Game doesn't load
- [ ] Can't login/register
- [ ] Database connection fails
- [ ] Win detection broken
- [ ] Data loss on save

### High Priority Bugs
- [ ] Visual glitches
- [ ] Sound not playing
- [ ] Incorrect stats
- [ ] Achievement not unlocking

### Medium Priority Bugs
- [ ] Minor UI issues
- [ ] Non-critical animations
- [ ] Theme switching delay

### Low Priority Bugs
- [ ] Typos
- [ ] Minor alignment issues

---

## ✅ Pre-Submission Checklist

### Code Quality
- [ ] No console.log() in production code
- [ ] All functions commented
- [ ] No unused variables
- [ ] Consistent code style
- [ ] No hardcoded passwords

### Functionality
- [ ] All features from proposal implemented
- [ ] Database fully functional
- [ ] No critical bugs
- [ ] Tested in multiple browsers

### Documentation
- [ ] README.md complete
- [ ] Setup instructions accurate
- [ ] Code comments clear
- [ ] SQL schema documented

### Assets
- [ ] All images present
- [ ] Audio files included (or placeholder comments)
- [ ] No broken links
- [ ] Correct file paths

---

## 📊 Test Results Template

```
Test Date: __________
Tester: __________

PASSED TESTS:
- [ ] Database setup
- [ ] User registration
- [ ] User login
- [ ] Puzzle generation
- [ ] Tile movement
- [ ] Win detection
- [ ] Score calculation
- [ ] Achievement unlocking
- [ ] Power-up usage
- [ ] Story progression
- [ ] Theme switching
- [ ] Statistics display

FAILED TESTS:
(List any failures with details)

NOTES:
(Additional observations)

OVERALL STATUS: [PASS / FAIL]
```

---

## 🎓 Academic Integrity Check

Before submission:
- [ ] All code is original or properly attributed
- [ ] No plagiarism from other projects
- [ ] External libraries credited (if used)
- [ ] Collaboration documented (if any)
- [ ] Honor code statement signed

---

**Last Updated:** November 25, 2025  
**Testing Version:** 1.0
