<?php
// db_connection.php - connection to the database
include '../../../connection.php';

// Check if the form is submitted to update referral levels
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_level'])) {
    // Iterate through each level's commission percentages
    foreach ($_POST['commission_percentage'] as $id => $commission_percentage) {
        // Sanitize and validate the commission percentage
        $commission_percentage = filter_var($commission_percentage, FILTER_VALIDATE_FLOAT);

        if ($commission_percentage !== false) {
            // Prepare SQL query to update the commission percentage
            $stmt = $conn->prepare("UPDATE referral_level SET commission_percentage = ? WHERE id = ?");
            $stmt->bind_param('di', $commission_percentage, $id);

            // Execute the query and check for success
            if ($stmt->execute()) {
                echo "Referral level updated successfully for ID: $id<br>";
            } else {
                echo "Error updating level ID: $id<br>";
            }
            $stmt->close();
        } else {
            echo "Invalid commission percentage for level ID: $id<br>";
        }

        // Handle the other level commissions (level_0 to level_7)
        $level_columns = ['level_0', 'level_1', 'level_2', 'level_3', 'level_4', 'level_5', 'level_6', 'level_7'];
        $update_values = [];

        foreach ($level_columns as $column) {
            if (isset($_POST[$column][$id])) {
                $level_value = filter_var($_POST[$column][$id], FILTER_VALIDATE_FLOAT);
                if ($level_value !== false) {
                    $update_values[$column] = $level_value;
                }
            }
        }

        // If there are updates for level commissions, update them as well
        if (!empty($update_values)) {
            $sql = "UPDATE referral_level SET " . implode(", ", array_map(function ($column) {
                return "$column = ?";
            }, array_keys($update_values))) . " WHERE id = ?";
            
            $stmt = $conn->prepare($sql);
            
            $types = str_repeat('d', count($update_values)) . 'i'; // 'd' for float, 'i' for integer
            $values = array_values($update_values);
            $values[] = $id; // Add ID at the end

            // Bind parameters dynamically
            $stmt->bind_param($types, ...$values);

            // Execute and check for success
            if ($stmt->execute()) {
                echo "Referral level commissions updated successfully for ID: $id<br>";
            } else {
                echo "Error updating commissions for level ID: $id<br>";
            }
            $stmt->close();
        }
    }
    
    // Redirect back to the admin page after submission
    header("Location: referral_level.php");
    exit();
} else {
    echo "Invalid request.";
}
?>
