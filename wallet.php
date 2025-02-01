<?php
include 'connection.php'; // Include your database connection

session_start(); // Start the session to get user info

// Check if the user is logged in (i.e., user_id is set in session)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit(); // Stop execution
}

$user_id = $_SESSION['user_id']; // Assuming user ID is stored in the session

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the total balance from the wallet table for the logged-in user
$sql_wallet = "SELECT total_balance FROM wallet WHERE user_id = ?";

$stmt_wallet = $conn->prepare($sql_wallet);

if ($stmt_wallet === false) {
    die('MySQL prepare error: ' . $conn->error);
}

$stmt_wallet->bind_param("i", $user_id); // Bind user_id to the prepared statement
$stmt_wallet->execute(); // Execute the query

$result_wallet = $stmt_wallet->get_result();
$totalBalance = 0.00; // Default value if no balance is found

if ($row_wallet = $result_wallet->fetch_assoc()) {
    $totalBalance = $row_wallet['total_balance']; // Get total balance from the wallet
} else {
    echo "No wallet found for this user.";
}

// Close the statement and connection
$stmt_wallet->close();
$conn->close();

// Output the total balance
// echo "Total Balance: " . number_format($totalBalance, 2);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Base styles */
        body {
            font-family: Arial, sans-serif;
            max-width: 375px; /* Limiting width for mobile view on larger screens */
            margin: 0 auto;
            overflow-x: hidden;
            background-color: gainsboro;
        }

        .wallet-container {
            padding: 20px;
            overflow: hidden;
        }

        /* Total Balance Section */
        .total-balance {
            background-color: #ffc107;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .total-balance h2 {
            font-size: 36px;
            margin: 0;
        }
        .total-balance p {
            font-size: 18px;
            margin: 5px 0 0;
        }

        /* Box Section */
        .box-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }

        .box {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .box:hover {
            transform: translateY(-5px);
        }

        .box i {
            font-size: 40px;
            color: #007bff;
            margin-bottom: 10px;
        }

        .box h3 {
            font-size: 18px;
            margin: 10px 0 0;
            color: #333;
        }

        /* Top Navbar */
        .top-navbar {
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            display: flex;
            justify-content: space-around;
            align-items: center;
            background-color: #ffc107;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            padding: 10px 0;
        }

        .nav-item {
            text-align: center;
            color: white;
            text-decoration: none;
            flex: 1;
            transition: color 0.3s;
        }

        .nav-item i {
            font-size: 24px;
        }

        .nav-item:hover {
            color: #007bff;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .total-balance h2 {
                font-size: 28px;
            }
            .box-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .box-container {
                grid-template-columns: 1fr;
            }
        }
        .main{
            background-color:white; /* White background */
    height: auto; /* Full height of the screen */
    padding: 20px; /* Add some padding to the content */
    box-sizing: border-box; /* Ensure padding does not affect the width */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
        }
    </style>
</head>
<body>
<div class="main">
    <!-- Top Navbar -->
    <div class="top-navbar">
        <a href="index.php" class="nav-item">
            <i class="fas fa-home"></i>
        </a>
        <a href="#wallet" class="nav-item">
            <i class="fas fa-wallet"></i>
        </a>
        <a href="#customer-care" class="nav-item">
            <i class="fas fa-headset"></i>
        </a>
    </div>

    <!-- Wallet Page Content -->
    <div class="wallet-container">
        <!-- Total Balance Section -->
        <div class="total-balance">
            <h2>Total Balance</h2>
            <p>₹<?php echo number_format($totalBalance, 2); ?></p>
        </div>

        <!-- Box Section -->
        <div class="box-container">
            <div class="box">
                <i class="fas fa-wallet"></i>
                <a href="deposit.php" style="text-decoration:none;"><h3>Deposit</h3></a>
            </div>
            <div class="box">
                <i class="fas fa-money-bill-wave"></i>
                <a href="withdrawals.php" style="text-decoration:none;"><h3>Withdraw</h3></a>
            </div>
            <div class="box">
                <i class="fas fa-history"></i>
                <a href="deposit_history.php" style="text-decoration:none;"><h3>Deposit History</h3></a>
            </div>
            <div class="box">
                <i class="fas fa-receipt"></i>
                <a href="withdraw_history.php" style="text-decoration:none;"><h3>Withdraw History</h3></a>
            </div>
        </div>
    </div><br><br><br>
</div>
</body>
</html>
