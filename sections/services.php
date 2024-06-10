<!-- services.php -->
<?php
session_start();
include '../functions.php';  // Include login and database connection functions

// if (!isset($_SESSION['userid'])) {
//     header("Location: login.php");  // Redirect to login if not logged in
//     exit;
// }

// Connect to database
// Fetch available services
// $query = "SELECT * FROM services WHERE available = 1";
// $result = $conn->query($query);

// $services = [];
// while ($row = $result->fetch_assoc()) {
//     $services[] = $row;
// }

// // Handle booking
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $service_id = $_POST['service'];
//     $date = $_POST['date'];
//     $time = $_POST['time'];
//     // Insert booking logic here, with validation and database insertion
// }

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
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="../index.php">Luxe Auto Repair</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#registerModal">Register</a></li>
                        <li class="nav-item"><a class="nav-link" href="sections/services.php">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="#testimonials">Testimonials</a></li>
                        <li class="nav-item"><a class="nav-link" href="#faqs">FAQs</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    </ul>
                    <form class="d-flex" role="search">
                        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container">
            <h2>Book a Service</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="service" class="form-label">Service</label>
                    <select class="form-select" id="service" name="service" required>
                        <?php foreach ($services as $service) : ?>
                            <option value="<?= $service['id'] ?>"><?= htmlspecialchars($service['name']) ?> - £<?= $service['cost'] ?></option>
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
                <button type="submit" class="btn btn-primary">Book Now</button>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>