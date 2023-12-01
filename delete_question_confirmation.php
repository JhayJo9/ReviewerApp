<?php
include("config.php");
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

// Assuming $user_id is the ID of the logged-in user
$user_id = $_SESSION["user_id"];

// Check if the question ID is provided in the URL
if (isset($_GET["id"])) {
    $question_id = $_GET["id"];

    // Retrieve question details for confirmation
    $sql = "SELECT question FROM questions WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $question_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $question = $result->fetch_assoc()["question"];
    $stmt->close();
} else {
    // Redirect if no question ID is provided
    header("Location: manage_questions.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Question Confirmation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Delete Question Confirmation</h2>
        <p>Are you sure you want to delete the following question?</p>
        <p><strong>Question:</strong> <?php echo $question; ?></p>
        <form method="post" action="delete_question.php">
            <input type="hidden" name="question_id" value="<?php echo $question_id; ?>">
            <button type="submit" class="btn btn-danger">Yes, Delete</button>
            <a href="manage_questions.php" class="btn btn-secondary">No, Cancel</a>
        </form>
    </div>
</body>
</html>
