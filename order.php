<?php
include 'dbms.php';
session_start();
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
    <?php
    require_once 'includes/header.php';
    ?>
    <main>
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
    </main>
</body>

<script>
    let orders = [];
    let orderCounter = 12347;

    function showSection(sectionId) {
        // Hide all sections
        const sections = document.querySelectorAll('.section');
        sections.forEach(section => section.classList.remove('active'));

        // Show selected section
        document.getElementById(sectionId).classList.add('active');
    }

    const prices = {
        'print-bw': 0.10,
        'print-color': 0.50,
        'photocopy-bw': 0.08,
        'photocopy-color': 0.40,
        'namecard': 0.25, // per piece, minimum 100
        'binding-spiral': 3.00,
        'binding-hard': 8.00,
        'lamination-a4': 2.00,
        'lamination-a3': 3.00
    };

    function updateCalculator() {
        const service = document.getElementById('serviceType').value;
        const quantity = parseInt(document.getElementById('quantity').value) || 0;
        const totalDiv = document.getElementById('orderTotal');

        // Reset total display if service or quantity is invalid
        if (!service || !quantity) {
            totalDiv.textContent = 'Total: RM 0.00';
            return;
        }

        let total = 0;
        let pricePerUnit = 0; // Variable to hold the determined price

        // Determine the correct price based on the selected service type
        if (service === 'namecard') {
            // Name cards have a minimum quantity logic
            const minQuantity = Math.max(quantity, 100);
            pricePerUnit = prices[service]; // prices['namecard'] is 0.25
            total = minQuantity * pricePerUnit;
        } else if (service === 'binding') { // This specific check might not be needed if your options are 'binding-spiral' etc.
            pricePerUnit = prices['binding-spiral'];
            total = quantity * pricePerUnit;
        } else if (service === 'lamination') { // Same as above for lamination
            pricePerUnit = prices['lamination-a4'];
            total = quantity * pricePerUnit;
        } else if (prices[service]) {
            // For other services (print-bw, print-color, photocopy-bw, photocopy-color)
            // where the dropdown value directly matches a key in the 'prices' object
            pricePerUnit = prices[service];
            total = quantity * pricePerUnit;
        } else {
            // Fallback for any service type not found in prices, or if it's the default "Select Service"
            totalDiv.textContent = 'Total: RM 0.00 (Service price not available)';
            return; // Exit function if price not found
        }

        // Update the total display
        totalDiv.textContent = `Total: RM ${total.toFixed(2)}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('fileInput');
        const fileUpload = document.querySelector('.file-upload');

        fileInput.addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                const fileNames = Array.from(files).map(file => file.name).join(', ');
                fileUpload.innerHTML = `<p><strong>Files selected:</strong></p><p>${fileNames}</p>`;
            }
        });

        // Drag and drop functionality
        fileUpload.addEventListener('dragover', function(e) {
            e.preventDefault();
            fileUpload.style.backgroundColor = '#f0f0ff';
        });

        fileUpload.addEventListener('dragleave', function(e) {
            e.preventDefault();
            fileUpload.style.backgroundColor = '';
        });

        // --- FIX STARTS HERE (corrected 'drop' event listener) ---
        fileUpload.addEventListener('drop', function(e) {
            e.preventDefault();
            fileUpload.style.backgroundColor = '';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                const fileNames = Array.from(files).map(file => file.name).join(', '); // Fixed this line
                fileUpload.innerHTML = `<p><strong>Files selected:</strong></p><p>${fileNames}</p>`; // Added this line back
            }
        }); // Correctly closed 'drop' event listener function
        // --- FIX ENDS HERE ---

    }); // Correctly closed DOMContentLoaded event listener

    // --- FIX STARTS HERE (corrected 'submitOrder' function) ---
    function submitOrder(event) {
        event.preventDefault(); // Prevent default form submission (page reload)

        const orderData = {
            id: orderCounter++,
            customerName: document.getElementById('customerName').value,
            customerPhone: document.getElementById('customerPhone').value,
            customerEmail: document.getElementById('customerEmail').value,
            serviceType: document.getElementById('serviceType').value,
            quantity: document.getElementById('quantity').value,
            instructions: document.getElementById('instructions').value,
            status: 'pending',
            timestamp: new Date().toLocaleString(),
            total: document.getElementById('orderTotal').textContent
        };

        orders.push(orderData); // This line is now INSIDE the function
        alert(`Order placed successfully! Your order ID is #${orderData.id}. You will receive a confirmation email shortly.`);
        
        // Optional: Clear the form after submission
        document.querySelector('.order-form').reset();
        document.getElementById('orderTotal').textContent = 'Total: RM 0.00'; // Reset total display
    }
    // --- FIX ENDS HERE ---

    // You might also need to call updateCalculator() on page load if you have default values
    // window.onload = updateCalculator; // Uncomment if you want to initialize total on load
</script>
