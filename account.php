<?php
include 'connection.php'; // Database connection

// Start session at the beginning of the script
session_start();

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$error_message = "";
if (isset($_SESSION['user_id'])) {
  // If already logged in, redirect to profile page
  header("Location: profile.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Dummy authentication (replace with database logic)
  $phone_number = $_POST['phone_number'];
  $password = $_POST['password'];

  if ($username === 'user' && $password === 'password') { // Example credentials
      $_SESSION['user_id'] = 1; // Set session variable for logged-in user
      $_SESSION['username'] = $username; // Optional: Store username in session
      header("Location: profile.php"); // Redirect to profile page
      exit();
  } else {
      $error = "Invalid username or password.";
  }
}
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize it
    $country_code = mysqli_real_escape_string($conn, $_POST['country_code']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Combine country code with phone number to form the full phone number
    $full_phone_number = $country_code . $phone_number;

    // Query to check if the phone number exists and if the user is active
    $sql = "SELECT * FROM users WHERE phone_number = '$full_phone_number' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User exists, fetch data
        $user = $result->fetch_assoc();

        // Check if the user is blocked
        if ($user['status'] == 0) {
            // User is blocked
            $error_message = "Your account has been blocked. Please contact customer support.";
        } else {
            // Check if the password matches
            if ($user['password'] === $password) {
                // Password matches, log in the user by storing user_id in the session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['phone_number'] = $user['phone_number'];

                // Redirect to the profile page
                header("Location: profile.php");
                exit();
            } else {
                // Invalid password
                $error_message = "Incorrect password.";
            }
        }
    } else {
        // User not found
        $error_message = "Phone number not registered.";
    }
}


// Close the database connection
$conn->close();
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
    Login
    <img src="https://img.icons8.com/color/48/000000/usa.png" alt="EN" class="lang-icon">
</div>

<div class="container">
  <div class="form-header">
    <img src="https://img.icons8.com/color/48/000000/phone.png" alt="Phone Icon">
    <h5>Log in with phone</h5>
    <p class="text-muted">Please log in with your phone number or email<br>If you forget your password, please contact customer service</p>
  </div>

  <form method="POST" action="account.php">
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
      <label>Password</label>
      <input type="password" name="password" class="form-control" placeholder="******" required>
    </div>

    <div class="form-check">
      <input type="checkbox" class="form-check-input" id="rememberPassword">
      <label class="form-check-label" for="rememberPassword">Remember password</label>
    </div>

    <!-- Error Message (if any) -->
    <?php if (isset($error_message)) { ?>
      <div class="alert alert-danger mt-2"><?php echo $error_message; ?></div>
    <?php } ?>

    <button type="submit" class="btn btn-login btn-block mt-4">Log in</button>
    <a href="register.php"><button type="button" class="btn btn-register btn-block">Register</button></a>
  </form>

  <div class="footer-links">
    <div>
      <img src="https://img.icons8.com/color/48/000000/lock.png" alt="Forgot Password">
      <p>Forgot password</p>
    </div>
    <div>
      <img src="https://img.icons8.com/color/48/000000/customer-support.png" alt="Customer Service">
      <p>Customer Service</p>
    </div>
  </div>
</div>

</body>
</html>