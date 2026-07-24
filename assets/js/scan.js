// Barcode scanning for the add/edit form. Uses the native BarcodeDetector API
// (Chrome / Android). Falls back silently to manual entry when unsupported.
(function () {
  'use strict';

  var panel = document.getElementById('scanPanel');
  if (!panel || !('BarcodeDetector' in window)) {
    return; // unsupported browser: leave the form as plain manual entry
  }

  var startBtn = document.getElementById('scanStart');
  var status = document.getElementById('scanStatus');
  var video = document.getElementById('scanVideo');
  var nameInput = document.getElementById('name');
  var strengthInput = document.getElementById('strength');

  panel.style.display = '';
  var stream = null;
  var scanning = false;

  var detector = new window.BarcodeDetector({
    formats: ['ean_13', 'ean_8', 'upc_a', 'upc_e', 'code_128', 'code_39']
  });

  function stop() {
    scanning = false;
    if (stream) {
      stream.getTracks().forEach(function (t) { t.stop(); });
      stream = null;
    }
    video.style.display = 'none';
  }

  function lookup(code) {
    status.textContent = 'Looking up ' + code + '...';
    fetch('api/lookup.php?barcode=' + encodeURIComponent(code))
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data && data.found) {
          if (nameInput && data.name) nameInput.value = data.name;
          if (strengthInput && data.strength) strengthInput.value = data.strength;
          status.textContent = 'Found: ' + data.name + '. Check the details and save.';
        } else {
          status.textContent = 'Barcode ' + code + ' not found. Please enter the details manually.';
        }
      })
      .catch(function () {
        status.textContent = 'Lookup failed. Please enter the details manually.';
      });
  }

  function tick() {
    if (!scanning) return;
    detector.detect(video)
      .then(function (codes) {
        if (codes.length) {
          var code = codes[0].rawValue;
          stop();
          lookup(code);
        } else if (scanning) {
          requestAnimationFrame(tick);
        }
      })
      .catch(function () {
        if (scanning) requestAnimationFrame(tick);
      });
  }

  startBtn.addEventListener('click', function () {
    if (scanning) { stop(); status.textContent = 'Scanning cancelled.'; return; }
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      status.textContent = 'Camera not available on this device.';
      return;
    }
    status.textContent = 'Starting camera...';
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
      .then(function (s) {
        stream = s;
        video.srcObject = s;
        video.style.display = 'block';
        video.play();
        scanning = true;
        status.textContent = 'Point the camera at the barcode.';
        requestAnimationFrame(tick);
      })
      .catch(function () {
        status.textContent = 'Camera permission denied. Enter the details manually.';
      });
  });
})();
