<?php
include 'connection.php';
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get category_id from URL
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Fetch category details
$category_sql = "SELECT name FROM categories WHERE id = $category_id";
$category_result = mysqli_query($conn, $category_sql);
$category = mysqli_fetch_assoc($category_result);

// Fetch products for the selected category
$product_sql = "SELECT * FROM products WHERE category_id = $category_id";
$product_result = mysqli_query($conn, $product_sql);

$products = [];
while ($row = mysqli_fetch_assoc($product_result)) {
    $products[] = $row;
}

$logo_sql = "SELECT logo FROM logo ORDER BY id DESC LIMIT 1";
$logo_result = mysqli_query($conn, $logo_sql);
$logo = 'images/default-logo.png'; // Default logo
if ($logo_result && mysqli_num_rows($logo_result) > 0) {
    $row = mysqli_fetch_assoc($logo_result);
    $logo = $row['logo'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <title><?php echo $category['name']; ?> Products</title>
    <style>
      body {
            font-family: Arial, sans-serif;
        }

        .navbar2 {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar2 .logo {
            display: flex;
            align-items: center;
        }

        .search-bar {
            display: none;
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            width: 225px;
            z-index: 1000;
        }

        /* Basic styling for the search input */
        .search-bar input {
            width: 100%;
            padding: 8px;
            font-size: 16px;
            border: 1px solid #ccc;
        }

        /* Styling for the search icon */
        .auth-buttons {
            position: relative;
        }

        .auth-buttons i {
            font-size: 24px;
            cursor: pointer;
        }

        /* Product card styling */
        .product-card {
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-card h4 {
            margin: 10px 0;
            font-size: 18px;
            color: #B71C1C;
        }

        .product-card p {
            margin: 5px 0;
        }

        .product-card .original-price {
            text-decoration: line-through;
            color: red;
        }

        .product-card .discounted-price {
            font-weight: bold;
            color: #388E3C;
        }

        .product-card .discount {
            color: red;
        }

        .auth-buttons {
            margin-right: 10px;
        }

        /* Adjustments for responsive grid */
        .products-grid {
            margin-top: 22px; 
           
        }

        /* Mobile and tablet grid: 2 products per row */
        @media (max-width: 768px) {
            .product-card {
                width: 45%; 
                gap:0;/* Two products per row on mobile */
            }
        }

        /* Large screen grid: 4 products per row */
        @media (min-width: 992px) {
            .product-card {
                width: 20%; 
                gap:0;/* Four products per row on larger screens */
            }
        }
        .bottom-navbar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    display: flex;
    justify-content: space-around;
    align-items: center;
    background-image: url("navbarbackground.webp");
    background-repeat: no-repeat;
    background-size: cover;
    background-position: center;
    z-index: 1000;
    height: 60px; /* Adjusted height for better proportions */
    transition: transform 0.3s ease;
    /* background-color: rgba(255, 255, 255, 0.9); Semi-transparent background for better visibility */
    box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    /* border-top: 1px solid #ddd; Adds a divider for clean design */
}

.nav-item {
    text-align: center;
    color: #555; /* Neutral color for icons and text */
    text-decoration: none;
    flex: 1;
    transition: color 0.3s, transform 0.3s;
}

.nav-item:hover {
    color: #007bff; /* Highlight color on hover */
    transform: scale(1.1); /* Subtle zoom-in effect */
}

.nav-item i {
    font-size: 20px; /* Adjusted size for better visibility */
    margin-bottom: 3px;
}

.nav-item span {
    display: block;
    font-size: 12px; /* Adjusted font size for a cleaner look */
    margin-top: 5px;
}

.promotion-item img {
    width: 50px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #007bff; /* Adds a border for visual focus */
}

.promotion-item img:hover {
    border-color: #0056b3; /* Darker border color on hover */
}
    </style>
</head>
<body>
<div class="navbar2">
    <div class="logo">
        <img src="uploads/<?php echo $logo; ?>" alt="Logo" height="50px" style="border-radius:50%">
    </div>
    <div class="auth-buttons">
        <i class="fas fa-search" id="searchIcon"></i>
    </div>
</div>

<!-- Search Bar -->
<div class="search-bar" id="searchBar">
    <input type="text" id="searchInput" placeholder="Search products..." onkeyup="filterProducts()">
</div>

<h1 style="margin-top:90px; text-align:center;"><?php echo $category['name']; ?> Products</h1>

<div class="container">
    <div class="row products-grid" id="productGrid">
        <?php foreach ($products as $product): ?>
            <div class="col-6 col-md-4 col-lg-3 product-card" data-name="<?php echo strtolower($product['name']); ?>">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                <h4><?php echo $product['name']; ?></h4>

                <?php
                // Calculate the discounted price if a discount is applied
                $originalPrice = $product['price'];
                $discount = $product['discount'];
                $discountedPrice = $originalPrice;

                if ($discount > 0) {
                    $discountAmount = ($originalPrice * $discount) / 100;
                    $discountedPrice = $originalPrice - $discountAmount;
                }
                ?>

                <p>
                    <?php if ($discount > 0): ?>
                        <span class="original-price">$<?php echo number_format($originalPrice, 2); ?></span>
                    <?php endif; ?>
                    <span class="discounted-price">$<?php echo number_format($discountedPrice, 2); ?></span>
                </p>

                <?php if ($discount > 0): ?>
                    <p class="discount">Discount: <?php echo $discount; ?>% off</p>
                <?php endif; ?>
                <a href="order-form.php?product_id=<?php echo $product['id']; ?>" class="btn btn-success btn-sm mt-2">Order Now</a>
            </div>
        <?php endforeach; ?>

        <?php if (empty($products)): ?>
            <p>No products found for this category.</p>
        <?php endif; ?>
    </div>
</div><br><br><br>
<div id="bottom-navbar" class="bottom-navbar">
    <a href="index.php" class="nav-item">
        <i class="fa fa-home"></i>
        <span>Home</span>
    </a>
    <a href="order_details.php" class="nav-item">
        <i class="fa fa-history"></i>
        <span>Order</span>
    </a>
    <a href="#promotion" class="nav-item promotion-item">
        <img src="cart.jpg" alt="Cart" height="50px" width="50px" style="margin-top: -37px; border-radius: 50%;">
    </a>
    <a href="wallet.php" class="nav-item wallet-item">
        <i class="fa fa-wallet"></i>
        <span>Wallet</span>
    </a>
    <a href="account.php" class="nav-item">
        <i class="fa fa-user"></i>
        <span>Account</span>
    </a>
</div>


<script>
// Get the search icon and search bar elements
const searchIcon = document.getElementById('searchIcon');
const searchBar = document.getElementById('searchBar');

// Toggle search bar visibility when search icon is clicked
searchIcon.addEventListener('click', function() {
    if (searchBar.style.display === 'none' || searchBar.style.display === '') {
        searchBar.style.display = 'block';  // Show the search bar
    } else {
        searchBar.style.display = 'none';   // Hide the search bar
    }
});

// Function to filter products based on search input
function filterProducts() {
    const query = document.getElementById('searchInput').value.toLowerCase();  // Get the query from the search input
    const products = document.querySelectorAll('.product-card');  // Select all product cards

    // Loop through each product card
    products.forEach(product => {
        const productName = product.getAttribute('data-name').toLowerCase(); // Get the product name from the data-name attribute
        if (productName.includes(query)) {
            product.style.display = 'block'; // Show the product if it matches the search query
        } else {
            product.style.display = 'none'; // Hide the product if it does not match the search query
        }
    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

</body>
</html>