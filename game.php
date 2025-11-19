<?php
/**
 * Jeopardy! Battle Arena - Main Game Board
 * 
 * Author: Ashir Imran
 * Course: CSC 4370/6370 - Web Programming (Fall 2025)
 * 
 * This page displays the Jeopardy board and handles question selection,
 * answer submission, Daily Doubles, and turn management.
 */

session_start();

// Check if game session is active
if (!isset($_SESSION['players']) || empty($_SESSION['players'])) {
    header('Location: index.php');
    exit();
}

// Load questions from data file
require_once 'data/questions.php';

// Initialize variables
$currentView = 'board'; // Can be: 'board', 'daily_double_wager', 'question', 'answer_result'
$selectedCategory = null;
$selectedValue = null;
$questionData = null;
$resultMessage = '';
$isDailyDouble = false;

// Check if all tiles are used - redirect to Final Jeopardy
$allUsed = true;
foreach ($_SESSION['used_tiles'] as $catTiles) {
    foreach ($catTiles as $used) {
        if (!$used) {
            $allUsed = false;
            break 2;
        }
    }
}
if ($allUsed) {
    header('Location: results.php');
    exit();
}

// Handle Daily Double wager submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_dd_wager') {
    $wager = isset($_POST['wager']) ? intval($_POST['wager']) : 0;
    $cat = intval($_POST['category']);
    $val = intval($_POST['value']);
    
    // Validate wager
    $currentPlayerScore = $_SESSION['scores'][$_SESSION['current_player']];
    $maxWager = max($currentPlayerScore, 500); // Minimum wager can be up to $500
    
    if ($wager < 0) {
        $wager = 0;
    }
    if ($wager > $maxWager) {
        $wager = $maxWager;
    }
    
    $_SESSION['dd_wager'] = $wager;
    $_SESSION['dd_category'] = $cat;
    $_SESSION['dd_value'] = $val;
    
    $currentView = 'question';
    $selectedCategory = $cat;
    $selectedValue = $val;
    $questionData = $categories[$cat]['questions'][$val];
    $isDailyDouble = true;
}

// Handle tile selection (initial click)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'select_tile') {
    $selectedCategory = intval($_POST['category']);
    $selectedValue = intval($_POST['value']);
    
    // Check if this is a Daily Double
    $isDailyDouble = false;
    foreach ($_SESSION['daily_doubles'] as $dd) {
        if ($dd['cat'] === $selectedCategory && $dd['val'] === $selectedValue) {
            $isDailyDouble = true;
            break;
        }
    }
    
    if ($isDailyDouble) {
        $currentView = 'daily_double_wager';
    } else {
        $currentView = 'question';
        $questionData = $categories[$selectedCategory]['questions'][$selectedValue];
    }
}

// Handle answer submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_answer') {
    $selectedCategory = intval($_POST['category']);
    $selectedValue = intval($_POST['value']);
    $userAnswer = trim($_POST['answer']);
    $isDailyDouble = isset($_POST['is_daily_double']) && $_POST['is_daily_double'] === '1';
    
    $questionData = $categories[$selectedCategory]['questions'][$selectedValue];
    $correctAnswer = $questionData['answer'];
    
    // Check if answer is correct (case-insensitive)
    $isCorrect = (strtolower($userAnswer) === strtolower($correctAnswer));
    
    // Calculate points
    if ($isDailyDouble) {
        $points = $_SESSION['dd_wager'];
    } else {
        $points = $questionData['value'];
    }
    
    // Update score
    $currentPlayerIndex = $_SESSION['current_player'];
    if ($isCorrect) {
        $_SESSION['scores'][$currentPlayerIndex] += $points;
        $resultMessage = "Correct! +" . ($isDailyDouble ? "$" : "$") . $points . " points!";
    } else {
        $_SESSION['scores'][$currentPlayerIndex] -= $points;
        $resultMessage = "Incorrect! The correct answer was: <strong>" . htmlspecialchars($correctAnswer) . "</strong>. -$" . $points . " points.";
    }
    
    // Mark tile as used
    $_SESSION['used_tiles'][$selectedCategory][$selectedValue] = true;
    
    // Move to next player
    $_SESSION['current_player'] = ($_SESSION['current_player'] + 1) % count($_SESSION['players']);
    
    // Clear Daily Double data if it was used
    if ($isDailyDouble) {
        unset($_SESSION['dd_wager']);
        unset($_SESSION['dd_category']);
        unset($_SESSION['dd_value']);
    }
    
    $currentView = 'answer_result';
}

