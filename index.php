<?php
include 'connection.php';
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch categories
$category_sql = "SELECT * FROM categories";
$category_result = mysqli_query($conn, $category_sql);

$categories = [];
while ($row = mysqli_fetch_assoc($category_result)) {
    $categories[] = $row;
}

// Fetch best sellers
$best_seller_sql = "SELECT * FROM products WHERE is_best_seller = 1";
$best_seller_result = mysqli_query($conn, $best_seller_sql);

$best_sellers = [];
while ($row = mysqli_fetch_assoc($best_seller_result)) {
    $best_sellers[] = $row;
}

// Fetch banners
$banner_sql = "SELECT img_path FROM banners ORDER BY id DESC";
$banner_result = mysqli_query($conn, $banner_sql);

$banners = [];
while ($row = mysqli_fetch_assoc($banner_result)) {
    $banners[] = $row['img_path'];
}

// Fetch the latest logo
$logo_sql = "SELECT logo FROM logo ORDER BY id DESC LIMIT 1";
$logo_result = mysqli_query($conn, $logo_sql);
$logo = 'images/default-logo.png'; // Default logo
if ($logo_result && mysqli_num_rows($logo_result) > 0) {
    $row = mysqli_fetch_assoc($logo_result);
    $logo = $row['logo'];
}
$productsQuery = "SELECT id, name, image, description, price, category_id FROM products"; // Replace with your actual table name
$productsResult = $conn->query($productsQuery);

$products = [];
if ($productsResult->num_rows > 0) {
    while ($row = $productsResult->fetch_assoc()) {
        // Attach the category name to the product (if needed for filtering)
        $categoryNameQuery = "SELECT name FROM categories WHERE id = " . $row['category_id'];
        $categoryNameResult = $conn->query($categoryNameQuery);
        if ($categoryNameResult->num_rows > 0) {
            $row['category'] = $categoryNameResult->fetch_assoc()['name'];
        } else {
            $row['category'] = 'Unknown';
        }

        $products[] = $row;
    }
}

// Encode products for JavaScript dynamic rendering (if using AJAX)
if (isset($_GET['fetch']) && $_GET['fetch'] === 'products') {
    header('Content-Type: application/json');
    echo json_encode($products);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-VuNXi1lL9+e5B4GeW60yz6ycnw8xyhA32DDH8JJpMqHe6eeb/+t4BkkG7t27p6mM" crossorigin="anonymous">

<!-- Optional JavaScript and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-ZdtCFfkRxScBG4RBPrRewwEaNOsYHY63c2V4FlPbE1iPeP72RgGM6CIk7B0eDAv1" crossorigin="anonymous"></script>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        body {
            margin: 0 auto;
            overflow-x: hidden;
            background-color: #F2F2F1;
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
        .categories-grid {
    display: flex;
    overflow-x: auto; /* Enable horizontal scroll */
    white-space: nowrap; /* Prevent wrapping */
    gap: 10px; /* Adjust spacing between items */
    padding: 10px; /* Add some padding */
}

.category-card {
    flex: 0 0 auto; /* Prevent shrinking and maintain card size */
    width: 150px; /* Adjust width for better responsiveness */
    text-align: center;
}

.products-grid {
    display: grid; /* Use CSS Grid layout */
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); /* Adjust columns dynamically */
    gap: 20px; /* Space between items */
    justify-content: center; /* Center the grid items */
    padding: 10px; /* Add some padding */
}

.product-card {
    text-align: center; /* Center-align the content */
    background-color: #f9f9f9; /* Add a subtle background color */
    border-radius: 8px; /* Add rounded corners */
    padding: 15px; /* Add padding inside the card */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Add a subtle shadow */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Hover effect animations */
}

.product-card:hover {
    transform: translateY(-5px); /* Slight lift on hover */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); /* Enhance shadow on hover */
}

.product-card img {
    width: 100%; /* Make image responsive */
    height: auto; /* Maintain aspect ratio */
    max-height: 200px; /* Limit maximum height for consistency */
    object-fit: cover; /* Ensure the image fits well */
    border-radius: 8px; /* Rounded corners for the image */
}

        .category-card img {
    width: 100%; /* Ensure the image takes up the full width of the card */
    height: auto; /* Maintain the image's aspect ratio */
    max-height: 150px; /* Set a maximum height to keep images uniform */
    object-fit: cover; /* Crop images to fit within their container */
    border-radius: 8px; /* Add rounded corners for a smooth appearance */
    transition: transform 0.3s ease, box-shadow 0.3s ease; /* Add smooth animations for hover effects */
}

