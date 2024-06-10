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

// Prepare statements based on user role
if ($role === 'customer') {
    $stmt = $conn->prepare("SELECT b.bookingID, s.serviceName, b.bookingDate, b.status 
                            FROM Bookings b 
                            JOIN Services s ON b.serviceID = s.serviceID 
                            WHERE b.userID = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $bookings = $stmt->get_result();
} elseif ($role === 'staff') {
    $stmt = $conn->prepare("SELECT b.bookingID, u.name AS customerName, s.serviceName, b.bookingDate, b.status 
                            FROM Bookings b 
                            JOIN Users u ON b.userID = u.userID 
                            JOIN Services s ON b.serviceID = s.serviceID 
                            WHERE b.status = 'scheduled'");
    $stmt->execute();
    $tasks = $stmt->get_result();
} elseif ($role === 'admin') {
    $stmt = $conn->prepare("SELECT dailyBookings, dailyEarnings, dateRecorded FROM Analytics ORDER BY dateRecorded DESC LIMIT 10");
    $stmt->execute();
    $analytics = $stmt->get_result();

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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <header class="mb-4">
            <h1 class="text-center">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
            <div class="text-center">
                <form method="post" action="sections/logout.php">
                    <button type="submit" name="logout" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </header>

        <!-- Responsive grid layout -->
        <div class="row">
            <?php if ($role === 'customer'): ?>
                <div class="col-md-12">
                    <h2>Your Bookings</h2>
                    <div class="list-group">
                        <?php if ($bookings->num_rows > 0): ?>
                            <?php while ($booking = $bookings->fetch_assoc()): ?>
                                <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($booking['serviceName']); ?></h5>
                                        <small><?php echo htmlspecialchars($booking['status']); ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($booking['bookingDate']); ?></p>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted">No bookings found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($role === 'staff'): ?>
                <div class="col-md-12">
                    <h2>Today's Tasks</h2>
                    <div class="list-group">
                        <?php if ($tasks->num_rows > 0): ?>
                            <?php while ($task = $tasks->fetch_assoc()): ?>
                                <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($task['customerName']); ?></h5>
                                        <small><?php echo htmlspecialchars($task['status']); ?></small>
                                    </div>
                                    <p class="mb-1"><?php echo htmlspecialchars($task['serviceName']); ?> on <?php echo htmlspecialchars($task['bookingDate']); ?></p>
                                </a>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted">No tasks found.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($role === 'admin'): ?>
                <div class="col-md-6">
                    <h2>Analytics</h2>
                    <ul class="list-group">
                        <?php if ($analytics->num_rows > 0): ?>
                            <?php while ($analytic = $analytics->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    Bookings: <?php echo htmlspecialchars($analytic['dailyBookings']); ?>
                                    - Earnings: $<?php echo htmlspecialchars($analytic['dailyEarnings']); ?>
                                    on <?php echo htmlspecialchars($analytic['dateRecorded']); ?>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted">No analytics data found.</p>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h2>Recent Feedback</h2>
                    <ul class="list-group">
                        <?php if ($feedbacks->num_rows > 0): ?>
                            <?php while ($feedback = $feedbacks->fetch_assoc()): ?>
                                <li class="list-group-item">
                                    <?php echo htmlspecialchars($feedback['customerName']); ?>: 
                                    <?php echo htmlspecialchars($feedback['serviceName']); ?>
                                    - Rating: <?php echo htmlspecialchars($feedback['rating']); ?>
                                    <blockquote><?php echo htmlspecialchars($feedback['comment']); ?></blockquote>
                                    <small><?php echo htmlspecialchars($feedback['feedbackDate']); ?></small>
                                </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted">No feedback found.</p>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer class="text-center mt-4">
        <p>&copy; 2024 Garage Booking Application. All rights reserved.</p>
    </footer>
</body>
</html>
