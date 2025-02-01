<?php
include 'connection.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the search query from the AJAX request
$searchQuery = isset($_GET['query']) ? mysqli_real_escape_string($conn, $_GET['query']) : '';

// Fetch matching products from the database
$sql = "SELECT * FROM products WHERE name LIKE '%$searchQuery%'";
$result = mysqli_query($conn, $sql);

$products = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

// Return the products as a JSON response
header('Content-Type: application/json');
echo json_encode($products);
?>
