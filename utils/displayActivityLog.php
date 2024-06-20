<?php
include 'db.php';

$date = $_GET['date'] ?? '';

$query = "SELECT u.name, a.activity, a.timestamp 
          FROM user_activities a 
          JOIN users u ON a.userID = u.userID 
          WHERE DATE(a.timestamp) = ?
          ORDER BY a.timestamp DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$activities = [];
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}

echo json_encode($activities);
exit;
?>
