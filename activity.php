<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;  /* Ensure the header stays above other content */
}

.header i {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 20px;
    color: yellow;
}

.activity {
    background-color: #ffd700;
    height: 170px;
    color: white;
    text-align: center;
    margin-top:64px; /* Added margin-top to push content below the header */
}

.activity h2 {
    margin-top: 50px; /* Optional: Adjust this if needed */
}
.activity p {
    padding-left: 20px; /* Add left padding */
    padding-right: 20px; /* Add right padding */
}
.section-title {
            text-align: center;
            margin-top: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        .reward-item {
            /* background-color: #fff; */
            padding: 15px;
            border-radius: 10px;
            /* box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); */
            text-align: center;
        }

        .reward-item img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .reward-item p {
            margin-top: 10px;
            color: #333;
        }
        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .card img {
            width: 100%;
            height: auto;
            border-radius: 10px 10px 0 0;
        }

        .card-body {
            padding: 15px;
            text-align: center;
        }

        .card-body h4 {
            font-size:30px;
            color: black;
        }

        .card-body p {
            color: #777;
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
    Daily Rewards
    <i class="fa fa-headset" style="font-size: 20px; color: white;"></i>
</div>
<div class="activity">
   <h2>Activity</h2>
   <p>Please remember to follow the event page
   We will launch user feedback activities from
                  time to time
               
   </p>
</div>
<div class="container" style="font-size:25px">
    <div class="row g-4 mt-4">
        <!-- First Item -->
        <div class="col-3 col-sm-3 col-md-3 col-lg-2">
            <div class="reward-item text-center">
               <a href="activity_Awards.php"> <img src="activityReward-66772619.png" alt="Activity Award" class="img-fluid">
                <p style="font-size:16px">Activity Award</p></a>
            </div>
        </div>
        <!-- Second Item -->
        <div class="col-3 col-sm-3 col-md-3 col-lg-2">
            <div class="reward-item text-center">
               <a href="bonus.php"><img src="invitationBonus-aa7acbd3.png" alt="Invitation Bonus" class="img-fluid">
                <p style="font-size:16px">Invitation Bonus</p></a>
            </div>
        </div>
        <!-- Third Item -->
        <div class="col-3 col-sm-3 col-md-3 col-lg-2">
            <div class="reward-item text-center">
                <img src="BettingRebate-17d35455.png" alt="Betting Rebate" class="img-fluid">
                <p style="font-size:16px">Betting Rebate</p>
            </div>
        </div>
        <!-- Fourth Item -->
        <div class="col-3 col-sm-3 col-md-3 col-lg-2">
            <div class="reward-item text-center">
                <img src="superJackpot-ecb648b4.png" alt="Super Jackpot" class="img-fluid">
                <p style="font-size:16px">Super Jackpot</p>
            </div>
        </div>
        <!-- Fifth Item -->
        <div class="col-3 col-sm-3 col-md-3 col-lg-2">
            <div class="reward-item text-center">
                <img src="memberGift-a0182789.png" alt="New Member" class="img-fluid">
                <p style="font-size:16px">New Member</p>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <div class="row g-4">
        <!-- First Card -->
        <div class=" col-6">
            <div class="card">
                <img src="signInBanner-33f86d3f.png" alt="Card Image">
                <div class="card-body">
                    <h3 class="card-title">Gifts</h3>
                    <p class="card-text">This is a description of sdfg aw sr aw  sdfgs wee a the first card. You can put any content here.</p>
                </div>
            </div>
        </div>

        <!-- Second Card -->
        <div class=" col-6">
            <div class="card">
                <img src="giftRedeem-45917887.png" alt="Card Image">
                <div class="card-body">
                    <h3 class="card-title">Attendance bonus</h3>
                    <p class="card-text">This is a description of the second card. You can also put any content here.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mt-5">
    <div class="row g-4">
        <!-- First Card -->
        <div class="col-12">
            <div class="card">
                <img src="Banner_20240828005909a8so.jpg" alt="Card Image">
                <div class="card-body">
                    <h2 class="card-title">Extra BONUS Aviator</h2>
                    
                </div>
            </div>
        </div>

        <!-- Second Card -->
        <div class="col-12">
            <div class="card">
                <img src="Banner_20240809192826wkea.jpg" alt="Card Image">
                <div class="card-body">
                    <h2 class="card-title">VIP Level Bonus</h2>
                
                </div>
            </div>
        </div>
    </div>
</div><br><br><br><br>
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
</html>
