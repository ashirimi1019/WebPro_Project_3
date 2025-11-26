# 📋 DEVELOPMENT CHECKLIST
## Santa's Workshop - Implementation Guide

This document provides a step-by-step guide to implement the complete project.

---

## ✅ Phase 1: Database & Backend Setup (Est. 3 hours)

### Database Setup
- [x] Install MySQL 8.0+
- [ ] Open command-line MySQL: `mysql -u root -p`
- [ ] Execute schema.sql: `SOURCE /path/to/schema.sql;`
- [ ] Verify tables created: `SHOW TABLES;`
- [ ] Check story chapters seeded: `SELECT COUNT(*) FROM story_chapters;` (should be 7)
- [ ] Test stored procedures exist: `SHOW PROCEDURE STATUS WHERE Db = 'christmas_puzzle';`
- [ ] Create dedicated user (optional): See sql/setup.txt

### PHP Backend Configuration
- [ ] Update `php/config.php` with correct MySQL credentials
- [ ] Test database connection by visiting `php/api.php?action=health`
- [ ] Should return JSON: `{"success":true,"message":"API is running"}`

---

## ✅ Phase 2: Authentication System (Est. 2 hours)

### Registration Flow
- [ ] Open browser to `login.html`
- [ ] Fill registration form with test user
- [ ] Click "Create Account"
- [ ] Check success message appears
- [ ] Verify redirect to index.html
- [ ] **Database Test:** `SELECT * FROM users WHERE username='testuser';`
- [ ] **Database Test:** Verify analytics, powerups, hints, story tables populated

### Login Flow
- [ ] Click "Login here" link
- [ ] Enter test credentials
- [ ] Verify successful login
- [ ] Check session persists on page reload
- [ ] Test logout functionality
- [ ] Verify redirect back to login page

### Session Management
- [ ] Verify username displays in navbar
- [ ] Check skill level shows correctly
- [ ] Test protected page access (try accessing index.html while logged out)
- [ ] Should redirect to login.html automatically

---

## ✅ Phase 3: Core Puzzle Mechanics (Est. 4 hours)

### Puzzle Generation
- [ ] Click "New Game" button
- [ ] Verify puzzle generates without errors
- [ ] Check 4×4 grid displays
- [ ] Confirm one empty tile (bottom-right initially)
- [ ] Verify tiles are shuffled (not in order)

### Tile Movement
- [ ] Click tile adjacent to empty space
- [ ] Verify tile moves smoothly
- [ ] Check move counter increments
- [ ] Verify timer starts on first move
- [ ] Test clicking non-adjacent tile (should not move)
- [ ] Verify "movable" class highlights valid tiles

### Win Detection
- [ ] Solve a puzzle completely
- [ ] Verify victory modal appears
- [ ] Check confetti animation triggers
- [ ] Confirm victory sound plays
- [ ] Verify stats displayed (moves, time, score)

### Save Session
- [ ] After winning, check database: `SELECT * FROM game_sessions ORDER BY created_at DESC LIMIT 1;`
- [ ] Verify session saved with correct data
- [ ] Check user_analytics table updated: `SELECT * FROM user_analytics WHERE user_id=1;`
- [ ] Confirm skill_level in users table incremented (after 5 wins)

---

## ✅ Phase 4: Adaptive Difficulty (Est. 3 hours)

### Difficulty Progression
- [ ] Complete 3-5 puzzles in a row
- [ ] After each win, check difficulty level in game UI
- [ ] Verify difficulty increases as skill improves
- [ ] **Database Test:** `SELECT difficulty FROM game_sessions WHERE user_id=1 ORDER BY created_at DESC;`
- [ ] Confirm shuffle_depth increases with difficulty

### Performance Tracking
- [ ] Win multiple games quickly (under 60 seconds)
- [ ] Check if difficulty jumps faster
- [ ] Intentionally lose a game (abandon)
- [ ] Start new game - verify difficulty doesn't spike too high
- [ ] Check skill_level updates: `SELECT skill_level FROM users WHERE user_id=1;`

---

## ✅ Phase 5: Hints & Power-ups (Est. 3 hours)

### Daily Hints
- [ ] Click "Use Hint" button
- [ ] Verify hint tile highlights
- [ ] Check hint counter decrements
- [ ] Use all 3 hints
- [ ] Try using 4th hint - should show "No hints remaining"
- [ ] **Database Test:** `SELECT hints_remaining FROM daily_hints WHERE user_id=1;`
- [ ] Change system date to tomorrow, restart - hints should reset to 3

### Power-up: Reveal Move
- [ ] Click "Reveal Move" power-up
- [ ] Verify tile highlights
- [ ] Check quantity decrements in UI
- [ ] **Database Test:** `SELECT quantity FROM powerups_inventory WHERE user_id=1 AND powerup_type='reveal_move';`

