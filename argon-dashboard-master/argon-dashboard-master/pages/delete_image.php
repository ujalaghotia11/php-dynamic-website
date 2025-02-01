<?php
include '../../../connection.php';

// Check if 'id' and 'product_id' are set in the URL
if (isset($_GET['id']) && isset($_GET['product_id'])) {
    $image_id = $_GET['id'];
    $product_id = $_GET['product_id'];

    // Prepare the delete statement to remove the image from the database
    $sql = "DELETE FROM product_images WHERE id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        die("Error preparing SQL statement: " . $conn->error);
    }

    // Bind the parameters to the prepared statement
    $stmt->bind_param('ii', $image_id, $product_id);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect back to the page showing the product images
        header("Location: product_images.php?product_id=" . $product_id);
        exit(); // Make sure no further code is executed after redirect
    } else {
        // If deletion fails, show an error message
        echo "Error deleting image. Please try again.";
    }
} else {
    // If 'id' or 'product_id' is not set, show an error message
    echo "Invalid request. Image ID and Product ID are required.";
}
?>
