<?php
session_start();
include '../utils/db.php';

// Check if the user is logged in
if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit;
}

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
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../resources/style.css">
</head>

<body>

    <header>
        <?php
        include '../common/navbar.php'
        ?>
    </header>

    <div class="container mt-2">
        <header class="mb-0">
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>