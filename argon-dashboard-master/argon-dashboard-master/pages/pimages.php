<?php
include '../../../connection.php'; // Ensure connection to the database

// Check if the form is submitted
if (isset($_POST['submit'])) {
    // Get the product ID from the form  
    $product_id = $_POST['product_id'];

    // Check if any files were uploaded
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        // Database connection
        // $conn = new mysqli("localhost", "root", "", "your_database_name");

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Define the upload directory
        // $uploadDir = 'uploads/'; // Ensure this directory exists and is writable
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']; // Allowed image types
        $maxSize = 5000000; // Maximum file size (5MB)

        // Initialize an array to store image paths
        $imagePaths = [];
        $errors = [];

        // Loop through each uploaded file
        for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
            $fileTmpName = $_FILES['images']['tmp_name'][$i];
            $fileName = basename($_FILES['images']['name'][$i]);
            $fileType = $_FILES['images']['type'][$i];
            $fileSize = $_FILES['images']['size'][$i];
            $filePath =  $fileName;

            // Validate file type and size
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "Invalid file type for image " . $fileName;
                continue;
            }

            if ($fileSize > $maxSize) {
                $errors[] = "File size too large for image " . $fileName;
                continue;
            }

            // Move the uploaded file to the upload directory
            if (move_uploaded_file($fileTmpName, $filePath)) {
                $imagePaths[] = $filePath; // Store the image path
            } else {
                $errors[] = "Failed to upload image " . $fileName;
            }
        }

        // If no errors occurred, insert image paths into the database
        if (empty($errors)) {
            // Prepare the SQL statement to insert each image path into the database
            $stmt = $conn->prepare("INSERT INTO product_images (product_id, img_path) VALUES (?, ?)");

            // Check if the prepare failed
            if ($stmt === false) {
                die('Error preparing the statement: ' . $conn->error);
            }

            // Loop through the image paths and insert them into the database
            foreach ($imagePaths as $img_path) {
                $stmt->bind_param("is", $product_id, $img_path);

                // Check if the bind_param failed
                if ($stmt->execute() === false) {
                    die('Error executing the statement: ' . $stmt->error);
                }
            }

            echo "<script>
        alert('Images uploaded successfully!');
        window.location.href ='product_images.php';
    </script>";
        } else {
            // Display errors if any
            foreach ($errors as $error) {
                echo "<p style='color:red;'>$error</p>";
            }
        }

        // Close the database connection
        $conn->close();
    } else {
        echo "<p style='color:red;'>Please select images to upload.</p>";
    }
}
?>
