<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'dbms.php'; // Connect to DB

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Generate quotation_id
    $result = $conn->query("SELECT quotation_id FROM quotation ORDER BY quotation_id DESC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $lastId = (int)substr($row['quotation_id'], 1);
        $newId = 'Q' . str_pad($lastId + 1, 7, '0', STR_PAD_LEFT);
    } else {
        $newId = 'Q0000001';
    }

    // Escape form data
    $requester_name = mysqli_real_escape_string($conn, $_POST['requester_name']);
    $requester_email = mysqli_real_escape_string($conn, $_POST['requester_email']);
    $requester_phone_number = mysqli_real_escape_string($conn, $_POST['requester_phone_number']);
    $contact_method = mysqli_real_escape_string($conn, $_POST['contact_method']);
    $request_type = mysqli_real_escape_string($conn, $_POST['request_type']);
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;
    $paper_size = mysqli_real_escape_string($conn, $_POST['paper_size']);
    $size_unit = mysqli_real_escape_string($conn, $_POST['custom_unit'] ?? $_POST['size_unit'] ?? '');
    $size_width = isset($_POST['custom_width']) && $_POST['custom_width'] !== '' ? (float)$_POST['custom_width'] : (float)($_POST['size_width'] ?? 0);
    $size_height = isset($_POST['custom_height']) && $_POST['custom_height'] !== '' ? (float)$_POST['custom_height'] : (float)($_POST['size_height'] ?? 0);
    $paper_type = mysqli_real_escape_string($conn, $_POST['paper_type']);
    $finishing = mysqli_real_escape_string($conn, $_POST['finishing']);
    $file_type = mysqli_real_escape_string($conn, $_POST['file_type']);
    $file_page = isset($_POST['file_page']) ? (int)$_POST['file_page'] : null;
    $remark = mysqli_real_escape_string($conn, $_POST['remark']);
    $quotation_status = mysqli_real_escape_string($conn, $_POST['quotation_status'] ?? 'Pending');

    // Handle file upload
    $file_data = null;
    if (
        isset($_FILES['file_data']) &&
        $_FILES['file_data']['error'] === 0 &&
        is_uploaded_file($_FILES['file_data']['tmp_name']) &&
        is_readable($_FILES['file_data']['tmp_name'])
    ) {
        $file_data = addslashes(file_get_contents($_FILES['file_data']['tmp_name']));
    }

    // Build and execute SQL
    $sql = "INSERT INTO quotation (
        quotation_id, requester_name, requester_email, requester_phone_number, contact_method, request_type,
        quantity, paper_size, size_unit, size_width, size_height, paper_type, finishing,
        file_type, file_data, file_page, remark, quotation_status, create_date
    ) VALUES (
        '$newId', '$requester_name', '$requester_email', '$requester_phone_number', '$contact_method', '$request_type',
        $quantity, '$paper_size', '$size_unit', $size_width, $size_height, '$paper_type', '$finishing',
        '$file_type', " . ($file_data ? "'$file_data'" : "NULL") . ", $file_page, '$remark', '$quotation_status', NOW()
    )";

    if ($conn->query($sql)) {
        echo "<script>alert('Quotation submitted successfully!'); window.location.href = 'customerOrderlist.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Quotation Request</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: #f8f9fa;
      padding: 40px;
    }
    .form-container {
      background: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    .help-btn {
      margin-left: 6px;
      padding: 0 6px;
      font-size: 0.8rem;
      line-height: 1;
    }
  </style>
</head>
<body>
<div class="container form-container">
  <h3 class="mb-4">Quotation Request Form</h3>

  <form method="POST" enctype="multipart/form-data">
    <!-- Customer Info -->
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="requester_name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="requester_email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Phone Number</label>
      <input type="text" name="requester_phone_number" class="form-control">
    </div>
    <!-- Prefered Contact Method -->
    <div class="mb-3">
    <label class="form-label d-block">Preferred Contact Method</label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="contact_method" id="contact_email" value="Email" required>
        <label class="form-check-label" for="contact_email">Email</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="contact_method" id="contact_whatsapp" value="WhatsApp">
        <label class="form-check-label" for="contact_whatsapp">WhatsApp</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="contact_method" id="contact_call" value="Phone Call">
        <label class="form-check-label" for="contact_call">Phone Call</label>
    </div>
    </div>

    <!-- Request Type -->
    <div class="mb-3">
    <label class="form-label">
        Request Type
        <button type="button" class="btn btn-outline-secondary help-btn"
                data-bs-toggle="popover"
                data-bs-container="body"
                data-bs-placement="right"
                data-bs-html="true"
                title="Examples"
                data-bs-content="<ul style='margin-left: -20px; padding-left: 0;'><li>Booklet</li><li>Poster</li><li>Flyer</li><li>Banner</li><li>Name Card</li></ul>">?</button>
    </label>
    <input type="text" name="request_type" class="form-control">
    </div>

    <!-- Quantity -->
    <div class="mb-3">
      <label class="form-label">Quantity</label>
      <input type="text" name="quantity" class="form-control">
    </div>

    <!-- Paper Size -->
    <div class="mb-3">
    <label class="form-label">Paper Size</label>
    <select name="paper_size" id="paper_size" class="form-select">
        <option value="" selected disabled>Select a size</option>
        <option value="A4">A4</option>
        <option value="A3">A3</option>
        <option value="A5">A5</option>
        <option value="Custom">Custom</option>
    </select>
    </div>

    <div id="size-display" class="mb-3" style="display: none;">
    <label class="form-label">Dimensions</label>
    <div class="d-flex align-items-center gap-2">
        <input type="number" class="form-control" id="width" placeholder="Width" readonly>
        <span>x</span>
        <input type="number" class="form-control" id="height" placeholder="Height" readonly>
        <select id="unit" class="form-select" style="width: 100px;">
        <option value="cm">cm</option>
        <option value="mm">mm</option>
        <option value="inch">inch</option>
        </select>
    </div>
    </div>

    <div id="custom-size-inputs" style="display: none;">
    <label class="form-label mt-2">Enter Custom Size</label>
    <div class="d-flex align-items-center gap-2">
        <input type="number" class="form-control" id="custom_width" name="custom_width" placeholder="Width">
        <span>x</span>
        <input type="number" class="form-control" id="custom_height" name="custom_height" placeholder="Height">
        <select id="custom_unit" name="custom_unit" class="form-select" style="width: 100px;">
        <option value="cm">cm</option>
        <option value="mm">mm</option>
        <option value="inch">inch</option>
        </select>
        <input type="hidden" name="size_unit" id="hidden_size_unit">
        <input type="hidden" name="size_width" id="hidden_size_width">
        <input type="hidden" name="size_height" id="hidden_size_height">
    </div>
    </div>

    <!-- Paper Type -->
    <div class="mb-3">
    <label class="form-label">
        Paper Type
        <button type="button" class="btn btn-outline-secondary help-btn"
                data-bs-toggle="popover"
                data-bs-container="body"
                data-bs-placement="right"
                data-bs-html="true"
                title="Examples"
                data-bs-content="<ul style='margin-left: -20px; padding-left: 0;'><li>Glossy Art Paper</li><li>Matte Art Paper</li><li>Simili</li><li>Sticker</li></ul>">?</button>
    </label>
    <input type="text" name="paper_type" class="form-control">
    </div>

    <!-- Finishing -->
    <div class="mb-3">
    <label class="form-label">
        Finishing
        <button type="button" class="btn btn-outline-secondary help-btn"
                data-bs-toggle="popover"
                data-bs-container="body"
                data-bs-placement="right"
                data-bs-html="true"
                title="Examples"
                data-bs-content="<ul style='margin-left: -20px; padding-left: 0;'><li>Lamination</li><li>Binding</li><li>Trimming</li><li>Folding</li></ul>">?</button>
    </label>
    <input type="text" name="finishing" class="form-control">
    </div>

    <!-- File Upload -->
    <div class="mb-3">
    <label class="form-label">Upload File</label>
    <input type="file" name="file_data" class="form-control" id="file_data" accept=".pdf,.jpg,.jpeg,.png">
    </div>
    <!-- File Info Display (Styled like Bootstrap helper text) -->
    <div id="file-info-box" class="text-secondary small mb-3 ps-1" style="display: none;">
    Type: <span id="file-type-text">N/A</span>
    <span id="page-info" style="display: none;">&nbsp;&nbsp;|&nbsp;&nbsp;Pages: <span id="page-count">0</span></span>
    </div>
    <!-- Hidden input -->
    <input type="hidden" name="file_type" id="file_type">
    <input type="hidden" name="file_page" id="file_page">



    <!-- Remarks -->
    <div class="mb-3">
      <label class="form-label">Remark</label>
      <textarea name="remark" rows="4" class="form-control" placeholder="Any notes or requests..."></textarea>
    </div>

    <input type="hidden" name="quotation_status" value="Pending">

    <div class="d-flex justify-content-between">
    <!-- Reset Button on the left -->
    <button type="reset" class="btn btn-outline-danger" id="reset-btn">Reset</button>

    <!-- Submit Button on the right -->
    <button type="submit" class="btn btn-primary">Submit Quotation</button>
    </div>


  </form>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popovers = [];

    popoverTriggerList.forEach(el => {
      const popover = new bootstrap.Popover(el);
      popovers.push({ trigger: el, instance: popover });
    });

    // Close all popovers on outside click
    document.addEventListener("click", function (e) {
      popovers.forEach(({ trigger, instance }) => {
        if (!trigger.contains(e.target) && !document.querySelector(".popover")?.contains(e.target)) {
          instance.hide();
        }
      });
    });
  });
