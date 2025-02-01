<?php
// Include database connection
include('../../../connection.php');

// Check if the category ID is passed in the URL
if (isset($_POST['delete_id'])) {
    $category_id = $_POST['delete_id'];

    // Check if the category exists in the database
    $result = $conn->query("SELECT * FROM categories WHERE id = $category_id");

    if ($result->num_rows > 0) {
        // If category exists, delete the category
        $delete_sql = "DELETE FROM categories WHERE id = $category_id";
        if ($conn->query($delete_sql) === TRUE) {
            // Redirect to manage categories page with a success message
            header("Location: manageproduct.php?success=Category deleted successfully");
            exit();
        } else {
            // If there was an error during deletion
            header("Location: manageproduct.php?error=Error deleting category");
            exit();
        }
    } else {
        // If category not found
        header("Location: manageproduct.php?error=Category not found");
        exit();
    }
} else {
    // If no category ID is provided in the POST request
    header("Location: manageproduct.php");
    exit();
}
