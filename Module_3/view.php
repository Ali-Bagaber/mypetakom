<?php

include '../../Databased/db_connect.php';

  include '../HADER_SIDER_FOOTER/HST.PHP';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>MyPetakom System - Scan Attendance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <link rel="stylesheet" href="view.css" />
</head>
<body>

<div class="layout-container">
   

    <div class="scanner-box">
    <div class="main-content">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-semibold text-sm select-none">
            Scan Attendance</h3>
      <button id="cancelBtn">Cancel</button>
      <div id="reader"></div>
      <div class="buttons">
        <button id="uploadBtn">Upload</button>
        
      <p id="resultMessage">Please scan a QR code.</p>
    </div>
  </div>
</div>

<!-- Footer -->
    <footer class="footer">
      <p>&copy; 2025 MyPetakom System. All rights reserved. | UMP Student Dashboard</p>
    </footer>
  </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.7/html5-qrcode.min.js"></script>
<script>
  const resultMessage = document.getElementById('resultMessage');
  const continueBtn = document.getElementById('continueBtn');
  const cancelBtn = document.getElementById('cancelBtn');
  const uploadBtn = document.getElementById('uploadBtn');
  let lastResult = null;
  let html5QrcodeScanner;

  function onScanSuccess(decodedText) {
    if (decodedText !== lastResult) {
      lastResult = decodedText;
      resultMessage.textContent = "QR Code detected: " + decodedText;
      continueBtn.disabled = false;
      html5QrcodeScanner.stop().catch(err => console.log("Stop failed", err));
    }
  }

  function startScanner() {
    html5QrcodeScanner = new Html5Qrcode("reader");
    html5QrcodeScanner.start(
      { facingMode: "environment" },
      { fps: 10, qrbox: 250 },
      onScanSuccess
    ).catch(err => {
      resultMessage.textContent = "Camera error or not found.";
    });
  }

  window.onload = startScanner;

  cancelBtn.addEventListener('click', () => {
    if (html5QrcodeScanner) {
      html5QrcodeScanner.stop().then(() => {
        resultMessage.textContent = "Scan cancelled.";
        continueBtn.disabled = true;
        document.getElementById('reader').innerHTML = "Scanner stopped.";
        lastResult = null;
      }).catch(err => console.log("Stop failed", err));
    }
  });

  uploadBtn.addEventListener('click', () => {
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/*';
    fileInput.onchange = e => {
      const file = e.target.files[0];
      if (!file) return;
      html5QrcodeScanner.scanFile(file, true)
        .then(decodedText => {
          resultMessage.textContent = "QR Code detected: " + decodedText;
          continueBtn.disabled = false;
          lastResult = decodedText;
        })
        .catch(() => {
          resultMessage.textContent = "No QR code found in image.";
        });
    };
    fileInput.click();
  });

  continueBtn.addEventListener('click', () => {
    if (lastResult) {
      alert("Continuing with QR Code: " + lastResult);
    }
  });
</script>
<!-- Footer -->
    <footer class="footer">
      <p>&copy; 2025 MyPetakom System. All rights reserved. | UMP Student Dashboard</p>
    </footer>
  </div>
</body>
</html>
