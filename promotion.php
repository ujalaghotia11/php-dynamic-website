 <?php
include 'connection.php';
 if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the invitation code for the logged-in user (assuming session is started)
session_start();
$user_id = $_SESSION['user_id'];  // Assuming the user ID is stored in session
$sql = "SELECT invite_code FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);  // Bind the user ID parameter to the query
$stmt->execute();
$result = $stmt->get_result();

// Get the invitation code if available
$invitation_code = '';
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $invitation_code = $row['invite_code'];
} else {
    $invitation_code = 'No Code Available'; // Fallback if no code is found
}

// Close the connection
$stmt->close();

?>
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotion Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f7f8ff;
            font-family: Arial, sans-serif;
        }

        .header {
            background-color: #ffd700;
            color: white;
            text-align: center;
            padding: 20px 0;
            font-weight: bold;
        }

        .card {
            background: #fff;
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            /* top:-173px; */

            margin-bottom: 1rem;
            /* padding: 15px; */

        }

         .card h6 {
            background: #ffd700;
            color: white;
            padding: 5px;
            /* border-radius: 5px; */
            margin-bottom: 15px;
            font-weight: bold;
        } 

        .highlight {
            background: linear-gradient(180deg, #ffd946, #ffc700);
            color: white;
            text-align: center;
            font-size: 14px;
            padding: 10px 0;
            /* margin-bottom: 1rem; */
            font-weight: bold;
            position:relative;
            height:300px;
            /* border-radius: 10px; */
        }

        .highlight h2 {
            color: #ff0000;
            margin: 0;
        }

        .btn-yellow {
            background-color: #ffd700;
            color: white;
            font-weight: bold;
            border-radius: 20px;
            border: none;
            width: 65%;
            padding: 10px 0;
            margin-left:18%;
        }

        .btn-yellow:hover {
            background-color: #ffc107;
        }

        .section-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 1rem;
        }

        .promotion-data {
            background-color:white;
            color:#757575;
            text-align: center;
            padding: 10px;
            border-radius: 10px;
            /* font-weight: bold; */
            margin-top: 10px;
        }

        .promotion-data span {
            display: block;
            font-size: 14px;
        }

        .card .data-item {
            margin-bottom: 10px;
        }

        .data-item span {
            color: #ff0000;
            font-weight: bold;
            display: block;
            font-size: 16px;
        }

        .data-item small {
            color: #555;
            font-size: 12px;
        }
        p {
    margin-top: 7px;
    margin-bottom: 1rem;
}
p {
    background-color: white;
    border-radius: 10px;
}
p {
    width: 50%;
    color: yellow;
    margin-left: 25%;
}
.list-group{
    gap:10px;
}
.yellow-icon {
    color: yellow;
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
    color: rgb(228, 213, 48); /* Highlight color on hover */
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
    border: 2px solid rgb(228, 213, 48); /* Adds a border for visual focus */
}

.promotion-item img:hover {
    border-color: rgb(228, 213, 48); /* Darker border color on hover */
}


    </style>
</head>
<body>

<div class="header">
    Promotion
</div>

