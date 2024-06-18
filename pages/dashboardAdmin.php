<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../resources/style.css">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.min.css' rel='stylesheet' />
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.min.css' rel='stylesheet' />
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/timegrid/main.min.css' rel='stylesheet' />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/core/main.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/interaction/main.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/daygrid/main.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/4.2.0/timegrid/main.min.js'></script>
</head>

<body class="bg-light">
    <main class="container mt-0">
        <h1>Admin Dashboard</h1>
        <div class="section">
            <!-- Weekly Bookings Section - to be allocated by staff member -->
            <h3 class="text-center mt-0">Staff Allocation: Current Week</h3>
            <?php
            include '../utils/db.php';
            $week_start = date('Y-m-d', strtotime('monday this week'));
            $week_end = date('Y-m-d', strtotime('sunday this week'));

            $query = "SELECT b.bookingID, b.startTime, s.serviceName, b.status, b.staffID 
                  FROM bookings b 
                  JOIN services s ON b.serviceID = s.serviceID
                  WHERE b.startTime BETWEEN ? AND ? AND b.status != 'cancelled'
                  ORDER BY b.startTime ASC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $week_start, $week_end);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo '<div class="table-responsive">';
                echo '<table class="table pb-0">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Service Type</th>';
                echo '<th>Date / Time</th>';
                echo '<th>Status</th>';
                echo '<th>Assign Staff</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                while ($booking = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($booking['serviceName']) . '</td>';
                    echo '<td>' . htmlspecialchars($booking['startTime']) . '</td>';
                    echo '<td>' . htmlspecialchars($booking['status']) . '</td>';
                    echo '<td>';
                    echo '<form action="../utils/assignStaff.php" method="post">';
                    echo '<input type="hidden" name="bookingID" value="' . $booking['bookingID'] . '">';
                    echo '<select name="staffID" class="form-select" onchange="this.form.submit()">';
                    echo '<option value="">Select Staff</option>';
                    $staffQuery = "SELECT userID, name FROM users WHERE role='staff'";
                    $staffResult = $conn->query($staffQuery);
                    while ($staff = $staffResult->fetch_assoc()) {
                        $selected = ($booking['staffID'] == $staff['userID']) ? 'selected' : '';
                        echo '<option value="' . $staff['userID'] . '" ' . $selected . '>' . htmlspecialchars($staff['name']) . '</option>';
                    }
                    echo '</select>';
                    echo '</form>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            } else {
                echo "<p>No bookings found for this week.</p>";
            }
            ?>
        </div>

        <!-- Bookings & Revenue Section -->
        <div class="section">
            <h3 class="text-center mt-3 mb-3">Bookings & Revenue by Period</h3>
            <div class="container">
                <form id="dateForm" class="row justify-content-center gx-3 gy-2 align-items-end">
                    <div class="col-auto">
                        <div class="input-group">
                            <span class="input-group-text">Start Date:</span>
                            <input type="date" id="startDate" name="startDate" class="form-control" value="<?php echo $week_start; ?>" required>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="input-group">
                            <span class="input-group-text">End Date:</span>
                            <input type="date" id="endDate" name="endDate" class="form-control" value="<?php echo $week_end; ?>" required>
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-secondary" onclick="updateData()">Show Data</button>
                    </div>
                </form>
            </div>
            <div class="mt-3">
                <canvas id="bookingsByDate"></canvas>
            </div>
            <div>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Calendar Section -->
        <div class="section">
            <h3 class="text-center mt-0">Calendar View</h3>
            <div id='calendar'></div>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateData(); // Fetch data for both charts on initial load
        });

        function updateData() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            fetchChartData(startDate, endDate);
            fetchRevenueData(startDate, endDate);
        }

        function fetchChartData(startDate, endDate) {
            fetch(`../utils/fetchChartData.php?startDate=${startDate}&endDate=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    if (Array.isArray(data)) { // Check if data is an array
                        renderChart(data); // If it's an array, proceed to render the chart
                    } else {
                        console.error("Data fetched is not an array:", data);
                    }
                })
                .catch(error => console.error('Failed to fetch chart data:', error));
        }

        function fetchRevenueData() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            fetch(`../utils/fetchRevenueData.php?startDate=${startDate}&endDate=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    renderRevenueChart(data);
                })
                .catch(error => {
                    console.error('Failed to fetch revenue data:', error);
                });
        }

        function renderChart(data) {
            const ctx = document.getElementById('bookingsByDate').getContext('2d');
            if (window.bookingsChart) window.bookingsChart.destroy();
            window.bookingsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.date),
                    datasets: [{
                        label: 'Scheduled',
                        backgroundColor: '#4D96FF',
                        data: data.map(item => item.scheduled)
                    }, {
                        label: 'Cancelled',
                        backgroundColor: '#FF9999',
                        data: data.map(item => item.cancelled)
                    }, {
                        label: 'Completed',
                        backgroundColor: '#75C692',
                        data: data.map(item => item.completed)
                    }]
                },
                options: {
                    scales: {
                        x: {
                            stacked: true,
                            title: {
                                display: true,
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                            title: {
                                display: true,
                                text: 'Number of Bookings'
                            }
                        }
                    }
                }
            });
        }

        function renderRevenueChart(data) {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            if (window.revenueChart && typeof window.revenueChart.destroy === 'function') {
                window.revenueChart.destroy();
            }

            // Extracting values from the object with default if undefined
            const completedValue = data['completed'] || 0;
            const scheduledValue = data['scheduled'] || 0;
            const cancelledValue = data['cancelled'] || 0;

            window.revenueChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Booking Status'],
                    datasets: [{
                            label: 'Completed',
                            backgroundColor: '#75C692',
                            data: [completedValue],
                        },
                        {
                            label: 'Scheduled',
                            backgroundColor: '#4D96FF',
                            data: [scheduledValue],
                        },
                        {
                            label: 'Cancelled',
                            backgroundColor: '#FF9999',
                            data: [cancelledValue],
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Revenue (£)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        }
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['interaction', 'dayGrid', 'timeGrid'],
                headerToolbar: {
                    left: 'prev,next today', // Buttons for navigation and today
                    center: 'title', // Title in the center
                    right: 'dayGridMonth,timeGridWeek,timeGridDay' // Buttons to switch views
                },
                initialView: 'timeGridWeek', // Set the initial view to week
                events: '../utils/fetchCalendarEvents.php', // URL to fetch events
                eventColor: function(event) {
                    switch (event.extendedProps.status) { // Use event status to determine color
                        case 'completed':
                            return '#28a745'; // Green for completed
                        case 'scheduled':
                            return '#007bff'; // Blue for scheduled
                        case 'cancelled':
                            return '#dc3545'; // Red for cancelled
                        default:
                            return '#ffc107'; // Yellow for others
                    }
                }
            });
            calendar.render();
        });
    </script>
</body>

</html>