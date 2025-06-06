<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = ''; // or 'localhost:3306' if required
$dbname = '';
$username = '';
$password = '';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Load the questions from the JSON file
$jsonData = file_get_contents(__DIR__ . '/questions.json');
$questions = json_decode($jsonData, true);

// Adjust column name according to your database schema
$correctColumn = 'is_correct'; // replace this with the correct column name

// Query to get the top 5 correct answers
$correctQuery = "
    SELECT question_id, COUNT(*) as correct_count 
    FROM quiz_answers 
    WHERE $correctColumn = 1 
    GROUP BY question_id 
    ORDER BY correct_count DESC 
    LIMIT 5
";

$correctResult = $conn->query($correctQuery);
if (!$correctResult) {
    die("Error in correct query: " . $conn->error);
}

// Query to get the top 5 incorrect answers
$incorrectQuery = "
    SELECT question_id, COUNT(*) as incorrect_count 
    FROM quiz_answers 
    WHERE $correctColumn = 0 
    GROUP BY question_id 
    ORDER BY incorrect_count DESC 
    LIMIT 5
";

$incorrectResult = $conn->query($incorrectQuery);
if (!$incorrectResult) {
    die("Error in incorrect query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Questions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .questions {
            margin-top: 20px;
        }
        .question {
            display: flex; /* Use flexbox */
            align-items: center; /* Center items vertically */
            margin: 10px 0;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
        }
        .rank {
            font-weight: bold;
            margin-right: 10px;
            font-size: 40px;
        }
                /* Button Styling */
        .btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px 20px;
            font-size: 1.2em;
            color: #fff;
            background-color: #3498db;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Question Statistics</h1>

    <h2>Top 5 Correctly Answered Questions</h2>
    <div class="questions">
        <?php if ($correctResult->num_rows > 0): ?>
            <?php $rank = 1; ?>
            <?php while($row = $correctResult->fetch_assoc()): ?>
                <?php
                // Find the question text for the correct question
                $questionText = '';
                foreach ($questions as $question) {
                    if ($question['id'] == $row['question_id']) {
                        $questionText = $question['question'];
                        break;
                    }
                }
                ?>
                <div class="question">
                    <span class="rank"><?php echo $rank++; ?>.</span>
                    <?php echo htmlspecialchars($questionText); ?> - Correctly Answered: <?php echo $row['correct_count']; ?> times
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div>No questions answered correctly yet.</div>
        <?php endif; ?>
    </div>

    <h2>Top 5 Incorrectly Answered Questions</h2>
    <div class="questions">
        <?php if ($incorrectResult->num_rows > 0): ?>
            <?php $rank = 1; ?>
            <?php while($row = $incorrectResult->fetch_assoc()): ?>
                <?php
                // Find the question text for the incorrect question
                $questionText = '';
                foreach ($questions as $question) {
                    if ($question['id'] == $row['question_id']) {
                        $questionText = $question['question'];
                        break;
                    }
                }
                ?>
                <div class="question">
                    <span class="rank"><?php echo $rank++; ?>.</span>
                    <?php echo htmlspecialchars($questionText); ?> - Incorrectly Answered: <?php echo $row['incorrect_count']; ?> times

                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div>No questions answered incorrectly yet.</div>
        <?php endif; ?>
    </div>
</div>
<a href="index.html" class="btn">Try Again</a>

</body>
</html>

<?php
// Close the connection
$conn->close();
?>