.category-card img:hover {
    transform: scale(1.05); /* Slightly zoom in the image on hover */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add a shadow effect on hover */
}

        .carousel-inner img {
            width: 100%;
            height: 400px;
            object-fit: cover;
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
    color: rgb(234, 215, 46); /* Highlight color on hover */
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
    border: 2px solid rgb(234, 215, 46); /* Adds a border for visual focus */
}

.promotion-item img:hover {
    border-color:rgb(234, 215, 46); /* Darker border color on hover */
}
h2{
    text-align: center;
    padding-top: 12px;
    /* padding-bottom: 34px; */
}
a {
    color: #B71C1C;
    text-decoration: none;
    background-color: transparent;
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
        #productCarousel {
    display: flex;
    overflow-x: auto;
    white-space: nowrap;
    padding-bottom: 1rem;
}

#productCarousel::-webkit-scrollbar {
    display: none; /* Hide the scrollbar */
}

.product-card {
    flex: 0 0 auto; /* Prevent shrinking */
    width: 169px; /* Set a fixed width for cards */
    scroll-snap-align: start;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 10px;
    text-align: center;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
/* Horizontal Scroll Style */
.horizontal-scroll {
    overflow-x: auto;
    white-space: nowrap;
    padding: 10px 0;
}

.horizontal-scroll .category-card {
    display: inline-block;
    flex: 0 0 auto;
    text-align: center;
    width: 70px; /* Ensure small size */
}

.horizontal-scroll .category-icon {
    width: 30px;
    height: 30px;

    object-fit: cover;
    /* border: 2px solid #ddd; */
}

.horizontal-scroll .category-card h6 {
    font-size: 12px; /* Smaller text for icon labels */
    margin-top: 5px;
    color:grey;
}
/* Horizontal Scroll */
.horizontal-scroll {
    overflow-x: auto;
    white-space: nowrap;
    padding: 10px 0;
    /* border: 1px solid #ddd; */
    background-color:white;
    border-radius: 8px;
    scroll-behavior: smooth; /* Smooth scrolling for controls */
    position: relative;
    gap:48px;
}

.horizontal-scroll .product-card {
    display: inline-block;
    flex: 0 0 auto;
}

/* Scroll Control Buttons */
.scroll-control {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background-color: rgba(0, 0, 0, 0.5);
    color: #fff;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
}

.prev-control {
    left: 7px;
}

.next-control {
    right: 0px;
}

.scroll-control:hover {
    background-color: rgba(0, 0, 0, 0.8);
    /* color:black; */
}

/* Active Category Highlight */
.category-link.active {
    /* border: 2px solid #007bff; */
    border-radius: 8px;
    background: linear-gradient(97deg, #ffcb00 9.64%, #ffcb00 84.03%);
    flex-shrink: 0;
    gap: 0.06667rem;
    padding:8px;
    color:black;
    /* width: 2rem; */
    /* height: 0.56667rem; */
    /* margin-left: auto; */
}



    </style>
</head>
<body>

<!-- Top Navbar -->
<div class="navbar2">
    <div class="logo">
        <img src="<?php echo $logo; ?>" alt="Logo" height="50px" style="border-radius:50%">
    </div>
    <div class="auth-buttons">
        <i class="fas fa-search" id="searchIcon"></i>
    </div>
    <div class="search-bar" id="searchBar">
        <input type="text" id="searchInput" placeholder="Search products..." onkeyup="filterProducts()">
    </div>

</div>

<!-- Banner Carousel -->
<div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel" style="margin-top: 60px;">
    <div class="carousel-inner">
        <?php if (count($banners) > 0): ?>
            <?php foreach ($banners as $index => $imgPath): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="<?php echo $imgPath; ?>" class="d-block w-100" alt="Banner">
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="carousel-item active">
                <img src="images/default-banner.jpg" class="d-block w-100" alt="Default Banner">
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Shop by Categories -->
<div class="shop-by-categories mt-4">
    <h2 class="text-center">Shop by Categories</h2>
    <hr>
    <!-- Horizontal Scroll for Categories -->
    <div class="horizontal-scroll">
        <div class="d-flex gap-2 justify-content-start" id="categoriesContainer">
            <!-- "All" Category Icon -->
            <div class="category-card text-center" data-name="all">
                <div class="category-link active">
                    <img src="all-da76a7fc.png" alt="All Categories" class="category-icon">
                    <h6 class="mt-1">All</h6>
        </div>
            </div>
            <!-- Dynamic Category Icons -->
            <?php foreach ($categories as $category): ?>
                <div class="category-card text-center" data-name="<?php echo strtolower($category['name']); ?>">
                    <div class="category-link">
                        <img src="<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>" class="category-icon">
                        <h6 class="mt-1"><?php echo $category['name']; ?></h6>
            </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Products Section -->
<div id="products-section" class="mt-5">
    <h2 class="text-center">Products</h2>
    <hr>
    <div class="position-relative">
        <!-- Scroll Controls -->
        <button class="scroll-control prev-control" id="scrollPrev">
            <span>&lt;</span>
        </button>
        <button class="scroll-control next-control" id="scrollNext">
            <span>&gt;</span>
        </button>
        <!-- Products Horizontal Scroll -->
        <div class="horizontal-scroll d-flex gap-3" id="productsContainer" style="gap:10px;">
            <?php foreach ($products as $product): ?>
                <div class="card product-card shadow text-center" 
                    data-category="<?php echo strtolower($product['category']); ?>" 
                    data-name="<?php echo strtolower($product['name']); ?>"
                    style="width: 13rem;">
                    <img src="<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>" style="height: 150px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title" style="color:red;"><?php echo $product['name']; ?></h5>
                        <p class="card-text text-danger"><strong>$<?php echo $product['price']; ?></strong></p>
                        <a href="product-detail.php?product_id=<?php echo $product['id']; ?>" class="btn btn-success">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php
$sql = "SELECT title, img_path , link FROM mid_banner";
$result = $conn->query($sql);

$banners = [];
if ($result->num_rows > 0) {
    // Fetch each row
    while ($row = $result->fetch_assoc()) {
        $banners[] = $row;
    }
} else {
    echo "No banners found.";
}
?>
<div id="carouselExampleSlidesOnly" class="carousel slide" data-ride="carousel" style="margin-top: 60px;">
    <div class="carousel-inner">
        <?php
        // Fetch banners with their links
        $result = $conn->query("SELECT * FROM mid_banner");
        $banners = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $banners[] = $row;
            }
        }
        ?>
        
        <?php if (count($banners) > 0): ?>
            <?php foreach ($banners as $index => $banner): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <a href="<?php echo $banner['link']; ?>" target="_blank">
                        <img src="<?php echo $banner['img_path']; ?>" class="d-block w-100" alt="Banner" style="height:200px">
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="carousel-item active">
                <img src="images/default-banner.jpg" class="d-block w-100" alt="Default Banner">
            </div>
        <?php endif; ?>
    </div>
