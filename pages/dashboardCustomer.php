<?php
// Ensure the user is a customer and logged in
if (!isset($role) || $role !== 'customer') {
    echo "<p>Access denied. You do not have permission to view this page.</p>";
    exit;
}

// Fetch customer-specific bookings
$stmt = $conn->prepare("SELECT b.bookingID, s.serviceName, s.serviceID, b.startTime, b.status 
                        FROM Bookings b 
                        JOIN Services s ON b.serviceID = s.serviceID 
                        WHERE b.userID = ? AND b.status != 'cancelled'");
$stmt->bind_param("i", $userID);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<div class="col-md-12">
    <h2>Your Bookings</h2>
    <?php if ($bookings->num_rows > 0) : ?>
        <div class="list-group">
            <?php while ($booking = $bookings->fetch_assoc()) : ?>
                <div class="list-group-item">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <h5 class="mb-0"><?php echo htmlspecialchars($booking['serviceName']); ?></h5>
                        </div>
                        <div class="col-md-2">
                            <h7><?php echo htmlspecialchars($booking['startTime']); ?></h7>
                        </div>
                        <div class="col-md-2">
                            <h7>Status: <?php echo htmlspecialchars($booking['status']); ?></h7>
                        </div>
                        <div class="col-md-6" style="display: flex; align-items: center;">
                            <?php if ($booking['status'] == 'scheduled') : ?>
                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#rescheduleModal<?php echo $booking['bookingID']; ?>" style="width: 160px; margin-right: 10px;">Reschedule</button>
                                <form action="../utils/cancelBooking.php" method="post">
                                    <input type="hidden" name="bookingID" value="<?php echo $booking['bookingID']; ?>">
                                    <button type="submit" class="btn btn-danger" style="width: 160px; margin-right: 10px;">Cancel</button>
                                </form>
                                <button class="btn btn-secondary" onclick="location.href='mailto:admin@example.com?subject=Query About Booking #<?php echo $booking['bookingID']; ?>'" style="width: 160px;">Contact</button>
                                <!-- Reschedule Modal -->
                                <div class="modal fade" id="rescheduleModal<?php echo $booking['bookingID']; ?>" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rescheduleModalLabel">Reschedule Booking</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="../utils/rescheduleBooking.php" method="post">
                                                    <input type="hidden" name="bookingID" value="<?php echo $booking['bookingID']; ?>">
                                                    <div class="form-group">
                                                        <label for="newDate">New Date:</label>
                                                        <input type="date" class="form-control" name="newDate" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="newTime">New Time:</label>
                                                        <input type="time" class="form-control" name="newTime" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Confirm</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($booking['status'] == 'completed') : ?>
                                <!-- Feedback Form -->
                                <form action="../utils/submitFeedback.php" method="post" style="width: 100%;">
                                    <input type="hidden" name="serviceID" value="<?= $booking['serviceID']; ?>">
                                    <input type="hidden" name="bookingID" value="<?= $booking['bookingID']; ?>">
                                    <textarea name="comment" required placeholder="Enter your feedback here..." class="form-control" style="width: 100%;"></textarea>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <div class="rating flex-grow-1 mr-2">
                                            <?php for ($i = 5; $i >= 1; $i--) : ?>
                                                <span class="star" data-value="<?= $i; ?>">&#9733;</span>
                                            <?php endfor; ?>
                                        </div>
                                        <button type="submit" class="btn btn-primary" style="width: 160px;">Submit Feedback</button>
                                    </div>
                                    <input type="hidden" id="rating" name="rating" value="0">
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <p class="text-muted">No bookings found.</p>
    <?php endif; ?>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const stars = document.querySelectorAll('.star');
        stars.forEach((star, index) => {
            star.onclick = function() {
                let currentRating = index + 1;
                document.getElementById('rating').value = currentRating; // Set the hidden input's value
                updateStars(currentRating); // Update visual stars
            };
        });

        function updateStars(rating) {
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('rated');
                } else {
                    star.classList.remove('rated');
                }
            });
        }
    });
</script>