<?php
session_start();
include 'connection.php'; // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get logged-in user's ID

// Ensure the connection is established
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the user's deposit history from the deposits table
$sql = "SELECT id, amount, transaction_id, created_at, updated_at 
        FROM deposits WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing SQL: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the total amount deposited by the user
$sql_total = "SELECT SUM(amount) AS total_deposited FROM deposits WHERE user_id = ?";
$stmt_total = $conn->prepare($sql_total);

if (!$stmt_total) {
    die("Error preparing SQL: " . $conn->error);
}

$stmt_total->bind_param("i", $user_id);
$stmt_total->execute();
$stmt_total->bind_result($total_deposited);
$stmt_total->fetch();
$stmt_total->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit History</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <style>
        body {
            background-color: #f9f9f9;
            font-family: 'Arial', sans-serif;
        }
        .container {
            background-color: #fff;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        h3 {
            color: #ffcc00;
            text-align: center;
            font-size: 2rem;
            margin-bottom: 30px;
            text-transform: uppercase;
        }
        .total-deposited {
            font-size: 1.5rem;
            color: #ffb600;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }
        .table th {
            background-color: #ffcc00;
            color: white;
            text-align: center;
        }
        .table td {
            background-color: #fff6cc;
            text-align: center;
            vertical-align: middle;
        }
        .table-striped tbody tr:nth-child(odd) {
            background-color: #fff0b3;
        }
        .table-bordered {
            border: 2px solid #ffcc00;
        }
        .table td, .table th {
            text-align: center;
            padding: 10px;
        }
        .btn-view {
            background-color: #ffb600;
            color: white;
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-view:hover {
            background-color: #ff9f00;
        }
        @media (max-width: 768px) {
            h3 {
                font-size: 1.6rem;
            }
            .table td, .table th {
                font-size: 0.9rem;
                padding: 8px;
            }
        }
    </style>
</head>
<body>

<div class="container py-4">
    <h3>Deposit History</h3>
    <p class="total-deposited">Total Amount Deposited: ₹<?php echo number_format($total_deposited, 2); ?></p>
    
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Transaction ID</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>₹<?php echo number_format($row['amount'], 2); ?></td>
                    <td><?php echo $row['transaction_id']; ?></td>
                    <td><?php echo date("d-m-Y H:i:s", strtotime($row['created_at'])); ?></td>
                    <td><?php echo date("d-m-Y H:i:s", strtotime($row['updated_at'])); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php
// Close the prepared statement and connection
$stmt->close();
$conn->close();
?>
