<?php
// Include the database connection file
include('../../../connection.php');

// Check if the logo ID is provided
if (isset($_GET['id'])) {
    $logo_id = $_GET['id'];

    // Fetch the logo details from the database based on the provided ID
    $sql = "SELECT * FROM logo WHERE id = $logo_id";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $logo = mysqli_fetch_assoc($result);
    } else {
        echo "Logo not found.";
        exit;
    }
} else {
    echo "Invalid logo ID.";
    exit;
}

// Handle the form submission to update the logo
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if a new image is uploaded
    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        $upload_dir = 'uploads/'; // Directory where images will be uploaded
        $image_name = $_FILES['img']['name'];
        $image_tmp = $_FILES['img']['tmp_name'];
        $image_path = $upload_dir . basename($image_name);

        // Check if the file is an image
        $image_check = getimagesize($image_tmp);
        if ($image_check !== false) {
            // Move the uploaded image to the server's directory
            if (move_uploaded_file($image_tmp, $image_path)) {
                // Update the logo path in the database
                $update_sql = "UPDATE logo SET logo = '$image_path' WHERE id = $logo_id";
                if (mysqli_query($conn, $update_sql)) {
                    echo "<script>
                            alert('Logo updated successfully!');
                            window.location.href = 'logo.php';
                        </script>";
                } else {
                    echo "Error updating logo in the database.";
                }
            } else {
                echo "Error uploading the image.";
            }
        } else {
            echo "The file is not a valid image.";
        }
    } else {
        // If no new image is uploaded, update other details
        $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
        $update_sql = "UPDATE logo SET id = '$id' WHERE id = $logo_id";
        if (mysqli_query($conn, $update_sql)) {
            echo "<script>
                    alert('Logo details updated successfully!');
                    window.location.href = 'logo.php';
                </script>";
        } else {
            echo "Error updating logo details.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Logo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0-alpha1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Logo</h2>
        <form action="edit_logo.php?id=<?php echo $logo_id; ?>" method="POST" enctype="multipart/form-data">
            
            <div class="mb-3">
                <label for="img" class="form-label">Upload New Logo </label>
                <input type="file" class="form-control" name="img" id="img" accept="image/*">
            </div>

            <div class="mb-3">
                <h5>Current Logo:</h5>
                <img src="<?php echo htmlspecialchars($logo['logo']); ?>" alt="Logo" style="max-width: 200px;">
            </div>

            <button type="submit" class="btn btn-primary">Update Logo</button>
            <a href="logo_management.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0-alpha1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