### Power-up: Swap Tiles
- [ ] Click "Swap Tiles" power-up
- [ ] Verify two tiles swap positions
- [ ] Check puzzle board updates
- [ ] Confirm quantity decrements

### Power-up: Freeze Timer
- [ ] Start puzzle and make a few moves
- [ ] Click "Freeze Timer" power-up
- [ ] Verify timer stops for 10 seconds
- [ ] After 10s, verify timer resumes
- [ ] Check quantity decrements

---

## ✅ Phase 6: Achievements (Est. 2 hours)

### Quick Solver Achievement
- [ ] Solve a puzzle in under 60 seconds
- [ ] Check if "Quick Solver" achievement appears in victory modal
- [ ] **Database Test:** `SELECT * FROM achievements WHERE user_id=1 AND achievement_type='quick_solver';`

### Perfect Run Achievement
- [ ] Solve puzzle without using hints or power-ups
- [ ] Verify "Perfect Run" unlocks

### Santa's Apprentice Achievement
- [ ] Win 3 puzzles in a row
- [ ] Check achievement unlock notification
- [ ] Verify power-ups granted: `SELECT * FROM powerups_inventory WHERE user_id=1;`

### Master Elf Achievement
- [ ] Complete 20 total puzzles
- [ ] Verify "Master Elf" achievement unlocks
- [ ] **Database Test:** `SELECT total_wins FROM user_analytics WHERE user_id=1;`

### View Achievements
- [ ] Go to profile page
- [ ] Check achievements section displays correctly
- [ ] Verify locked achievements show as locked
- [ ] Confirm unlocked achievements have checkmarks/icons

---

## ✅ Phase 7: Story Mode (Est. 2 hours)

### Story Unlocking
- [ ] Start with 0 wins - only Chapter 1 should be unlocked
- [ ] Win 3 puzzles
- [ ] Check if Chapter 2 unlocks in victory modal
- [ ] **Database Test:** `SELECT * FROM story_progress WHERE user_id=1;`
- [ ] Continue winning to unlock more chapters (7, 12, 18, 25, 30 wins)

### Story Viewing
- [ ] Click "View Story" button
- [ ] Verify story modal appears
- [ ] Check unlocked chapters display full content
- [ ] Verify locked chapters show unlock requirements
- [ ] Test chapter replay functionality

### Story Progress Display
- [ ] Check profile page shows "X of 7 chapters unlocked"
- [ ] Verify progress bar fills correctly
- [ ] Confirm percentage matches: (unlocked / 7) * 100

---

## ✅ Phase 8: Theme System (Est. 2 hours)

### Theme Switching
- [ ] Click "Change Theme" button
- [ ] Select "Snowy" theme
- [ ] Verify page colors/background change
- [ ] **Database Test:** `SELECT theme_preference FROM users WHERE user_id=1;`
- [ ] Reload page - theme should persist
- [ ] Switch to "Workshop" theme - verify changes
- [ ] Switch to "Reindeer" theme - verify changes

### Theme Persistence
- [ ] Log out after setting theme
- [ ] Log back in
- [ ] Verify theme is still applied
- [ ] Check API response includes theme: `/api.php?action=check_auth`

---

## ✅ Phase 9: Statistics & Profile (Est. 2 hours)

### Profile Page Display
- [ ] Navigate to profile.html
- [ ] Verify username, email, skill level display
- [ ] Check total games, wins, losses accurate
- [ ] Verify win rate calculates correctly: (wins / total) * 100
- [ ] Check best time displays
- [ ] Verify average stats show

### Game History
- [ ] Scroll to "Recent Games" table
- [ ] Verify last 20 games listed
- [ ] Check dates, times, scores display correctly
- [ ] Confirm completed games show ✓

### Leaderboard
- [ ] Check leaderboard table populates
- [ ] Create multiple test accounts and play games
- [ ] Verify players ranked by skill level, then wins

### Power-up Inventory
- [ ] Check power-up quantities match database
- [ ] Use power-ups in game
- [ ] Return to profile - verify counts updated

---

## ✅ Phase 10: Audio & Visual Polish (Est. 3 hours)

### Background Music
- [ ] Start game, verify music plays (may need user interaction first)
- [ ] Check music loops continuously
- [ ] Test music volume is appropriate (not too loud)
- [ ] Verify music stops on puzzle completion

### Sound Effects
- [ ] Click tile - should hear click sound
- [ ] Complete move - should hear move sound
- [ ] Win puzzle - should hear victory jingle
- [ ] Use power-up - should hear power-up sound

### Animations
- [ ] Verify snowfall animation displays on all pages
- [ ] Check tile movement is smooth (CSS transitions)
- [ ] Confirm confetti explodes on victory
- [ ] Test modal open/close animations
- [ ] Verify loading overlay appears during AJAX requests

