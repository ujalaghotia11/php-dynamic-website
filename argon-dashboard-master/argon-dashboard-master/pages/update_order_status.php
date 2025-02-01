<?php
// Include database connection
include('../../../connection.php');

function fetchOrderAndUser() {
    global $conn;

    // Query to get the next pending order
    $sql = "SELECT order_id, user_id FROM orders WHERE order_status = 'Pending' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return [
            'order_id' => $row['order_id'],
            'user_id' => $row['user_id']
        ];
    } else {
        // Exit when no more pending orders are found
        echo "No pending orders found.<br>";
        exit;
    }
}

// Function to approve the order and update user's progress and referral level
function processOrder($order_id, $user_id) {
    global $conn;

    // Fetch order amount
    $order_sql = "SELECT order_amount FROM orders WHERE order_id = '$order_id'";
    $order_result = mysqli_query($conn, $order_sql);

    if ($order_result && mysqli_num_rows($order_result) > 0) {
        $order_data = mysqli_fetch_assoc($order_result);
        $order_amount = $order_data['order_amount'];

        // Approve the order
        $update_order_sql = "UPDATE orders SET order_status = 'Approved' WHERE order_id = '$order_id'";
        if (mysqli_query($conn, $update_order_sql)) {
            echo "Order ID $order_id approved successfully.<br>";
           
            updateVIPLevel($user_id);
            updateReferralCommission($user_id, $order_amount);  // New function for referral commission

            // Update Referral Level based on Invite Code
            updateReferralLevel($user_id);
        } else {
            echo "Failed to approve the order: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "Order details not found.<br>";
        exit;
    }
}

function updateVIPLevel($user_id) {
    global $conn;

    // Fetch user stats (current VIP level, orders needed to next VIP, amount needed to next VIP)
    $user_sql = "SELECT vip_level, orders_needed_to_next_vip, amount_needed_to_next_vip 
                 FROM users WHERE id = '$user_id'";
    $user_result = mysqli_query($conn, $user_sql);

    if (!$user_result) {
        echo "Error fetching user stats: " . mysqli_error($conn) . "<br>";
        return;
    }

    if (mysqli_num_rows($user_result) > 0) {
        $user_data = mysqli_fetch_assoc($user_result);
        $current_vip_level = $user_data['vip_level'];

        // Fetch user orders stats (total orders and total order amount)
        $order_stats_sql = "SELECT COUNT(*) AS total_orders, SUM(order_amount) AS total_order_amount 
                            FROM orders WHERE user_id = '$user_id' AND order_status = 'Approved'";
        $order_stats_result = mysqli_query($conn, $order_stats_sql);

        if (!$order_stats_result) {
            echo "Error fetching user order stats: " . mysqli_error($conn) . "<br>";
            return;
        }

        if (mysqli_num_rows($order_stats_result) > 0) {
            $order_stats = mysqli_fetch_assoc($order_stats_result);
            $total_orders = $order_stats['total_orders'] ?? 0;
            $total_order_amount = $order_stats['total_order_amount'] ?? 0;

            // Fetch all VIP levels and determine the highest eligible level
            $vip_levels_sql = "SELECT * FROM vip_levels ORDER BY vip_level ASC";
            $vip_levels_result = mysqli_query($conn, $vip_levels_sql);

            if (!$vip_levels_result) {
                echo "Error fetching VIP levels: " . mysqli_error($conn) . "<br>";
                return;
            }

            if (mysqli_num_rows($vip_levels_result) > 0) {
                $eligible_vip_level = $current_vip_level;

                while ($vip_data = mysqli_fetch_assoc($vip_levels_result)) {
                    $vip_level = $vip_data['vip_level'];
                    $min_amount = $vip_data['min_amount'];
                    $max_amount = $vip_data['max_amount'];
                    $min_orders = $vip_data['min_order_count'];
                    $max_orders = $vip_data['max_order_count'];

                    // Check if the user qualifies for this VIP level
                    if (
                        $total_order_amount >= $min_amount &&
                        $total_order_amount <= $max_amount &&
                        $total_orders >= $min_orders &&
                        $total_orders <= $max_orders &&
                        $vip_level > $eligible_vip_level
                    ) {
                        $eligible_vip_level = $vip_level;
                    }
                }

                // Update the user's VIP level if they qualify for a higher level
                if ($eligible_vip_level > $current_vip_level) {
                    $update_user_sql = "UPDATE users SET 
                        vip_level = '$eligible_vip_level', 
                        amount_needed_to_next_vip = GREATEST(0, (IFNULL((SELECT max_amount FROM vip_levels WHERE vip_level = $eligible_vip_level), 0) - $total_order_amount)), 
                        orders_needed_to_next_vip = GREATEST(0, (IFNULL((SELECT max_order_count FROM vip_levels WHERE vip_level = $eligible_vip_level), 0) - $total_orders)) 
                        WHERE id = '$user_id'";

                    if (mysqli_query($conn, $update_user_sql)) {
                        echo "User ID $user_id upgraded to VIP Level $eligible_vip_level.<br>";
                    } else {
                        echo "Error updating VIP Level for User ID $user_id: " . mysqli_error($conn) . "<br>";
                    }
                } else {
                    echo "User ID $user_id remains at VIP Level $current_vip_level.<br>";
                }
            } else {
                echo "No VIP levels found in the database.<br>";
            }
        } else {
            echo "No approved orders found for User ID $user_id.<br>";
        }
    } else {
        echo "No user found with ID $user_id.<br>";
    }
}


function updateReferralCommission($user_id, $order_amount) {
    global $conn;

    // Step 1: Get the referral chain (who referred the current user and their chain)
    $referral_chain = [];
    $current_user_id = $user_id;
    
    // Collect all the referrers in the chain
    while ($current_user_id != 0) {  // Stop if there is no referrer
        // Step 2: Fetch the referral code for the current user
        $user_sql = "SELECT referral_code, vip_level FROM users WHERE id = '$current_user_id'";
        $user_result = mysqli_query($conn, $user_sql);
        
        if (!$user_result || mysqli_num_rows($user_result) == 0) {
            echo "No referral code found for User ID $current_user_id.<br>";
            return;
        }
        
        $user_data = mysqli_fetch_assoc($user_result);
        $referral_code = $user_data['referral_code'];
        $current_vip_level = $user_data['vip_level'];
        
        // Step 3: Find the referrer based on the referral code
        $referrer_sql = "SELECT id, vip_level FROM users WHERE invite_code = '$referral_code'";
        $referrer_result = mysqli_query($conn, $referrer_sql);

        if (!$referrer_result || mysqli_num_rows($referrer_result) == 0) {
            // No referrer found, stop the chain
            break;
        }

        $referrer_data = mysqli_fetch_assoc($referrer_result);
        $referrer_id = $referrer_data['id'];
        $referrer_vip_level = $referrer_data['vip_level'];

        // Store the referrer with their id, VIP level, and current VIP level of the user
        $referral_chain[] = [
            'referrer_id' => $referrer_id,
            'referrer_vip_level' => $referrer_vip_level,
            'user_vip_level' => $current_vip_level
        ];
        
        // Move up the referral chain
        $current_user_id = $referrer_id;
    }

    // Step 4: Check the referral chain
    if (empty($referral_chain)) {
        echo "No referral chain found for User ID $user_id.<br>";
        return;
    }

    // Step 5: Fetch commission percentages from the referral_level table
    $commission_sql = "SELECT * FROM referral_level"; // Get all levels commission data
    $commission_result = mysqli_query($conn, $commission_sql);

    if (!$commission_result || mysqli_num_rows($commission_result) == 0) {
        echo "No commission levels found.<br>";
        return;
    }

    $commission_data = [];
    while ($row = mysqli_fetch_assoc($commission_result)) {
        $commission_data[$row['level']] = $row;
    }

    // Step 6: Calculate and distribute commissions based on the referral chain
    foreach ($referral_chain as $index => $referrer_data) {
        $referrer_id = $referrer_data['referrer_id'];
        $referrer_vip_level = $referrer_data['referrer_vip_level'];
        $user_vip_level = $referrer_data['user_vip_level'];

        // Step 7: Fetch the correct commission percentage for this referrer based on their VIP level and the level in the referral chain
        $level = $index + 1; // Referral level starts from 1
        if (isset($commission_data[$level])) {
            // Get commission percentage for the given referral level
            $commission_percentage = $commission_data[$level]['level_' . $referrer_vip_level] ?? 0;
        } else {
            $commission_percentage = 0;
        }

        // Debugging: Output the calculated commission percentage
        echo "Level " . $level . ": Referrer ID $referrer_id (VIP Level $referrer_vip_level) => Commission Percentage: $commission_percentage%<br>";

        // Step 8: Calculate the referral commission for the referrer
        $referral_commission = ($order_amount * $commission_percentage) / 100;

        if ($referral_commission > 0) {
            // Step 9: Update the total commission for the referrer
            $update_commission_sql = "UPDATE users SET total_commission = total_commission + $referral_commission WHERE id = '$referrer_id'";

            if (mysqli_query($conn, $update_commission_sql)) {
                echo "Order commission of $referral_commission added to Referrer ID $referrer_id for User ID $user_id.<br>";
            } else {
                echo "Error updating referral commission for Referrer ID $referrer_id: " . mysqli_error($conn) . "<br>";
            }
        }
    }
}

// Update referral level
function updateReferralLevel($user_id) {
    global $conn;

    $user_sql = "SELECT referral_code FROM users WHERE id = '$user_id'";
    $user_result = mysqli_query($conn, $user_sql);

    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user_data = mysqli_fetch_assoc($user_result);
        $referral_code = $user_data['referral_code'];

        $referral_count_sql = "SELECT COUNT(*) AS total_referrals FROM users WHERE referral_code = '$referral_code'";
        $referral_count_result = mysqli_query($conn, $referral_count_sql);

        if ($referral_count_result && mysqli_num_rows($referral_count_result) > 0) {
            $referrals = mysqli_fetch_assoc($referral_count_result)['total_referrals'];
            $level = $referrals >= 10 ? 'Level_1' : ($referrals >= 5 ? 'Level_2' : 'Level_3');

            $update_sql = "UPDATE users SET referral_level = '$level' WHERE id = '$user_id'";
            mysqli_query($conn, $update_sql);
            echo "Updated referral level for User ID $user_id to $level.<br>";
        }
    }
}

// Main loop
while (true) {
    $order_details = fetchOrderAndUser();
    processOrder($order_details['order_id'], $order_details['user_id']);
}

// Main logic for handling POST request to approve the order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $user_id = $_POST['user_id'];  // Retrieve user_id from the POST request

    if ($order_status === 'Approved') {
        processOrder($order_id, $user_id);  // Pass user_id to the function
    }
}
?>
