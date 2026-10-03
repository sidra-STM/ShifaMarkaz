<?php
$pageTitle = 'Clinic Dashboard';
$active = 'dashboard';
include 'includes/header.php';
?>
<div class="container">
  <h1 class="page-title">Clinic Dashboard</h1>
  <p class="page-sub"><span class="live"></span>Live. Updates every 3 seconds. Demo only, no login.</p>

  <div class="form">
    <label for="doctorSelect">Doctor</label>
    <select id="doctorSelect"></select>
  </div>

  <div class="stats">
    <div class="stat big"><span>NOW SERVING</span><strong id="nowServing">-</strong><small id="servingName"></small></div>
    <div class="stat"><span>WAITING</span><strong id="waitingCount">-</strong></div>
    <div class="stat"><span>SERVED TODAY</span><strong id="servedCount">-</strong></div>
  </div>

  <button id="callNext" class="btn btn-big">Call Next Patient</button>
  <p id="msg" class="error"></p>

  <h2>Waiting patients</h2>
  <table>
    <thead><tr><th>Token</th><th>Name</th><th>Phone</th><th>Time taken</th></tr></thead>
    <tbody id="rows"></tbody>
  </table>
  <p><button id="resetBtn" class="btn-small">Reset demo queue</button></p>
</div>
<script src="js/common.js"></script>
<script src="js/dashboard.js"></script>
<?php include 'includes/footer.php'; ?>