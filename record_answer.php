<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $questionId = $_POST['question_id'];
    $selectedAnswer = $_POST['selected_answer'];
    $isCorrect = $_POST['is_correct'] === 'true' ? 1 : 0;  // Store boolean as integer (1 or 0)

    // Load the questions from the JSON file
    $jsonData = file_get_contents(__DIR__ . '/questions.json');
    $questions = json_decode($jsonData, true);

    // Search for the question in the JSON data using the question ID
    $question = 'Unknown question';
    foreach ($questions as $q) {
        if ($q['id'] == $questionId) {
            $question = $q['question'];
            break;
        }
    }

    // Log the question ID and question text to the error log
    error_log("Recording answer for Question ID: $questionId, Question Text: $question");

    // Database connection details
    $servername = '';
    $dbname = '';
    $username = '';
    $password = '';

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind the statement
    $stmt = $conn->prepare("INSERT INTO quiz_answers (question, question_id, selected_answer, is_correct) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sisi", $question, $questionId, $selectedAnswer, $isCorrect);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Answer recorded successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close connections
    $stmt->close();
    $conn->close();
}
?>