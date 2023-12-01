<?php 
session_start();
include("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $db_username, $db_password);
        $stmt->fetch();

        if (password_verify($password, $db_password)) {
            // Login successful
            session_start();
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $db_username;
            header("Location: home.php"); // Change this to your main page
            exit;
        } else {
            // Incorrect password
            echo '<script>alert("Incorrect password");</script>';
            // Note: You can also redirect to the login page after displaying the alert
        }
    } else {
        // User not found
        echo '<script>alert("User not found");</script>';
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Reviewer</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css"> <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container mt-5">
        <h2>Login</h2>
        <form action="index.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <p>Don't have an account? <a href="signup_form.php">Signup here</a></p>
    </div>

    <!-- JavaScript to show alert on incorrect password -->
    <script>
        <?php
        // Use PHP to check if there's an alert message to display
        if (isset($_SESSION['alert_message'])) {
            echo 'alert("' . $_SESSION['alert_message'] . '");';
            // Clear the alert message from the session
            unset($_SESSION['alert_message']);
        }
        ?>
    </script>
</body>
</html>
