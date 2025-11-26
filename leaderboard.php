<?php
/**
 * Jeopardy! Battle Arena - Leaderboard
 * 
 * Author: Ashir Imran
 * Course: CSC 4370/6370 - Web Programming (Fall 2025)
 * 
 * This page displays the persistent leaderboard from file storage
 * and allows admin to reset the leaderboard.
 */

session_start();

$leaderboardFile = 'data/leaderboard.txt';
$message = '';
$error = '';

// Handle leaderboard reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset') {
    $password = isset($_POST['admin_password']) ? $_POST['admin_password'] : '';
    
    if ($password === 'admin123') {
        // Reset the leaderboard by truncating the file
        file_put_contents($leaderboardFile, '');
        $message = 'Leaderboard has been reset successfully!';
    } else {
        $error = 'Incorrect admin password!';
    }
}

// Read leaderboard data
$leaderboardEntries = [];
if (file_exists($leaderboardFile)) {
    $lines = file($leaderboardFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $parts = explode(' | ', $line);
        if (count($parts) === 3) {
            $leaderboardEntries[] = [
                'name' => $parts[0],
                'score' => $parts[1],
                'date' => $parts[2]
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeopardy! Battle Arena - Leaderboard</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <header class="game-header">
            <h1>Jeopardy! Battle Arena</h1>
            <h2 class="leaderboard-title">Hall of Champions</h2>
        </header>
        
        <?php if (!empty($message)): ?>
            <div class="success-message">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
        
        <section class="leaderboard-section">
            <?php if (empty($leaderboardEntries)): ?>
                <div class="no-data-message">
                    <p>No games recorded yet. Be the first to compete!</p>
                </div>
            <?php else: ?>
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Winner</th>
                            <th>Score</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($leaderboardEntries as $index => $entry): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($entry['name']); ?></td>
                                <td><?php echo htmlspecialchars($entry['score']); ?></td>
                                <td><?php echo htmlspecialchars($entry['date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
        
        <section class="admin-section">
            <details class="admin-panel">
                <summary>Admin Controls</summary>
                <form method="POST" action="leaderboard.php" class="reset-form">
                    <input type="hidden" name="action" value="reset">
                    <div class="form-group">
                        <label for="admin_password">Admin Password:</label>
                        <input type="password" id="admin_password" name="admin_password" required>
                    </div>
                    <button type="submit" class="btn-danger">Reset Leaderboard</button>
                    <p class="help-text">Warning: This will permanently delete all leaderboard data!</p>
                </form>
            </details>
        </section>
        
        <div class="navigation-links">
            <a href="index.php" class="btn-primary">Play New Game</a>
            <a href="index.php" class="btn-secondary">Back to Home</a>
        </div>
        
        <footer class="game-footer">
            <p>Developed by: <strong>Ashir Imran</strong></p>
            <p>CSC 4370/6370 - Web Programming (Fall 2025)</p>
        </footer>
    </div>
</body>
</html>
