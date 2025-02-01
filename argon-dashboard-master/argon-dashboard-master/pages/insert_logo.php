<?php
include '../../../../../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['logo'])) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['logo']['name']);
    
    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Move the uploaded file to the server
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadFile)) {
        // Create a database connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check for connection errors
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Insert the logo path into the `img` column of the `logo` table
        $sql = "INSERT INTO logo (img) VALUES ('$uploadFile')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Logo uploaded and inserted into database successfully!";
        } else {
            echo "Error: " . $conn->error;
        }

        // Close the connection
        $conn->close();
    } else {
        echo "Failed to upload the logo.";
    }
}
?>
