<?php
// Include the Router class
require 'Router.php';

// Initialize the Router
$router = new Router();

// Define the routes
$router->get('/admin', function() {
    include('loginadmin.php');  // Redirect to login page
});

$router->get('/admin/login', function() {
    include('loginadmin.php');  // Redirect to login page
});

// POST route for login form submission
$router->post('/admin/login', function() {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Authentication logic goes here
        echo "Login successful! Email: $email, Password: $password";  
    } else {
        echo "Please enter email and password.";
    }
});

// Start the router to listen for requests
$router->listen();
?>