</div>


<!-- Best Sellers -->
<div class="best-sellers mt-4">
    <h2>Best Sellers</h2><hr>
    <div class="products-grid">
        <?php foreach ($best_sellers as $product): ?>
            <div class="product-card"data-name="<?php echo strtolower($product['name']); ?>">
            <a href="product-detail.php?product_id=<?php echo $product['id']; ?>">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                <h4 style="color:#B71C1C"><?php echo $product['name']; ?></h4>

                <?php
                // Calculate discounted price and discount amount
                $originalPrice = $product['price'];
                $discount = $product['discount'];
                $discountAmount = ($originalPrice * $discount) / 100;
                $discountedPrice = $originalPrice - $discountAmount;
                ?>

                <p>
                    <!-- Show original price with strike-through if there's a discount -->
                    <?php if ($discount > 0): ?>
                        <span style="text-decoration: line-through; color: red;">$<?php echo number_format($originalPrice, 2); ?></span>
                    <?php endif; ?>
                    <!-- Show the discounted price -->
                    <strong>$<?php echo number_format($discountedPrice, 2); ?></strong>
                </p>

                <!-- Show discount percentage if there's a discount -->
                <?php if ($discount > 0): ?>
                    <p style="color: red;">Discount: <?php echo $discount; ?>% off</p>
                    <p style="color: red;">You save: $<?php echo number_format($discountAmount, 2); ?></p>
                <?php endif; ?>
                <a href="order-form.php?product_id=<?php echo $product['id']; ?>" class="btn btn-success">Order</a>
                
            </div>
        <?php endforeach; ?>
    </div>
</div>
<br><br><br>

<!-- Bottom Navbar -->
<div id="bottom-navbar" class="bottom-navbar">
    <a href="#home" class="nav-item">
        <i class="fa fa-home"></i>
        <span>Home</span>
    </a>
    <a href="activity.php" class="nav-item">
    <i class="fa fa-tasks"></i>
    <span>Activity</span>
    </a>
    <a href="promotion.php" class="nav-item promotion-item">
        <img src="diam1.jpg" alt="Cart" height="50px" width="50px" style="margin-top: -37px; border-radius: 50%;">
    </a>
    <a href="wallet.php" class="nav-item wallet-item">
        <i class="fa fa-wallet"></i>
        <span>Wallet</span>
    </a>
    <div class="nav-item">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- If the user is logged in, show profile link -->
                <a href="profile.php" class="nav-link">
                    <i class="fa fa-user"></i>
                    <span>Profile</span>
                </a>
            <?php else: ?>
                <!-- If the user is not logged in, show account (login) link -->
                <a href="account.php" class="nav-link">
                    <i class="fa fa-user"></i>
                    <span>Account</span>
                </a>
            <?php endif; ?>
        </div>
