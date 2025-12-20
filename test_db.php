<?php
// Test script to check database connections
echo "Testing database connections...\n";

// Test allergypass_db connection
echo "Testing allergypass database connection...\n";
include 'allergypass/db_connect.php';

if ($conn) {
    echo "✓ Successfully connected to allergypass_db\n";
    
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "✓ 'users' table exists in allergypass_db\n";
    } else {
        echo "✗ 'users' table does not exist in allergypass_db\n";
    }
} else {
    echo "✗ Failed to connect to allergypass_db\n";
}

// Close connection
$conn->close();

// Test food_queue connection
echo "\nTesting food ordering system database connection...\n";
include 'food_ordering_system/db_connect.php';

if ($conn) {
    echo "✓ Successfully connected to food_queue\n";
    
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows > 0) {
        echo "✓ 'users' table exists in food_queue\n";
    } else {
        echo "✗ 'users' table does not exist in food_queue\n";
    }
} else {
    echo "✗ Failed to connect to food_queue\n";
}

$conn->close();
echo "\nDatabase test completed.\n";
?>