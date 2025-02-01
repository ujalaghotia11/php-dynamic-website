<?php
include '../../../connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted to update referral levels
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_level'])) {
    $level_id = $_POST['update_level'];
    
    
    // Update the commission percentage for the given level
    $level_0 = $_POST['level_0'][$level_id];
    $level_1 = $_POST['level_1'][$level_id];
    $level_2 = $_POST['level_2'][$level_id];
    $level_3 = $_POST['level_3'][$level_id];
    $level_4 = $_POST['level_4'][$level_id];
    $level_5 = $_POST['level_5'][$level_id];
    $level_6 = $_POST['level_6'][$level_id];
    $level_7 = $_POST['level_7'][$level_id];

    // Update all referral commission values
    $stmt = $conn->prepare("UPDATE referral_level SET  level_0 = ?, level_1 = ?, level_2 = ?, level_3 = ?, level_4 = ?, level_5 = ?, level_6 = ?, level_7 = ? WHERE id = ?");
    $stmt->bind_param("ddddddddi", $level_0, $level_1, $level_2, $level_3, $level_4, $level_5, $level_6, $level_7, $level_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Referral level updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating referral level: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}

// Fetch all referral levels from the database
$sql = "SELECT * FROM referral_level ORDER BY level";
$result = $conn->query($sql);

// Check for SQL errors
if ($result === false) {
    die("Error fetching referral levels: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Modify Referral Levels</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Additional styling */
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
        input[type="text"] {
            width: 100px;
            padding: 5px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 6px 12px;
            font-size: 14px;
            border: none;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            border-radius: 4px;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modify Referral Levels</h1>
        
        <!-- Form to edit referral levels -->
        <form action="" method="POST">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>Referral Level</th>
                            
                            <th>Level 0</th>
                            <th>Level 1</th>
                            <th>Level 2</th>
                            <th>Level 3</th>
                            <th>Level 4</th>
                            <th>Level 5</th>
                            <th>Level 6</th>
                            <th>Level 7</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- PHP loop to display all referral levels -->
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>Level " . htmlspecialchars($row['level']) . "</td>";
                            
                                echo "<td><input type='text' name='level_0[" . $row['id'] . "]' value='" . htmlspecialchars($row['level_0']) . "' required></td>";
                                echo "<td><input type='text' name='level_1[" . $row['id'] . "]' value='" . htmlspecialchars($row['level_1']) . "' required></td>";
                                echo "<td><input type='text' name='level_2[" . $row['id'] . "]' value='" . htmlspecialchars($row['level_2']) . "' required></td>";
                                echo "<td><input type='text' name='level_3[" . $row['id'] . "]' value='" . htmlspecialchars($row['level_3']) . "' required></td>";
                                echo "<td><input type='text' name='level_4[" . $row['id'] . "]' value='" . htmlspecialchars($row['level_4']) . "' required></td>";
                                echo "<td><input type='text' name='level_5[" . $row['id'] . "]' value='" . htmlspecialchars($row['level_5']) . "' required></td>";
                                echo "<td><input type='text' name='level_6[" . $row['id'] . "]' value='" . htmlspecialchars($row['level_6']) . "' required></td>";
                                echo "<td><input type='text' name='level_7[" . $row['id'] . "]' value='" . htmlspecialchars($row['level_7']) . "' required></td>";
                                echo "<td><button type='submit' name='update_level' value='" . $row['id'] . "'>Update</button></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='11'>No referral levels found</td></tr>";
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
