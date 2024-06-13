<?php
session_start();
include '../utils/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['userid'], $_POST['bookingID'])) {
    $userID = $_SESSION['userid'];
    $bookingID = $_POST['bookingID'];

    // Verify the booking belongs to the user
    $stmt = $conn->prepare("SELECT userID FROM bookings WHERE bookingID = ?");
    $stmt->bind_param("i", $bookingID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->fetch_assoc()['userID'] == $userID) {
        // Cancel the booking
        $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE bookingID = ?");
        $stmt->bind_param("i", $bookingID);
        if ($stmt->execute()) {
            echo "Booking cancelled successfully.";
        } else {
            echo "Error cancelling booking: " . $stmt->error;
        }
    } else {
        echo "You do not have permission to cancel this booking.";
    }
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>