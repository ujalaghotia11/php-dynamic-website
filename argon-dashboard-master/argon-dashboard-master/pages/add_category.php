<?php
// Include the database connection
include('../../../connection.php');

// Add categories form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = $_POST['name'];
    $image = $_FILES['image']['name'];

    // Upload image
    move_uploaded_file($_FILES['image']['tmp_name'], "$image");

    // Insert category into database
    $sql = "INSERT INTO categories (name, image, slug) VALUES ('$name', '$image', '" . strtolower(str_replace(' ', '-', $name)) . "')";
    if (mysqli_query($conn, $sql)) {
        // If insert successful, show a message and redirect to manageproduct.php
        echo "<script>
                alert('Category added successfully!');
                window.location.href = 'manageproduct.php';
              </script>";
        exit; // Ensure no further code execution
    } else {
        // If there's an error with the insert
        echo "<script>
                alert('Error adding category. Please try again.');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Category</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
        }
        .card {
            margin: 20px 0;
        }
        .card-header {
            font-size: 24px;
            font-weight: bold;
        }
        .form-label {
            font-weight: bold;
        }
        .btn {
            font-size: 16px;
        }
        .container {
            max-width: 800px;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <!-- Add Category Form -->
    <div class="card shadow-sm">
        <div class="card-header text-center bg-success text-white">
            <h3>Add Category</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label text-primary">Category Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter category name" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label text-primary">Category Image</label>
                    <input type="file" name="image" class="form-control" required>
                </div>
                <button type="submit" name="add_category" class="btn btn-success w-100">Add Category</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
