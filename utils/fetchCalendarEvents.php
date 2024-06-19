<?php
header('Content-Type: application/json');
include 'db.php';

$query = "SELECT 
          bookings.bookingID AS id, 
          bookings.startTime AS start, 
          ADDDATE(bookings.startTime, INTERVAL services.duration HOUR) AS end, 
          services.serviceName AS title, 
          IFNULL(users.name, 'Unassigned') AS staffName, 
          bookings.status 
          FROM bookings 
          JOIN services ON bookings.serviceID = services.serviceID
          LEFT JOIN users ON bookings.staffID = users.userID";

$result = $conn->query($query);
$events = [];
$staffColors = []; // Array to store colors assigned to staff

while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id'    => $row['id'],
        'title' => $row['title'],
        'start' => $row['start'],
        'end'   => $row['end'],
        'status'  => $row['status'],
        'staffName' => $row['staffName'],
        'color' => getColor($row['staffName'], $staffColors)
    ];
}

echo json_encode($events);

function getColor($staffName, &$colors)
{
    if (!isset($colors[$staffName])) {
        // Generate a random color
        $colors[$staffName] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }
    return $colors[$staffName];
}

$conn->close();
