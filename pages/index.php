<?php
session_start();
include '../utils/functions.php';

$message = ''; // To store messages for the user

// Fetch testimonials data
$feedbackData = fetchFeedbackData($conn);

// Login/Registration handler
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $result = handleLogin($conn);
        if (isset($result['success'])) {
            // Process login success without redirection
        } else {
            $message = $result['error'];
        }
    } elseif (isset($_POST['register'])) {
        $result = handleRegistration($conn);
        if (isset($result['success'])) {
            // Process registration success without redirection
        } else {
            $message = $result['error'];
        }
    }
}

// Close the connection after all database interactions are complete
if ($conn) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Luxe Auto Repair</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../resources/style.css">
</head>

<header>
    <?php
    include '../common/navbar.php'
    ?>
</header>

<body class="bg-light">
    <main class="container mt-0">
        <!-- Display content of $message variable-->
        <div class="container mt-2">
            <?php if (!empty($message)) : ?>
                <div class="alert alert-warning"><?php echo $message; ?></div>
            <?php endif; ?>
        </div>

        <!-- Login/Registration Modal -->
        <?php
        include '../common/modal.php';
        ?>

        <!-- Services Preview -->
        <section id="services" class="text-center">
            <section class="section-header">
                <h2>Our Services</h2>
            </section>
            <div class="row">
                <div class="col-md-3 pb-2">
                    <div class="card">
                        <img src="../resources/services/full_service.jpg" class="card-img-top" alt="Full Service">
                        <div class="card-body">
                            <h5 class="card-title">Full Service</h5>
                            <p class="card-text">Complete vehicle checkup.</p>
                            <p>Starting at £139.99</p>
                            <a href="#" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 pb-2">
                    <div class="card">
                        <img src="../resources/services/oil_change.jpg" class="card-img-top" alt="Oil Change">
                        <div class="card-body">
                            <h5 class="card-title">Oil Change</h5>
                            <p class="card-text">Regular and synthetic options.</p>
                            <p>Starting at £39.99</p>
                            <a href="#" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 pb-2">
                    <div class="card">
                        <img src="../resources/services/tyre_change.jpg" class="card-img-top" alt="Tire Change">
                        <div class="card-body">
                            <h5 class="card-title">Tyre Change</h5>
                            <p class="card-text">Mounting and rotation.</p>
                            <p>Starting at £29.99</p>
                            <a href="#" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 pb-2">
                    <div class="card">
                        <img src="../resources/services/brake_replacement.jpg" class="card-img-top" alt="Brake Service">
                        <div class="card-body">
                            <h5 class="card-title">Brake Service</h5>
                            <p class="card-text">Pads, discs, and inspection.</p>
                            <p>Starting at £99.99</p>
                            <a href="#" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 pb-2">
                    <div class="card">
                        <img src="../resources/services/battery.webp" class="card-img-top" alt="Battery Replacement">
                        <div class="card-body">
                            <h5 class="card-title">Battery Replacement</h5>
                            <p class="card-text">Testing and replacement.</p>
                            <p>Starting at £69.99</p>
                            <a href="#" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 pb-2">
                    <div class="card">
                        <img src="../resources/services/ac_service.jpg" class="card-img-top" alt="AC Service">
                        <div class="card-body">
                            <h5 class="card-title">Air Conditioning</h5>
                            <p class="card-text">Check and recharge AC unit.</p>
                            <p>Starting at £59.99</p>
                            <a href="#" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 pb-2">
                    <div class="card">
                        <img src="../resources/services/transmission_flush.jpg" class="card-img-top" alt="Transmission Fluid Flush">
                        <div class="card-body">
                            <h5 class="card-title">Transmission Fluid Flush</h5>
                            <p class="card-text">Transmission fluid replacement.</p>
                            <p>Starting at £89.99</p>
                            <a href="#" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <img src="../resources/services/engine_diagnostics.jpg" class="card-img-top" alt="Engine Diagnostics">
                        <div class="card-body">
                            <h5 class="card-title">Engine Diagnostics</h5>
                            <p class="card-text">Advanced troubleshooting.</p>
                            <p>Starting at £49.99</p>
                            <a href="#" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="moreInformation pb-2">
                <a href="bookService.php" class="btn btn-primary">Book a Service</a>
            </div>
            </div>
        </section>

        <!-- Testimonials (5-star only, must be conditional) -->
        <section id="testimonials" class="text-center pt-2">
            <h2>Testimonials</h2>
            <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($feedbackData as $index => $feedback) : ?>
                        <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">
                            <blockquote class="blockquote text-center">
                                <p class="mb-4"><?= htmlspecialchars($feedback['comment']); ?></p>
                                <footer class="blockquote-footer"><?= htmlspecialchars($feedback['customerName']); ?> on <cite title="Source Title"><?= date("F j, Y", strtotime($feedback['feedbackDate'])); ?></cite></footer>
                            </blockquote>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a class="carousel-control-prev" href="#testimonialCarousel" role="button" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only">Previous</span>
                </a>
                <a class="carousel-control-next" href="#testimonialCarousel" role="button" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only">Next</span>
                </a>
            </div>
        </section>

        <!-- FAQs Section -->
        <section id="faqs" class="pt-2">
            <h2 class="text-center">Frequently Asked Questions</h2>
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <span class="fw-bold">What services do you offer?</span>
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            We offer a wide range of automotive repair services, including oil changes, brake repairs, engine diagnostics, and much more. All services are performed by certified technicians to ensure the highest quality.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            <span class="fw-bold">How can I book an appointment?</span>
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            You can book an appointment either by calling us directly at our service desk or by using our online booking platform available on our website. We recommend booking in advance to ensure your preferred time slot.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <span class="fw-bold">Do you offer any warranties on your repairs?</span>
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Yes, all our repairs come with a minimum 90-day warranty. Specific details about the warranty terms depend on the type of repair and parts used. Please ask our service advisor for more information during your visit.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive contact section, consider adding in-app whatsapp contact -->
        <section id="contact" class="pt-2">
            <h2 class=text-center>Contact Us</h2>
            <div class="row pb-2">
                <div class="col-md-8">
                    <form>
                        <div class="form-group pb-2">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" placeholder="Enter your name">
                        </div>
                        <div class="form-group pb-2">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter your email">
                        </div>
                        <div class="form-group pb-2">
                            <label for="message">Message</label>
                            <textarea class="form-control" id="message" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                </div>
                <!-- Google Maps Embeded -->
                <div class="col-md-4">
                    <div class="col-md-4 google-map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d16784.917083037733!2d1.0588930978998212!3d51.29101531802176!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47decba7b82b98fd%3A0xf768ede137aa2890!2sUniversity%20of%20Kent%2C%20Canterbury%20Campus!5e0!3m2!1sen!2suk!4v1718036342579!5m2!1sen!2suk" frameborder="0" style="border:0" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
                    </div>
                </div>
            </div>
        </section>

        <!-- Social media links -->
        <footer class="footer">
            <div class="container text-center">
                <h2>Follow Us</h2>
                <a href="https://www.facebook.com/UniversityofKent/?locale=en_GB"><img src="../resources/socials/facebook.webp" alt="Facebook" style="width: 40px; height: 40px;"></a>
                <a href="https://x.com/UniKent"><img src="../resources/socials/twitter.webp" alt="X" style="width: 40px; height: 40px;"></a>
                <a href="https://www.instagram.com/unikentlive/?hl=en"><img src="../resources/socials/instagram.webp" alt="Instagram" style="width: 40px; height: 40px;"></a>
                <!-- More icons as needed -->
            </div>
        </footer>

        <footer class="text-center mt-4">
            <p>&copy; 2024 Garage Booking Application. All rights reserved.</p>
        </footer>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>