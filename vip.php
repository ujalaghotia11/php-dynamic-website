<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{
            background-color: #f5f7fc
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
.header-section {
            background-color: #ffd93d;
            color: #ffffff;
            text-align: center;
            padding: 74px 10px;
            position: relative;
            margin-top:60px;
        }

        .header-section img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #ffffff;
        }

        .header-section h3 {
            font-weight: bold;
            margin-top: 10px;
            font-size: 1.5rem;
            margin-left: 35px;

        }
        .experience-section .box {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
            position:relative;
            top:-50px;
        }

        .experience-section .box:hover {
            transform: translateY(-5px);
        }

        .experience-section .exp-value,
        .experience-section .payout-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #f4c700;
        }

        .experience-section div p {
            margin: 0;
            font-size: 1rem;
            color: #6c757d;
        }
        .vip-card {
            background-color: #e6eefb;
            padding: 36px;
            border-radius: 8px;
            /* margin: 20px; */
            color: #4b4f56;
        }

        .vip-card h4 {
            font-size: 1.8rem;
            font-weight: bold;
            color: #4a6ed1;
        }

        .vip-card p {
            margin: 0;
            color: #333;
        }

        .vip-card span {
            color: #28a745;
            font-weight: bold;
        }

        .vip-icon {
            float: right;
            width: 80px;
            height: 80px;
        }
        .footer-text {
            background-color: #ffffff;
            /* padding: 10px; */
            text-align: center;
            color: #6c757d;
        }
        .vip-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 10px;
        }

        /* Header Section */
        .vip-header {
            font-size: 1.5rem;
            font-weight: bold;
            color: #303972;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .wallet-icon {
            width: 40px;
            height: 40px;
            background-color: #ffdd57;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        /* Benefit Boxes */
        .benefit-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .benefit-item:last-child {
            border-bottom: none;
        }

        .benefit-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .benefit-left i {
            font-size: 3rem;
            color: #ffdd57;
            background-color: #fff2d4;
            border-radius: 50%;
            padding: 10px;
        }

        .benefit-right {
            /* display: flex; */
            align-items: center;
            gap: 10px;
            justify-content: space-between;
            flex-direction: column;
        
        }

        .benefit-box {
            padding: 5px;
            /* background-color: #fff7d6; */
            border: 1px solid #ffdd57;
            border-radius: 5px;
            margin-right: -10px;
            /* display: flex; */
            width: 72px;
        
            align-items: center;
            justify-content: center;
        }

        .benefit-value {
            font-size: 1rem;
            font-weight: bold;
            color: #ff9800;
        }

        /* Footer Buttons */
        .vip-footer {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .vip-btn {
            width: 40%;
            padding: 10px;
            font-size: 1rem;
            text-align: center;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        .vip-btn.history {
            background-color: #fff7d6;
            color: #ffbe00;
            border: 2px solid #ffbe00;
        }

        .vip-btn.rules {
            background-color: #f5f6fa;
            color: #b0b5c2;
        }
        </style>

</head>
<body>
<div class="header">
    VIP
    <i class="fa fa-headset" style="font-size: 20px; color: white;"></i>
</div>
<div class="header-section" style="display:flex;">
        
        <img src="https://via.placeholder.com/80" alt="Profile Picture"style=" margin-left:20px ;">
        <div>
        <img src="" style="margin-left:-60px;"><br>
        <h5>MemberNNGVBLHY</h5>
       </div>

    </div>

    <!-- Experience and Payout Section -->
    <div class="container">
    <div class="row g-4 experience-section text-center">
            <!-- First Box -->
            <div class="col-6 col-md-6 col-lg-6">
                <div class="box shadow">
                    <div class="exp-value">207658 EXP</div>
                    <p>My experience</p>
                </div>
            </div>
            <!-- Second Box -->
            <div class="col-6 col-md-6 col-lg-6">
                <div class="box shadow">
                    <div class="payout-value">0 Days</div>
                    <p>Payout time</p>
                </div>
            </div>
        </div>
        

        <div class="footer-text" style="border:1px solid black; text-align:center;">
            VIP level rewards are settled at 2:00 am on the 1st of every month
        </div>
        
        <div class="vip-card shadow" style="background:linear-gradient(117.29deg, #a6b7d0 21.85%, #889ebe 67.02%); margin-top:20px; border-radius:10px;">
            <h4>
                <span>VIP1</span> Achieved
            </h4>

            <p>Dear <strong>VIP1</strong> customer</p>
            <p class="fw-bold">Received VIP level up bonus1</p>
            <!-- <img src="https://via.placeholder.com/80" class="vip-icon" alt="VIP Badge"> -->
        </div>


      
    
    <div class="vip-container">
        <!-- Header -->
        <div class="vip-header">
            <div class="wallet-icon">
                <i class="fas fa-wallet"></i>
            </div>
            VIP1 Benefits level
        </div>

        <!-- Benefits List -->
        <div class="benefit-item">
            <div class="benefit-left">
                <i class="fas fa-gift"></i>
                <div>
                    <div style="font-weight: bold;">Level up rewards</div>
                    <small>Each account can only receive 1 time</small>
                </div>
            </div>
            <div class="benefit-right d-flex">
                <div class="benefit-box">
                    <span class="benefit-value">170</span>
                    <i class="fas fa-coins" style="color: #ffdd57;"></i>
                </div>
                <div class="benefit-box">
                    <span class="benefit-value">0</span>
                    <i class="fas fa-gift" style="color: #ffdd57;"></i>
                </div>
            </div>
        </div>

        <div class="benefit-item">
            <div class="benefit-left">
                <i class="fas fa-award"></i>
                <div>
                    <div style="font-weight: bold;">Monthly reward</div>
                    <small>Each account can only receive 1 time per month</small>
                </div>
            </div>
            <div class="benefit-right d-flex">
                <div class="benefit-box">
                    <span class="benefit-value">100</span>
                    <i class="fas fa-coins" style="color: #ffdd57;"></i>
                </div>
                <div class="benefit-box">
                    <span class="benefit-value">0</span>
                    <i class="fas fa-award" style="color: #ffdd57;"></i>
            </div>
        </div>
    </div>

        <div class="benefit-item">
            <div class="benefit-left">
                <i class="fas fa-coins"></i>
                <div>
                    <div style="font-weight: bold;">Rebate rate</div>
                    <small>Increase income of rebate</small>
                </div>
            </div>
            <div class="benefit-right d-flex">
                <div class="benefit-box">
                    <span class="benefit-value">0.05%</span>
                    <!-- <i class="fas fa-percent" style="color: #ffdd57;"></i> -->
                </div>
            </div>
        </div>

        <!-- Footer Buttons -->
        <div class="vip-footer">
            <button class="vip-btn history">History</button>
            <button class="vip-btn rules">Rules</button>
        </div>
    </div>
    </div>
</body>
</html>