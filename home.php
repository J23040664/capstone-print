<?php
include 'includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art & Print SS15 - Professional Printing Services</title>
    <link rel="stylesheet" href="css/style.css">
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

                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon">📄</div>
                        <h3>Document Printing</h3>
                        <p>High-quality document printing for all your needs</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">📋</div>
                        <h3>Photocopy</h3>
                        <p>Fast and reliable photocopying services</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">💳</div>
                        <h3>Name Cards</h3>
                        <p>Professional business card printing</p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">📚</div>
                        <h3>Binding & Lamination</h3>
                        <p>Complete finishing services for your documents</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Order Section -->
        <section id="order" class="section">
            <div class="container">
                <h2 class="section-title">Place Your Order</h2>
                <form class="order-form" onsubmit="submitOrder(event)">
                    <div class="form-group">
                        <label>Customer Name</label>
                        <input type="text" id="customerName" required>
                    </div>

                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" id="customerPhone" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="customerEmail" required>
                    </div>

                    <div class="form-group">
                        <label>Service Type</label>
                        <select id="serviceType" onchange="updateCalculator()" required>
                            <option value="">Select Service</option>
                            <option value="print-bw">Document Printing (B&W)</option>
                            <option value="print-color">Document Printing (Color)</option>
                            <option value="photocopy-bw">Photocopy (B&W)</option>
                            <option value="photocopy-color">Photocopy (Color)</option>
                            <option value="namecard">Name Cards</option>
                            <option value="binding">Binding</option>
                            <option value="lamination">Lamination</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity/Pages</label>
                        <input type="number" id="quantity" min="1" onchange="updateCalculator()" required>
                    </div>

                    <div class="form-group">
                        <label>Upload Files</label>
                        <div class="file-upload" onclick="document.getElementById('fileInput').click()">
                            <input type="file" id="fileInput" multiple accept=".pdf,.doc,.docx,.jpg,.png">
                            <p>Click to upload files or drag and drop</p>
                            <p><small>Supported formats: PDF, DOC, DOCX, JPG, PNG</small></p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Special Instructions</label>
                        <textarea id="instructions" rows="3" placeholder="Any special requirements..."></textarea>
                    </div>

                    <div class="calc-result" id="orderTotal">
                        Total: RM 0.00
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">Place Order</button>
                </form>
            </div>
        </section>

        <!-- Calculator Section -->
        <section id="calculator" class="section">
            <div class="container">
                <h2 class="section-title">Fee Calculator</h2>
                <div class="calculator">
                    <div class="form-group">
                        <label>Service Type</label>
                        <select id="calcService" onchange="calculateFee()">
                            <option value="">Select Service</option>
                            <option value="print-bw">Document Printing (B&W) - RM 0.10/page</option>
                            <option value="print-color">Document Printing (Color) - RM 0.50/page</option>
                            <option value="photocopy-bw">Photocopy (B&W) - RM 0.08/page</option>
                            <option value="photocopy-color">Photocopy (Color) - RM 0.40/page</option>
                            <option value="namecard">Name Cards - RM 25/100pcs</option>
                            <option value="binding-spiral">Spiral Binding - RM 3.00/book</option>
                            <option value="binding-hard">Hard Cover Binding - RM 8.00/book</option>
                            <option value="lamination-a4">Lamination A4 - RM 2.00/sheet</option>
                            <option value="lamination-a3">Lamination A3 - RM 3.00/sheet</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity</label>
                        <input type="number" id="calcQuantity" min="1" onchange="calculateFee()">
                    </div>

                    <div class="calc-result" id="calcResult">
                        Enter service and quantity to calculate
                    </div>
                </div>
            </div>
        </section>

        <!-- Tracking Section -->
        <section id="tracking" class="section">
            <div class="container">
                <h2 class="section-title">Track Your Order</h2>
                <div class="order-form">
                    <div class="form-group">
                        <label>Order ID</label>
                        <input type="text" id="trackingId" placeholder="Enter your order ID">
                    </div>
                    <button type="button" onclick="trackOrder()" class="btn btn-primary">Track Order</button>
                    
                    <div id="trackingResult" style="margin-top: 2rem; display: none;">
                        <div class="order-item">
                            <h4>Order #12345</h4>
                            <p><strong>Status:</strong> <span class="status-badge status-processing">Processing</span></p>
                            <p><strong>Service:</strong> Document Printing (Color)</p>
                            <p><strong>Quantity:</strong> 25 pages</p>
                            <p><strong>Total:</strong> RM 12.50</p>
                            <p><strong>Estimated Ready:</strong> Tomorrow 2:00 PM</p>
                        </div>
                    </div>
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
                        <p><strong>Phone:</strong> +60X-XXX-XXXX</p>
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

        <!-- Login Section -->
        <section id="login" class="section">
            <div class="container">
                <form class="login-form" onsubmit="handleLogin(event)">
                    <h2 style="text-align: center; margin-bottom: 2rem;">Customer Login</h2>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-bottom: 1rem;">Login</button>
                    <p style="text-align: center;">
                        Don't have an account? <a href="#" style="color: #667eea;">Register here</a>
                    </p>
                </form>
            </div>
        </section>

        <!-- Admin Section -->
        <section id="admin" class="section">
            <div class="container">
                <h2 class="section-title">Admin Dashboard</h2>
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <h3>Recent Orders</h3>
                        <div class="order-item">
                            <h4>Order #12345</h4>
                            <p>Customer: John Doe</p>
                            <p>Service: Document Printing (Color)</p>
                            <span class="status-badge status-pending">Pending</span>
                            <button class="btn btn-primary" style="margin-top: 0.5rem; padding: 0.3rem 0.8rem; font-size: 0.8rem;">Process</button>
                        </div>
                        <div class="order-item">
                            <h4>Order #12346</h4>
                            <p>Customer: Jane Smith</p>
                            <p>Service: Name Cards</p>
                            <span class="status-badge status-processing">Processing</span>
                            <button class="btn btn-secondary" style="margin-top: 0.5rem; padding: 0.3rem 0.8rem; font-size: 0.8rem;">Mark Ready</button>
                        </div>
                    </div>
                    <div class="dashboard-card">
                        <h3>Quick Stats</h3>
                        <p><strong>Today's Orders:</strong> 15</p>
                        <p><strong>Pending:</strong> 5</p>
                        <p><strong>Processing:</strong> 7</p>
                        <p><strong>Ready:</strong> 3</p>
                        <p><strong>Revenue Today:</strong> RM 250.00</p>
                    </div>
                    <div class="dashboard-card">
                        <h3>Price Management</h3>
                        <div class="form-group">
                            <label>Document Printing (B&W)</label>
                            <input type="number" step="0.01" value="0.10" style="margin-bottom: 1rem;">
                        </div>
                        <div class="form-group">
                            <label>Document Printing (Color)</label>
                            <input type="number" step="0.01" value="0.50" style="margin-bottom: 1rem;">
                        </div>
                        <button class="btn btn-primary">Update Prices</button>
                    </div>
                </div>
            </div>
        </section>
    </main>
