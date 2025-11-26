<?php
// Test database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'aimran6';
$pass = 'aimran6';
$db = 'aimran6';

echo "Testing MySQL connection...\n";
echo "Host: $host\n";
echo "User: $user\n";
echo "Database: $db\n\n";

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error . "\n");
    }
    
    echo "✓ Connected successfully!\n";
    echo "MySQL server version: " . $conn->server_info . "\n";
    
    // Test query
    $result = $conn->query("SHOW TABLES");
    echo "\nTables in database:\n";
    while ($row = $result->fetch_array()) {
        echo "  - " . $row[0] . "\n";
    }
    
    $conn->close();
    echo "\n✓ Everything works!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
