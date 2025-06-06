<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$servername = '';
$dbname = 'h';
$username = '';
$password = '';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch all scores ordered by highest score
$sql = "SELECT name, score, date_taken FROM scores ORDER BY score DESC";
$result = $conn->query($sql);

// Check if the query was successful
if ($result === false) {
    die("Error in SQL query: " . $conn->error);
}

// Check if any results were returned
if ($result->num_rows > 0) {
    // Create an array to store scores
    $scores = [];
    while($row = $result->fetch_assoc()) {
        $scores[] = [
            'name' => htmlspecialchars($row['name']),
            'score' => htmlspecialchars($row['score']),
            'date_taken' => htmlspecialchars($row['date_taken']),
        ];
    }
    // Return scores in JSON format
    echo json_encode($scores);
} else {
    echo json_encode(["error" => "No scores found."]);
}

$conn->close();
?>
