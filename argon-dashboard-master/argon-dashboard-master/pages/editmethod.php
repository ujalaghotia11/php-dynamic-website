<?php 
include '../../../connection.php';

// Check if the database connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the 'edit' parameter is set in the URL and is numeric
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = $_GET['edit'];

    // Fetch the current payment method data from the database
    $edit_sql = "SELECT * FROM payment_methods WHERE id = $id";
    $edit_result = $conn->query($edit_sql);

    // Check if the query returns a result
    if ($edit_result && $edit_result->num_rows > 0) {
        $edit_payment_method = $edit_result->fetch_assoc(); // Fetch the row
    } else {
        echo "Payment method not found or invalid ID.";
        exit;
    }
} else {
    echo "Invalid ID.";
    exit;
}

// Handle form submission to update the payment method
if (isset($_POST['update_payment_method'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $existing_qr_code = $_POST['existing_qr_code']; // Retain the existing QR code URL if no new file is uploaded

    // Handle QR Code file upload if a new file is uploaded
    if (isset($_FILES['qr_code']) && $_FILES['qr_code']['error'] == 0) {
        $upload_dir = "uploads/"; // Directory to store the uploaded files

        // Get file details
        $file_name = $_FILES['qr_code']['name'];
        $file_tmp_name = $_FILES['qr_code']['tmp_name'];
        $file_size = $_FILES['qr_code']['size'];
        $file_error = $_FILES['qr_code']['error'];

        // Validate the file type (Only image files are allowed)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_extensions)) {
            // Generate a unique name for the file to avoid conflicts
            $new_file_name = uniqid('', true) . '.' . $file_ext;
            $file_path = $upload_dir . $new_file_name;

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($file_tmp_name, $file_path)) {
                // Successfully uploaded the new file, update the database with the new path
                $qr_code = $file_path;
            } else {
                echo "Error uploading the QR code file.";
                exit;
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, GIF files are allowed.";
            exit;
        }
    } else {
        // If no new file is uploaded, retain the existing QR code
        $qr_code = $existing_qr_code;
    }

    // Update the payment method in the database
    $update_sql = "UPDATE payment_methods 
                   SET name = '$name', qr_code = '$qr_code', description = '$description' 
                   WHERE id = $id";

    if ($conn->query($update_sql) === TRUE) {
        echo "Payment method updated successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Payment Method</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h3>Edit Payment Method</h3>
        
        <?php if (isset($edit_payment_method)): ?>
            <!-- Form to Edit the Payment Method -->
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Payment Method Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo $edit_payment_method['name']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="qr_code">QR Code Image</label>
                    <input type="file" class="form-control" id="qr_code" name="qr_code" accept="image/*">
                    <small>Current QR Code: <img src="<?php echo $edit_payment_method['qr_code']; ?>" alt="QR Code" width="50"></small>
                    <input type="hidden" name="existing_qr_code" value="<?php echo $edit_payment_method['qr_code']; ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo $edit_payment_method['description']; ?></textarea>
                </div>

                <!-- Hidden field for ID -->
                <input type="hidden" name="id" value="<?php echo $edit_payment_method['id']; ?>">

                <!-- Submit Button -->
                <button type="submit" name="update_payment_method" class="btn btn-primary">Update Payment Method</button>
            </form>
        <?php else: ?>
            <p>Payment method not found or invalid ID.</p>
        <?php endif; ?>
    </div>
</body>
</html>
