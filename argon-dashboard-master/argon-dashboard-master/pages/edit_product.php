<?php
include '../../../connection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch product details
    $query = "SELECT * FROM products WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $product = mysqli_fetch_assoc($result);
}

// Update product details
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $discount = $_POST['discount'];
    $image = $_FILES['image']['name'];
    // $targetDir = "uploads/";
    $targetFilePath =  basename($image);

    // Calculate the discounted price
    if ($discount > 0) {
        $discountedPrice = $price - ($price * ($discount / 100)); // Apply discount
    } else {
        $discountedPrice = $price; // No discount, keep the original price
    }

    // Handle image upload
    if (!empty($image)) {
        // If a new image is uploaded, move the file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            // Image upload successful, update the database with the new image
            $update_query = "UPDATE products SET name = '$name', price = $discountedPrice, image = '$targetFilePath', discount = $discount WHERE id = $id";
        } else {
            echo "<script>alert('Error uploading image!');</script>";
            exit;
        }
    } else {
        // If no image is uploaded, keep the old image
        $update_query = "UPDATE products SET name = '$name', price = $discountedPrice, discount = $discount WHERE id = $id";
    }

    // Execute the update query
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Product updated successfully!'); window.location.href = 'mangageproduct.php';</script>";
    } else {
        echo "<script>alert('Error updating product!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Product</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $product['name']; ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo $product['price']; ?>" required>
            </div>
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" class="form-control" id="image" name="image">
                <p>Current Image: <img src="<?php echo $product['image']; ?>" style="max-width: 100px;"></p>
            </div>
            <div class="form-group">
                <label for="discount">Discount (%)</label>
                <input type="number" class="form-control" id="discount" name="discount" value="<?php echo $product['discount']; ?>" required>
            </div>
            <button type="submit" name="update" class="btn btn-success">Update</button>
            <a href="admin_products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
