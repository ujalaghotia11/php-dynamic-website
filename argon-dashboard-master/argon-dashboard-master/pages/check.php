<?php
include '../../../connection.php';
$img_path = []; // Initialize an empty array to store image paths

// Fetch all product images from the product_images table
$sql = "SELECT * FROM product_images";
$stmt = $conn->prepare($sql);

// Check if the statement was prepared successfully
if ($stmt === false) {
    die("Error preparing SQL statement: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

// Check if images exist
if ($result->num_rows > 0) {
    // Store the image paths in an array
    while ($row = $result->fetch_assoc()) {
        $img_path[] = $row; // Store the entire row (including img_path and product_id)
    }
} else {
    // Debugging message if no images are found
    echo "<p>No images found.</p>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Images</title>
    <!-- Add any required CSS files here, like Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <?php if (!empty($img_path)) : ?>
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Product Images</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Product ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Image</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($img_path as $key => $image) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($image['product_id']); ?></td> <!-- Displaying product_id -->
                                        <td>
                                            <img src="<?php echo htmlspecialchars($image['img_path']); ?>" alt="Product Image" class="img-fluid" width="100px">
                                        </td>
                                        <td>
                                            <a href="delete_image.php?id=<?php echo $image['id']; ?>&product_id=<?php echo $image['product_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p>No images available.</p>
        <?php endif; ?>
    </div>

    <!-- Optional: Add Bootstrap JS for additional interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
