<?php
include '../utils/db.php';

// Redirect if not logged in or not staff
if (!isset($_SESSION['userid']) || $_SESSION['role'] != 'staff') {
    header("Location: login.php");
    exit;
}

$staffID = $_SESSION['userid'];

?>

<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
        <h1>Staff Dashboard</h1>
        <!-- Filters -->
        <div class="filters my-3">
            <label for="filterType">Filter by status:</label>
            <select id="filterType" class="form-control w-auto d-inline-block" onchange="applyFilters()">
                <option value="">All</option>
                <option value="scheduled">Scheduled</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <section>
            <h2>Bookings</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Service Type</th>
                        <th>Date / Time</th>
                        <th>End Time</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody id="bookingList">
                    <!-- Booking items will be loaded here -->
                </tbody>
            </table>
        </section>
    </div>

    <script>
        function applyFilters() {
            const statusFilter = document.getElementById('filterType').value;
            const staffID = <?php echo json_encode($staffID); ?>;

            fetch(`../utils/getBookings.php?status=${statusFilter}&staffID=${encodeURIComponent(staffID)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    const bookingList = document.getElementById('bookingList');
                    bookingList.innerHTML = '';
                    data.forEach(booking => {
                        bookingList.innerHTML += `<tr>
                            <td>${booking.serviceName}</td>
                            <td>${booking.startTime}</td>
                            <td>${booking.endTime}</td>
                            <td>
                                <select onchange="updateStatus(${booking.bookingID}, this.value)">
                                    <option value="scheduled" ${booking.status === 'scheduled' ? 'selected' : ''}>Scheduled</option>
                                    <option value="completed" ${booking.status === 'completed' ? 'selected' : ''}>Completed</option>
                                    <option value="cancelled" ${booking.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </td>
                        </tr>`;
                    });
                })
                .catch(error => {
                    console.error('Error loading bookings:', error);
                    alert('Failed to load bookings. Please check the console for more information.');
                });
        }

        function updateStatus(bookingID, newStatus) {
            fetch('../utils/updateBookingStatus.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `bookingID=${bookingID}&status=${newStatus}`
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    applyFilters(); // Refresh list after status update
                })
                .catch(error => console.error('Error updating booking status:', error));
        }

        document.addEventListener('DOMContentLoaded', applyFilters);
    </script>
</body>

</html>