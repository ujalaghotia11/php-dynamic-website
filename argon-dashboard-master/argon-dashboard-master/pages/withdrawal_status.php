<?php
session_start();
include '../../../connection.php'; // Database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ensure the connection is established
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the 'approve_id' is passed in the URL
if (!isset($_GET['approve_id'])) {
    die("No withdrawal ID provided.");
}

$withdrawal_id = $_GET['approve_id'];

// Fetch the withdrawal request details
$sql_withdrawal = "SELECT amount, user_id FROM withdrawals WHERE id = ?";
$stmt_withdrawal = $conn->prepare($sql_withdrawal);

if (!$stmt_withdrawal) {
    die("Error preparing SQL: " . $conn->error);
}

$stmt_withdrawal->bind_param("i", $withdrawal_id);
$stmt_withdrawal->execute();
$stmt_withdrawal->bind_result($amount, $user_id);
$stmt_withdrawal->fetch();
$stmt_withdrawal->close();

// Check if the withdrawal exists
if (!$amount || !$user_id) {
    die("Withdrawal request not found.");
}

// Fetch user's wallet balance
$sql_wallet = "SELECT total_balance FROM wallet WHERE user_id = ?";
$stmt_wallet = $conn->prepare($sql_wallet);

if (!$stmt_wallet) {
    die("Error preparing SQL: " . $conn->error);
}

$stmt_wallet->bind_param("i", $user_id);
$stmt_wallet->execute();
$stmt_wallet->bind_result($user_balance);
$stmt_wallet->fetch();
$stmt_wallet->close();

// Verify if the user has enough balance
if ($user_balance < $amount) {
    die("Insufficient balance in user's wallet.");
}

// Update the withdrawal status to 'approved'
$sql_approve = "UPDATE withdrawals SET status = 'approved' WHERE id = ?";
$stmt_approve = $conn->prepare($sql_approve);

if (!$stmt_approve) {
    die("Error preparing SQL: " . $conn->error);
}

$stmt_approve->bind_param("i", $withdrawal_id);
if ($stmt_approve->execute()) {
    // Deduct the amount from the user's wallet balance
    $new_balance = $user_balance - $amount;
    $sql_update_wallet = "UPDATE wallet SET total_balance = ? WHERE user_id = ?";
    $stmt_update_wallet = $conn->prepare($sql_update_wallet);

    if (!$stmt_update_wallet) {
        die("Error preparing SQL: " . $conn->error);
    }

    $stmt_update_wallet->bind_param("di", $new_balance, $user_id);
    $stmt_update_wallet->execute();
    $stmt_update_wallet->close();

    echo "Withdrawal approved and wallet updated successfully!";
    // Redirect to pending withdrawals page (optional)
    header("Location: payment_method.php");
    exit();
} else {
    echo "Error: Could not approve the withdrawal.";
}

$stmt_approve->close();
$conn->close();
?>
