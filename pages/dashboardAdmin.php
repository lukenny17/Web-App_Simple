<head>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        <!-- Modal for Event Details -->
        <div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="eventDetailsModalLabel">Booking Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="eventTitle">Title: </p>
                        <p id="eventStart">Starts: </p>
                        <p id="eventEnd">Ends: </p>
                        <p id="eventStatus">Status: </p>
                        <p id="eventStaff">Staff: </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Display Activity Log -->
        <div class="section">
            <h3 class="text-center mt-3 mb-3">Activity Log</h3>
            <div class="container">
                <form id="filterForm" class="mb-3">
                    <div class="input-group">
                        <input type="date" class="form-control" id="filterDate" aria-label="Filter by date">
                        <button class="btn btn-outline-secondary" type="button" onclick="filterActivities()">Filter</button>
                    </div>
                </form>
                <div class="activity-log-container" style="max-height: 300px; overflow-y: auto;">
                    <ul class="list-group" id="activityLogList">
                        <li class="list-group-item">No activities to display. Please select a date to filter.</li>
                    </ul>
                </div>
            </div>
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
            var eventModal = new bootstrap.Modal(document.getElementById('eventDetailsModal'));
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    document.getElementById('eventTitle').textContent = 'Service: ' + info.event.title;
                    document.getElementById('eventStart').textContent = 'Start: ' + (info.event.start ? info.event.start.toLocaleString() : 'No start time');
                    document.getElementById('eventEnd').textContent = 'End: ' + (info.event.end ? info.event.end.toLocaleString() : 'No end time');
                    document.getElementById('eventStatus').textContent = 'Status: ' + info.event.extendedProps.status;
                    document.getElementById('eventStaff').textContent = 'Assigned to: ' + info.event.extendedProps.staffName;
                    eventModal.show();
                },
                events: '../utils/fetchCalendarEvents.php'
            });
            calendar.render();
        });

        // Activity Log Filters

        function filterActivities() {
            var date = document.getElementById('filterDate').value;
            if (!date) {
                alert("Please select a date to filter.");
                return;
            }

            fetch('../utils/displayActivityLog.php?date=' + date)
                .then(response => response.json())
                .then(data => {
                    displayActivities(data);
                })
                .catch(error => {
                    console.error('Error fetching activities:', error);
                    alert('Failed to fetch activities. Please try again.');
                });
        }

        function displayActivities(activities) {
            const list = document.getElementById('activityLogList');
            list.innerHTML = ''; // Clear existing entries

            activities.forEach(activity => {
                const item = document.createElement('li');
                item.className = 'list-group-item';
                item.innerHTML = `
            <strong>${activity.name}</strong>
            - ${activity.activity}
            <span>${new Date(activity.timestamp).toLocaleString()}</span>
        `;
                list.appendChild(item);
            });

            if (activities.length === 0) {
                list.innerHTML = '<li class="list-group-item">No activities found for the selected date.</li>';
            }
        }
    </script>
</body>

</html>