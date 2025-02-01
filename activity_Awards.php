<?php
// Include database connection
include 'connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: account.php"); // Redirect to login page
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Function to get the user's VIP level based on total amount and order count
function getActivityAward($userTotalAmount, $userOrderCount, $conn) {
    // Get all VIP levels from the database dynamically
    $vipQuery = "SELECT * FROM vip_levels"; // Assuming vip_levels is the correct table name
    $result = mysqli_query($conn, $vipQuery);

    // Check if there are any VIP levels in the table
    if (mysqli_num_rows($result) > 0) {
        // Loop through each VIP level
        while ($row = mysqli_fetch_assoc($result)) {
            // Check if the user matches the criteria for the current VIP level
            if ($userTotalAmount >= $row['min_amount'] && $userTotalAmount <= $row['max_amount'] &&
                $userOrderCount >= $row['min_order_count'] && $userOrderCount <= $row['max_order_count']) {
                // Return the VIP level and associated data
                return [
                    'vip_level' => $row['vip_level'],  // e.g., 0, 1, 2, etc.
                    'monthly_salary' => $row['monthly_salary'],
                    'level_up_reward' => $row['level_up_reward']
                ];
            }
        }
    }
    return null;  // Return null if no level matches
}

// Get the user details from the database
$sql = "SELECT * FROM users WHERE id = '$user_id'"; // Query user details based on the user ID
$userResult = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($userResult);

// Get the total order amount and order count (you may need to calculate this dynamically from the orders table)
$userTotalAmount = $user['total_commission']; // Assuming the total_commission field represents total order amount
$userOrderCount = 20; // Example: You should calculate the order count from the orders table

// Get the activity award and VIP level details for the user based on their total amount and orders
$vipDetails = getActivityAward($userTotalAmount, $userOrderCount, $conn);

if ($vipDetails) {
    $newVipLevel = $vipDetails['vip_level'];
    $monthlySalary = $vipDetails['monthly_salary'];
    $levelUpReward = $vipDetails['level_up_reward'];
} else {
    $newVipLevel = "N/A";  // In case the user doesn't qualify for any level
    $monthlySalary = 0;
    $levelUpReward = "No Reward";
}

// Update the user's activity award in the database
$updateSql = "UPDATE users SET activity_award = '$levelUpReward' WHERE id = '$user_id'";
mysqli_query($conn, $updateSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Awards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1d;
            color: #fff;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            background: rgba(0, 0, 0, 0.8);
            border: none;
            border-radius: 15px;
            padding: 20px;
            color: #fff;
        }
        .btn-custom {
            background-color: #ffb703;
            color: #000;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 25px;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background-color: #ffaa00;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">VIP Activity Awards</h1>
        <div class="card text-center">
            <h3>Congratulations, Player!</h3>
            <p>You are now at <strong>VIP Level <?php echo $newVipLevel; ?></strong>.</p>
            <p>Your Monthly Salary: <strong>$<?php echo number_format($monthlySalary, 2); ?></strong></p>
            <p>Level-Up Reward: <strong><?php echo $levelUpReward; ?></strong></p>
            <a href="activity.php" class="btn btn-custom">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
