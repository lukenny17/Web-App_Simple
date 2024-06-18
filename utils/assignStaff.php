<?php
include '../utils/db.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingID = $_POST['bookingID'] ?? null;
    $staffID = $_POST['staffID'] ?? null;

    if ($bookingID && $staffID) {
        // Prepare the update statement to assign a staff member to a booking
        $stmt = $conn->prepare("UPDATE bookings SET staffID = ? WHERE bookingID = ?");
        $stmt->bind_param("ii", $staffID, $bookingID);
        $success = $stmt->execute();

        // Check execution status
        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'Staff assigned successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to assign staff']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid booking or staff ID']);
    }

    $stmt->close();
    $conn->close();
} else {
    // Not a POST request
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

header("Location: ../pages/dashboard.php");

?>
