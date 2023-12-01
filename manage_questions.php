<?php
include("config.php");
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$sql = "SELECT q.id AS question_id, q.question, a.user_answer
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
    <title>Manage Questions - Reviewer</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css"> <!-- Add your custom styles if needed -->
</head>
<body>
    <div class="container mt-5">
        <h2>Manage Questions</h2>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="card mb-3">
                <div class="card-body">
                  
                    <h5 class="card-title">Question</h5>
                    <p class="card-text"><?php echo $row["question"]; ?></p>
                    <h5 class="card-title">Answer</h5>
                    <p class="card-text"><?php echo $row["user_answer"]; ?></p>
                    <div class="btn-group" role="group">
                        <a href="edit_question.php?id=<?php echo $row["question_id"]; ?>" class="btn btn-primary">Edit</a>
                        <a href="delete_question_confirmation.php?id=<?php echo $row["question_id"]; ?>" class="btn btn-danger">Delete</a>
                    </div>
                </div>
            </div>
        <?php } ?>
        <a href="add_question.php" class="btn btn-success">Add Question</a>
        <a href="home.php" class="btn btn-secondary">Back to Home</a>
    </div>

    <!-- Include Bootstrap JS and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
