<?php
include '../../../connection.php'; // Include your database connection

// Start session if needed
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the file is uploaded
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == 0) {
        // Get file details
        $file_name = $_FILES['banner_image']['name'];
        $file_tmp_name = $_FILES['banner_image']['tmp_name'];
        $file_size = $_FILES['banner_image']['size'];
        $file_type = $_FILES['banner_image']['type'];
        
        // Define allowed file types (image types)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        // Check if the file type is allowed
        if (in_array($file_type, $allowed_types)) {
            // Generate a unique name for the file
            $unique_name = uniqid('banner_', true) . '.' . pathinfo($file_name, PATHINFO_EXTENSION);
            $upload_path = 'uploads/' . $unique_name;

            // Move the file to the "uploads" directory
            if (move_uploaded_file($file_tmp_name, $upload_path)) {
                // File uploaded successfully, now insert into the database
                $sql = "INSERT INTO banners (img_path) VALUES ('$unique_name')";

                if ($conn->query($sql) === TRUE) {
                    // Redirect or show a success message
                    header("Location: topbanner.php");
                    exit();
                } else {
                    echo "Error inserting record: " . $conn->error;
                }
            } else {
                echo "Error uploading file.";
            }
        } else {
            echo "Invalid file type. Please upload an image (JPEG, PNG, GIF).";
        }
    } else {
        echo "No file uploaded or there was an upload error.";
    }
}

// Close the database connection
$conn->close();
?>
