<?php
session_start(); // Start the session
include 'connection.php'; // Include your DB connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $country_code = mysqli_real_escape_string($conn, $_POST['country_code']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); // Storing plain text password
    $invite_code = mysqli_real_escape_string($conn, $_POST['invite_code']); // Invite code for registration

    // Validate if the invite code is provided and valid
    if (empty($invite_code)) {
        echo "<script>alert('Invite code is required!');</script>";
        exit(); // Stop registration if no invite code is provided
    }

    // Validate invite code to find the referrer
    $referred_by = null;
    // Check if the invite code exists in the database
    $query = "SELECT id FROM users WHERE invite_code = '$invite_code' LIMIT 1";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $referred_by = $row['id']; // ID of the referring user
    } else {
        // If the invite code is invalid, alert the user
        echo "<script>alert('Invalid invite code! Please try again.');</script>";
        $conn->close();
        exit(); // Stop registration if the invite code is invalid
    }

    // Insert the new user into the users table (without referral code yet)
    $sql = "INSERT INTO users (phone_number, password, referral_code,invite_code) 
            VALUES ('$country_code$phone_number', '$password','invite_code', '$referral_code')";

    if ($conn->query($sql) === TRUE) {
        $user_id = $conn->insert_id; // Get the last inserted user ID

        // Generate a unique invite code for the user (based on user ID)
        $unique_invite_code = strtoupper(substr(md5($user_id . uniqid(rand(), true)), 0, 8));

        // Update the users table with the generated invite code as the referral code
        $update_sql = "UPDATE users SET referral_code = '$invite_code', invite_code = '$unique_invite_code' WHERE id = $user_id";
        if ($conn->query($update_sql) === TRUE) {
            // Create a wallet for the user
            $sql_create_wallet = "INSERT INTO wallet (user_id, total_balance) VALUES ($user_id, 0)";
            if ($conn->query($sql_create_wallet) === TRUE) {
                // Show success message and the referral link
                echo "<script>alert('Registration successful! Your referral code is $unique_invite_code.');</script>";
                
                // Redirect to the account page
                header("Location: account.php");
                exit();
            } else {
                echo "Error creating wallet: " . $conn->error;
            }
        } else {
            echo "Error updating invite code: " . $conn->error;
        }
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      max-width: 375px;
      margin: 0 auto;
      padding: 0;
      background-color: #f7c600;
      font-family: Arial, sans-serif;
      padding: 0;
      overflow-x: hidden;
      background-color: gainsboro;
    }
    .container {
      background-color: white;
      padding: 20px;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    }
    .header {
      text-align: center;
      color: white;
      font-weight: bold;
      padding: 10px 0;
      font-size: 18px;
      background-color: #f7c600;
      position: relative;
    }
    .header .back-arrow {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 24px;
      cursor: pointer;
      color: white;
    }
    .header .lang-icon {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
    }
    .form-header {
      text-align: center;
      color: #f7c600;
      margin-bottom: 20px;
    }
    .form-header img {
      width: 40px;
      height: 40px;
      margin-bottom: 10px;
    }
    .form-group label {
      font-weight: bold;
      color: #f7c600;
      font-size: 14px;
    }
    .form-control {
      background-color: #f1f4f8;
      border: none;
      border-radius: 10px;
    }
    .form-check-label {
      font-size: 14px;
    }
    .btn-login {
      background-color: #f7c600;
      color: white;
      border-radius: 20px;
      padding: 10px 0;
      font-weight: bold;
    }
    .btn-register {
      border: 2px solid #f7c600;
      border-radius: 20px;
      padding: 10px 0;
      font-weight: bold;
      color: #f7c600;
      background: none;
      margin-top: 10px;
    }
    .footer-links {
      display: flex;
      justify-content: space-around;
      padding-top: 20px;
      color: #f7c600;
      font-weight: bold;
      text-align: center;
    }
    .footer-links img {
      width: 24px;
      height: 24px;
      margin-bottom: 5px;
    }
  </style>
</head>
<body>
  <div class="header">
    <a href="index.php"><span class="back-arrow">&larr;</span></a>
    Register
    <img src="https://img.icons8.com/color/48/000000/usa.png" alt="EN" class="lang-icon">
  </div>

  <div class="container">
    <div class="form-header">
      <img src="https://img.icons8.com/color/48/000000/phone.png" alt="Phone Icon">
      <h5>Register your phone</h5>
     <hr style="border:1px solid  #f7c600;">
    </div>

    <form action="register.php" method="POST">
   <div class="form-group">
     <label>Phone number</label>
     <div class="d-flex">
       <select name="country_code" class="form-control mr-2" style="width: 80px;">
         <option>+91</option>
         <!-- Add other country codes as needed -->
       </select>
       <input type="text" name="phone_number" class="form-control" placeholder="1234567891" required>
     </div>
   </div>

   <div class="form-group">
     <label>Set Password</label>
     <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
   </div>
   <div class="form-group">
     <label>Invite code</label>
     <input type="text" name="invite_code" class="form-control" placeholder="Please enter the invitation code">
   </div>

   <div class="form-check">
     <input type="checkbox" class="form-check-input" id="rememberPassword" required>
     <label class="form-check-label" for="rememberPassword">I have read and agree <span style="color: #f7c600">[Privacy Agreement]</span></label>
   </div>

   <button type="submit" class="btn btn-login btn-block mt-4">Register</button>
   <a href="account.php" style="text-decoration:none;">
     <button type="button" class="btn btn-register btn-block">I have an account <span>Login</span></button>
   </a>
 </form>

    <div class="footer-links">
      <div>
        <img src="https://img.icons8.com/color/48/000000/lock.png" alt="Forgot Password">
        <p>Forgot password</p>
      </div>
      <div>
        <a href="customer_service.php" style="text-decoration:none;"><img src="https://img.icons8.com/color/48/000000/customer-support.png" alt="Customer Service">
        <p>Customer Service</p></a>
      </div>
    </div>
  </div>

  <!-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script> -->
</body>
</html>
