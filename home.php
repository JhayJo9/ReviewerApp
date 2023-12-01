<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    // Redirect to the login page if not logged in
    header("Location: index.php");
    exit;
}

// Include the configuration file
include("config.php");

// Assuming $user_id is the ID of the logged-in user
$user_id = $_SESSION["user_id"];

// Retrieve questions and answers for the user using prepared statement
$sql = "SELECT q.id AS question_id, q.question, q.answer_text
        FROM questions q
        LEFT JOIN answers a ON q.id = a.question_id AND a.user_id = ?
        WHERE q.user_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }

        .container {
            text-align: center;
        }

        .btn {
            margin: 5px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>Welcome, <?php echo $_SESSION["username"]; ?>!</h2>
    <a href="logout.php" class="btn btn-danger">Logout</a>
    <a href="add_question.php" class="btn btn-success">Add Question</a>
    <a href="manage_questions.php" class="btn btn-success">Manage Questions</a>
    <a href="show_question.php" class="btn btn-info">View Questions</a>
</div>

<!-- Include Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>