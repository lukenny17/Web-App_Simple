<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit;
}

include '../utils/db.php';

// Fetch user information
$userID = $_SESSION['userid'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Garage Booking Application</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../resources/style.css">
</head>

<body>

    <header>
        <?php
        include '../common/navbar.php'
        ?>
    </header>

    <div class="container mt-5">
        <header class="mb-4">
            <h1 class="text-center">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        </header>

        <!-- Include role-specific dashboard content -->
        <?php
        switch ($role) {
            case 'customer':
                include 'dashboardCustomer.php';
                break;
            case 'staff':
                include 'dashboardStaff.php';
                break;
            case 'admin':
                include 'dashboardAdmin.php';
                break;
            default:
                echo "<p class='text-center'>Invalid role detected. Contact administrator.</p>";
                break;
        }
        // Close database connection
        $conn->close();
        ?>

    </div>

    <footer class="text-center mt-4">
        <p>&copy; 2024 Garage Booking Application. All rights reserved.</p>
    </footer>
</body>

</html>