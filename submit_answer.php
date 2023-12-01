<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
    // Redirect to the login page
    header("Location: index.php");
    exit;
}

// Include the configuration file
include("config.php");

// Assuming $user_id is the ID of the logged-in user
$user_id = $_SESSION["user_id"];

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $question_id = $_POST["question_id"];
    $user_answer = $_POST["user_answer"];

    // Update the answer in the database
    $sql_update_answer = "UPDATE answers SET user_answer = ? WHERE question_id = ? AND user_id = ?";
    $stmt_update_answer = $conn->prepare($sql_update_answer);
    $stmt_update_answer->bind_param("sii", $user_answer, $question_id, $user_id);

    if ($stmt_update_answer->execute()) {
        echo "Answer submitted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }

    // Close the prepared statement
    $stmt_update_answer->close();
} else {
    // Redirect to view_questions.php if the form was not submitted
    header("Location: view_questions.php");
    exit;
}

// Close the database connection
$conn->close();
?>
