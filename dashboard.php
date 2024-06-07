<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit;
}

include 'db.php';

// Fetch user information
$userID = $_SESSION['userid'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Fetch data based on user role
if ($role === 'customer') {
    // Fetch bookings for the customer
    $stmt = $conn->prepare("SELECT b.bookingID, s.serviceName, b.bookingDate, b.status 
                            FROM Bookings b 
                            JOIN Services s ON b.serviceID = s.serviceID 
                            WHERE b.userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $bookings = $stmt->get_result();
} elseif ($role === 'staff') {
    // Fetch tasks for the staff
    $stmt = $conn->prepare("SELECT b.bookingID, u.name AS customerName, s.serviceName, b.bookingDate, b.status 
                            FROM Bookings b 
                            JOIN Users u ON b.userID = u.userID 
                            JOIN Services s ON b.serviceID = s.serviceID 
                            WHERE b.status = 'scheduled'");
    $stmt->execute();
    $tasks = $stmt->get_result();
} elseif ($role === 'admin') {
    // Fetch analytics for the admin
    $stmt = $conn->prepare("SELECT dailyBookings, dailyEarnings, dateRecorded FROM Analytics ORDER BY dateRecorded DESC LIMIT 10");
    $stmt->execute();
    $analytics = $stmt->get_result();

    // Fetch feedback for the admin
    $stmt = $conn->prepare("SELECT f.feedbackID, u.name AS customerName, s.serviceName, f.rating, f.comment, f.feedbackDate 
                            FROM Feedback f 
                            JOIN Users u ON f.userID = u.userID 
                            JOIN Services s ON f.serviceID = s.serviceID 
                            ORDER BY f.feedbackDate DESC LIMIT 10");
    $stmt->execute();
    $feedbacks = $stmt->get_result();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Garage Booking Application</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <form method="post" action="logout.php">
                <button type="submit" name="logout">Logout</button>
            </form>
        </header>

        <?php if ($role === 'customer'): ?>
            <section id="bookings" class="container">
                <h2>Your Bookings</h2>
                <?php if ($bookings->num_rows > 0): ?>
                    <ul>
                        <?php while ($booking = $bookings->fetch_assoc()): ?>
                            <li>
                                <?php echo htmlspecialchars($booking['serviceName']) . " - " . htmlspecialchars($booking['bookingDate']) . " - " . htmlspecialchars($booking['status']); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No bookings found.</p>
                <?php endif; ?>
            </section>
        <?php elseif ($role === 'staff'): ?>
            <section id="tasks" class="container">
                <h2>Today's Tasks</h2>
                <?php if ($tasks->num_rows > 0): ?>
                    <ul>
                        <?php while ($task = $tasks->fetch_assoc()): ?>
                            <li>
                                <?php echo htmlspecialchars($task['customerName']) . " - " . htmlspecialchars($task['serviceName']) . " - " . htmlspecialchars($task['bookingDate']) . " - " . htmlspecialchars($task['status']); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No tasks found.</p>
                <?php endif; ?>
            </section>
        <?php elseif ($role === 'admin'): ?>
            <section id="analytics" class="container">
                <h2>Analytics</h2>
                <?php if ($analytics->num_rows > 0): ?>
                    <ul>
                        <?php while ($analytic = $analytics->fetch_assoc()): ?>
                            <li>
                                <?php echo "Bookings: " . htmlspecialchars($analytic['dailyBookings']) . " - Earnings: $" . htmlspecialchars($analytic['dailyEarnings']) . " - Date: " . htmlspecialchars($analytic['dateRecorded']); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No analytics data found.</p>
                <?php endif; ?>
            </section>

            <section id="feedback" class="container">
                <h2>Recent Feedback</h2>
                <?php if ($feedbacks->num_rows > 0): ?>
                    <ul>
                        <?php while ($feedback = $feedbacks->fetch_assoc()): ?>
                            <li>
                                <?php echo htmlspecialchars($feedback['customerName']) . " - " . htmlspecialchars($feedback['serviceName']) . " - Rating: " . htmlspecialchars($feedback['rating']) . " - " . htmlspecialchars($feedback['comment']) . " - " . htmlspecialchars($feedback['feedbackDate']); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No feedback found.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </div>
</body>
</html>