</div>
<script>
function showProducts(categoryId) {
    let url = categoryId === 'all' ? `fetch_products.php?all=true` : `fetch_products.php?category_id=${categoryId}`;

    fetch(url)
        .then(response => response.json()) // Assume the server returns a JSON array of product data
        .then(products => {
            const productCarousel = document.getElementById('productCarousel');
            productCarousel.innerHTML = ''; // Clear existing products

            // Create and append product cards
            products.forEach(product => {
                const productCard = document.createElement('div');
                productCard.className = 'product-card';

                productCard.innerHTML = `
                    <img src="${product.image}" alt="${product.name}" style="width: 100%; height: 150px; object-fit: cover; border-radius: 8px;">
                    <h5>${product.name}</h5>
                    <p>${product.price}</p>
                `;

                productCarousel.appendChild(productCard);
            });
        })
        .catch(error => {
            console.error('Error fetching products:', error);
        });
}
</script>
<!-- JavaScript -->
<script>
   document.addEventListener("DOMContentLoaded", () => {
    const categoryCards = document.querySelectorAll(".category-card");
    const productCarouselInner = document.querySelector("#productsCarousel .carousel-inner");

    let allProducts = [];

    // Fetch products from the server
    fetch("fetch_products.php") // Adjust endpoint to fetch products
        .then((response) => response.json())
        .then((data) => {
            allProducts = data; // Save all products
            renderProducts("all"); // Show all products initially
        })
        .catch((error) => console.error("Error fetching products:", error));

    // Add click event to categories
    categoryCards.forEach((card) => {
        card.addEventListener("click", () => {
            const category = card.getAttribute("data-name");
            renderProducts(category);
        });
    });

    /**
     * Render products in the carousel
     * @param {string} category - Category to filter by (or 'all' for all products)
     */
    function renderProducts(category) {
        const filteredProducts =
            category === "all"
                ? allProducts
                : allProducts.filter(
                      (product) =>
                          product.category &&
                          product.category.toLowerCase() === category
                  );

        // Clear existing products
        productCarouselInner.innerHTML = "";

        if (filteredProducts.length === 0) {
            productCarouselInner.innerHTML =
                `<div class="carousel-item active text-center"><p>No products found for this category.</p></div>`;
            return;
        }

        const productChunks = chunkArray(filteredProducts, 3); // Split products into chunks of 3

        productChunks.forEach((chunk, index) => {
            const carouselItem = document.createElement("div");
            carouselItem.className = `carousel-item ${index === 0 ? "active" : ""}`;
            const productsRow = document.createElement("div");
            productsRow.className = "d-flex justify-content-center gap-4 flex-wrap";

            chunk.forEach((product) => {
                const productCard = `
                    <div class="card text-center shadow" style="width: 18rem;">
                        <img src="${product.image}" class="card-img-top" alt="${product.name}" style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title">${product.name}</h5>
                            <p class="card-text text-success"><strong>$${product.price}</strong></p>
                            <a href="product-details.php?id=${product.id}" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                `;
                productsRow.innerHTML += productCard;
            });

            carouselItem.appendChild(productsRow);
            productCarouselInner.appendChild(carouselItem);
        });
    }

    /**
     * Helper function to chunk an array into smaller arrays
     * @param {Array} array - Array to chunk
     * @param {number} size - Size of each chunk
     * @returns {Array[]} - Array of chunks
     */
    function chunkArray(array, size) {
        const results = [];
        for (let i = 0; i < array.length; i += size) {
            results.push(array.slice(i, i + size));
        }
        return results;
    }
});

</script>
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
function filterProducts() {
    var input = document.getElementById('searchInput'); // Get the input element
    var filter = input.value.toLowerCase(); // Get the value and convert to lowercase
    var products = document.querySelectorAll('.product-card'); // Get all product cards

    // Loop through all product cards and hide those that don't match the search
    products.forEach(function(product) {
        var productName = product.getAttribute('data-name'); // Get the name from data-name attribute
        if (productName.includes(filter)) {
            product.style.display = ''; // Show product
        } else {
            product.style.display = 'none'; // Hide product
        }
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

    </script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

</body>
</html>
