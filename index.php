<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Service Booking</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Book a Service</h1>
    <form id="bookingForm">
        <label for="name">Your Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="service">Select Service:</label>
        <select id="service" name="service">
            <option value="oil_change">Oil Change</option>
            <option value="tire_rotation">Tire Rotation</option>
            <option value="general_checkup">General Checkup</option>
        </select>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required>

        <button type="submit">Book Service</button>
    </form>
    <script src="script.js"></script>
</body>
</html>