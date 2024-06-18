<?php
include '../utils/db.php';

$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];

// Function to generate a range of dates
function createDateRangeArray($strDateFrom, $strDateTo) {
    $aryRange = [];

    $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
    $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

    if ($iDateTo >= $iDateFrom) {
        array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
        while ($iDateFrom < $iDateTo) {
            $iDateFrom += 86400; // add 24 hours
            array_push($aryRange, date('Y-m-d', $iDateFrom));
        }
    }
    return $aryRange;
}

$dateRange = createDateRangeArray($startDate, $endDate);

// Initialize the data array with all dates set to zero bookings
$data = array();
foreach ($dateRange as $date) {
    $data[$date] = ['date' => $date, 'scheduled' => 0, 'cancelled' => 0, 'completed' => 0];
}

// Query to get counts from database
$query = $conn->prepare("SELECT DATE(startTime) AS date, status, COUNT(*) AS count FROM bookings WHERE DATE(startTime) BETWEEN ? AND ? GROUP BY DATE(startTime), status");
$query->bind_param("ss", $startDate, $endDate);
$query->execute();
$result = $query->get_result();

while ($row = $result->fetch_assoc()) {
    $data[$row['date']][$row['status']] = (int) $row['count'];
}

// Convert associative array to indexed array to ensure JSON structure is correct
$finalData = array_values($data);

echo json_encode($finalData);

$query->close();
$conn->close();
?>
