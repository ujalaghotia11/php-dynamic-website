<?php
// Include database connection
include('connection.php');

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User is not logged in.";
    exit();
}

// Get the logged-in user ID
$user_id = $_SESSION['user_id'];

// Check if form data is received
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $customer_name = $_POST['name'];
    $phone = $_POST['phone'];
    $utr_number = $_POST['utr_number'];
    $order_amount = $_POST['order_amount'];
    $transaction_image = null;

    // Handle file upload
    if (isset($_FILES['transaction_image']) && $_FILES['transaction_image']['error'] === 0) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["transaction_image"]["name"]);
        $target_file = $target_dir . $file_name;

        // Validate file type and size
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png']) && $_FILES["transaction_image"]["size"] <= 5000000) {
            if (move_uploaded_file($_FILES["transaction_image"]["tmp_name"], $target_file)) {
                $transaction_image = $target_file;
            } else {
                echo "Error uploading file.";
                exit();
            }
        } else {
            echo "Invalid file type or size.";
            exit();
        }
    }

    // Insert data into the orders table
    $sql = "INSERT INTO orders (user_id, product_id, product_name, customer_name, phone, utr_number, transaction_image, order_status, order_amount) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("iisssssd", $user_id, $product_id, $product_name, $customer_name, $phone, $utr_number, $transaction_image, $order_amount);

        if ($stmt->execute()) {
            echo "Order submitted successfully!";
        } else {
            echo "Error submitting order: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "Invalid request method.";
}



// Start session to retrieve user_id from session



    // // Mark the order as approved (this step is assumed to be done after admin approves it)
    // // $order_status = 'Approved';

    // // Update order status to 'Approved'
    // $update_order_status_sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    // if ($stmt = $conn->prepare($update_order_status_sql)) {
    //     $stmt->bind_param("si", $order_status, $order_id);
    //     if ($stmt->execute()) {
    //         echo "Order approved successfully.";
    //     } else {
    //         echo "Error updating order status: " . $stmt->error;
    //     }
    //     $stmt->close();
    // } else {
    //     echo "Error preparing statement for order approval: " . $conn->error;
    //     exit();
    // }

    // Now, track the referral commission
    ?>