<?php
session_start();
include 'connection.php'; // Your DB connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in first.";
    exit();
}

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Verify the user_id exists in the 'users' table
$sql_get_user = "SELECT id, phone_number FROM users WHERE id = $user_id";
$result = $conn->query($sql_get_user);

if ($result && $result->num_rows > 0) {
    // User exists, fetch user data
    $user = $result->fetch_assoc();
    $id = $user['id'];
    $phone_number = $user['phone_number'];

    // Now check if the user already has a wallet
    $sql_check_wallet = "SELECT id FROM wallet WHERE user_id = $user_id";
    $wallet_result = $conn->query($sql_check_wallet);

    if ($wallet_result && $wallet_result->num_rows == 0) {
        // No wallet found, create a new wallet entry
        $sql_create_wallet = "INSERT INTO wallet (user_id, total_balance) VALUES ($user_id, 0)";
        
        if ($conn->query($sql_create_wallet) === TRUE) {
            echo "Wallet created successfully for user ID $user_id.";
        } else {
            echo "Error: Could not create wallet. " . $conn->error;
        }
    } else {
        echo "User already has a wallet.";
    }
} else {
    echo "User not found.";
}

// Close the DB connection
$conn->close();
?>
