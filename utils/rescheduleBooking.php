<?php
session_start();
include '../utils/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['userid'], $_POST['bookingID'], $_POST['newDate'], $_POST['newTime'])) {
    $userID = $_SESSION['userid'];
    $bookingID = $_POST['bookingID'];
    $newDate = $_POST['newDate'];
    $newTime = $_POST['newTime'];
    $newStartTime = $newDate . ' ' . $newTime;

    // Verify the booking belongs to the user
    $stmt = $conn->prepare("SELECT userID FROM bookings WHERE bookingID = ?");
    $stmt->bind_param("i", $bookingID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->fetch_assoc()['userID'] == $userID) {
        // Update booking time
        $stmt = $conn->prepare("UPDATE bookings SET startTime = ? WHERE bookingID = ?");
        $stmt->bind_param("si", $newStartTime, $bookingID);
        if ($stmt->execute()) {
            echo "Booking rescheduled successfully.";
        } else {
            echo "Error updating booking: " . $stmt->error;
        }
    } else {
        echo "You do not have permission to modify this booking.";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
