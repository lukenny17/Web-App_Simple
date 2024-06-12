<?php
session_start();
include '../utils/functions.php';  // Include login and database connection functions

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Handle login
        $result = handleLogin($conn);
        if (isset($result['success'])) {
            // Stay on the same page
        } else {
            $message = $result['error'];
        }
    } elseif (isset($_POST['register'])) {
        // Handle registration
        $result = handleRegistration($conn);
        if (isset($result['success'])) {
            // Stay on the same page
        } else {
            $message = $result['error'];
        }
    }
    // If booking form submitted
    if (isset($_POST['bookService'])) {
        // Proceed with booking logic
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
        <!-- Navigation and Dashboard Modal -->
        <?php
            include '../common/navbar.php';
            include '../common/modal.php';
        ?> 
    </header>

    <main>
        <div class="container">
            <h2>Book a Service</h2>

            <?php if (!isset($_SESSION['userid'])) : ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Automatically open the login/register modal
                        var modal = new bootstrap.Modal(document.getElementById('dashboardModal'));
                        modal.show();
                    });
                </script>
            <?php endif; ?>

            <!-- Only show booking form if logged in -->
            <?php if (isset($_SESSION['userid'])) : ?>
                <form method="POST" action="bookService.php">
                    <div class="mb-3">
                        <label for="service" class="form-label">Service</label>
                        <select class="form-select" id="service" name="service" required>
                            <?php foreach ($services as $service) : ?>
                                <option value="<?= $service['serviceID'] ?>"><?= htmlspecialchars($service['serviceName']) ?> - £<?= $service['cost'] ?></option>
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
            <?php endif; ?>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>