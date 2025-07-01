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
    <link rel="stylesheet" href="./assets/css/landingStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php
    require_once './assets/includes/header.php';
    ?>
    <main>
        <section id="calculator" class="section active">
            <div class="container">
                <h2 class="section-title">Fee Calculator</h2>
                <div class="calculator">

                    <div class="form-group">
                        <h4>Calculating your budget? Try using our calculator to see how much you need to pay. (Only show available services options) </h4><br>
                        <label>Number Of Pages in File (Estimate) <span style="color: red;">*</span></label>
                        <input type="number" id="pageCount" min="1">
                    </div>

                    <div class="form-group">
                        <label>Number Of Copies <span style="color: red;">*</span></label>
                        <input type="number" id="copyCount" min="1">
                    </div>

                    <div class="form-group">
                        <label>Paper Size <span style="color: red;">*</span></label>
                        <select id="paperSize">
                            <option value="">Select Paper Size</option>
                            <?php
                            $sqlpapersize = "SELECT * FROM service_list WHERE service_status = 'Available' AND service_type = 'size'";
                            $querypapersize = mysqli_query($conn, $sqlpapersize);
                            while ($rowpapersize = mysqli_fetch_assoc($querypapersize)) { ?>
                                <option value="<?php echo $rowpapersize['service_price']; ?>"><?php echo $rowpapersize['service_desc']; ?> - RM <?php echo $rowpapersize['service_price']; ?> / Per</option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Print Colour <span style="color: red;">*</span></label>
                        <select id="printColour">
                            <option value="">Select Color Option</option>
                            <?php
                            $sqlcolor = "SELECT * FROM service_list WHERE service_status = 'Available' AND (service_type = 'colour' OR service_type = 'color')";
                            $querycolor = mysqli_query($conn, $sqlcolor);
                            while ($rowcolor = mysqli_fetch_assoc($querycolor)) { ?>
                                <option value="<?php echo $rowcolor['service_price']; ?>"><?php echo $rowcolor['service_desc']; ?> - RM <?php echo $rowcolor['service_price']; ?> / Per</option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Service Type <span style="color: red;">*</span></label>
                        <select id="serviceType">
                            <option value="">Select Service</option>
                            <?php
                            $serviceList = "SELECT * FROM service_list WHERE service_status = 'Available' AND service_type = 'print'";
                            $queryServiceList = mysqli_query($conn, $serviceList) or die(mysqli_error($conn));
                            while ($rowServiceList = mysqli_fetch_assoc($queryServiceList)) { ?>
                                <option value="<?php echo $rowServiceList['service_price']; ?>"><?php echo $rowServiceList['service_desc']; ?> - RM <?php echo $rowServiceList['service_price']; ?> / Per</option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Finishing 1</label>
                        <select id="finishing1" class="finishing-select" onchange="preventFinishingSelect()">
                            <option id="" value="">Select Finishing</option>
                            <?php
                            $finishingList = "SELECT * FROM finishing_list WHERE finishing_status = 'Available'";
                            $queryFinishingList = mysqli_query($conn, $finishingList) or die(mysqli_error($conn));
                            while ($rowFinishingList = mysqli_fetch_assoc($queryFinishingList)) { ?>
                                <option id="<?php echo $rowFinishingList['finishing_id']; ?>" value="<?php echo $rowFinishingList['finishing_price']; ?>"><?php echo $rowFinishingList['finishing_desc']; ?> - RM <?php echo $rowFinishingList['finishing_price']; ?> / Per</option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Finishing 2</label>
                        <select id="finishing2" class="finishing-select" onchange="preventFinishingSelect()">
                            <option value="">Select Finishing</option>
                            <?php
                            $finishingList = "SELECT * FROM finishing_list WHERE finishing_status = 'Available'";
                            $queryFinishingList = mysqli_query($conn, $finishingList) or die(mysqli_error($conn));
                            while ($rowFinishingList = mysqli_fetch_assoc($queryFinishingList)) { ?>
                                <option id="<?php echo $rowFinishingList['finishing_id']; ?>" value="<?php echo $rowFinishingList['finishing_price']; ?>"><?php echo $rowFinishingList['finishing_desc']; ?> - RM <?php echo $rowFinishingList['finishing_price']; ?> / Per</option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Finishing 3</label>
                        <select id="finishing3" class="finishing-select" onchange="preventFinishingSelect()">
                            <option value="">Select Finishing</option>
                            <?php
                            $finishingList = "SELECT * FROM finishing_list WHERE finishing_status = 'Available'";
                            $queryFinishingList = mysqli_query($conn, $finishingList) or die(mysqli_error($conn));
                            while ($rowFinishingList = mysqli_fetch_assoc($queryFinishingList)) { ?>
                                <option id="<?php echo $rowFinishingList['finishing_id']; ?>" value="<?php echo $rowFinishingList['finishing_price']; ?>"><?php echo $rowFinishingList['finishing_desc']; ?> - RM <?php echo $rowFinishingList['finishing_price']; ?> / Per</option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="calc-result" id="calcResult" onclick="calculateFee()">
                        Calculate Price
                    </div>

                    <br><br>

                    <div class="calc-order">
                        <p>Ready to make order with us?</p>
                        <a href="customerDashboard.php" class="btn btn-secondary">Place Order Now</a>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php require_once './assets/includes/footer.php'; ?>
</body>

<script src="./assets/js/hamburger.js"></script>
<script src="./assets/js/smooth-scrolling.js"></script>

<script>
    function preventFinishingSelect() {
        const selectedValues = [
            document.getElementById('finishing1').value,
            document.getElementById('finishing2').value,
            document.getElementById('finishing3').value
        ];

        document.querySelectorAll('.finishing-select').forEach(select => {
            const currentValue = select.value;

            Array.from(select.options).forEach(option => {
                if (option.value !== '') {
                    option.disabled = selectedValues.includes(option.value) && currentValue !== option.value;
                } else {
                    option.disabled = false;
                }
            });
        });
    }

    document.getElementById('finishing1').addEventListener('change', preventFinishingSelect);
    document.getElementById('finishing2').addEventListener('change', preventFinishingSelect);
    document.getElementById('finishing3').addEventListener('change', preventFinishingSelect);
</script>

<script>
    const copyInput = document.getElementById('copyCount');
    const calcResult = document.getElementById('calcResult');

    copyInput.addEventListener('input', () => {
        calcResult.innerHTML = 'Calculate Price';
    });
</script>

<script>
    function calculateFee() {
        const pages = parseInt(document.getElementById('pageCount').value) || 0;
        const copies = parseInt(document.getElementById('copyCount').value) || 0;

        const service = parseFloat(document.getElementById('serviceType').value) || 0;
        const size = parseFloat(document.getElementById('paperSize').value) || 0;
        const color = parseFloat(document.getElementById('printColour').value) || 0;

        const finishing1 = parseFloat(document.getElementById('finishing1').value) || 0;
        const finishing2 = parseFloat(document.getElementById('finishing2').value) || 0;
        const finishing3 = parseFloat(document.getElementById('finishing3').value) || 0;

        const calcResult = document.getElementById('calcResult');

        if (copies === 0 || pages === 0 || size === 0 || color === 0 || service === 0) {
            document.getElementById('calcResult').innerText = "Please fill up required fields before click here.";
            return;
        }

        const cost = copies * pages * size * color * service;
        const finishingCost = finishing1 + finishing2 + finishing3;
        const totalCost = cost + finishingCost;

        calcResult.innerHTML =
            `<strong>Total Price: RM ${totalCost.toFixed(2)}</strong>`;
    }

    // Run calculateFee() when input
    window.addEventListener('DOMContentLoaded', () => {
        const inputFields = ['pageCount', 'copyCount'];
        const selectFields = ['serviceType', 'paperSize', 'printColour', 'finishing1', 'finishing2', 'finishing3'];

        inputFields.forEach(id => {
            const field = document.getElementById(id);
            if (field) {
                field.addEventListener('input', calculateFee);
            }
        });

        selectFields.forEach(id => {
            const field = document.getElementById(id);
            if (field) {
                field.addEventListener('change', calculateFee);
            }
        });
    });
</script>