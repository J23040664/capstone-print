<?php
include "./dbms.php";

?>

<html>
    <head>

    </head>
    <body>
        <!-- Service Type Dropdown -->
        <div class="mb-4">
        <label for="typeservices" class="form-label">Type Of Services:</label>
        <select class="form-select" id="serviceType" name="serviceType">
            <option value="None">None</option>
            <option value="Lamination">Lamination</option>
            <option value="Stapler">Stapler</option>
        </select>
        </div>
    </body>
    <form action="your_submission_page.php" method="post">
    <label for="service_type">Service Type:</label>

    <select name="service_type" class="form-control" required>
        <option value="">-- Select Service --</option>
        <?php
        // Include your database connection
        include 'dbms.php'; // make sure this file connects to your DB

        $sql = "SELECT service_id, service_type FROM service_list WHERE service_status = 'Available'";
        $result = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='" . $row['service_id'] . "'>" . $row['service_type'] . "</option>";
        }
        ?>
    </select>

    <!-- Add any other input fields, e.g. quantity, user name, etc. -->

    <br>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>

</html>