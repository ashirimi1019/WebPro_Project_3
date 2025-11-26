<?php
/**
 * Database Connection Test
 * Use this to verify your database setup is working
 */

// Include config
require_once 'php/config.php';

// Set content type
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .result {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success {
            color: #10b981;
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        .error {
            color: #ef4444;
            font-size: 1.5em;
            margin-bottom: 20px;
        }
        .info {
            background: #f0f9ff;
            padding: 15px;
            border-left: 4px solid #3b82f6;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #3b82f6;
            color: white;
        }
        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .btn:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="result">
        <h1>🗄️ Database Connection Test</h1>
        
        <?php
        try {
            // Try to get database instance
            $db = Database::getInstance();
            
            echo '<div class="success">✅ Database Connection Successful!</div>';
            
            // Display connection info
            echo '<div class="info">';
            echo '<strong>Connected to:</strong><br>';
            echo 'Host: ' . DB_HOST . '<br>';
            echo 'Database: ' . DB_NAME . '<br>';
            echo 'User: ' . DB_USER . '<br>';
            echo '</div>';
            
            // Get list of tables
            $stmt = $db->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            echo '<h2>📋 Database Tables (' . count($tables) . ' found)</h2>';
            
            if (count($tables) > 0) {
                echo '<table>';
                echo '<thead><tr><th>Table Name</th><th>Row Count</th></tr></thead>';
                echo '<tbody>';
                
                foreach ($tables as $table) {
                    $countStmt = $db->query("SELECT COUNT(*) FROM `$table`");
                    $count = $countStmt->fetchColumn();
                    echo "<tr><td>$table</td><td>$count rows</td></tr>";
                }
                
                echo '</tbody></table>';
                
                echo '<div class="info" style="margin-top: 20px;">';
                echo '<strong>Expected Tables:</strong> 9<br>';
                echo '<strong>Found Tables:</strong> ' . count($tables) . '<br>';
                
                $expectedTables = [
                    'users', 'puzzles', 'game_sessions', 'user_analytics',
                    'achievements', 'powerups_inventory', 'story_progress',
                    'story_chapters', 'daily_hints'
                ];
                
                $missingTables = array_diff($expectedTables, $tables);
                
                if (empty($missingTables)) {
                    echo '<strong style="color: #10b981;">✅ All required tables present!</strong>';
                } else {
                    echo '<strong style="color: #ef4444;">⚠️ Missing tables:</strong> ' . implode(', ', $missingTables);
                    echo '<br><em>Run sql/schema.sql to create missing tables</em>';
                }
                echo '</div>';
                
            } else {
                echo '<div class="error">⚠️ No tables found in database!</div>';
                echo '<p>Please run <code>sql/schema.sql</code> to create database tables.</p>';
            }
            
            // Test stored procedures
            echo '<h2>⚙️ Stored Procedures</h2>';
            $procStmt = $db->query("SHOW PROCEDURE STATUS WHERE Db = '" . DB_NAME . "'");
            $procedures = $procStmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($procedures) > 0) {
                echo '<table>';
                echo '<thead><tr><th>Procedure Name</th><th>Type</th></tr></thead>';
                echo '<tbody>';
                foreach ($procedures as $proc) {
                    echo '<tr><td>' . $proc['Name'] . '</td><td>Stored Procedure</td></tr>';
                }
                echo '</tbody></table>';
                echo '<p style="color: #10b981;">✅ Found ' . count($procedures) . ' stored procedures</p>';
            } else {
                echo '<p style="color: #ef4444;">⚠️ No stored procedures found. Run schema.sql to create them.</p>';
            }
            
            echo '<div class="info" style="margin-top: 30px; background: #d1fae5; border-color: #10b981;">';
            echo '<strong>🎉 Database is ready!</strong><br>';
            echo 'Your application should now work correctly.<br>';
            echo 'You can now register users and play the game.';
            echo '</div>';
            
        } catch (PDOException $e) {
            echo '<div class="error">❌ Database Connection Failed!</div>';
            echo '<div class="info" style="border-color: #ef4444; background: #fee;">';
            echo '<strong>Error Message:</strong><br>';
            echo htmlspecialchars($e->getMessage());
            echo '</div>';
            
            echo '<h2>💡 Troubleshooting Tips:</h2>';
            echo '<ol>';
            echo '<li><strong>Check php/config.php</strong> - Verify DB_USER, DB_PASS, and DB_NAME are correct</li>';
            echo '<li><strong>Database exists?</strong> - Run: <code>CREATE DATABASE ' . DB_NAME . ';</code></li>';
            echo '<li><strong>User permissions?</strong> - Make sure user "' . DB_USER . '" has access to database</li>';
            echo '<li><strong>MySQL running?</strong> - Ensure MySQL service is active</li>';
            echo '</ol>';
            
        } catch (Exception $e) {
            echo '<div class="error">❌ Unexpected Error!</div>';
            echo '<div class="info" style="border-color: #ef4444; background: #fee;">';
            echo htmlspecialchars($e->getMessage());
            echo '</div>';
        }
        ?>
        
        <hr style="margin: 30px 0;">
        
        <h2>📚 Next Steps</h2>
        <ol>
            <li>If connection failed, check <code>php/config.php</code> settings</li>
            <li>If tables are missing, run <code>sql/schema.sql</code></li>
            <li>Once successful, try registering a user at <a href="login.html">login.html</a></li>
            <li>Play the game at <a href="index.html">index.html</a></li>
        </ol>
        
        <a href="login.html" class="btn">Go to Login Page →</a>
        <a href="index.html" class="btn">Go to Game →</a>
        
        <p style="margin-top: 30px; color: #666; font-size: 0.9em;">
            <strong>Note:</strong> Delete this file (test_db.php) before final submission for security.
        </p>
    </div>
</body>
</html>
