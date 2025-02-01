<?php
// Include database connection
include('connection.php');


// Start the session to access user data
session_start();

// Check if the user is logged in (user_id should be stored in session)
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];  // Get the user_id from the session

    // Fetch orders for the logged-in user
    $order_sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
    $order_result = mysqli_query($conn, $order_sql);

    // Check for query errors
    if (!$order_result) {
        echo "Error executing query: " . mysqli_error($conn);
        exit();
    }

    if (mysqli_num_rows($order_result) == 0) {
        echo "No orders found for this user.";
        exit();
    }
} else {
    echo "User is not logged in.";
    exit();
}

// Ensure that POST data is set before proceeding
if (isset($_POST['user_id']) && isset($_POST['order_id']) && isset($_POST['order_amount'])) {
    // Assuming the order details and user info are passed via POST
    $user_id = $_POST['user_id'];  // Get the user ID from the order submission
    $order_id = $_POST['order_id'];  // Order ID should be passed from the order details
    $order_amount = $_POST['order_amount'];  // Get order amount from the order details

    // Step 1: Get the referral level and commission percentage for the user's referrer
    $sql = "SELECT referral_level FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($referral_level);
    $stmt->fetch();
    $stmt->close();

    // Step 2: Get the commission percentage from the referral_levels table
    $sql = "SELECT commission_percentage FROM referral_levels WHERE level = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }
    $stmt->bind_param("i", $referral_level);
    $stmt->execute();
    $stmt->bind_result($commission_percentage);
    $stmt->fetch();
    $stmt->close();

    // Step 3: Calculate the commission based on the order amount
    $commission = ($commission_percentage / 100) * $order_amount;

    // Step 4: Update the user's total commission
    $sql = "UPDATE users SET total_commission = total_commission + ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo "Error preparing statement: " . $conn->error;
        exit();
    }
    $stmt->bind_param("di", $commission, $user_id);
    if ($stmt->execute()) {
        echo "Commission of $commission has been added to your balance!";
    } else {
        echo "Error updating total commission: " . $stmt->error;
    }
    $stmt->close();

}
if (isset($_POST['order_id']) && isset($_POST['order_amount'])) {
    // Get order details from POST
    $order_id = $_POST['order_id'];
    $order_amount = $_POST['order_amount'];

    // Step 1: Check if the order status is already approved
    $check_status_sql = "SELECT order_status FROM orders WHERE order_id = ?";
    if ($stmt = $conn->prepare($check_status_sql)) {
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->bind_result($order_status);
        $stmt->fetch();
        $stmt->close();

        // If the status is not approved, return an error
        if ($order_status !== 'Approved') {
            echo "Order status is not approved. Commissions cannot be processed.";
            exit();
        }
    } else {
        echo "Error checking order status: " . $conn->error;
        exit();
    }

    // Step 2: Fetch the referral code for the user who placed the order
    $sql = "SELECT referral_code, user_id FROM users WHERE id = (SELECT user_id FROM orders WHERE order_id = ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->bind_result($referral_code, $user_id);
        $stmt->fetch();
        $stmt->close();

        // Proceed to track the referral commissions
        if (!empty($referral_code)) {
            trackReferral($referral_code, $user_id, $order_amount);
        } else {
            echo "No referral code found for this user.";
        }
    } else {
        echo "Error preparing statement for fetching referral code: " . $conn->error;
        exit();
    }
} else {
    echo "Required data (order_id, order_amount) not received via POST.";
}

