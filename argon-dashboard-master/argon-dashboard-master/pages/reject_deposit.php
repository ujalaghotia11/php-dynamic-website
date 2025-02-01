<?php
session_start();
include '../../../connection.php'; // Include your DB connection

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("Please log in as an admin.");
}

// Get the deposit ID
$deposit_id = $_GET['id'];

// Update deposit status to 'rejected'
$sql_update_deposit = "UPDATE deposits SET status = 'rejected' WHERE id = ?";
$stmt_update_deposit = $conn->prepare($sql_update_deposit);
$stmt_update_deposit->bind_param("i", $deposit_id);

if ($stmt_update_deposit->execute()) {
    echo "Deposit rejected successfully.";
} else {
    echo "Error: Could not reject deposit.";
}
?>
