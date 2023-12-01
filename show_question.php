<?php
// Start the session
session_start();

// Redirect to the login page if the user is not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

// Include the configuration file
include("config.php");

// Get the user ID from the session
$user_id = $_SESSION["user_id"];

// Check if the form has been submitted to show the question or answer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["question_id"])) {
    $question_id = $_POST["question_id"];

    // Check if the button for showing the answer is clicked
    if (isset($_POST["show_answer"])) {
        // Retrieve the answer for the selected question_id
        $sqlAnswer = "SELECT user_answer FROM answers WHERE user_id = ? AND question_id = ?";
        $stmtAnswer = $conn->prepare($sqlAnswer);
        $stmtAnswer->bind_param("ii", $user_id, $question_id);
        $stmtAnswer->execute();
        $resultAnswer = $stmtAnswer->get_result();
        $stmtAnswer->close();

        if ($resultAnswer->num_rows > 0) {
            $rowAnswer = $resultAnswer->fetch_assoc();
            $answer = $rowAnswer['user_answer'];
            $answerOutput = "<p class='mt-3'><strong>Answer:</strong> $answer</p>";
        } else {
            $answerOutput = "<p class='mt-3'><strong>No answer found for the selected question.</strong></p>";
        }

        echo $answerOutput;
        exit;
    } else {
        // Retrieve the question for the selected question_id
        $sqlQuestion = "SELECT question FROM questions WHERE user_id = ? AND id = ?";
        $stmtQuestion = $conn->prepare($sqlQuestion);
        $stmtQuestion->bind_param("ii", $user_id, $question_id);
        $stmtQuestion->execute();
        $resultQuestion = $stmtQuestion->get_result();
        $stmtQuestion->close();

        if ($resultQuestion->num_rows > 0) {
            $rowQuestion = $resultQuestion->fetch_assoc();
            $question = $rowQuestion['question'];
            $questionOutput = "<p class='mt-3'><strong>Question:</strong> $question</p>";
        } else {
            $questionOutput = "<p class='mt-3'><strong>No question found for the selected ID.</strong></p>";
        }

        echo $questionOutput;
        exit;
    }
}

// Retrieve the current question ID from the session
$currentQuestionId = isset($_SESSION['current_question_id']) ? $_SESSION['current_question_id'] : 0;

// Retrieve the next question ID
$sqlNextQuestion = "SELECT id AS question_id
                    FROM questions
                    WHERE user_id = ? AND id > ?
                    ORDER BY id ASC
                    LIMIT 1";

$stmtNextQuestion = $conn->prepare($sqlNextQuestion);
$stmtNextQuestion->bind_param("ii", $user_id, $currentQuestionId);
$stmtNextQuestion->execute();
$resultNextQuestion = $stmtNextQuestion->get_result();
$stmtNextQuestion->close();

if ($resultNextQuestion->num_rows > 0) {
    $rowNextQuestion = $resultNextQuestion->fetch_assoc();
    $nextQuestionId = $rowNextQuestion['question_id'];
} else {
    // If no next question is found, reset to the first question
    $sqlFirstQuestion = "SELECT id AS question_id
                        FROM questions
                        WHERE user_id = ?
                        ORDER BY id ASC
                        LIMIT 1";

    $stmtFirstQuestion = $conn->prepare($sqlFirstQuestion);
    $stmtFirstQuestion->bind_param("i", $user_id);
    $stmtFirstQuestion->execute();
    $resultFirstQuestion = $stmtFirstQuestion->get_result();
    $stmtFirstQuestion->close();

    if ($resultFirstQuestion->num_rows > 0) {
        $rowFirstQuestion = $resultFirstQuestion->fetch_assoc();
        $nextQuestionId = $rowFirstQuestion['question_id'];
    } else {
        $nextQuestionId = 0;
    }
}

// Store the next question ID in the session
$_SESSION['current_question_id'] = $nextQuestionId;

// Retrieve the next question and answer
$sqlNextQuestionText = "SELECT question FROM questions WHERE user_id = ? AND id = ?";
$stmtNextQuestionText = $conn->prepare($sqlNextQuestionText);
$stmtNextQuestionText->bind_param("ii", $user_id, $nextQuestionId);
$stmtNextQuestionText->execute();
$resultNextQuestionText = $stmtNextQuestionText->get_result();
$stmtNextQuestionText->close();

if ($resultNextQuestionText->num_rows > 0) {
    $rowNextQuestionText = $resultNextQuestionText->fetch_assoc();
    $nextQuestion = $rowNextQuestionText['question'];

    // Retrieve the answer for the next question
    $sqlNextAnswer = "SELECT user_answer FROM answers WHERE user_id = ? AND question_id = ?";
    $stmtNextAnswer = $conn->prepare($sqlNextAnswer);
    $stmtNextAnswer->bind_param("ii", $user_id, $nextQuestionId);
    $stmtNextAnswer->execute();
    $resultNextAnswer = $stmtNextAnswer->get_result();
    $stmtNextAnswer->close();

    if ($resultNextAnswer->num_rows > 0) {
        $rowNextAnswer = $resultNextAnswer->fetch_assoc();
        $nextAnswer = $rowNextAnswer['user_answer'];
        $nextAnswerOutput = "<p class='mt-3'><strong>Answer:</strong> $nextAnswer</p>";
    } else {
        $nextAnswerOutput = "<p class='mt-3'><strong>No answer found for the next question.</strong></p>";
    }
} else {
    $nextQuestion = "No next question found";
    $nextAnswerOutput = "";
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Show Question</title>
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include custom styles -->
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
            width: 80%;
        }

        .card {
            margin-top: 20px;
        }

        #answerContainer {
            margin-top: 20px;
        }

        .btn-margin {
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Show Question</h2>
    <a href="home.php" class="btn btn-primary btn-block btn-margin">Back to Home</a>

    <?php if ($nextQuestionId > 0) { ?>
        <!-- Display the next question and answer -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Question:</h5>
                <p class="card-text"><?php echo $nextQuestion; ?></p>
                <button type="button" class="btn btn-info btn-block btn-margin" id="showAnswerBtn">Show Answer</button>
                <div id="answerContainer" style="display: none;">
                    <?php echo $nextAnswerOutput; ?>
                </div>
            </div>
        </div>

        <!-- Form to show the next question -->
        <form method="post" class="mt-3" id="showNextQuestionForm">
            <input type="hidden" name="question_id" value="<?php echo $nextQuestionId; ?>">
            <button type="submit" class="btn btn-success btn-block btn-margin" onClick="window.location.reload();">Next Question</button>
        </form>
    <?php } else { ?>
        <!-- Display message when no next question is found -->
        <p class="mt-3 text-center"><strong><?php echo $nextQuestion; ?></strong></p>
    <?php } ?>
</div>

<!-- Include Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function () {
        // Show answer when the button is clicked
        $('#showAnswerBtn').click(function () {
            $('#answerContainer').toggle();
        });

        // Show next question or answer on form submission
        $('#showNextQuestionForm').submit(function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: 'show_question.php',
                data: $(this).serialize(),
                success: function (response) {
                    $('#answerContainer').html(response);
                },
                error: function () {
                    alert('Error fetching next question or answer. Please try again.');
                }
            });
        });
    });
</script>

</body>
</html>

