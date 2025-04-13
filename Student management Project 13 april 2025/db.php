<?php
// db.php - Database connection
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'student_manager';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}

// Function to check if a student ID exists in the students table
function studentExists($conn, $student_id) {
    $stmt = $conn->prepare("SELECT id FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
?>