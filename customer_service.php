<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Service</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .header {
      background-color: #f7c600;
      padding: 15px;
      color: white;
      text-align: center;
      font-weight: bold;
      position: relative;
    }
    .header .back-arrow {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 24px;
      color: white;
      cursor: pointer;
    }
    .banner {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
    }
    .banner img {
      width: 100px;
      height: 100%;
      border-radius:50%;
    }
    .icon {
      position: absolute;
      font-size: 24px;
      color: #f7c600;
    }
    .icon-left {
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
    }
    .icon-right {
      right: 20px;
      top: 50%;
      transform: translateY(-50%);
    }
    .service-option {
      background-color: white;
      padding: 15px;
      margin: 10px 0;
      display: flex;
      align-items: center;
      border-radius: 10px;
    }
    .service-option img {
      width: 24px;
      height: 24px;
      margin-right: 10px;
    }
    .service-option .arrow {
      margin-left: auto;
      font-size: 16px;
      color: gray;
    }
    body {
      max-width: 375px; /* Limiting width for mobile view on larger screens */
      margin: 0 auto;
      padding: 0;
      overflow-x: hidden;
      background-color: gainsboro;
    }
    .main {
      background-color: white;
      min-height: 100vh; /* Full screen height */
    }
  </style>
</head>
<body>
<div class="main">
  <div class="header">
   <a href="index.php"> <span class="back-arrow">&larr;</span></a>
    Customer Service
  </div>

  <div class="banner">
  <!-- Make the entire anchor tag clickable and apply cursor styling -->
  <span class="icon icon-left">
    &#128172; <!-- Chat Icon -->
</span>
  <img src="helpcenterimg.jpeg" alt="Customer Service" style="width: 100px; height: 100%;">
  <span class="icon icon-right">&#128515;</span> <!-- Smiley Icon -->
</div>

  <div class="container">
    <div class="service-option">
      <img src="https://img.icons8.com/color/48/000000/phone.png" alt="LiveChat Icon"> <!-- Phone icon -->
      <span>LiveChat</span>
      <span class="arrow">&gt;</span>
    </div>

    <div class="service-option">
      <img src="https://img.icons8.com/color/48/000000/telegram-app.png" alt="Telegram Icon"> <!-- Telegram icon -->
      <span>Telegram</span>
      <span class="arrow">&gt;</span>
    </div>
  </div>
</div>

<!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
</body>
</html>
