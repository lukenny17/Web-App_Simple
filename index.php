<?php
session_start();
include 'db.php';

$message = ''; // To store messages for the user

if (isset($_SESSION['userid'])) {
    header("Location: dashboard.php"); // Redirect to dashboard if already logged in
    exit;
}

// Handle registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hashing the password
    $role = $_POST['role'];

    // Server-side validation for email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format!";
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
                header("Location: dashboard.php"); // Redirect to the dashboard
                exit;
            } else {
                $message = "Registration failed: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = "Failed to prepare statement.";
        }
    }
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
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
                header("Location: dashboard.php"); // Redirect to the dashboard
                exit;
            } else {
                $message = "Invalid password!";
            }
        } else {
            $message = "User not found!";
        }
        $stmt->close();
    } else {
        $message = "Failed to prepare statement.";
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Booking Application</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <div class="container">
        <header>
            <h1>Garage Booking Application</h1>
        </header>

        <nav>
            <button onclick="showForm('login')">Login</button>
            <button onclick="showForm('register')">Register</button>
        </nav>

        <!-- Display message -->
        <?php if (!empty($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Hidden Login Form -->
        <div id="loginForm" style="display:none;">
            <h2>Login</h2>
            <form method="POST">
                Email: <input type="email" name="email" required><br>
                Password: <input type="password" name="password" required><br>
                <input type="submit" name="login" value="Login">
            </form>
        </div>

        <!-- Hidden Registration Form -->
        <div id="registerForm" style="display:none;">
            <h2>Register</h2>
            <form method="POST">
                Name: <input type="text" name="name" required><br>
                Email: <input type="email" name="email" required><br>
                Password: <input type="password" name="password" required><br>
                Role:
                <select name="role">
                    <option value="customer">Customer</option>
                    <option value="staff">Staff</option>
                    <option value="admin">Admin</option>
                </select><br>
                <input type="submit" name="register" value="Register">
            </form>
        </div>

        <section id="services" class="container">
            <h2>Our Services</h2>
            <article>
                <img src="oil-change.jpg" alt="Oil Change" style="width:100px;height:100px;">
                <h3>Oil Change</h3>
                <p>Starting at $39.99</p>
            </article>
            <article>
                <img src="tire-rotation.jpg" alt="Tire Rotation" style="width:100px;height:100px;">
                <h3>Tire Rotation</h3>
                <p>Starting at $29.99</p>
            </article>
        </section>

        <section id="about" class="container">
            <h2>Contact Details</h2>
            <p>Email: contact@garageapp.com</p>
            <p>Phone: (123) 456-7890</p>
            <h2>Location</h2>
            <p>1234 Car Street, Auto City</p>
            <h2>Opening Hours</h2>
            <p>Monday - Friday: 9 AM - 5 PM</p>
            <p>Saturday: 10 AM - 4 PM</p>
        </section>

        <section id="feedback" class="container">
            <h2>Recent Feedback</h2>
            <blockquote>"Very satisfied with the quick service!" - Jane Doe</blockquote>
            <blockquote>"Affordable prices and friendly staff." - John Smith</blockquote>
        </section>

        <footer>
            <p>&copy; 2024 Garage Booking Application. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
