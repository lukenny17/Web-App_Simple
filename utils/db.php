<?php
$host = 'localhost';
$dbname = 'garage_system';
$username = 'root'; 
$password = '';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
