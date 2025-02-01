<?php
// Include database connection
include('../../../connection.php');

// Check if the category ID is passed in the URL
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // Fetch category details based on the ID
    $result = $conn->query("SELECT * FROM categories WHERE id = $category_id");
    
    if ($result->num_rows > 0) {
        $category = $result->fetch_assoc();
    } else {
        // If category not found, redirect to manage categories page
        header("Location: manageproduct.php");
        exit();
    }
} else {
    // If no ID is passed, redirect to manage categories page
    header("Location: manageproduct.php");
    exit();
}

// Handle form submission to update category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    $name = $_POST['name'];
    $image = $_FILES['image']['name'];
    
    // If a new image is uploaded, move the file to the server
    if (!empty($image)) {
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $image);
    } else {
        // If no new image is uploaded, keep the old one
        $image = $category['image'];
    }

    // Generate a slug from the category name
    $slug = strtolower(str_replace(' ', '-', $name));

    // Update the category in the database
    $sql = "UPDATE categories SET name = '$name', image = '$image', slug = '$slug' WHERE id = $category_id";
    if ($conn->query($sql) === TRUE) {
        // Redirect to the manage categories page with a success message
        header("Location: manageproduct.php?success=Category updated successfully");
        exit();
    } else {
        // If an error occurs, display an error message
        $error = "Error updating category: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <!-- Add Bootstrap for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Category</h2>
        
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo $category['name']; ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="image" class="form-label">Category Image</label>
                <input type="file" class="form-control" id="image" name="image">
                <small>Current Image: <img src="uploads/<?php echo $category['image']; ?>" alt="Category Image" class="img-thumbnail" style="max-width: 100px;"></small>
            </div>
            
            <button type="submit" class="btn btn-primary" name="update_category">Update Category</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
