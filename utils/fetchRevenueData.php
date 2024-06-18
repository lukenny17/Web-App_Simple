<?php
include '../utils/db.php'; // Adjust the path as necessary

$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];

$query = $conn->prepare("SELECT status, SUM(s.cost) AS revenue 
                         FROM bookings b
                         JOIN services s ON b.serviceID = s.serviceID
                         WHERE DATE(b.startTime) BETWEEN ? AND ? AND b.status IN ('completed', 'scheduled', 'cancelled')
                         GROUP BY b.status
                         ORDER BY b.status");
$query->bind_param("ss", $startDate, $endDate);
$query->execute();
$result = $query->get_result();

$revenues = ['completed' => 0, 'scheduled' => 0, 'cancelled' => 0]; // Initialize all possible statuses

while ($row = $result->fetch_assoc()) {
    $revenues[$row['status']] = (float) $row['revenue'];
}

echo json_encode($revenues);

$query->close();
$conn->close();
?>
