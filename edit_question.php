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

// Get the question ID from the URL parameter
$question_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Retrieve the question and answer text for editing
$sql = "SELECT q.id AS question_id, q.question, a.user_answer
        FROM questions q
        LEFT JOIN answers a ON q.id = a.question_id AND a.user_id = ?
        WHERE q.user_id = ? AND q.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $user_id, $question_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_question = $conn->real_escape_string($_POST["question"]);
    $new_user_answer = $conn->real_escape_string($_POST["user_answer"]);

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Update the question in the database
        $sql_update_question = "UPDATE questions SET question = ? WHERE id = ? AND user_id = ?";
        $stmt_update_question = $conn->prepare($sql_update_question);
        $stmt_update_question->bind_param("sii", $new_question, $question_id, $user_id);
        $stmt_update_question->execute();

        // Update the answer in the database
        $sql_update_answer = "UPDATE answers SET user_answer = ? WHERE question_id = ? AND user_id = ?";
        $stmt_update_answer = $conn->prepare($sql_update_answer);
        $stmt_update_answer->bind_param("sii", $new_user_answer, $question_id, $user_id);
        $stmt_update_answer->execute();

        // Commit the transaction
        $conn->commit();
        
        echo "Question and Answer updated successfully!";
        
    } catch (Exception $e) {
        // Rollback the transaction on exception
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }

    // Close the prepared statements
    $stmt_update_question->close();
    $stmt_update_answer->close();
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Question</h2>
    <a href="home.php" class="btn btn-primary">Back to Home</a>

    <?php
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $question = $row['question'];
        $user_answer = $row['user_answer'];
    ?>
        <!-- Form to edit the question -->
        <form method="post" action="edit_question.php?id=<?php echo $question_id; ?>" class="mt-3">
            <div class="form-group">
                <label for="question">Question:</label>
                <input type="text" name="question" id="question" class="form-control" value="<?php echo $question; ?>" required>
            </div>
            <div class="form-group">
                <label for="user_answer">Answer:</label>
                <textarea name="user_answer" id="user_answer" class="form-control" rows="3" required><?php echo $user_answer; ?></textarea>
            </div>
            <button type="submit" class="btn btn-success">Update Question</button>
        </form>
    <?php
    } else {
        echo "<p>No question found for editing.</p>";
    }
    ?>
</div>

<!-- Include Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
