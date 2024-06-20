<?php
header('Content-Type: application/json');
include 'db.php';

$staffID = $_GET['staffID'] ?? ''; // Default to empty if not provided
$statusFilter = $_GET['status'] ?? ''; // Default to empty if not provided

// Construct the base query
$query = "SELECT b.bookingID, s.serviceName, b.startTime, s.duration, b.status
          FROM bookings b
          JOIN services s ON b.serviceID = s.serviceID
          WHERE b.staffID = ?";

// If a status filter is provided, add it to the query
if (!empty($statusFilter)) {
    $query .= " AND b.status = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $staffID, $statusFilter);
} else {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $staffID);
}

$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($bookings);
exit;
?>
