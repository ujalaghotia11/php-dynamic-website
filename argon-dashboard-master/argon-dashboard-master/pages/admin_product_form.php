<?php
// Include the connection to the database
include '../../../connection.php';

// Variables to store form data
$product_name = '';
$product_description = '';
$product_price = '';
$product_discount = '';
$product_images = ''; // Default to an empty string
$product_id = '';

// Check if we are editing an existing product
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Fetch product data from the database
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if product exists
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        // Fill in form data
        $product_name = $product['name'];
        $product_description = $product['description'];
        $product_price = $product['price'];
        $product_discount = $product['discount'];
        // Check if 'images' exists and set it, otherwise use an empty string
        $product_images = isset($product['images']) ? $product['images'] : '';
    } else {
        echo "Product not found.";
        exit;
    }
}

// Handle form submission for adding or editing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $product_name = $_POST['product_name'];
    $product_description = $_POST['product_description'];
    $product_price = $_POST['product_price'];
    $product_discount = $_POST['product_discount'];
    $product_images = $_POST['product_images'];

    // Handle the file upload for images
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        // Save the uploaded image to the server
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["product_image"]["name"]);

        // Check if the image file is an actual image
        if (getimagesize($_FILES["product_image"]["tmp_name"]) !== false) {
            if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                $product_images = $target_file; // Set the image path for database
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
        } else {
            echo "File is not an image.";
            exit;
        }
    }

    // If product_id is set, update the product; otherwise, insert a new product
    if ($product_id) {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, discount = ?, images = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo "Error in preparing the SQL statement: " . $conn->error;
            exit;
        }
        $stmt->bind_param("ssdisi", $product_name, $product_description, $product_price, $product_discount, $product_images, $product_id);
    } else {
        $sql = "INSERT INTO products (name, description, price, discount, images) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo "Error in preparing the SQL statement: " . $conn->error;
            exit;
        }
        $stmt->bind_param("ssdis", $product_name, $product_description, $product_price, $product_discount, $product_images);
    }

    // Execute the query
    if ($stmt->execute()) {
        header('Location: admin_product_list.php'); // Redirect to product list
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Product Form</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2><?php echo $product_id ? 'Edit Product' : 'Add New Product'; ?></h2>
        <form action="admin_product_form.php<?php echo $product_id ? '?product_id=' . $product_id : ''; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="productName">Product Name</label>
                <input type="text" class="form-control" id="productName" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="productDescription">Product Description</label>
                <textarea class="form-control" id="productDescription" name="product_description" rows="4" required><?php echo htmlspecialchars($product_description); ?></textarea>
            </div>
            <div class="form-group">
                <label for="productPrice">Price</label>
                <input type="number" step="0.01" class="form-control" id="productPrice" name="product_price" value="<?php echo htmlspecialchars($product_price); ?>" required>
            </div>
            <div class="form-group">
                <label for="productDiscount">Discount</label>
                <input type="number" step="0.01" class="form-control" id="productDiscount" name="product_discount" value="<?php echo htmlspecialchars($product_discount); ?>" required>
            </div>
            <div class="form-group">
                <label for="productImages">Product Images (comma separated)</label>
                <input type="text" class="form-control" id="productImages" name="product_images" value="<?php echo htmlspecialchars($product_images); ?>" required>
            </div>
            <div class="form-group">
                <label for="product_image">Upload Product Image</label>
                <input type="file" class="form-control-file" id="product_image" name="product_image">
            </div>
            <button type="submit" class="btn btn-primary">Save Product</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
