<?php
include 'connection.php'; // Database connection

// Start session
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: account.php"); // Redirect to login page
    exit();
}
// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Query to fetch user data from the database
$sql = "SELECT * FROM users WHERE id = '$user_id' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}
$sql = "SELECT invite_code FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $invite_code = $row['invite_code'];
} else {
    $invite_code = "No invite code found"; // Fallback if no invite code is found
}
 

$logo_sql = "SELECT logo FROM logo ORDER BY id DESC LIMIT 1";
$logo_result = mysqli_query($conn, $logo_sql);
$logo = 'images/default-logo.png'; // Default logo
if ($logo_result && mysqli_num_rows($logo_result) > 0) {
    $row = mysqli_fetch_assoc($logo_result);
    $logo = $row['logo'];
}
$sql_wallet = "SELECT total_balance FROM wallet WHERE user_id = ?";

$stmt_wallet = $conn->prepare($sql_wallet);
if ($stmt_wallet === false) {
    die('MySQL prepare error: ' . $conn->error);
}

$stmt_wallet->bind_param("i", $user_id); // Bind user_id to the prepared statement
$stmt_wallet->execute(); // Execute the query

$result_wallet = $stmt_wallet->get_result();
$totalBalance = 0.00; // Default value if no balance is found
if ($row_wallet = $result_wallet->fetch_assoc()) {
    $totalBalance = $row_wallet['total_balance']; // Get total balance from the wallet
}

// Fetch total approved deposits for the user
$sql_total_deposits = "SELECT SUM(amount) AS total_deposit FROM deposits WHERE user_id = ? AND status = 'approved'";
$stmt_total_deposits = $conn->prepare($sql_total_deposits);
if ($stmt_total_deposits === false) {
    die('MySQL prepare error: ' . $conn->error);
}
$stmt_total_deposits->bind_param("i", $user_id); // Bind user_id to the prepared statement
$stmt_total_deposits->execute(); // Execute the query
$result_deposits = $stmt_total_deposits->get_result();
$totalDeposit = 0.00; // Default value if no deposits are found
if ($row_deposits = $result_deposits->fetch_assoc()) {
    $totalDeposit = $row_deposits['total_deposit']; // Get total deposits
}

// Fetch total approved withdrawals for the user
$sql_total_withdrawals = "SELECT SUM(amount) AS total_withdrawal FROM withdrawals WHERE user_id = ? AND status = 'approved'";
$stmt_total_withdrawals = $conn->prepare($sql_total_withdrawals);
if ($stmt_total_withdrawals === false) {
    die('MySQL prepare error: ' . $conn->error);
}
$stmt_total_withdrawals->bind_param("i", $user_id); // Bind user_id to the prepared statement
$stmt_total_withdrawals->execute(); // Execute the query
$result_withdrawals = $stmt_total_withdrawals->get_result();
$totalWithdrawal = 0.00; // Default value if no withdrawals are found
if ($row_withdrawals = $result_withdrawals->fetch_assoc()) {
    $totalWithdrawal = $row_withdrawals['total_withdrawal']; // Get total withdrawals
}

