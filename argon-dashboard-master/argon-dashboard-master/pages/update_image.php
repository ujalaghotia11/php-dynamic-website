<?php
include '../../../connection.php';

// Check if 'id' and 'product_id' are set in the URL
if (isset($_GET['id']) && isset($_GET['product_id'])) {
    $image_id = $_GET['id'];
    $product_id = $_GET['product_id'];

    // Fetch the current image details to populate the form
    $sql = "SELECT * FROM product_images WHERE id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);

    // Check if the statement was prepared successfully
    if ($stmt === false) {
        die("Error preparing SQL statement: " . $conn->error);
    }

    $stmt->bind_param('ii', $image_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the image exists, fetch its data
    if ($result->num_rows > 0) {
        $image = $result->fetch_assoc();
    } else {
        echo "Image not found.";
        exit();
    }

    // If form is submitted to update the image
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Check if a new image file is uploaded
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $upload_dir = 'uploads/'; // Set your image upload directory
            $upload_file = $upload_dir . basename($_FILES['image']['name']);
            
            // Move the uploaded file to the desired directory
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
                // Update the image path in the database
                $new_image_path = $upload_file;
                
                // Prepare the update statement
                $update_sql = "UPDATE product_images SET img_path = ? WHERE id = ? AND product_id = ?";
                $update_stmt = $conn->prepare($update_sql);

                if ($update_stmt === false) {
                    die("Error preparing SQL statement: " . $conn->error);
                }

                $update_stmt->bind_param('sii', $new_image_path, $image_id, $product_id);
                $update_stmt->execute();

                // Redirect back to the product images page
                header("Location: product_images.php?product_id=" . $product_id);
                exit();
            } else {
                echo "Failed to upload the image.";
            }
        } else {
            echo "No image file selected or error during upload.";
        }
    }
} else {
    // Handle case where 'id' or 'product_id' is not provided
    echo "Invalid request. Image ID and Product ID are required.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Image</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h4>Update Image for Product ID: <?php echo htmlspecialchars($product_id); ?></h4>

        <!-- Display the current image -->
        <div class="mb-3">
            <img src="<?php echo htmlspecialchars($image['img_path']); ?>" alt="Current Image" class="img-fluid" width="200px">
        </div>

        <!-- Form to update the image -->
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="image" class="form-label">Select New Image</label>
                <input type="file" class="form-control" id="image" name="image" required>
            </div>
            <button type="submit" class="btn btn-success">Update Image</button>
            <a href="product_images.php?product_id=<?php echo $product_id; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
