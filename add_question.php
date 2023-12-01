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

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $question_text = $_POST["question"];
    $user_answer = $_POST["user_answer"];

    // Check if the question already exists for the user
    $sql_check_question = "SELECT id FROM questions WHERE user_id = ? AND question = ?";
    $stmt_check_question = $conn->prepare($sql_check_question);
    $stmt_check_question->bind_param("is", $user_id, $question_text);
    $stmt_check_question->execute();
    $stmt_check_question->store_result();

    // If the question already exists, display a warning
    if ($stmt_check_question->num_rows > 0) {
        $notificationMessage = "Warning: Question already exists for this user!";
        echo '<script>showNotification("' . $notificationMessage . '")</script>';
        $stmt_check_question->close();
    } else {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Insert the question into the database
            $sql_insert_question = "INSERT INTO questions (user_id, question) VALUES (?, ?)";
            $stmt_insert_question = $conn->prepare($sql_insert_question);
            $stmt_insert_question->bind_param("is", $user_id, $question_text);
            $stmt_insert_question->execute();

            // Get the inserted question ID
            $question_id = $stmt_insert_question->insert_id;

            // Insert the answer into the database
            $sql_insert_answer = "INSERT INTO answers (user_id, question_id, user_answer) VALUES (?, ?, ?)";
            $stmt_insert_answer = $conn->prepare($sql_insert_answer);
            $stmt_insert_answer->bind_param("iis", $user_id, $question_id, $user_answer);
            $stmt_insert_answer->execute();

            // Commit the transaction
            $conn->commit();

            // Notification for successful addition
            $notificationMessage = "Question and Answer added successfully!";
            echo '<script>
                    showNotification("' . $notificationMessage . '");
                    alert("Successfully Added");
                    window.location.href = "manage_questions.php";
                  </script>';
        } catch (Exception $e) {
            // Rollback the transaction on exception
            $conn->rollback();
            $notificationMessage = "Error: " . $e->getMessage();
            echo '<script>showNotification("' . $notificationMessage . '")</script>';
        }

        // Close the prepared statements
        $stmt_insert_question->close();
        $stmt_insert_answer->close();
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
    <title>Add Question</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
        
        <!-- Include Bootstrap JS and jQuery -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

        <script>
        function showNotification(message) {
            // Set the notification content
            document.getElementById("notificationModalBody").innerHTML = message;
            // Show the modal
            $('#notificationModal').modal('show');
        }
        </script>
<div class="modal" id="notificationModal" tabindex="-1" role="dialog" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notification</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="notificationModalBody">
                <!-- Notification content will be displayed here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="container mt-5">
    <h2>Add New Question</h2>
    <a href="home.php" class="btn btn-primary">Back to Home</a>
    <a href="manage_questions.php" class="btn btn-primary">Back to Manage Questions</a>
    <!-- Form to add a new question -->
    <form method="post" action="add_question.php" class="mt-3">
        <div class="form-group">
            <label for="question">Question:</label>
            <input type="text" name="question" id="question" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="user_answer">Answer:</label>
            <textarea name="user_answer" id="user_answer" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-success">Add Question</button>
    </form>
</div>

<!-- Include Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
     <!-- Bootstrap Modal for Notifications -->


</body>
</html>
