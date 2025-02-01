<?php
// Include database connection
include 'connection.php';

// Start session
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: account.php"); // Redirect to login page
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Query to fetch user ID and total commission from the database
$sql = "SELECT id, total_commission FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $fetched_user_id = $user['id']; // Fetch the user's ID
    $bonus = $user['total_commission'] ?? 0; // Fetch total commission or default to 0 if null
} else {
    echo "User not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gaming Page - Total Bonus</title>
    <!-- Include Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom, #1f1c2c, #928dab);
            color: #ffffff;
            font-family: 'Arial', sans-serif;
        }
        .container {
            margin-top: 100px;
            text-align: center;
        }
        .card {
            background: rgba(0, 0, 0, 0.8);
            border: 1px solid #444;
            border-radius: 15px;
            padding: 30px;
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.6);
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
        .highlight {
            color: #ffb703;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 class="display-4">Welcome to the Gaming Arena</h1>
            <p class="lead">Hello, Player <span class="highlight">#<?php echo htmlspecialchars($fetched_user_id); ?></span></p>
            <p>Your Total Bonus: <span class="highlight"><?php echo number_format($bonus, 2); ?></span></p>
            <a href="activity.php" class="btn btn-custom mt-3">Back to Activity Page</a>
        </div>
    </div>
    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