</script>

<!-- Handle Paper Size Function -->
<script>
    const sizeData = {
        A4: { width: 21.0, height: 29.7 },
        A3: { width: 29.7, height: 42.0 },
        A5: { width: 14.8, height: 21.0 }
    };

    const cmTo = {
        mm: val => val * 10,
        inch: val => val / 2.54,
        cm: val => val
    };

    const updateDisplay = (size, unit) => {
        if (!sizeData[size]) return;
        const base = sizeData[size];
        const width = cmTo[unit](base.width).toFixed(2);
        const height = cmTo[unit](base.height).toFixed(2);

        document.getElementById("width").value = width;
        document.getElementById("height").value = height;

        // Set hidden input values
        document.getElementById("hidden_size_width").value = width;
        document.getElementById("hidden_size_height").value = height;
        document.getElementById("hidden_size_unit").value = unit;

  };

    document.getElementById("paper_size").addEventListener("change", function () {
        const selected = this.value;
        const sizeBox = document.getElementById("size-display");
        const customBox = document.getElementById("custom-size-inputs");

        if (selected === "Custom") {
        sizeBox.style.display = "none";
        customBox.style.display = "block";
        } else {
        sizeBox.style.display = "block";
        customBox.style.display = "none";
        updateDisplay(selected, document.getElementById("unit").value);
        }
  });

  document.getElementById("unit").addEventListener("change", function () {
    const selected = document.getElementById("paper_size").value;
    if (sizeData[selected]) {
      updateDisplay(selected, this.value);
    }
  });
