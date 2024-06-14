<?php
include 'db.php';

$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];

function createDateRange($start, $end) {
    $interval = new DateInterval('P1D');
    $realEnd = new DateTime($end);
    $realEnd->add($interval);
    $dateRange = new DatePeriod(new DateTime($start), $interval, $realEnd);
    $range = [];
    foreach ($dateRange as $date) {
        $range[$date->format("Y-m-d")] = ['scheduled' => 0, 'cancelled' => 0, 'completed' => 0];
    }
    return $range;
}

$allDates = createDateRange($startDate, $endDate);

$sql = "SELECT DATE(startTime) as date, status, COUNT(*) as count FROM bookings WHERE DATE(startTime) BETWEEN ? AND ? GROUP BY DATE(startTime), status";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $allDates[$row['date']][$row['status']] = (int) $row['count'];
}

$data = [];
foreach ($allDates as $date => $statuses) {
    $data[] = [
        'date' => $date,
        'scheduled' => $statuses['scheduled'],
        'cancelled' => $statuses['cancelled'],
        'completed' => $statuses['completed']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);

$stmt->close();
$conn->close();
?>
