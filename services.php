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
        <div class="section active">
            <div class="container">
                <h2 class="section-title">Our Services</h2>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fa-solid fa-print"></i>
                        </div>
                        <h3>Document Printing</h3>
                        <p>Black & White: RM 0.10/page</p>
                        <p>Color: RM 0.50/page</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fa-solid fa-copy"></i>
                        </div>
                        <h3>Photocopy</h3>
                        <p>Black & White: RM 0.08/page</p>
                        <p>Color: RM 0.40/page</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fa-solid fa-address-card"></i>
                        </div>
                        <h3>Name Cards</h3>
                        <p>Starting from RM 25/100pcs</p>
                        <p>Minimum order 100pcs</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <h3>Binding</h3>
                        <p>Spiral: RM 3.00</p>
                        <p>Hard Cover: RM 8.00</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <h3>Lamination</h3>
                        <p>A4: RM 2.00/sheet</p>
                        <p>A3: RM 3.00/sheet</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="fa-solid fa-file-contract"></i>
                        </div>
                        <h3>Digital Scanning</h3>
                        <p>RM 1.00/page</p>
                    </div>
                </div>
            </div>
        </main>
        <?php
            require_once 'includes/footer.php';
        ?>
    <script src="js/smooth-scrolling.js"></script>
    <script src="js/hamburger.js"></script>
    </body>
</html>
