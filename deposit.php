<?php
session_start();
include 'connection.php'; // Your DB connection

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit(); // Stop execution
}

$user_id = $_SESSION['user_id']; // Assuming user ID is stored in the session

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize the user's balance to 0
$totalBalance = 0.00; 

// Fetch user's wallet balance
$sql_wallet = "SELECT total_balance FROM wallet WHERE user_id = ?";
$stmt_wallet = $conn->prepare($sql_wallet);
$stmt_wallet->bind_param("i", $user_id);
$stmt_wallet->execute();
$result_wallet = $stmt_wallet->get_result();

// If the user already has a wallet, fetch their balance
if ($row_wallet = $result_wallet->fetch_assoc()) {
    $totalBalance = $row_wallet['total_balance']; // Get total balance from the wallet
} else {
    // If user doesn't have a wallet, we can initialize it to 0
    $totalBalance = 0.00;
}

// Check if the form has been submitted and process the deposit
if (isset($_POST['submit_deposit'])) {
    // Sanitize and fetch form inputs
    $amount = floatval($_POST['amount']); // Ensure the amount is a float
    $transaction_id = $_POST['transaction_id'];
    $mobile = $_POST['mobile'];
    $transaction_image = $_FILES['transaction_image'];

    // Ensure that the file is uploaded
    if ($transaction_image['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/"; // Directory to store images
        $target_file = $target_dir . basename($transaction_image['name']);
        
        // Move the uploaded file to the target directory
        if (move_uploaded_file($transaction_image['tmp_name'], $target_file)) {
            // Prepare the SQL query for the deposit insert
            $sql_deposit = "INSERT INTO deposits (user_id, amount, transaction_id, status, transaction_image) 
                            VALUES (?, ?, ?, 'pending', ?)";
            $stmt_deposit = $conn->prepare($sql_deposit);
            $stmt_deposit->bind_param("idss", $user_id, $amount, $transaction_id, $target_file);

            // Execute the query to insert deposit record
            if ($stmt_deposit->execute()) {
                // Now update the user's wallet with the new deposit amount
                $newBalance = $totalBalance + $amount; // Calculate new balance

                // Check if the user already has a wallet entry
                if ($result_wallet->num_rows > 0) {
                    // Update the existing wallet entry
                    $sql_update_wallet = "UPDATE wallet SET total_balance = ? WHERE user_id = ?";
                    $stmt_update_wallet = $conn->prepare($sql_update_wallet);
                    $stmt_update_wallet->bind_param("di", $newBalance, $user_id);
                    if ($stmt_update_wallet->execute()) {
                        echo "Deposit request submitted successfully! Waiting for admin approval. Your new balance is ₹" . number_format($newBalance, 2);
                    } else {
                        echo "Error updating wallet balance: " . $stmt_update_wallet->error;
                    }
                } else {
                    // If no wallet exists, insert a new wallet entry for the user
                    $sql_insert_wallet = "INSERT INTO wallet (user_id, total_balance) VALUES (?, ?)";
                    $stmt_insert_wallet = $conn->prepare($sql_insert_wallet);
                    $stmt_insert_wallet->bind_param("id", $user_id, $newBalance);
                    if ($stmt_insert_wallet->execute()) {
                        echo "Deposit request submitted successfully! Waiting for admin approval. Your new balance is ₹" . number_format($newBalance, 2);
                    } else {
                        echo "Error inserting wallet: " . $stmt_insert_wallet->error;
                    }
                }
            } else {
                echo "Error: Could not submit deposit request. " . $stmt_deposit->error;
            }
        } else {
            echo "Error uploading image.";
        }
    } else {
        echo "Error: Please upload a valid transaction image.";
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
         body {
            font-family: Arial, sans-serif;
            max-width: 375px; /* Limiting width for mobile view on larger screens */
            margin: 0 auto;
            overflow-x: hidden;
            background-color: gainsboro;
        }
        .bg-yellow { background-color: #ffc107; }
        .text-warning { color: #ffc107; }
        .deposit-header, .total-amount { font-weight: bold; }
        .payment-method-container { background-color: #f1f5f9; border-radius: 8px; padding: 15px; }
        .payment-option {
            padding: 20px;
            border-radius: 8px;
            font-weight: bold;
            text-align: center;
            width: 48%;
            margin-bottom: 10px;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }
        .payment-option input[type="radio"] { display: none; }
        .payment-option:hover, .payment-option.details-visible { background-color: white; }
        .btn-yellow { background-color: #ffc107; color: #fff; font-weight: bold; border-radius: 8px; }
        .qr-code {
            max-width: 80px;
            max-height: 80px;
            margin-top: 10px;
            display: none;
        }
        .details-visible .qr-code { display: block; }
        .main{
            background-color:white;
            height:auto;
        }
    </style>
</head>
<body>
    <div class="main">
        <a href="wallet.php"><button><</button></a>
        <div class="container py-4">
            <div class="deposit-header bg-yellow text-center p-3 mb-3">
                <h5 class="text-white">Deposit</h5>
                <div class="total-amount text-white font-weight-bold">Total amount: <p>₹<?php echo number_format($totalBalance, 2); ?></p></div>
            </div>

            <form method="POST" action="deposit.php" enctype="multipart/form-data">
                <div class="payment-method-container mb-3">
                    <h6 class="font-weight-bold text-warning">Payment Method</h6>
                    <div class="d-flex flex-wrap justify-content-between">
                        <?php
                        // Fetch payment methods from the database (if any)
                        $paymentMethods = $conn->query("SELECT * FROM payment_methods"); // Assuming you have a payment_methods table
                        while ($method = $paymentMethods->fetch_assoc()): ?>
                            <label class="payment-option" id="payment_<?php echo htmlspecialchars($method['name']); ?>">
                                <input type="radio" name="payment_method" value="<?php echo htmlspecialchars($method['name']); ?>" 
                                       onchange="togglePaymentDetails('<?php echo htmlspecialchars($method['name']); ?>')" required>
                                <?php echo htmlspecialchars($method['name']); ?>
                                <?php if (!empty($method['qr_code'])): ?>
                                    <div><img src="<?php echo htmlspecialchars($method['qr_code']); ?>" class="qr-code" alt="QR Code"></div>
                                <?php endif; ?>
                            </label>
                        <?php endwhile; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold text-warning" for="amount-input">INR</label>
                    <input type="text" class="form-control" id="amount-input" name="amount" placeholder="Enter Amount" required>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold text-warning" for="transaction-id-input">Transaction ID</label>
                    <input type="text" class="form-control" id="transaction-id-input" name="transaction_id" placeholder="Enter Transaction ID" required>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold text-warning" for="Mobile-input">Mobile Number</label>
                    <input type="text" class="form-control" id="mobile-input" name="mobile" placeholder="Enter Mobile number" required>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold text-warning" for="transaction-image-input">Upload Transaction Image</label>
                    <input type="file" class="form-control-file" id="transaction-image-input" name="transaction_image" accept="image/*" required>
                </div>
                <button type="submit" name="submit_deposit" class="btn btn-yellow btn-block mt-3">Recharge</button>
            </form>
        </div>
    </div>
</body>
</html>

<script>
    function togglePaymentDetails(selectedMethod) {
        document.querySelectorAll('.payment-option').forEach(option => {
            option.classList.remove('details-visible');
        });
        const selectedOption = document.getElementById('payment_' + selectedMethod);
        if (selectedOption) {
            selectedOption.classList.add('details-visible');
        }
    }
</script>
