<?php
/**
 * Jeopardy! Battle Arena - Final Jeopardy and Results
 * 
 * Author: Ashir Imran
 * Course: CSC 4370/6370 - Web Programming (Fall 2025)
 * 
 * This page handles Final Jeopardy betting round and displays final results.
 * Saves winner data to leaderboard file.
 */

session_start();

// Check if game session is active
if (!isset($_SESSION['players']) || empty($_SESSION['players'])) {
    header('Location: index.php');
    exit();
}

// Load questions for Final Jeopardy
require_once 'data/questions.php';

$currentPhase = 'wager'; // Can be: 'wager', 'question', 'results'
$errors = [];

// Determine current phase based on session data
if (isset($_SESSION['fj_wagers']) && !isset($_SESSION['fj_answers'])) {
    $currentPhase = 'question';
} elseif (isset($_SESSION['fj_answers'])) {
    $currentPhase = 'results';
}

// Handle wager submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_wagers') {
    $wagers = [];
    $valid = true;
    
    foreach ($_SESSION['players'] as $index => $playerName) {
        $wager = isset($_POST["wager_$index"]) ? intval($_POST["wager_$index"]) : 0;
        $currentScore = $_SESSION['scores'][$index];
        
        // Validate wager
        if ($wager < 0) {
            $wager = 0;
        }
        if ($wager > $currentScore) {
            $wager = $currentScore;
        }
        
        $wagers[$index] = $wager;
    }
    
    // Store wagers in session
    $_SESSION['fj_wagers'] = $wagers;
    $currentPhase = 'question';
}

// Handle answer submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_answers') {
    $answers = [];
    
    foreach ($_SESSION['players'] as $index => $playerName) {
        $answer = isset($_POST["answer_$index"]) ? trim($_POST["answer_$index"]) : '';
        $answers[$index] = $answer;
    }
    
    // Store answers in session
    $_SESSION['fj_answers'] = $answers;
    
    // Calculate final scores
    $correctAnswer = strtolower($finalJeopardy['answer']);
    
    foreach ($_SESSION['players'] as $index => $playerName) {
        $userAnswer = strtolower($answers[$index]);
        $wager = $_SESSION['fj_wagers'][$index];
        
        if ($userAnswer === $correctAnswer) {
            $_SESSION['scores'][$index] += $wager;
        } else {
            $_SESSION['scores'][$index] -= $wager;
        }
    }
    
    // Save to leaderboard
    saveToLeaderboard();
    
    $currentPhase = 'results';
}

/**
 * Save game results to leaderboard file
 */
function saveToLeaderboard() {
    $leaderboardFile = 'data/leaderboard.txt';
    
    // Find winner (highest score)
    $maxScore = max($_SESSION['scores']);
    $winnerIndex = array_search($maxScore, $_SESSION['scores']);
    $winnerName = $_SESSION['players'][$winnerIndex];
    
    // Format: Winner Name | Score | Date
    $timestamp = date('Y-m-d H:i:s');
    $entry = $winnerName . " | $" . $maxScore . " | " . $timestamp . "\n";
    
    // Append to file
    file_put_contents($leaderboardFile, $entry, FILE_APPEND);
}

/**
 * Get ranked players by score
 */
function getRankedPlayers() {
    $ranked = [];
    foreach ($_SESSION['players'] as $index => $playerName) {
        $ranked[] = [
            'name' => $playerName,
            'score' => $_SESSION['scores'][$index],
            'index' => $index
        ];
    }
    
    // Sort by score descending
    usort($ranked, function($a, $b) {
        return $b['score'] - $a['score'];
    });
    
    return $ranked;
}