<!-- <div class="container mt-4"> -->
    <div class="highlight">
        <h2>0</h2>
        <p>Yesterday's total commission</p>
        <small>Upgrade the level to increase commission income</small>
    </div>

    <div class="row" style="margin-top: -173px;">
        <div class="col-2"></div>
    <!-- Direct Subordinates Column -->
    <div class="col-4" style="padding-right: 0; padding-left: 0;">
        <div class="card text-center" style="border-right: 2px solid #000; border-radius: 0;">
            <h6><i class="fa fa-users yellow-icon" style="font-size: 20px; margin-right: 10px;"></i> Direct Subordinates</h6>
            <div class="data-item">
                <span style="color: green;">0</span>
                <small>Number of Registers</small>
            </div>
            <div class="data-item">
                <span style="color: red;">0</span>
                <small>Deposit Number</small>
            </div>
            <div class="data-item">
                <span style="color: blue;">0</span>
                <small>Total Deposit</small>
            </div>
            <div class="data-item">
                <span style="color: orange;">0</span>
                <small>Number of people making first deposit</small>
            </div>
        </div>
    </div>
    
    <!-- Team Subordinates Column -->
    <div class="col-4" style="padding-left: 0; padding-right: 0;">
        <div class="card text-center" style="border-radius: 0;">
            <h6><i class="fa fa-users-cog yellow-icon" style="font-size: 20px; margin-right: 10px;"></i> Team Subordinates</h6>
            <div class="data-item">
                <span style="color: green;">0</span>
                <small>Number of Registers</small>   
            </div>
            <div class="data-item">
                <span style="color: red;">0</span>
                <small>Deposit Number</small>
            </div>
            <div class="data-item">
                <span style="color: blue;">0</span>
                <small>Total Deposit</small>
            </div>
            <div class="data-item">
                <span style="color: orange;">0</span>
                <small>Number of people making first deposit</small>
            </div>
        </div>
    </div>
</div>


    <button class="btn-yellow">INVITATION LINK</button>

    <div class="list-group mt-3">
    <!-- Copy Invitation Code with icon -->
    <a href="#" class="list-group-item list-group-item-action">
        <i class="fa fa-copy yellow-icon" style="font-size: 20px; margin-right: 10px;"></i> Copy Invitation Code 
        <span class="float-end"><?= htmlspecialchars($invitation_code) ?></span> 
        <i class="fa fa-copy float-end" style="font-size: 20px; margin-left: 10px;"></i>
    </a>
    
    <!-- Subordinate Data with image -->
    <a href="#" class="list-group-item list-group-item-action">
        <img src="comm.png" alt="Subordinate Data" style="height: 20px; width: 20px; margin-right: 10px;"> Subordinate Data
        <i class="fa fa-chevron-right float-end" style="font-size: 20px;"></i>
    </a>
    
    <!-- Commission Detail with image -->
    <a href="#" class="list-group-item list-group-item-action">
        <img src="teams.png" alt="Commission Detail" style="height: 20px; width: 20px; margin-right: 10px;"> Commission Detail
        <i class="fa fa-chevron-right float-end" style="font-size: 20px;"></i>
    </a>
    
    <!-- Invitation Rules with image -->
    <a href="#" class="list-group-item list-group-item-action">
        <img src="rules.png" alt="Invitation Rules" style="height: 20px; width: 20px; margin-right: 10px;"> Invitation Rules
        <i class="fa fa-chevron-right float-end" style="font-size: 20px;"></i>
    </a>
    
    <!-- Agent Line Customer Service with icon -->
    <a href="#" class="list-group-item list-group-item-action">
        <i class="fa fa-headset yellow-icon" style="font-size: 20px; margin-right: 10px;"></i> Agent Line Customer Service
        <i class="fa fa-chevron-right float-end" style="font-size: 20px;"></i>
    </a>
    
    <!-- Rebate Ratio with icon -->
    <a href="#" class="list-group-item list-group-item-action">
        <i class="fa fa-percent yellow-icon" style="font-size: 20px; margin-right: 10px;"></i> Rebate Ratio
        <i class="fa fa-chevron-right float-end" style="font-size: 20px;"></i>
    </a>
</div>


    <div class="promotion-data">
    <a href="#" class="list-group-item list-group-item-action" style="text-align:left; color:black;">
        <i class="fa fa-gift yellow-icon" style="font-size: 20px; margin-right: 10px;"></i> Promotion-data
    </a>
    <div class="row d-flex" style="margin-top: 25px;">
        <div class="col-6" style="border-right: 2px solid #000;">
            <span>35<br> This Week</span><br>
            <span>37<br> Direct Subordinates</span>
        </div>
        <div class="col-6">
            <span>101<br>Total Commission</span><br>
            <span>97<br> Total Subordinates in the Team</span>
        </div>
    </div>
</div>
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
<br><br><br><br>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
