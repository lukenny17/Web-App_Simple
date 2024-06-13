<?php
// Only start a new session if one isn't already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">Luxe Auto Repair</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Conditionally display links based on user login status -->
                <?php if (isset($_SESSION['userid'])): ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <!-- Logout Link -->
                    <li class="nav-item"><a class="nav-link" href="../utils/logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="../common/modal.php" data-bs-toggle="modal" data-bs-target="#modal">Login/Register</a></li>
                <?php endif; ?>
                <li class="nav-item"><a class="nav-link" href="bookService.php">Book a Service</a></li>
            </ul>
            <form class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-light" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>
