<?php
include '../../../connection.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Check if the 'id' parameter is set in the URL for the approve action
if (isset($_GET['id'])) {
    $deposit_id = $_GET['id']; // Get the deposit ID from the URL

    // Step 1: Fetch deposit details
    $sql_get_deposit = "SELECT * FROM deposits WHERE id = ? AND user_id = ? AND status = 'pending'";
    $stmt_get_deposit = $conn->prepare($sql_get_deposit);
    
    if ($stmt_get_deposit === false) {
        die('Error preparing query: ' . $conn->error);
    }

    $stmt_get_deposit->bind_param("ii", $deposit_id, $user_id);
    $stmt_get_deposit->execute();
    $result = $stmt_get_deposit->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $deposit_amount = $row['amount'];
        $transaction_id = $row['transaction_id'];

        // Step 2: Update deposit status to 'approved' in the deposits table
        $sql_approve_deposit = "UPDATE deposits SET status = 'approved' WHERE id = ? AND user_id = ?";
        $stmt_approve_deposit = $conn->prepare($sql_approve_deposit);

        if ($stmt_approve_deposit === false) {
            die('Error preparing query: ' . $conn->error);
        }

        $stmt_approve_deposit->bind_param("ii", $deposit_id, $user_id);
        if ($stmt_approve_deposit->execute()) {
            // Step 3: Insert the approved deposit into the approved_deposits table
            $sql_insert_approved = "INSERT INTO approved_deposits (user_id, amount, transaction_id) VALUES (?, ?, ?)";
            $stmt_insert_approved = $conn->prepare($sql_insert_approved);

            if ($stmt_insert_approved === false) {
                die('Error preparing insert query: ' . $conn->error);
            }

            $stmt_insert_approved->bind_param("ids", $user_id, $deposit_amount, $transaction_id);
            if ($stmt_insert_approved->execute()) {
                // Step 4: Delete the deposit from the pending deposits table
                $sql_delete_deposit = "DELETE FROM deposits WHERE id = ? AND user_id = ?";
                $stmt_delete_deposit = $conn->prepare($sql_delete_deposit);

                if ($stmt_delete_deposit === false) {
                    die('Error preparing delete query: ' . $conn->error);
                }

                $stmt_delete_deposit->bind_param("ii", $deposit_id, $user_id);
                if ($stmt_delete_deposit->execute()) {
                    echo "Deposit approved, moved to approved deposits, and removed from pending list.";
                    // Redirect to the payment method page or wherever you need
                    header("Location: payment_method.php");
                    exit();
                } else {
                    echo "Error deleting deposit from pending list.";
                }

                $stmt_delete_deposit->close();
            } else {
                echo "Error moving deposit to approved deposits.";
            }

            $stmt_insert_approved->close();
        } else {
            echo "Error approving deposit: " . $conn->error;
        }

        $stmt_approve_deposit->close();
    } else {
        echo "Error: Deposit not found or already processed.";
    }

    $stmt_get_deposit->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
