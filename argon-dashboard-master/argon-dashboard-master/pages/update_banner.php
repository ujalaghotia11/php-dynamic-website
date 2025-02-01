<?php
include '../../../connection.php'; // Include your database connection file

// Start the session (if needed)
session_start();

// Check if the banner ID is provided in the URL
if (isset($_GET['id'])) {
    $banner_id = $_GET['id'];

    // Fetch existing banner details from the database
    $sql = "SELECT * FROM banners WHERE id = '$banner_id'";
    $result = $conn->query($sql);

    // If the banner is not found
    if ($result->num_rows == 0) {
        die("Banner not found.");
    }

    // Get the current banner details
    $banner = $result->fetch_assoc();
} else {
    die("Invalid request. No banner ID specified.");
}

// Handle form submission for updating the banner
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a new file is uploaded
    if (isset($_FILES['img_path']) && $_FILES['img_path']['error'] == 0) {
        // Specify the folder to store uploaded images
        $upload_dir = 'uploads/';
        $file_name = $_FILES['img_path']['name'];
        $file_tmp = $_FILES['img_path']['tmp_name'];
        $file_path = $upload_dir . basename($file_name);

        // Validate the uploaded file (you can add additional validation here)
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Update the banner image in the database
            $sql = "UPDATE banners SET img_path = '$file_name' WHERE id = '$banner_id'";

            if ($conn->query($sql) === TRUE) {
                echo "Banner updated successfully.";
                header("Location: topbanner.php"); // Redirect to the banners list page after updating
                exit();
            } else {
                echo "Error updating banner: " . $conn->error;
            }
        } else {
            echo "Failed to upload the image.";
        }
    } else {
        echo "No image uploaded or error in file upload.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Banner</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Update Banner</h2>

    <!-- Display current banner details -->
    <form action="update_banner.php?id=<?php echo $banner['id']; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="banner_id">Banner ID</label>
            <input type="text" name="banner_id" id="banner_id" class="form-control" value="<?php echo $banner['id']; ?>" readonly>
        </div>

        <div class="form-group">
            <label for="current_img">Current Banner Image</label>
            <br>
            <img src="uploads/<?php echo $banner['img_path']; ?>" alt="Current Banner Image" style="width: 200px; height: auto;">
        </div>

        <div class="form-group">
            <label for="img_path">Upload New Banner Image</label>
            <input type="file" name="img_path" id="img_path" class="form-control" accept="image/*">
        </div>

        <button type="submit" class="btn btn-primary">Update Banner</button>
        <a href="banners.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
