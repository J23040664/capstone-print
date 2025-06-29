<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Art & Print SS15 - Professional Printing Services</title>
        <link rel="stylesheet" href="css/style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <body>

        <main>
        <!-- Home Section -->
            <section id="home" class="section active">
                <div class="container">
                    <div class="hero">
                      <h1>Art & Print SS15</h1>
                      <p>Professional printing services in Subang SS15, Malaysia</p>
                      <a href="order.php" class="btn btn-primary">Place Order Now</a>
                    </div>
                    <div class="section-title">
                        <p>Service That We Offered</p>
                    </div>
                    <div class="services-grid">
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fa-solid fa-print"></i>
                            </div>
                        <h3>Document Printing</h3>
                        <p>High-quality document printing for all your needs</p>
                        </div>
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fa-solid fa-copy"></i>
                            </div>
                            <h3>Photocopy</h3>
                            <p>Fast and reliable photocopying services</p>
                        </div>
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fa-solid fa-address-card"></i>
                            </div>
                            <h3>Name Cards</h3>
                            <p>Professional business card printing</p>
                        </div>
                        <div class="service-card">
                            <div class="service-icon">
                                <i class="fa-solid fa-file-contract"></i>
                            </div>
                            <h3>Binding & Lamination</h3>
                            <p>Complete finishing services for your documents</p>
                            </div>               
                        </div>
                        <div class="section-test">
                        <a href="services.php" class="btn btn-secondary">
                        Explore more  &nbsp;<i class="fas fa-arrow-right"></i>
                        </a>                      
                    </div>
                </div>
            </section>

        <!-- Contact Section -->
        <section id="contact" class="section">
            <div class="container">
                <h2 class="section-title">Contact Us</h2>
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <h3>Store Information</h3>
                        <p><strong>Address:</strong><br>Subang SS15, Selangor, Malaysia</p>
                        <p><strong>Phone:</strong> +6012-345 6789</p>
                        <p><strong>Email:</strong> info@artprintss15.com</p>
                        <p><strong>Hours:</strong><br>Mon-Fri: 9AM-7PM<br>Sat: 9AM-5PM<br>Sun: Closed</p>
                    </div>
                    <div class="dashboard-card">
                        <h3>Get Quote</h3>
                        <form onsubmit="requestQuote(event)">
                            <div class="form-group">
                                <input type="text" placeholder="Your Name" required>
                            </div>
                            <div class="form-group">
                                <input type="email" placeholder="Your Email" required>
                            </div>
                            <div class="form-group">
                                <textarea placeholder="Describe your printing needs..." rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Request Quote</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <?php
            require_once 'includes/footer.php';
        ?>

        <script src="js/theme.js"></script>
        <script src="js/smooth-scrolling.js"></script>
        <script src="js/hamburger.js"></script>
    </main>
</body>
        