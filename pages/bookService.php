<?php
session_start();
include '../utils/functions.php';  // Include login and database connection functions

$message = '';
$availableTimes = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Handle login
        $result = handleLogin($conn);
        if (!isset($result['success'])) {
            $message = $result['error'];
        }
    } elseif (isset($_POST['register'])) {
        // Handle registration
        $result = handleRegistration($conn);
        if (!isset($result['success'])) {
            $message = $result['error'];
        }
    }

    function getServiceDuration($conn, $serviceId) {
        $stmt = $conn->prepare("SELECT duration FROM services WHERE serviceID = ?");
        $stmt->bind_param("i", $serviceId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['duration'];
        }
        return 0; // Default to 0 if no duration found
    }

    // If booking form submitted
    if (isset($_POST['bookService']) && isset($_SESSION['userid'])) {
        $userID = $_SESSION['userid'];
        $make = $_POST['make'];
        $model = $_POST['model'];
        $year = $_POST['year'];
        $licensePlate = $_POST['licensePlate'];
        $serviceID = $_POST['service'];
        $serviceDuration = getServiceDuration($conn, $serviceID);
        $date = $_POST['date'];
        $startTime = $date . ' ' . $_POST['time'];
        $status = 'scheduled';

        // Insert vehicle information
        $stmt = $conn->prepare("INSERT INTO vehicles (userID, make, model, year, licensePlate) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('issis', $userID, $make, $model, $year, $licensePlate);
        if ($stmt->execute()) {
            $vehicleID = $conn->insert_id;

            // Insert booking information
            $stmt = $conn->prepare("INSERT INTO bookings (userID, serviceID, vehicleID, startTime, duration, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('iiisss', $userID, $serviceID, $vehicleID, $startTime, $serviceDuration, $status);
            if ($stmt->execute()) {
                $message = "Booking successful!";
            } else {
                $message = "Error booking service: " . $stmt->error;
            }
        } else {
            $message = "Error registering vehicle: " . $stmt->error;
        }
    }
}


// Fetch available services
$services = fetchServices($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <header>
        <?php include '../common/navbar.php'; ?>
        <?php include '../common/modal.php'; ?>
    </header>
    <main class="container">
        <h2>Book a Service</h2>
        <p><?= $message ?></p>

        <?php if (!isset($_SESSION['userid'])) : ?>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var modal = new bootstrap.Modal(document.getElementById('dashboardModal'));
                    modal.show();
                });
            </script>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="make" class="form-label">Make</label>
                <input type="text" class="form-control" id="make" name="make" required>
            </div>
            <div class="mb-3">
                <label for="model" class="form-label">Model</label>
                <input type="text" class="form-control" id="model" name="model" required>
            </div>
            <div class="mb-3">
                <label for="year" class="form-label">Year</label>
                <input type="number" class="form-control" id="year" name="year" required min="1900" max="<?= date("Y") ?>">
            </div>
            <div class="mb-3">
                <label for="licensePlate" class="form-label">License Plate</label>
                <input type="text" class="form-control" id="licensePlate" name="licensePlate" required>
            </div>
                <!-- Add in regex for license plate if desired -->
                <!-- pattern="^([A-Z]{3}\s?(\d{3}|\d{2}|\d{1})\s?[A-Z])|([A-Z]\s?(\d{3}|\d{2}|\d{1})\s?[A-Z]{3})|(([A-HK-PRSVWY][A-HJ-PR-Y])\s?([0][2-9]|[1-9][0-9])\s?[A-HJ-PR-Z]{3})$" title="Please enter a valid registration number." -->
            <div class="mb-3">
                <label for="service" class="form-label">Service</label>
                <select class="form-select" id="service" name="service" required>
                    <?php foreach ($services as $service) : ?>
                        <option value="<?= $service['serviceID'] ?>"><?= htmlspecialchars($service['serviceName']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
                <label for="time" class="form-label">Time</label>
                <input type="time" class="form-control" id="time" name="time" required>
            </div>
            <button type="submit" name="bookService" class="btn btn-primary">Book Now</button>
        </form>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>