<?php
/**
 * Jeopardy! Battle Arena - Homepage and Player Setup
 * 
 * Author: Ashir Imran
 * Course: CSC 4370/6370 - Web Programming (Fall 2025)
 * 
 * This page handles player registration and game initialization.
 * Uses PHP sessions to store player data and initialize game state.
 */

session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $players = [];
    $errors = [];
    
    // Collect player names from form
    for ($i = 1; $i <= 4; $i++) {
        $playerName = isset($_POST["player$i"]) ? trim($_POST["player$i"]) : '';
        if (!empty($playerName)) {
            $players[] = $playerName;
        }
    }
    
    // Validate: at least one player required
    if (count($players) === 0) {
        $errors[] = "At least one player name is required!";
    }
    
    // If valid, initialize game session
    if (empty($errors)) {
        // Store player names
        $_SESSION['players'] = $players;
        
        // Initialize scores (all start at 0)
        $_SESSION['scores'] = array_fill(0, count($players), 0);
        
        // Current player index (starts with player 0)
        $_SESSION['current_player'] = 0;
        
        // Track which tiles have been used (5 categories x 5 values = 25 tiles)
        // Initialize as false (not used)
        $_SESSION['used_tiles'] = array_fill(0, 5, array_fill(0, 5, false));
        
        // Mark specific tiles as Daily Doubles (category, value_index)
        // Let's make 2 Daily Doubles at random positions
        $_SESSION['daily_doubles'] = [
            ['cat' => 1, 'val' => 2],  // Science, $300
            ['cat' => 3, 'val' => 3]   // Movies, $400
        ];
        
        // Game is not in Final Jeopardy yet
        $_SESSION['final_jeopardy'] = false;
        
        // Redirect to game board
        header('Location: game.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeopardy! Battle Arena - Home</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <header class="welcome-header">
            <h1 class="game-title">Jeopardy! Battle Arena</h1>
            <p class="subtitle">Test Your Knowledge in the Ultimate Quiz Competition!</p>
        </header>
        
        <section class="rules-section">
            <h2>How to Play</h2>
            <div class="rules-content">
                <p><strong>Game Rules:</strong></p>
                <ul>
                    <li>1 to 4 players can compete in this turn-based quiz game.</li>
                    <li>Players take turns selecting question tiles from 5 categories.</li>
                    <li>Each tile has a point value: $100, $200, $300, $400, or $500.</li>
                    <li>Answer correctly to earn points; answer incorrectly to lose points.</li>
                    <li><strong>Daily Doubles:</strong> Some tiles are Daily Doubles! You can wager up to your current score.</li>
                    <li><strong>Final Jeopardy:</strong> After all tiles are used, compete in a final betting round!</li>
                    <li>The player with the highest score at the end wins!</li>
                </ul>
            </div>
        </section>
        
        <?php if (isset($errors) && count($errors) > 0): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <section class="player-setup">
            <h2>Enter Player Names</h2>
            <form method="POST" action="index.php" class="player-form">
                <div class="form-group">
                    <label for="player1">Player 1 (Required):</label>
                    <input type="text" id="player1" name="player1" maxlength="50" required>
                </div>
                
                <div class="form-group">
                    <label for="player2">Player 2 (Optional):</label>
                    <input type="text" id="player2" name="player2" maxlength="50">
                </div>
                
                <div class="form-group">
                    <label for="player3">Player 3 (Optional):</label>
                    <input type="text" id="player3" name="player3" maxlength="50">
                </div>
                
                <div class="form-group">
                    <label for="player4">Player 4 (Optional):</label>
                    <input type="text" id="player4" name="player4" maxlength="50">
                </div>
                
                <button type="submit" class="btn-primary">Start Game</button>
            </form>
        </section>
        
        <footer class="home-footer">
            <p>Developed by: <strong>Ashir Imran</strong></p>
            <p>CSC 4370/6370 - Web Programming (Fall 2025)</p>
            <p><a href="leaderboard.php" class="link-button">View Leaderboard</a></p>
        </footer>
    </div>
</body>
</html>
