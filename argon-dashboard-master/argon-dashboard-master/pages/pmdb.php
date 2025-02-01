<?php
include '../../../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_payment_method'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];

    // Handle QR code file upload
    if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] == 0) {
        $file_name = $_FILES['qr_code']['name'];
        $file_tmp_name = $_FILES['qr_code']['tmp_name'];
        $file_size = $_FILES['qr_code']['size'];
        $file_error = $_FILES['qr_code']['error'];

        // Check if the file is an image (QR code)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_extensions)) {
            // Generate a unique name for the file to avoid conflicts
            $new_file_name = uniqid('', true) . '.' . $file_ext;
            $file_path =  $new_file_name;

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file_tmp_name, $file_path)) {
                // Insert into the payment_methods table
                $sql = "INSERT INTO payment_methods (name, qr_code, description) 
                        VALUES ('$name', '$file_path', '$description')";
                if ($conn->query($sql) === TRUE) {
                    echo "New payment method added successfully!";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            } else {
                echo "Error uploading the QR code file.";
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.";
        }
    } else {
        echo "Please upload a QR code image.";
    }
}
?>