// Calculate the final total balance (wallet balance + approved deposits - approved withdrawals)
$totalBalance += $totalDeposit - $totalWithdrawal;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .profile-container {
            max-width: 400px;
            margin: auto;
            background: #fff;
            border-radius: 15px;
            overflow: hidden;
            margin-top: 55px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .profile-header {
            background-color: #FFD54F;
            /* text-align: center; */
            padding: 20px;
            padding-bottom: 54px;
            padding-top: 54px;
            position: relative;
            color: #fff;
            display:flex;
            gap:6px;
            border-bottom-left-radius: 57px;
    border-bottom-right-radius: 57px;
            
            
        }

        .profile-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
        }

        .balance-section {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background-color: #fff;
            border-radius: 10px;
            position: relative;
        }

        .balance-section .balance {
            font-size: 39px;
            font-weight: bold;
            letter-spacing: 3px;
            text-align: left;
        }

        .balance-section .eye-icon {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            cursor: pointer;
            color: #888;
        }

        .icon-buttons {
            display: flex;
            justify-content: space-between;
            margin: 20px 10px;
        }

        .icon-button {
            text-align: center;
            width: 70px;
            flex-direction: column;
        }

        .icon-button i {
            font-size: 28px;
        }

        .icon-button p {
            margin: 5px 0 0;
            font-size: 14px;
        }

        /* Icon Colors */
        .icon-button.orange-icon i {
            color: #FF7043; /* Bright Orange */
        }

        .icon-button.green-icon i {
            color: #4CAF50; /* Bright Green */
        }

        .icon-button.yellow-icon i {
            color: #FFC107; /* Bright Yellow */
        }

        .icon-button.blue-icon i {
            color: #42A5F5; /* Bright Blue */
        }

        .sections {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 10px;
        }

        .section-item {
            flex: 1 1 48%;
            background-color: #fff;
            padding: 15px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .section-item:hover {
            transform: translateY(-5px);
        }

        .section-item i {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .section-item i.blue-icon {
            color: #42A5F5; /* Bright Blue */
        }

        .section-item i.green-icon {
            color: #4CAF50; /* Bright Green */
        }

        .section-item i.orange-icon {
            color: #FF7043; /* Bright Orange */
        }

        .section-item i.yellow-icon {
            color: #FFC107; /* Bright Yellow */
        }

        .section-item p {
            margin: 0;
        }

        .section-title {
            font-weight: bold;
            margin-top: 5px;
        }

        .section-desc {
            font-size: 13px;
            color: #666;
        }

        /* Referral Program and Revoke Styles */
        .admin-section {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #f8f9fa;
            border-top: 1px solid #ddd;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .admin-section:hover {
            background-color: #fff;
        }

        .admin-section i {
            font-size: 24px;
            margin-right: 15px;
        }

        .admin-section.referral i {
            color: #4CAF50; /* Green for Referral */
        }

        .admin-section.revoke i {
            color: #FF7043; /* Orange for Revoke */
        }

        .admin-section .admin-text {
            flex-grow: 1;
        }

        .admin-section .admin-title {
            font-weight: bold;
            margin: 0;
        }

        .admin-section .admin-desc {
            font-size: 13px;
            color: #666;
        }

        .admin-section .arrow {
            font-size: 18px;
            color: #888;
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
    color:rgb(232, 232, 59); /* Highlight color on hover */
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
    border: 2px solid rgb(232, 232, 59); /* Adds a border for visual focus */
}

.promotion-item img:hover {
    border-color:rgb(232, 232, 59); /* Darker border color on hover */
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
        .dec {
    background-color: #fff; /* White background */
    border-radius: 15px; /* Rounded corners */
    box-shadow: 0 -8px 20px rgba(0, 0, 0, 0.3); /* Stronger shadow at the top */
    padding: 15px;
    margin-top: -55px;
}
#balance {
    color: black; /* Black stars */
    animation: blink 1.5s infinite; /* Blink the stars every 1.5 seconds */
}

.eye-icon {
    color: black; /* Black eye icon */
    animation: blink 1.5s infinite; /* Blink the eye icon every 1.5 seconds */
}

/* Keyframes for blinking animation */
@keyframes blink {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0;
    }
    100% {
        opacity: 1;
    }
}

    </style>
</head>
<body>
<div class="navbar2">
    <div class="logo">
        <img src="<?php echo $logo; ?>" alt="Logo" height="50px" style="border-radius:50%">
    </div>
    <!-- <div class="auth-buttons">
        <i class="fas fa-search" id="searchIcon"></i>
    </div>
    <div class="search-bar" id="searchBar">
        <input type="text" id="searchInput" placeholder="Search products..." onkeyup="filterProducts()">
    </div> -->

</div>
    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
    <img src="men.jpeg" alt="Profile Picture">
    <!-- <h2><?php echo htmlspecialchars($user['name']); ?></h2> -->
     <div>
    <b style="text-align: left;">ID: <?php echo htmlspecialchars($user['id']); ?></b><br>
    <b style="text-align: left;">Mobile Number: <?php echo $user['phone_number']; ?></b>
     
    </div>
</div>

        <!-- Balance Section -->
        <div class="dec">
        <div class="balance-section">
            <p style="text-align:left;">Balance</p>
    <div id="balance" class="balance">********</div>
    <i id="toggleEye" class="fas fa-eye eye-icon"></i>
    <span id="actual-balance" style="display: none;"><?php echo number_format($totalBalance, 2); ?></span> <!-- Hidden balance -->
</div>

        <!-- Icons Section -->
        <div class="icon-buttons">
            <a href="wallet.php" style="text-decoration:none; color:black"><div class="icon-button orange-icon">
                <i class="fas fa-wallet"></i>
                <p>Wallet</p></a>
            </div>
           <a href="deposit.php" style="text-decoration:none; color:black"> <div class="icon-button orange-icon">
                <i class="fas fa-money-bill"></i>
                <p>Deposit</p></a>
            </div>
            <a href="withdrawals.php" style="text-decoration:none; color:black"><div class="icon-button green-icon">
                <i class="fas fa-credit-card"></i>
                <p>Withdraw</p></a>
            </div>
           <a href="vip.php"style="text-decoration:none; color:black"> <div class="icon-button yellow-icon">
                <i class="fas fa-crown"></i>
                <p>VIP</p></a>
            </div>
        </div>
    </div>
        <!-- Sections -->
        <div class="sections">
            <div class="section-item">
                <i class="fas fa-gamepad blue-icon"></i>
               <a href="order_details.php" style="text-decoration:none; color:black"> <p class="section-title">Order History</p>
                <p class="section-desc">My game history</p></a>
            </div>
            <div class="section-item">
                <i class="fas fa-file-invoice green-icon"></i>
                <a href="" style="text-decoration:none; color:black"><p class="section-title">Transaction</p>
                <p class="section-desc">My transaction history</p></a>
            </div>
            <div class="section-item">
                <i class="fas fa-money-check-alt orange-icon"></i>
                <a href="deposit_history.php" style="text-decoration:none; color:black"><p class="section-title">Deposit History</p>
                <p class="section-desc">My deposit history</p></a>
            </div>
            <div class="section-item">
                <i class="fas fa-hand-holding-usd yellow-icon"></i>
                <a href="withdraw_history.php" style="text-decoration:none; color:black"><p class="section-title">Withdraw History</p>
                <p class="section-desc">My withdraw history</p></a>
            </div>
        </div>

        <!-- Referral Program -->
        <div class="admin-section referral">
            <i class="fas fa-user-friends"></i>
            <div class="admin-text">
                <p class="admin-title">Referral Program</p>
                <p class="admin-desc">Invite friends and earn rewards</p>
            </div>
            <i class="fas fa-chevron-right arrow"></i>
        </div>

        <!-- Revoke -->
        <div class="admin-section revoke">
            <i class="fas fa-undo-alt"></i>
            <div class="admin-text">
                <p class="admin-title">Revoke</p>
                <p class="admin-desc">Cancel transactions or actions</p>
            </div>
            <i class="fas fa-chevron-right arrow"></i>
        </div>
    
    
     <div class="admin-section ">
            
            <div class="admin-text">
               <a href="logout.php" class="btn btn-danger" style="width:100%; background-color:white; border:1px solid red; color:black;">Logout</a>
            </div>
</div>

    </div><br>
    <br><br>
    <div id="bottom-navbar" class="bottom-navbar">
    <a href="index.php" class="nav-item">
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
</body>
<script>
        // JavaScript to toggle balance visibility
const balanceElement = document.getElementById("balance");
const toggleEyeIcon = document.getElementById("toggleEye");
const actualBalanceElement = document.getElementById("actual-balance");

let isBalanceHidden = true;

toggleEyeIcon.addEventListener("click", () => {
    if (isBalanceHidden) {
        balanceElement.textContent = "₹" + actualBalanceElement.textContent; // Show actual balance
        toggleEyeIcon.classList.remove("fa-eye");
        toggleEyeIcon.classList.add("fa-eye-slash");
    } else {
        balanceElement.textContent = "********"; // Hide balance
        toggleEyeIcon.classList.remove("fa-eye-slash");
        toggleEyeIcon.classList.add("fa-eye");
    }
    isBalanceHidden = !isBalanceHidden;
});

    </script>
</html>
