<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Bookings Chart</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <h1>Admin Dashboard</h1>
    <div>
        <form id="dateForm">
            Start Date: <input type="date" id="startDate" name="startDate" required>
            End Date: <input type="date" id="endDate" name="endDate" required>
            <button type="button" onclick="fetchData()">Show Data</button>
        </form>
    </div>

    <div>
        <canvas id="bookingsByDate"></canvas>
    </div>

    <script>
        let bookingsChart;

        function fetchData() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const url = `../utils/fetchBookings.php?startDate=${startDate}&endDate=${endDate}`;

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    updateChart(data);
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    alert('Failed to fetch data: ' + error.message);
                });
        }

        function updateChart(data) {
            const ctx = document.getElementById('bookingsByDate').getContext('2d');
            if (bookingsChart) {
                bookingsChart.destroy();
            }

            bookingsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.date),
                    datasets: [{
                        label: 'Scheduled',
                        data: data.map(item => item.scheduled),
                        backgroundColor: 'rgba(54, 162, 235, 0.6)'
                    }, {
                        label: 'Cancelled',
                        data: data.map(item => item.cancelled),
                        backgroundColor: 'rgba(255, 99, 132, 0.6)'
                    }, {
                        label: 'Completed',
                        data: data.map(item => item.completed),
                        backgroundColor: 'rgba(75, 192, 192, 0.6)'
                    }]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Daily Bookings',
                            font: {
                                size: 24
                            },
                            padding: {
                                top: 10,
                            }
                        }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            },
                            stacked: true,
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Number of Bookings'
                            },
                            stacked: true,
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            },
                        }
                    }
                }
            });
        }
    </script>

</body>

</html>