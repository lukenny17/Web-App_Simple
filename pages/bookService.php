<?php
session_start();
include '../utils/functions.php';  // Include login and database connection functions

$message = '';
$availableTimes = [];
$vehicles = [];

if (isset($_SESSION['userid'])) {
    $userID = $_SESSION['userid'];
    $vehicles = fetchUserVehicles($conn, $userID);
}

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

    if (isset($_POST['bookService']) && isset($_SESSION['userid'])) {
        $userID = $_SESSION['userid'];
        $vehicleID = $_POST['vehicleID'];  // Comes from the form, can be new or existing
        if ($vehicleID == "new") {
            $make = $_POST['make'];
            $model = $_POST['model'];
            $year = $_POST['year'];
            $licensePlate = $_POST['licensePlate'];

            // Insert vehicle information
            $stmt = $conn->prepare("INSERT INTO vehicles (userID, make, model, year, licensePlate) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('issis', $userID, $make, $model, $year, $licensePlate);
            $stmt->execute();
            $vehicleID = $conn->insert_id; // Newly created vehicle ID
        }

        $serviceID = $_POST['service'];
        $serviceDuration = getServiceDuration($conn, $serviceID);
        $date = $_POST['date'];
        $startTime = $date . ' ' . $_POST['time'];
        $status = 'scheduled';

        // Insert booking information
        $stmt = $conn->prepare("INSERT INTO bookings (userID, serviceID, vehicleID, startTime, duration, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iiisds', $userID, $serviceID, $vehicleID, $startTime, $serviceDuration, $status);
        if ($stmt->execute()) {
            logActivity($conn, $userID, 'Made a booking');
            $message = "Booking successful!";
        } else {
            $message = "Error booking service: " . $stmt->error;
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
                    var modal = new bootstrap.Modal(document.getElementById('modal'));
                    modal.show();
                });
            </script>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="vehicleID" class="form-label">Vehicle</label>
                <select class="form-select" id="vehicleID" name="vehicleID" onchange="toggleVehicleForm(this.value)">
                    <option value="new">Add New Vehicle</option>
                    <?php foreach ($vehicles as $vehicle) : ?>
                        <option value="<?= $vehicle['vehicleID'] ?>">
                            <?= htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model'] . ' - ' . $vehicle['licensePlate']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- New Vehicle Details Form -->
            <div id="newVehicleDetails" style="display:none;">
                <div class="mb-3">
                    <label for="make" class="form-label">Make</label>
                    <input type="text" class="form-control" id="make" name="make">
                </div>
                <div class="mb-3">
                    <label for="model" class="form-label">Model</label>
                    <input type="text" class="form-control" id="model" name="model">
                </div>
                <div class="mb-3">
                    <label for="year" class="form-label">Year</label>
                    <input type="number" class="form-control" id="year" name="year" min="1900" max="<?= date("Y") ?>">
                </div>
                <div class="mb-3">
                    <label for="licensePlate" class="form-label">License Plate</label>
                    <input type="text" class="form-control" id="licensePlate" name="licensePlate">
                </div>
            </div>
            <!-- Remaining form fields -->
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
        <script>
            function toggleVehicleForm(value) {
                var display = (value === 'new') ? 'block' : 'none';
                document.getElementById('newVehicleDetails').style.display = display;
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>