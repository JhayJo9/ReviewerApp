<?php
// Check if BASE_URL is not defined before defining it
if (!defined("BASE_URL")) {
    // Define your site's base URL
    define("BASE_URL", "http://unocutie.onlinewebshop.net"); // Change this to your actual base URL
}

// Database Configuration
$hostname = "fdb1032.awardspace.net"; // Your database server address
$username = "4409076_reviewer"; // Your database username
$password = "JhayTheGosu1212@"; // Your database password
$database = "4409076_reviewer"; // Your database name

// Create Connection
$conn = new mysqli($hostname, $username, $password, $database);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set Charset
mysqli_set_charset($conn, "utf8mb4");
?>
