<?php
include '../../../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_discount = $_POST['product_discount'];
    $product_images = '';

    if (isset($_FILES['product_images']) && count($_FILES['product_images']['name']) > 0) {
        // Handle file uploads (you can extend this to save images to a folder and update the database)
        $images = [];
        foreach ($_FILES['product_images']['name'] as $key => $name) {
            $temp_name = $_FILES['product_images']['tmp_name'][$key];
            $new_name = 'uploads/' . time() . '-' . $name;
            move_uploaded_file($temp_name, $new_name);
            $images[] = $new_name;
        }
        $product_images = implode(',', $images); // Save as a comma-separated list of image paths
    }

    if ($product_id) {
        // Update existing product
        $sql = "UPDATE products SET name=?, description=?, price=?, discount=?, images=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdisi', $product_name, $product_description, $product_price, $product_discount, $product_images, $product_id);
    } else {
        // Insert new product
        $sql = "INSERT INTO products (name, description, price, discount, images) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssdis', $product_name, $product_description, $product_price, $product_discount, $product_images);
    }

    if ($stmt->execute()) {
        header('Location: table.php');
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
