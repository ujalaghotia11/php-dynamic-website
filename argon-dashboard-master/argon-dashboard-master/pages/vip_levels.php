<?php
include '../../../connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted to update VIP levels
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_level'])) {
    $level_id = $_POST['update_level'];

    // Safely access POST data with default values
    $vip_level = isset($_POST['vip_level'][$level_id]) ? $_POST['vip_level'][$level_id] : null;
    $min_amount = isset($_POST['min_amount'][$level_id]) ? $_POST['min_amount'][$level_id] : null;
    $max_amount = isset($_POST['max_amount'][$level_id]) ? $_POST['max_amount'][$level_id] : null;
    $min_order_count = isset($_POST['min_order_count'][$level_id]) ? $_POST['min_order_count'][$level_id] : null;
    $max_order_count = isset($_POST['max_order_count'][$level_id]) ? $_POST['max_order_count'][$level_id] : null;
    $monthly_salary = isset($_POST['monthly_salary'][$level_id]) ? $_POST['monthly_salary'][$level_id] : null;
    $level_up_reward = isset($_POST['level_up_reward'][$level_id]) ? $_POST['level_up_reward'][$level_id] : null;
    $order_reward = isset($_POST['order_reward'][$level_id]) ? $_POST['order_reward'][$level_id] : null;

    // Validation: Ensure required fields are not null
    if ($vip_level !== null && $min_amount !== null && $max_amount !== null &&
        $min_order_count !== null && $max_order_count !== null && $monthly_salary !== null) {

        // Update query for VIP levels
        $stmt = $conn->prepare(
            "UPDATE vip_levels 
             SET vip_level = ?, min_amount = ?, max_amount = ?, 
                 min_order_count = ?, max_order_count = ?, monthly_salary = ?, 
                 level_up_reward = ?, order_reward = ? 
             WHERE id = ?"
        );
        $stmt->bind_param(
            "siiddiisi",
            $vip_level,
            $min_amount,
            $max_amount,
            $min_order_count,
            $max_order_count,
            $monthly_salary,
            $level_up_reward,
            $order_reward,
            $level_id
        );

        if ($stmt->execute()) {
            echo "<script>alert('VIP level updated successfully!');</script>";
        } else {
            echo "<script>alert('Error updating VIP level: " . $stmt->error . "');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Please fill out all required fields!');</script>";
    }
}

// Fetch all VIP levels from the database where VIP levels are between 0 and 7
$sql = "SELECT * FROM vip_levels WHERE vip_level BETWEEN 0 AND 7 ORDER BY id";
$result = $conn->query($sql);

// Check for SQL errors
if ($result === false) {
    die("Error fetching VIP levels: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Modify VIP Levels</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table th, table td {
            text-align: center;
            vertical-align: middle;
        }
        input[type="text"], input[type="number"] {
            width: 120px;
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 6px 12px;
            font-size: 14px;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modify VIP Levels</h1>
        <form action="" method="POST">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>VIP Level</th>
                            <th>Min Amount</th>
                            <th>Max Amount</th>
                            <th>Min Order Count</th>
                            <th>Max Order Count</th>
                            <th>Monthly Salary</th>
                            <th>Level Up Reward</th>
                            <th>Order Reward</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                                echo "<td><input type='number' name='vip_level[" . $row['id'] . "]' value='" . htmlspecialchars($row['vip_level']) . "' min='0' max='7' required></td>";
                                echo "<td><input type='number' name='min_amount[" . $row['id'] . "]' value='" . htmlspecialchars($row['min_amount']) . "' required></td>";
                                echo "<td><input type='number' name='max_amount[" . $row['id'] . "]' value='" . htmlspecialchars($row['max_amount']) . "' required></td>";
                                echo "<td><input type='number' name='min_order_count[" . $row['id'] . "]' value='" . htmlspecialchars($row['min_order_count']) . "' required></td>";
                                echo "<td><input type='number' name='max_order_count[" . $row['id'] . "]' value='" . htmlspecialchars($row['max_order_count']) . "' required></td>";
                                echo "<td><input type='number' step='0.01' name='monthly_salary[" . $row['id'] . "]' value='" . htmlspecialchars($row['monthly_salary']) . "' required></td>";
                                echo "<td><input type='text' name='level_up_reward[" . $row['id'] . "]' value='" . htmlspecialchars($row['level_up_reward']) . "'></td>";
                                echo "<td><input type='text' name='order_reward[" . $row['id'] . "]' value='" . htmlspecialchars($row['order_reward']) . "'></td>";
                                echo "<td><button type='submit' name='update_level' value='" . $row['id'] . "'>Update</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10'>No VIP levels found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
