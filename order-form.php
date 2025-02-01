<?php
// Include database connection
include('connection.php');

// Start the session to access user data
session_start();

// Check if the user is logged in (user_id should be stored in session)
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  // Get the user_id from the session

    // Ensure that the product_id is passed in the URL
    if (isset($_GET['product_id'])) {
        $product_id = $_GET['product_id'];  // Get the product_id from the URL

        // Fetch the product details based on the product_id
        $product_sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($product_sql);
        if ($stmt === false) {
            echo "Error preparing statement: " . $conn->error;
            exit();
        }
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
        
        if (!$product) {
            echo "Product not found!";
            exit();
        }

        // Set product name and order amount dynamically
        $product_name = $product['name'];  // Get product name from the database
        $order_amount = $product['price']; // Get product price for the order
    } else {
        echo "Product ID is missing!";
        exit();
    }

    // Fetch orders for the logged-in user using prepared statements
    $order_sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($order_sql);
    if (!$stmt) {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $order_result = $stmt->get_result();

    // Check for query errors
    if (!$order_result) {
        echo "Error executing query: " . $stmt->error;  // For debugging
        exit();
    }

    // Check if the query returned results
    // if (mysqli_num_rows($order_result) == 0) {
    //     echo "No orders found for this user.";
    //     exit();
    // }

    // Generate order ID dynamically (auto-increment is preferred)
    $order_id = null;  // Set to null for auto-increment if it's an auto-increment field
} else {
    echo "User is not logged in.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header text-center">
                    <?php if (isset($product)): ?>
                        <h3>Order Details for <span class="product-name"><?php echo htmlspecialchars($product['name']); ?></span></h3>
                    <?php else: ?>
                        <h3>Product not found</h3>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <form action="process-order.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="order_amount" value="<?php echo $order_amount; ?>">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <input type="hidden" name="product_name" value="<?php echo $product_name; ?>">

                        <div class="mb-3">
                            <label for="name" class="form-label">Your Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" name="phone" required>
                        </div>

                        <div class="mb-3">
                            <label for="utr_number" class="form-label">UTR Number</label>
                            <input type="text" class="form-control" name="utr_number" required>
                        </div>

                        <div class="mb-3">
                            <label for="transaction_image" class="form-label">Transaction Image</label>
                            <input type="file" class="form-control" name="transaction_image" accept="image/*" required>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-submit btn-lg">Submit Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
