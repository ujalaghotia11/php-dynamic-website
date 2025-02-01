<?php
include '../../../connection.php'; // Include your database connection file

// Start the session (if needed)
session_start();

// Check if the banner ID is provided in the URL
if (isset($_GET['id'])) {
    $banner_id = $_GET['id'];

    // Fetch the banner details from the database
    $sql = "SELECT * FROM banners WHERE id = '$banner_id'";
    $result = $conn->query($sql);

    // If the banner is not found
    if ($result->num_rows == 0) {
        die("Banner not found.");
    }

    // Get the banner details
    $banner = $result->fetch_assoc();

    // Path to the banner image
    $banner_image_path = 'uploads/' . $banner['img_path'];

    // Delete the banner image from the server
    if (file_exists($banner_image_path)) {
        unlink($banner_image_path); // Delete the image file
    }

    // Delete the banner record from the database
    $delete_sql = "DELETE FROM banners WHERE id = '$banner_id'";

    if ($conn->query($delete_sql) === TRUE) {
        // Redirect to the banners page after successful deletion
        header("Location: topbanner.php"); // Change this URL as needed
        exit();
    } else {
        echo "Error deleting banner: " . $conn->error;
    }
} else {
    // If no ID is specified
    echo "Invalid request. No banner ID specified.";
}

// Close the database connection
$conn->close();
?>
