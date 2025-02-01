<?php
include 'connection.php';

// Get the product ID from the URL
$product_id = isset($_GET['product_id']) ? $_GET['product_id'] : 0;

if ($product_id) {
    // Fetch the product details from the database
    $product_sql = "SELECT * FROM products WHERE id = $product_id";
    $product_result = mysqli_query($conn, $product_sql);
    
    if ($product_result && mysqli_num_rows($product_result) > 0) {
        $product = mysqli_fetch_assoc($product_result);

        // Fetch product images
        $images_sql = "SELECT * FROM product_images WHERE product_id = $product_id";
        $images_result = mysqli_query($conn, $images_sql);
        $product_images = [];
        while ($image_row = mysqli_fetch_assoc($images_result)) {
            $product_images[] = $image_row['img_path'];
        }
    } else {
        echo "Product not found.";
        exit;
    }
} else {
    echo "Invalid product ID.";
    exit;
}

$related_products_sql = "SELECT * FROM products WHERE id != $product_id LIMIT 10"; // Change LIMIT as needed
$related_products_result = mysqli_query($conn, $related_products_sql);
$related_products = [];

// Check if related products are available
if ($related_products_result) {
    while ($related_product = mysqli_fetch_assoc($related_products_result)) {
        $related_products[] = $related_product;
    }
} else {
    // If product_id is not provided
    echo "Invalid product ID.";
    exit;
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
    <title><?php echo $product['name']; ?> - Product Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-VuNXi1lL9+e5B4GeW60yz6ycnw8xyhA32DDH8JJpMqHe6eeb/+t4BkkG7t27p6mM" crossorigin="anonymous">

<!-- Optional JavaScript and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-ZdtCFfkRxScBG4RBPrRewwEaNOsYHY63c2V4FlPbE1iPeP72RgGM6CIk7B0eDAv1" crossorigin="anonymous"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        .product-detail {
            margin-top: 30px;
        }
        .product-info h3 {
            font-size: 2rem;
            margin-bottom: 20px;
        }
        .product-info p {
            font-size: 1.2rem;
            color: #555;
        }
        .price {
            font-size: 1.5rem;
            color: #B71C1C;
            margin-bottom: 20px;
        }
        .discounted-price {
            text-decoration: line-through;
            color: #888;
        }
        .product-description {
            margin-top: 20px;
        }
        .carousel-item img {
            width: 100%;
            height: 500px;
            object-fit: cover;
        }
        .horizontal-scroll {
            display: flex;
            overflow-x: auto;
            gap: 1rem;
            margin-top: 30px;
            padding: 1rem 0;
        }
        .product-card {
            width: 18rem;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            flex-shrink: 0; /* Prevent shrinking */
        }
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .product-card .card-body {
            padding: 1rem;
        }
        .product-card .card-body h5 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        .product-card .card-body p {
            font-size: 1.1rem;
            color: #333;
        }
        .product-card .card-body .btn {
            width: 100%;
            margin-top: 10px;
        }
        @media (max-width: 768px) {
            .product-card {
                width: 16rem;
            }
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
        .search-bar {
            display: none;
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            width: 300px;
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
        <img src="<?php echo $logo; ?>" alt="Logo" height="50px" style="border-radius:50%">
    </div>
    <div class="auth-buttons">
        <i class="fas fa-search" id="searchIcon"></i>
    </div>
    <div class="search-bar" id="searchBar">
    <input type="text" id="searchInput" placeholder="Search related products..." onkeyup="filterRelatedProducts()">
</div>

</div>
<div class="container">
    <!-- Product Images Slider -->
    <div id="productCarousel" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <?php foreach ($product_images as $index => $image): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="<?php echo $image; ?>" alt="Product Image">
                </div>
            <?php endforeach; ?>
        </div>
        <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <!-- Product Details -->
    <div class="product-detail">
        <div class="product-info">
            <h3><?php echo $product['name']; ?></h3>

            <!-- Price -->
            <div class="price">
                <?php
                    $originalPrice = $product['price'];
                    $discount = $product['discount'];
                    $discountAmount = ($originalPrice * $discount) / 100;
                    $discountedPrice = $originalPrice - $discountAmount;
                ?>
                <?php if ($discount > 0): ?>
                    <span class="discounted-price">$<?php echo number_format($originalPrice, 2); ?></span>
                <?php endif; ?>
                <strong>$<?php echo number_format($discountedPrice, 2); ?></strong>
            </div>

            <!-- Description -->
            <div class="product-description">
                <p><strong>Description:</strong> <?php echo $product['description']; ?></p>
            </div>

            <!-- Additional Details -->
            <?php if ($discount > 0): ?>
                <p style="color: red;">Discount: <?php echo $discount; ?>% off</p>
                <p style="color: red;">You save: $<?php echo number_format($discountAmount, 2); ?></p>
            <?php endif; ?>

            <a href="order-form.php?product_id=<?php echo $product['id']; ?>" class="btn btn-success">Order Now</a>
        </div>
    </div>
   <br><br><br>
    <!-- Related Products -->
    <h3>Related Products</h3>
    <div class="horizontal-scroll" id="productsContainer">
    <?php if (isset($related_products) && !empty($related_products)): ?>
        <?php foreach ($related_products as $related_product): ?>
            <div class="card product-card shadow text-center" data-name="<?php echo strtolower($related_product['name']); ?>" style="width: 13rem;">
                <img src="<?php echo isset($related_product['image']) ? $related_product['image'] : 'path/to/default-image.jpg'; ?>" class="card-img-top" alt="<?php echo isset($related_product['name']) ? $related_product['name'] : 'Related Product'; ?>" style="height: 150px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title"><?php echo isset($related_product['name']) ? $related_product['name'] : 'Unknown Product'; ?></h5>
                    <p class="card-text text-danger"><strong>$<?php echo number_format(isset($related_product['price']) ? $related_product['price'] : 0, 2); ?></strong></p>
                    <a href="product-detail.php?product_id=<?php echo isset($related_product['id']) ? $related_product['id'] : ''; ?>" class="btn btn-danger">View Details</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No related products found. Please search for related products.</p>
    <?php endif; ?>
</div>


<br><br><br>
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
    const query = document.getElementById('searchInput').value.toLowerCase(); // Get search term

    // Make an AJAX request to fetch matching products
    fetch(`fetch_products.php?query=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(data => {
            // Inject the HTML into the product grid
            document.getElementById('productGrid').innerHTML = data;
        })
        .catch(error => {
            console.error('Error fetching products:', error);
        });
}


// Call filterProducts on keyup in the search input to perform live filtering
document.getElementById('searchInput').addEventListener('keyup', filterProducts);
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    const productsContainer = document.getElementById("productsContainer");
    const categoriesContainer = document.getElementById("categoriesContainer");
    const scrollPrev = document.getElementById("scrollPrev");
    const scrollNext = document.getElementById("scrollNext");
    const categoryLinks = document.querySelectorAll(".category-link");
    const productCards = document.querySelectorAll(".product-card");

    // Scroll left when the previous button is clicked
    scrollPrev.addEventListener("click", () => {
        productsContainer.scrollBy({ left: -300, behavior: "smooth" });
    });

    // Scroll right when the next button is clicked
    scrollNext.addEventListener("click", () => {
        productsContainer.scrollBy({ left: 300, behavior: "smooth" });
    });

    // Handle Category Click
    categoryLinks.forEach((link) => {
        link.addEventListener("click", function () {
            // Highlight the active category
            categoryLinks.forEach((item) => item.classList.remove("active"));
            this.classList.add("active");

            // Get selected category
            const selectedCategory = this.parentElement.getAttribute("data-name");

            // Show/Hide products based on category
            productCards.forEach((card) => {
                const productCategory = card.getAttribute("data-category");
                if (selectedCategory === "all" || productCategory === selectedCategory) {
                    card.style.display = "inline-block";
                } else {
                    card.style.display = "none";
                }
            });
        });
    });
});
function filterRelatedProducts() {
    var input = document.getElementById('searchInput'); // Get the input element
    var filter = input.value.toLowerCase(); // Get the value and convert to lowercase
    var products = document.querySelectorAll('.product-card'); // Get all related product cards

    // Loop through all product cards and hide those that don't match the search
    products.forEach(function(product) {
        var productName = product.getAttribute('data-name'); // Get the product name from data-name attribute
        if (productName.includes(filter)) {
            product.style.display = ''; // Show product
        } else {
            product.style.display = 'none'; // Hide product
        }
    });
}
    </script>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

</body>
</html>
