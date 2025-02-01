<?php
include '../../../connection.php';

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Delete the product
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    if ($stmt->execute()) {
        header('Location: admin_product_list.php');
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
