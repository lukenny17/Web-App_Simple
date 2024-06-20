<?php
// Include database connection settings
require_once 'db.php';
require_once '../config/config.php'; //Contains access code for admin/staff role creation

function handleLogin($conn)
{
    $email = $_POST['email'];
    $password = $_POST['password'];
    $sql = "SELECT userID, name, email, password, role FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['userid'] = $user['userID'];
                $_SESSION['username'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                logActivity($conn, $_SESSION['userid'], 'Logged in');
                return ['success' => true];
            } else {
                return ['error' => "Invalid password!"];
            }
        } else {
            return ['error' => "User not found!"];
        }
        $stmt->close();
    } else {
        return ['error' => "Failed to prepare statement."];
    }
}

function handleRegistration($conn)
{
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $accessCode = $_POST['access_code']; // Only required for staff or admin

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => "Invalid email format."];
    } else {
        if ($role !== 'customer' && $accessCode !== ACCESS_CODE) {
            return ['error' => "Invalid access code for staff/admin registration."];
        } else {
            $sql = "INSERT INTO Users (name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ssss", $name, $email, $password, $role);
                $stmt->execute();
                if ($stmt->affected_rows === 1) {
                    $_SESSION['userid'] = $stmt->insert_id;
                    $_SESSION['username'] = $name;
                    $_SESSION['role'] = $role;
                    logActivity($conn, $_SESSION['userid'], 'Registered an account');
                    return ['success' => true];
                } else {
                    return ['error' => "Registration failed: " . $stmt->error];
                }
            } else {
                return ['error' => "Failed to prepare statement."];
            }
        }
    }
}

function fetchFeedbackData($conn)
{
    $feedbackData = [];
    // Adjust the query to join with the Users table and select the necessary columns
    $feedbackQuery = "SELECT f.comment, f.rating, f.feedbackDate, u.name AS customerName 
                      FROM Feedback f 
                      JOIN Users u ON f.userID = u.userID 
                      ORDER BY f.feedbackDate DESC 
                      LIMIT 5";
    if ($feedbackResult = $conn->query($feedbackQuery)) {
        while ($feedback = $feedbackResult->fetch_assoc()) {
            $feedbackData[] = $feedback;
        }
    }
    return $feedbackData;
}

function fetchServices($conn)
{
    $services = [];
    // Fetch available services
    $query = "SELECT * FROM services";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
    return $services;
}

function fetchUserVehicles($conn, $userId) {
    $vehicles = [];
    $stmt = $conn->prepare("SELECT vehicleID, make, model, year, licensePlate FROM vehicles WHERE userID = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $vehicles[] = $row;
    }
    return $vehicles;
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

function logActivity($conn, $userID, $activity) {
    $stmt = $conn->prepare("INSERT INTO user_activities (userID, activity) VALUES (?, ?)");
    $stmt->bind_param("is", $userID, $activity);
    $stmt->execute();
}