---

## ✅ Phase 11: Security & Validation (Est. 2 hours)

### Input Validation
- [ ] Try registering with username < 3 characters - should fail
- [ ] Try registering with password < 6 characters - should fail
- [ ] Test invalid email format - should fail
- [ ] Try SQL injection in username field: `' OR '1'='1` - should sanitize
- [ ] Test XSS in username: `<script>alert('xss')</script>` - should escape

### Password Security
- [ ] Check database - passwords should be hashed (not plain text)
- [ ] Verify login with wrong password fails
- [ ] Confirm session cookie is httpOnly (check browser DevTools)

### API Security
- [ ] Try accessing protected endpoint without login: `/api.php?action=get_stats`
- [ ] Should return `{"success":false,"error":"Not authenticated"}`
- [ ] Verify CSRF protection (if implemented)

---

## ✅ Phase 12: Cross-Browser Testing (Est. 1 hour)

### Browser Compatibility
- [ ] Test in Google Chrome
- [ ] Test in Mozilla Firefox
- [ ] Test in Microsoft Edge
- [ ] Test in Safari (if on Mac)
- [ ] Verify layout consistent across browsers
- [ ] Check JavaScript works in all browsers

### Responsive Design
- [ ] Open browser DevTools
- [ ] Test on mobile viewport (375px width)
- [ ] Test on tablet viewport (768px width)
- [ ] Test on desktop (1920px width)
- [ ] Verify layout adapts properly

---

## ✅ Phase 13: Performance Optimization (Est. 1 hour)

### Load Time
- [ ] Open DevTools Network tab
- [ ] Reload page
- [ ] Check total page load time (should be < 3 seconds)
- [ ] Verify no 404 errors
- [ ] Confirm all assets load successfully

### Database Performance
- [ ] Run query: `EXPLAIN SELECT * FROM game_sessions WHERE user_id=1;`
- [ ] Verify indexes are being used
- [ ] Check query execution time (should be < 100ms)

---

## ✅ Phase 14: Final Testing & Polish (Est. 2 hours)

### End-to-End Test
- [ ] Register brand new account
- [ ] Complete entire game flow from start to finish
- [ ] Play 10 games in a row
- [ ] Verify all features work seamlessly
- [ ] Check no errors in browser console
- [ ] Confirm no PHP errors in server logs

### Code Quality
- [ ] Review all PHP files for proper comments
- [ ] Check JavaScript for console.log statements (remove if not needed)
- [ ] Verify all CSS is organized
- [ ] Remove any TODO comments

### Documentation
- [ ] Update README.md with any changes
- [ ] Verify setup instructions are accurate
- [ ] Check all file paths are correct
- [ ] Ensure code comments are clear

---

## ✅ Phase 15: Submission Preparation (Est. 1 hour)

### File Cleanup
- [ ] Remove any test files
- [ ] Delete unnecessary assets
- [ ] Clean up any backup files (.bak, ~)

### Zip Archive
- [ ] Create zip file of entire project
- [ ] Test extracting zip to new location
- [ ] Follow setup instructions from scratch
- [ ] Verify project works in new location

### Submission Checklist
- [ ] Project proposal (docs/proposal.md) completed
- [ ] README.md is comprehensive
- [ ] Database schema (sql/schema.sql) included
- [ ] All source code files present
- [ ] Documentation complete
- [ ] No sensitive data (passwords) in code

---

## 🎯 Success Criteria

### Must Have (100% Required)
- ✅ User registration and login work
- ✅ Puzzle generates and is solvable
- ✅ Adaptive difficulty functions
- ✅ Database stores all game data
- ✅ All 4 custom features implemented (themes, achievements, power-ups, story)
- ✅ Command-line SQL setup documented

### Should Have (Recommended)
- ✅ Audio working properly
- ✅ Animations smooth
- ✅ Responsive design
- ✅ No console errors
- ✅ Clean code with comments

### Nice to Have (Bonus)
- ⭐ Extra achievements
- ⭐ Additional themes
- ⭐ Leaderboard enhancements
- ⭐ Social sharing features

---

## ⏱️ Time Tracking

Estimated total: **25 hours**

Recommended schedule:
- **Day 1 (4 hrs):** Phases 1-2 (Database & Auth)
- **Day 2 (5 hrs):** Phases 3-4 (Core Game & Difficulty)
- **Day 3 (5 hrs):** Phases 5-6 (Power-ups & Achievements)
- **Day 4 (4 hrs):** Phases 7-8 (Story & Themes)
- **Day 5 (4 hrs):** Phases 9-10 (Stats & Polish)
- **Day 6 (3 hrs):** Phases 11-15 (Testing & Submission)

---

**Last Updated:** November 25, 2025  
**Status:** Ready for Implementation
