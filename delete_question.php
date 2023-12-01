<?php
include("config.php");
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

// Assuming $user_id is the ID of the logged-in user
$user_id = $_SESSION["user_id"];

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["question_id"])) {
    $question_id = $_POST["question_id"];

    // Delete associated answers first
    $sql_delete_answers = "DELETE FROM answers WHERE question_id = ?";
    $stmt_delete_answers = $conn->prepare($sql_delete_answers);
    $stmt_delete_answers->bind_param("i", $question_id);
    $stmt_delete_answers->execute();
    $stmt_delete_answers->close();

    // Now, delete the question
    $sql_delete_question = "DELETE FROM questions WHERE id = ? AND user_id = ?";
    $stmt_delete_question = $conn->prepare($sql_delete_question);
    $stmt_delete_question->bind_param("ii", $question_id, $user_id);
    $stmt_delete_question->execute();
    $stmt_delete_question->close();

    // Redirect to the manage questions page after deletion
    header("Location: manage_questions.php");
    exit;
} else {
    // Redirect if the form has not been submitted
    header("Location: manage_questions.php");
    exit;
}
?>
