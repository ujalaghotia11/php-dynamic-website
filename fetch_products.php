

<?php
include 'connection.php';

$query = isset($_GET['query']) ? strtolower(trim($_GET['query'])) : '';

// Fetch products matching the query
$sql = "SELECT * FROM products WHERE LOWER(name) LIKE '%$query%'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($product = $result->fetch_assoc()) {
        // Calculate discounted price
        $originalPrice = $product['price'];
        $discount = $product['discount'];
        $discountedPrice = $originalPrice - ($originalPrice * $discount / 100);
        
        echo '<div class="col-6 col-sm-6 col-md-4 col-lg-3 product-card mb-4">'; // Added "mb-4" for vertical spacing
        echo '<a href="product-detail.php?product_id=' . $product['id'] . '" class="text-decoration-none">';
        echo '<img src="' . $product['image'] . '" alt="' . htmlspecialchars($product['name']) . '" class="img-fluid" style="height: 250px; object-fit: cover;">';
        echo '<h4 class="mt-2">' . htmlspecialchars($product['name']) . '</h4>';
        echo '</a>';

        // Display price and discount
        echo '<div class="price mt-2">';
        if ($discount > 0) {
            echo '<span class="text-muted" style="text-decoration: line-through;">$' . number_format($originalPrice, 2) . '</span> ';
        }
        echo '<strong>$' . number_format($discountedPrice, 2) . '</strong>';
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<p class="text-center">No products found.</p>';
}
?>


