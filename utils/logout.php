<?php
session_start();
include '../utils/functions.php';
logActivity($conn, $_SESSION['userid'], 'Logged out');
session_destroy();
header("Location: ../pages/index.php");
exit;
?>