// Handle "Play Again" button
if (isset($_GET['action']) && $_GET['action'] === 'play_again') {
    // Clear all session data
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeopardy! Battle Arena - Final Jeopardy</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <div class="container">
        <header class="game-header">
            <h1>Jeopardy! Battle Arena</h1>
            <h2 class="fj-header">Final Jeopardy!</h2>
        </header>
        
        <?php if ($currentPhase === 'wager'): ?>
            <!-- Wager Phase -->
            <section class="fj-wager-section">
                <div class="fj-intro">
                    <h3>Time to Place Your Wagers!</h3>
                    <p>Each player can wager up to their current score on the Final Jeopardy question.</p>
                </div>
                
                <div class="current-scores">
                    <h4>Current Scores:</h4>
                    <div class="score-list">
                        <?php foreach ($_SESSION['players'] as $index => $playerName): ?>
                            <div class="score-item">
                                <span class="player-name"><?php echo htmlspecialchars($playerName); ?>:</span>
                                <span class="player-score">$<?php echo $_SESSION['scores'][$index]; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <form method="POST" action="results.php" class="wager-form">
                    <input type="hidden" name="action" value="submit_wagers">
                    
                    <?php foreach ($_SESSION['players'] as $index => $playerName): ?>
                        <div class="form-group">
                            <label for="wager_<?php echo $index; ?>">
                                <?php echo htmlspecialchars($playerName); ?>'s Wager:
                            </label>
                            <input type="number" 
                                   id="wager_<?php echo $index; ?>" 
                                   name="wager_<?php echo $index; ?>" 
                                   min="0" 
                                   max="<?php echo $_SESSION['scores'][$index]; ?>" 
                                   value="<?php echo $_SESSION['scores'][$index]; ?>" 
                                   required>
                            <span class="help-text">Max: $<?php echo $_SESSION['scores'][$index]; ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <button type="submit" class="btn-primary">Submit Wagers</button>
                </form>
            </section>
            
        <?php elseif ($currentPhase === 'question'): ?>
            <!-- Question Phase -->
            <section class="fj-question-section">
                <div class="fj-category">
                    <h3>Category: <?php echo htmlspecialchars($finalJeopardy['category']); ?></h3>
                </div>
                
                <div class="fj-question">
                    <h4>Final Jeopardy Question:</h4>
                    <p class="question-text"><?php echo htmlspecialchars($finalJeopardy['text']); ?></p>
                </div>
                
                <div class="player-wagers">
                    <h4>Player Wagers:</h4>
                    <?php foreach ($_SESSION['players'] as $index => $playerName): ?>
                        <p><strong><?php echo htmlspecialchars($playerName); ?>:</strong> $<?php echo $_SESSION['fj_wagers'][$index]; ?></p>
                    <?php endforeach; ?>
                </div>
                
                <form method="POST" action="results.php" class="answer-form">
                    <input type="hidden" name="action" value="submit_answers">
                    
                    <?php foreach ($_SESSION['players'] as $index => $playerName): ?>
                        <div class="form-group">
                            <label for="answer_<?php echo $index; ?>">
                                <?php echo htmlspecialchars($playerName); ?>'s Answer:
                            </label>
                            <input type="text" 
                                   id="answer_<?php echo $index; ?>" 
                                   name="answer_<?php echo $index; ?>" 
                                   maxlength="200" 
                                   required>
                        </div>
                    <?php endforeach; ?>
                    
                    <button type="submit" class="btn-primary">Submit All Answers</button>
                </form>
            </section>
            
        <?php elseif ($currentPhase === 'results'): ?>
            <!-- Results Phase -->
            <section class="fj-results-section">
                <div class="correct-answer-box">
                    <h3>Correct Answer:</h3>
                    <p class="correct-answer"><?php echo htmlspecialchars($finalJeopardy['answer']); ?></p>
                </div>
                
                <div class="player-answers">
                    <h4>Player Answers:</h4>
                    <?php foreach ($_SESSION['players'] as $index => $playerName): ?>
                        <?php 
                            $userAnswer = $_SESSION['fj_answers'][$index];
                            $isCorrect = (strtolower($userAnswer) === strtolower($finalJeopardy['answer']));
                        ?>
                        <div class="answer-item <?php echo $isCorrect ? 'correct' : 'incorrect'; ?>">
                            <strong><?php echo htmlspecialchars($playerName); ?>:</strong> 
                            <?php echo htmlspecialchars($userAnswer); ?>
                            <?php echo $isCorrect ? '✓' : '✗'; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php $rankedPlayers = getRankedPlayers(); ?>
                <?php $winner = $rankedPlayers[0]; ?>
                
                <div class="winner-announcement">
                    <h2 class="winner-title">🏆 Winner: <?php echo htmlspecialchars($winner['name']); ?>! 🏆</h2>
                    <p class="winner-score">Final Score: $<?php echo $winner['score']; ?></p>
                </div>
                
                <div class="final-standings">
                    <h3>Final Standings:</h3>
                    <table class="standings-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Player</th>
                                <th>Final Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rankedPlayers as $rank => $player): ?>
                                <tr class="<?php echo $rank === 0 ? 'first-place' : ''; ?>">
                                    <td><?php echo $rank + 1; ?></td>
                                    <td><?php echo htmlspecialchars($player['name']); ?></td>
                                    <td>$<?php echo $player['score']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="results-actions">
                    <a href="leaderboard.php" class="btn-primary">View Leaderboard</a>
                    <a href="results.php?action=play_again" class="btn-secondary">Play Again</a>
                </div>
            </section>
        <?php endif; ?>
        
        <footer class="game-footer">
            <p>Developed by: <strong>Ashir Imran</strong></p>
        </footer>
    </div>
</body>
</html>