// Function to track referral commissions
function trackReferral($referral_code, $user_id, $order_amount) {
    global $conn;

    // Step 1: Find the user who referred the new user based on the referral code
    $stmt = $conn->prepare("SELECT id, referred_by FROM users WHERE invite_code = ?");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $referral_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $referred_by_user_id = $row['id'];
        $referral_level = 1;

        // Step 2: Track referral levels dynamically
        while ($referred_by_user_id != NULL) {
            $commission_percentage = getCommissionForLevel($referral_level);
            if ($commission_percentage > 0) {
                $commission_amount = calculateCommission($order_amount, $commission_percentage);

                // Insert commission data into the `commissions` table
                $stmt = $conn->prepare("INSERT INTO commissions (user_id, referrer_id, commission_amount, referral_level) VALUES (?, ?, ?, ?)");
                if (!$stmt) {
                    die("Error preparing statement for inserting commissions: " . $conn->error);
                }
                $stmt->bind_param("iiid", $referred_by_user_id, $user_id, $commission_amount, $referral_level);
                if (!$stmt->execute()) {
                    die("Error executing statement for inserting commissions: " . $stmt->error);
                }

                // Update the total commission for the referrer
                updateTotalCommission($referred_by_user_id, $commission_amount);
            }

            // Move to the next level in the chain
            $stmt = $conn->prepare("SELECT referred_by FROM users WHERE id = ?");
            if (!$stmt) {
                die("Error preparing statement for fetching referrer: " . $conn->error);
            }
            $stmt->bind_param("i", $referred_by_user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $referred_by_user_id = $row['referred_by'] ?? NULL;

            // Increment referral level
            $referral_level++;

            // Limit the referral chain to 10 levels
            if ($referral_level > 10) break;
        }
    } else {
        // Invalid referral code
        echo "Invalid referral code.";
    }
}

// Function to get the commission percentage for a given referral level
function getCommissionForLevel($level) {
    global $conn;

    // Query to fetch the commission percentage for the specific level
    $stmt = $conn->prepare("SELECT commission_percentage FROM referral_level WHERE level = ?");
    if (!$stmt) {
        die("Error preparing statement for getting commission percentage: " . $conn->error);
    }

    $stmt->bind_param("i", $level);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['commission_percentage'];
    } else {
        return 0; // Return 0 if no commission percentage is found for the level
    }
}

// Function to calculate the commission amount based on percentage
function calculateCommission($order_amount, $percentage) {
    return ($order_amount * $percentage) / 100;
}

// Function to update total commission for the user
function updateTotalCommission($user_id, $commission_amount) {
    global $conn;

    // Update the total commission for the user directly
    $stmt = $conn->prepare("UPDATE users SET total_commission = total_commission + ? WHERE id = ?");
    if (!$stmt) {
        die("Error preparing statement for updating total commission: " . $conn->error);
    }
    $stmt->bind_param("di", $commission_amount, $user_id);
    $stmt->execute();
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .order-detail { margin-top: 20px; }
        .order-header { background-color: #f8f9fa; padding: 10px; margin-bottom: 10px; }
        .order-item { padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .order-item p { margin-bottom: 5px; }
        .status-pending { color: orange; }
        .status-approved { color: #007bff; }
        .status-shipped { color: #17a2b8; }
        .status-delivered { color: #28a745; }
        .status-cancelled { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="my-4">Order History</h2>
        <?php while ($order = mysqli_fetch_assoc($order_result)): ?>
            <div class="order-detail">
                <div class="order-header">
                    <h4>Order ID: <?php echo $order['order_id']; ?></h4>
                    <p><strong>Product:</strong> <?php echo $order['product_name']; ?></p>
                    <p><strong>Customer Name:</strong> <?php echo $order['customer_name']; ?></p>
                    <p><strong>Phone Number:</strong> <?php echo $order['phone']; ?></p>
                    <p><strong>UTR Number:</strong> <?php echo $order['utr_number']; ?></p>
                    <p><strong>Order Status:</strong> 
                        <?php
                        switch ($order['order_status']) {
                            case 'Pending':
                                echo '<span class="status-pending">Pending</span>';
                                break;
                            case 'Approved':
                                echo '<span class="status-approved">Approved</span>';
                                break;
                            case 'Shipped':
                                echo '<span class="status-shipped">Shipped</span>';
                                break;
                            case 'Delivered':
                                echo '<span class="status-delivered">Delivered</span>';
                                break;
                            case 'Cancelled':
                                echo '<span class="status-cancelled">Cancelled</span>';
                                break;
                            default:
                                echo 'Unknown Status';
                                break;
                        }
                        ?>
                    </p>
                </div>
                <div class="order-item">
                    <h5>Transaction Image:</h5>
                    <?php if ($order['transaction_image']): ?>
                        <img src="<?php echo $order['transaction_image']; ?>" alt="Transaction Image" class="img-fluid" style="max-width: 300px;">
                    <?php else: ?>
                        <p>No transaction image uploaded.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
