<?php
include('../../../connection.php');

// Fetch all categories
$sql_categories = "SELECT * FROM categories";
$result_categories = mysqli_query($conn, $sql_categories);

// Check if form is submitted to add a product
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $image = $_FILES['image']['name'];

    // Upload image
    if ($image) {
        move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $image);
    }

    // Insert product into the database
    $sql = "INSERT INTO products (name, description, price, category_id, image) 
            VALUES ('$name', '$description', '$price', '$category_id', '$image')";

    if (mysqli_query($conn, $sql)) {
        header('Location: manageproduct.php');
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
</head>
<body>
<h1 class="text-center my-4 text-primary">Add Product</h1>

<div class="container">
    <form action="add_product.php" method="POST" enctype="multipart/form-data" class="form-horizontal p-4" style="background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        
        <!-- Product Name -->
        <div class="row mb-3">
            <label for="name" class="col-sm-2 col-form-label text-success">Product Name</label>
            <div class="col-sm-10">
                <input type="text" name="name" class="form-control border-info" required placeholder="Enter product name" style="border-radius: 10px;">
            </div>
        </div>

        <!-- Product Description -->
        <div class="row mb-3">
            <label for="description" class="col-sm-2 col-form-label text-success">Description</label>
            <div class="col-sm-10">
                <textarea name="description" class="form-control border-info" rows="4" required placeholder="Enter product description" style="border-radius: 10px;"></textarea>
            </div>
        </div>

        <!-- Product Price -->
        <div class="row mb-3">
            <label for="price" class="col-sm-2 col-form-label text-success">Price ($)</label>
            <div class="col-sm-10">
                <input type="number" name="price" step="0.01" class="form-control border-info" required placeholder="Enter product price" style="border-radius: 10px;">
            </div>
        </div>

        <!-- Category Selection -->
        <div class="row mb-3">
            <label for="category_id" class="col-sm-2 col-form-label text-success">Category</label>
            <div class="col-sm-10">
                <select name="category_id" class="form-select border-info" required style="border-radius: 10px; background-color: #e2f4f4;">
                    <option value="" disabled selected>Select a category</option>
                    <?php while ($category = mysqli_fetch_assoc($result_categories)): ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <!-- Product Image Upload -->
        <div class="row mb-3">
            <label for="image" class="col-sm-2 col-form-label text-success">Product Image</label>
            <div class="col-sm-10">
                <input type="file" name="image" class="form-control border-info" style="border-radius: 10px;">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="row mb-3">
            <div class="col-sm-10 offset-sm-2">
                <button type="submit" class="btn btn-lg btn-primary" style="background-color: #ff6f61; border-radius: 50px; width: 200px;">Add Product</button>
            </div>
        </div>
    </form>
</div>

</body>
</html>
