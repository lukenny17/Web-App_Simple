<?php
// Include database connection settings
require_once 'db.php';

function handleLogin($conn) {
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

function handleRegistration($conn) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['error' => "Invalid email format!"];
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
                return ['success' => true];
            } else {
                return ['error' => "Registration failed: " . $stmt->error];
            }
        } else {
            return ['error' => "Failed to prepare statement."];
        }
    }
}

function fetchFeedbackData($conn) {
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
