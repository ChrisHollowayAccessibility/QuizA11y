<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = '';
$dbname = '';
$username = '';
$password = '';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Retrieve name and score from POST request
$name = isset($_POST['name']) ? $_POST['name'] : null;
$score = isset($_POST['score']) ? (int)$_POST['score'] : null;

if ($name && $score !== null) {
    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO scores (name, score) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $score);

    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(["success" => "Score submitted successfully."]);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Error: " . $stmt->error]);
    }

    $stmt->close();
} else {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "Missing name or score data."]);
}

$conn->close();
?>