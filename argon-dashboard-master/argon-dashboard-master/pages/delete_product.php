<?php
// Include the database connection
include '../../../connection.php';

// Check if the request method is POST and the 'delete' key is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    // Get the product ID from the form
    $productId = $_POST['delete_id'];

    // Validate the product ID
    if (filter_var($productId, FILTER_VALIDATE_INT)) {
        // Begin by deleting the related images from the product_images table
        $stmt = $conn->prepare("DELETE FROM product_images WHERE product_id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->close();

        // Retrieve the product image path
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->bind_result($filePath);
        $stmt->fetch();
        $stmt->close();

        // Delete the product image file if it exists
        if ($filePath && file_exists($filePath)) {
            unlink($filePath); // Delete image file
        }

        // Delete the product from the products table
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        if ($stmt->execute()) {
            header("Location: manageproduct.php"); // Redirect after successful delete
            exit;
        } else {
            echo "Failed to delete the product.";
        }
        $stmt->close();
    } else {
        echo "Invalid product ID.";
    }
} else {
    // Redirect if accessed directly
    header("Location: manageproduct.php");
    exit;
}
?>
