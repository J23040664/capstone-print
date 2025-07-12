<header>
    <div class="container">
        <nav>
            <a href="index.php" class="logo">
                <img src="assets/img/logo.png" alt="Art & Print SS15 Logo" class="nav-logo-img">
            </a>

            <div class="nav-toggle" id="navToggle">
                <i class="fas fa-bars"></i>
            </div>

            <ul class="nav-links" id="navLinks">
                <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Home</a></li>
                <li><a href="services.php" class="<?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">Services</a></li>
                <li><a href="fee_calculator.php" class="<?php echo ($current_page == 'fee_calculator.php') ? 'active' : ''; ?>">Calculator</a></li>
                <li><a href="track_order.php" class="<?php echo ($current_page == 'track_order.php') ? 'active' : ''; ?>">Track Order</a></li>
                <li><a href="index.php#contact" class="nav-link-contact">Contact</a></li>
                <li class="nav-mobile-buttons">
                    <div class="auth-buttons">
                        <a href="login.php" class="btn btn-secondary">Login</a>
                        <a href="signup.php" class="btn btn-primary">Register</a>
                    </div>
                </li>
            </ul>

            <div class="auth-buttons desktop-visible">
                <a href="login.php" class="btn btn-secondary">Login</a>
                <a href="signup.php" class="btn btn-primary">Register</a>
            </div>
        </nav>
    </div>
</header>