// Form submission for booking

document.getElementById('bookingForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const service = document.getElementById('service').value;
    const date = document.getElementById('date').value;

    console.log('Booking Details:', { name, email, service, date });

    // Here you might want to send the data to the server using fetch API or another method
    alert('Service booked successfully!');
});
