<?php
include '../../../connection.php';

// Check if the 'delete' parameter is set and is numeric
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = $_GET['delete'];

    // SQL query to delete the record from the database
    $delete_sql = "DELETE FROM payment_methods WHERE id = $id";

    if ($conn->query($delete_sql) === TRUE) {
        echo "Payment method deleted successfully!";
        // Optionally, redirect after deletion to avoid reloading the page with the DELETE parameter
        header("Location: table.php");
        exit; // Stop further execution
    } else {
        echo "Error deleting payment method: " . $conn->error;
    }
}
?>