</script>

<!-- Handle File Function -->
<script>
    document.getElementById("file_data").addEventListener("change", function () {
    const file = this.files[0];

    const fileInfoBox = document.getElementById("file-info-box");
    const fileTypeText = document.getElementById("file-type-text");
    const fileTypeHidden = document.getElementById("file_type");
    const pageInfo = document.getElementById("page-info");
    const pageCountSpan = document.getElementById("page-count");

    // Reset display
    fileInfoBox.style.display = "none";
    pageInfo.style.display = "none";
    fileTypeText.textContent = "N/A";
    fileTypeHidden.value = "";

    if (!file) return;

    const mime = file.type;
    let detectedType = "";

    if (mime === "application/pdf") {
        detectedType = "PDF";

        const fileReader = new FileReader();
        fileReader.onload = function () {
        const typedarray = new Uint8Array(this.result);
        pdfjsLib.getDocument(typedarray).promise.then(function (pdf) {
            pageCountSpan.textContent = pdf.numPages;
            document.getElementById("file_page").value = pdf.numPages;
            pageInfo.style.display = "inline";
        }).catch(function () {
            pageInfo.style.display = "none";
        });
        };
        fileReader.readAsArrayBuffer(file);

    } else if (mime === "image/jpeg" || mime === "image/jpg") {
        detectedType = "JPG";
    } else if (mime === "image/png") {
        detectedType = "PNG";
    } else {
        detectedType = "Unknown";
    }

    // Show type and store
    fileTypeText.textContent = detectedType;
    fileTypeHidden.value = detectedType;
    fileInfoBox.style.display = "block";
});
</script>

<!-- Handle Reset Function -->
<script>
  document.getElementById("reset-btn").addEventListener("click", function () {
    // Hide file info and reset display values
    document.getElementById("file-info-box").style.display = "none";
    document.getElementById("file-type-text").textContent = "N/A";
    document.getElementById("page-count").textContent = "0";
    document.getElementById("file_type").value = "";

    // Hide custom size and show nothing for paper size
    document.getElementById("size-display").style.display = "none";
    document.getElementById("custom-size-inputs").style.display = "none";

    // Optionally reset paper size select to placeholder
    document.getElementById("paper_size").selectedIndex = 0;
  });
</script>





</body>
</html>
