<?php
session_start();
include 'connection.php'; // Your DB connection

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch total balance from wallet table
$sql_wallet = "SELECT total_balance FROM wallet WHERE user_id = ?";
$stmt_wallet = $conn->prepare($sql_wallet);
if ($stmt_wallet === false) {
    die('Error preparing wallet query: ' . $conn->error);
}
$stmt_wallet->bind_param("i", $user_id);
$stmt_wallet->execute();
$result_wallet = $stmt_wallet->get_result();
$totalBalance = 0.00; // Default balance
if ($row_wallet = $result_wallet->fetch_assoc()) {
    $totalBalance = $row_wallet['total_balance'];
}
$stmt_wallet->close();

// Handle form submission for withdrawal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = filter_var($_POST['amount'], FILTER_VALIDATE_FLOAT);
    $transaction_id = htmlspecialchars($_POST['transaction_id']);
    $mobile_number = htmlspecialchars($_POST['mobile_number']);

    if ($amount === false || $amount <= 0) {
        echo "Invalid withdrawal amount.";
        exit();
    }

    if ($amount > $totalBalance) {
        echo "Insufficient balance for withdrawal.";
        exit();
    }

    // Insert withdrawal request into database
    $sql_insert = "INSERT INTO withdrawals (user_id, amount, transaction_id, mobile_number, status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt_insert = $conn->prepare($sql_insert);
    if ($stmt_insert === false) {
        die('Error preparing withdrawal query: ' . $conn->error);
    }

    $stmt_insert->bind_param("idss", $user_id, $amount, $transaction_id, $mobile_number);
    if ($stmt_insert->execute()) {
        echo "Withdrawal request submitted successfully!";
        header("Location: withdrawals.php");
        exit();
    } else {
        echo "Error submitting withdrawal request. Please try again.";
    }
    $stmt_insert->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawals</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Base styles */
        body {
            font-family: Arial, sans-serif;
            max-width: 375px;
            margin: 0 auto;
            overflow-x: hidden;
            background-color: gainsboro;
        }

        .main {
            background-color: white;
            height: auto;
            padding: 20px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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

        /* Withdrawals Section */
        .withdrawals-container {
            padding: 20px;
            overflow: hidden;
        }

        /* Title Section */
        .withdrawals-header {
            background-color: #ffc107;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .withdrawals-header h2 {
            font-size: 36px;
            margin: 0;
        }

        .withdrawals-header p {
            font-size: 18px;
            margin: 5px 0 0;
        }

        /* Withdrawal Form Section */
        .withdrawal-form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .withdrawal-form h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .withdrawal-form label {
            font-size: 18px;
            display: block;
            margin-bottom: 8px;
        }

        .withdrawal-form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .withdrawal-form button {
            width: 100%;
            padding: 12px;
            background-color: #ffc107;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .withdrawal-form button:hover {
            background-color: #ff9800;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .withdrawals-header h2 {
                font-size: 28px;
            }

            .withdrawal-form h3 {
                font-size: 20px;
            }

            .withdrawal-form input {
                font-size: 14px;
            }

            .withdrawal-form button {
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .withdrawals-header h2 {
                font-size: 24px;
            }

            .withdrawal-form h3 {
                font-size: 18px;
            }
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
        <a href="wallet.php" class="nav-item">
            <i class="fas fa-wallet"></i>
        </a>
        <a href="#customer-care" class="nav-item">
            <i class="fas fa-headset"></i>
        </a>
    </div>

    <!-- Withdrawals Page Content -->
    <div class="withdrawals-container">
        <!-- Withdrawals Header -->
        <div class="withdrawals-header">
            <p>Your total available balance is ₹<?php echo number_format($totalBalance, 2); ?></p>
        </div>

        <!-- Withdrawal Form -->
        <div class="withdrawal-form">
            <h3>Enter Withdrawal Information</h3>
            <form action=" " method="POST">
                <!-- Amount to Withdraw -->
                <label for="amount">Amount to Withdraw (₹)</label>
                <input type="number" id="amount" name="amount" placeholder="Enter amount" required>

                <!-- Transaction ID -->
                <label for="transaction_id">Transaction ID</label>
                <input type="text" id="transaction_id" name="transaction_id" placeholder="Transaction ID" required>

                <!-- Mobile Number -->
                <label for="mobile_number">Mobile Number</label>
                <input type="text" id="mobile_number" name="mobile_number" placeholder="Your mobile number" required>

                <!-- Submit Button -->
                <button type="submit">Submit Withdrawal</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
