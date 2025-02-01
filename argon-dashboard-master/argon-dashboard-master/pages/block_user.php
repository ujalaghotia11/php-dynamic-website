<?php
// Include database connection file
include '../../../connection.php'; // Replace with your actual connection file path

// Check if 'id' parameter is passed in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $userId = $_GET['id'];

    // Validate the user ID (make sure it's an integer)
    if (filter_var($userId, FILTER_VALIDATE_INT)) {

        // SQL query to update the user's status to 'blocked' (0)
        $sql = "UPDATE users SET status = 0 WHERE id = ?";

        // Prepare the query
        if ($stmt = $conn->prepare($sql)) {
            // Bind the user ID parameter
            $stmt->bind_param("i", $userId);

            // Execute the query
            if ($stmt->execute()) {
                // Redirect to the user list page with success message
                header("Location: user.php?status=blocked");
                exit();
            } else {
                // If execution failed, show an error message
                echo "Error: Unable to block the user.";
            }

            // Close the statement
            $stmt->close();
        } else {
            echo "Error: Invalid query.";
        }

    } else {
        // Invalid user ID
        echo "Error: Invalid user ID.";
    }
} else {
    // No user ID provided
    echo "Error: User ID not specified.";
}

// Close the database connection
$conn->close();
?>
