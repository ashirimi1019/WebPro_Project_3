===============================================================================
JEOPARDY! BATTLE ARENA - README
===============================================================================

Project Name: Jeopardy! Battle Arena

Author: Ashir Imran
Course: CSC 4370/6370 - Web Programming (Fall 2025)

===============================================================================
PROJECT DESCRIPTION
===============================================================================

Jeopardy! Battle Arena is a turn-based quiz game web application inspired by 
the classic TV game show "Jeopardy!". The game supports 1 to 4 players 
competing by selecting question tiles from five different categories, 
answering questions to earn points, and competing in a Final Jeopardy round.

Key Features:
- Turn-based gameplay with 1-4 players
- 5 categories with 5 questions each (25 total questions)
- Point values: $100, $200, $300, $400, $500
- Daily Double challenges with wagering
- Final Jeopardy betting round
- Persistent leaderboard stored in a text file
- Blue and gold Jeopardy-themed interface
- PHP session-based state management
- No JavaScript - pure PHP/HTML/CSS implementation

===============================================================================
TECHNOLOGIES USED
===============================================================================

- HTML5 for structure
- CSS3 for styling and animations
- PHP for server-side logic and session management
- Flat file storage (text files) for data persistence

NO JavaScript is used in this project as per course requirements.

===============================================================================
FILE STRUCTURE
===============================================================================

proj2/
│
├── index.php               # Homepage and player setup
├── game.php                # Main game board and question handling
├── results.php             # Final Jeopardy and final results
├── leaderboard.php         # Persistent leaderboard display
│
├── assets/
│   └── styles.css          # All styling (blue and gold theme)
│
├── data/
│   ├── questions.php       # Question bank (5 categories)
│   └── leaderboard.txt     # Persistent score storage
│
└── docs/
    ├── README.txt          # This file
    └── design-notes.txt    # Technical design documentation

===============================================================================
HOW TO DEPLOY ON CODD SERVER
===============================================================================

1. Connect to GSU CODD server via SSH or SFTP:
   Server: codd.cs.gsu.edu
   Username: aimran6

2. Navigate to your public_html directory:
   cd ~/public_html

3. Create a proj2 directory if it doesn't exist:
   mkdir proj2

4. Upload all project files maintaining the folder structure:
   - Upload all .php files to proj2/
   - Upload styles.css to proj2/assets/
   - Upload questions.php and leaderboard.txt to proj2/data/
   - Upload documentation files to proj2/docs/

5. Set proper file permissions:
   chmod 755 proj2
   chmod 644 proj2/*.php
   chmod 755 proj2/assets
   chmod 644 proj2/assets/styles.css
   chmod 755 proj2/data
   chmod 644 proj2/data/questions.php
   chmod 666 proj2/data/leaderboard.txt  # Needs write permission

6. Access the application in a web browser:
   http://codd.cs.gsu.edu/~aimran6/proj2/

===============================================================================
HOW TO PLAY
===============================================================================

1. Open index.php in your web browser
2. Enter player names (1 to 4 players)
3. Click "Start Game" to begin
4. Players take turns selecting question tiles
5. Answer questions correctly to earn points
6. Watch for Daily Doubles to wager extra points
7. After all tiles are used, compete in Final Jeopardy
8. View the winner and check the leaderboard

===============================================================================
ADMIN FEATURES
===============================================================================

Leaderboard Reset:
- Navigate to leaderboard.php
- Click "Admin Controls" to expand the admin panel
- Enter password: admin123
- Click "Reset Leaderboard" to clear all records

===============================================================================
COURSE REQUIREMENTS MET
===============================================================================

Core Requirements:
✓ Turn-based answering system
✓ PHP session-based score tracking
✓ Category-based question board (5x5 grid)
✓ CSS-based Jeopardy-style layout (blue and gold theme)
✓ No JavaScript usage

Additional Features (3 of 4 required):
✓ Daily Double Challenges with wagering
✓ Final Jeopardy Betting Round
✓ Persistent Leaderboard via file I/O

===============================================================================
BROWSER COMPATIBILITY
===============================================================================

This application has been tested and works on:
- Google Chrome (recommended)
- Mozilla Firefox
- Microsoft Edge
- Safari

===============================================================================
TROUBLESHOOTING
===============================================================================

If leaderboard is not saving:
- Ensure leaderboard.txt has write permissions (chmod 666)
- Check that the data/ directory exists and is accessible

If session data is lost:
- Ensure PHP sessions are enabled on the server
- Check that the browser accepts cookies

If styles are not loading:
- Verify the assets/styles.css file exists
- Check file path in <link> tags is correct
- Clear browser cache and reload

===============================================================================
CONTACT INFORMATION
===============================================================================

Developer: Ashir Imran
Course: CSC 4370/6370 - Web Programming (Fall 2025)
Institution: Georgia State University

===============================================================================
