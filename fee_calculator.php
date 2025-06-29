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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <?php
    require_once 'includes/header.php';
    ?>
    <main>
        <section id="calculator" class="section active">
            <div class="container">
                <h2 class="section-title">Fee Calculator</h2>
                <div class="calculator">
                    <div class="form-group">
                        <h4>Calculating your budget? Try using our calculator to see how much you need to pay. </h4>
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

                    <div class="calc-order">
                        <p>Ready to make order with us?</p>
                        <a href="order.php" class="btn btn-secondary">Place Order Now</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php
        require_once 'includes/footer.php';
    ?>
    <script src="js/hamburger.js"></script>
</body>
<script>
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
        function calculateFee() {
        console.log("calculateFee() called!");
        const service = document.getElementById('calcService').value;
        const quantity = parseInt(document.getElementById('calcQuantity').value) || 0;
        const resultDiv = document.getElementById('calcResult');

        if (!service || !quantity) {
            resultDiv.textContent = 'Enter service and quantity to calculate';
            return;
        }

        let total = 0;
            const minQuantity = Math.max(quantity, 100);
            total = minQuantity * prices[service];
            resultDiv.innerHTML = `<strong>Total: RM ${total.toFixed(2)}</strong><br><small>Minimum 100 pieces for name cards</small>`;
        } else {
            total = quantity * prices[service];
            resultDiv.innerHTML = `<strong>Total: RM ${total.toFixed(2)}</strong>`;
        }
        <script src="js/smooth-scrolling.js"></script>
        <script src="js/hamburger.js"></script>
</script>