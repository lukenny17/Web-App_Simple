<?php
include 'db.php';

$bookingID = $_POST['bookingID'];
$status = $_POST['status'];

$stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE bookingID = ?");
$stmt->bind_param("si", $status, $bookingID);
$stmt->execute();

echo $stmt->affected_rows > 0 ? "Status updated successfully." : "No changes made.";

$stmt->close();
$conn->close();
?>