// Get current player info
$currentPlayerIndex = $_SESSION['current_player'];
$currentPlayerName = $_SESSION['players'][$currentPlayerIndex];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeopardy! Battle Arena - Game Board</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <header class="game-header">
            <h1>Jeopardy! Battle Arena</h1>
            <p class="current-player">Current Player: <strong><?php echo htmlspecialchars($currentPlayerName); ?></strong></p>
        </header>
        
        <!-- Scoreboard -->
        <section class="scoreboard">
            <h2>Scores</h2>
            <div class="score-grid">
                <?php foreach ($_SESSION['players'] as $index => $playerName): ?>
                    <div class="score-item <?php echo $index === $currentPlayerIndex ? 'active-player' : ''; ?>">
                        <span class="player-name"><?php echo htmlspecialchars($playerName); ?>:</span>
                        <span class="player-score">$<?php echo $_SESSION['scores'][$index]; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
        
        <?php if ($currentView === 'board'): ?>
            <!-- Main Game Board -->
            <section class="game-board">
                <div class="board-grid">
                    <!-- Category Headers -->
                    <?php foreach ($categories as $catIndex => $category): ?>
                        <div class="category-header">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Question Tiles -->
                    <?php for ($valueIndex = 0; $valueIndex < 5; $valueIndex++): ?>
                        <?php foreach ($categories as $catIndex => $category): ?>
                            <?php $isUsed = $_SESSION['used_tiles'][$catIndex][$valueIndex]; ?>
                            <?php if ($isUsed): ?>
                                <div class="tile tile-used"></div>
                            <?php else: ?>
                                <form method="POST" action="game.php" class="tile-form">
                                    <input type="hidden" name="action" value="select_tile">
                                    <input type="hidden" name="category" value="<?php echo $catIndex; ?>">
                                    <input type="hidden" name="value" value="<?php echo $valueIndex; ?>">
                                    <button type="submit" class="tile tile-available">
                                        $<?php echo $category['questions'][$valueIndex]['value']; ?>
                                    </button>
                                </form>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endfor; ?>
                </div>
            </section>
            
        <?php elseif ($currentView === 'daily_double_wager'): ?>
            <!-- Daily Double Wager Screen -->
            <section class="daily-double-section">
                <div class="dd-announcement">
                    <h2 class="dd-title">DAILY DOUBLE!</h2>
                    <p class="dd-subtitle">You found a Daily Double, <?php echo htmlspecialchars($currentPlayerName); ?>!</p>
                    <p>Your current score: <strong>$<?php echo $_SESSION['scores'][$currentPlayerIndex]; ?></strong></p>
                </div>
                
                <form method="POST" action="game.php" class="wager-form">
                    <input type="hidden" name="action" value="submit_dd_wager">
                    <input type="hidden" name="category" value="<?php echo $selectedCategory; ?>">
                    <input type="hidden" name="value" value="<?php echo $selectedValue; ?>">
                    
                    <div class="form-group">
                        <label for="wager">Enter your wager:</label>
                        <input type="number" id="wager" name="wager" 
                               min="0" 
                               max="<?php echo max($_SESSION['scores'][$currentPlayerIndex], 500); ?>" 
                               value="<?php echo min($_SESSION['scores'][$currentPlayerIndex], 500); ?>" 
                               required>
                        <p class="help-text">Maximum wager: $<?php echo max($_SESSION['scores'][$currentPlayerIndex], 500); ?></p>
                    </div>
                    
                    <button type="submit" class="btn-primary">Submit Wager</button>
                </form>
            </section>
            
        <?php elseif ($currentView === 'question'): ?>
            <!-- Question Screen -->
            <section class="question-section">
                <?php if ($isDailyDouble): ?>
                    <div class="dd-indicator">
                        <p>Daily Double - Wager: <strong>$<?php echo $_SESSION['dd_wager']; ?></strong></p>
                    </div>
                <?php else: ?>
                    <div class="question-value">
                        <p>Value: <strong>$<?php echo $questionData['value']; ?></strong></p>
                    </div>
                <?php endif; ?>
                
                <div class="question-text">
                    <h2>Question:</h2>
                    <p><?php echo htmlspecialchars($questionData['text']); ?></p>
                </div>
                
                <form method="POST" action="game.php" class="answer-form">
                    <input type="hidden" name="action" value="submit_answer">
                    <input type="hidden" name="category" value="<?php echo $selectedCategory; ?>">
                    <input type="hidden" name="value" value="<?php echo $selectedValue; ?>">
                    <input type="hidden" name="is_daily_double" value="<?php echo $isDailyDouble ? '1' : '0'; ?>">
                    
                    <div class="form-group">
                        <label for="answer">Your Answer:</label>
                        <input type="text" id="answer" name="answer" maxlength="200" required autofocus>
                    </div>
                    
                    <button type="submit" class="btn-primary">Submit Answer</button>
                </form>
            </section>
            
        <?php elseif ($currentView === 'answer_result'): ?>
            <!-- Answer Result Screen -->
            <section class="result-section">
                <div class="result-message">
                    <p><?php echo $resultMessage; ?></p>
                </div>
                
                <form method="GET" action="game.php">
                    <button type="submit" class="btn-primary">Back to Board</button>
                </form>
            </section>
        <?php endif; ?>
        
        <footer class="game-footer">
            <form method="POST" action="index.php" style="display: inline;">
                <button type="submit" class="btn-secondary">Quit Game</button>
            </form>
        </footer>
    </div>
</body>
</html>
