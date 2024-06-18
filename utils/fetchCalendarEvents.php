<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);


include 'db.php';

header('Content-Type: application/json');

// Prepare and execute the query to fetch events with staff information
$query = "SELECT 
            bookings.bookingID, 
            bookings.startTime, 
            DATE_ADD(bookings.startTime, INTERVAL services.duration HOUR) as endTime, 
            services.serviceName AS title, 
            bookings.status, 
            users.name as staffName
          FROM bookings
          JOIN services ON bookings.serviceID = services.serviceID
          LEFT JOIN users ON bookings.staffID = users.userID";

$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    // Assign color based on status
    $color = '#4D96FF'; // Default blue for scheduled
    if ($row['status'] === 'completed') {
        $color = '#75C692'; // Green for completed
    } elseif ($row['status'] === 'cancelled') {
        $color = '#FF9999'; // Red for cancelled
    }

    $events[] = [
        'id'    => $row['bookingID'],
        'title' => $row['title'] . " - " . $row['staffName'],
        'start' => $row['startTime'],
        'end'   => $row['endTime'],
        'color' => $color
    ];
}

echo json_encode($events);

$stmt->close();
$conn->close();
?>
