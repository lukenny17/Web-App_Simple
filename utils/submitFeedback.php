<?php
session_start();
include '../utils/db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['userid'], $_POST['serviceID'], $_POST['comment'], $_POST['rating'])) {
    $userID = $_SESSION['userid'];
    $serviceID = $_POST['serviceID'];
    $comment = $_POST['comment'];
    $rating = $_POST['rating'];

    // Insert feedback into database
    $stmt = $conn->prepare("INSERT INTO feedback (userID, serviceID, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $userID, $serviceID, $rating, $comment);
    if ($stmt->execute()) {
        $message = "Feedback submitted successfully.";
    } else {
        $message = "Error submitting feedback: " . $stmt->error;
    }
    $stmt->close();
}

$conn->close();
?>