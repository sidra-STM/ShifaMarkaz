<?php
$pageTitle = 'Clinic Dashboard';
$active = 'dashboard';
include 'includes/header.php';
?>
<div class="container">
  <h1 class="page-title">Clinic Dashboard</h1>
  <p class="page-sub"><span class="live"></span>Demo queue updates every 3 seconds. Sample data only; no clinic is connected and no login is required.</p>

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

  <section class="section">
    <div class="container narrow">
      <h2>Doctor and clinic registration</h2>
      <p class="page-sub">Submit your details to request a listing in the ShifaMarkaz directory. Submissions are saved for review and are not published automatically.</p>
      <form id="registrationForm" class="card form">
        <label for="clinicName">Doctor or clinic name</label>
        <input id="clinicName" name="clinicName" type="text" required maxlength="100">

        <label for="specialty">Specialty</label>
        <input id="specialty" name="specialty" type="text" required maxlength="80">

        <label for="contactName">Contact person</label>
        <input id="contactName" name="contactName" type="text" required maxlength="80">

        <label for="contactPhone">Phone number</label>
        <input id="contactPhone" name="phone" type="tel" required maxlength="24">

        <label for="contactEmail">Email (optional)</label>
        <input id="contactEmail" name="email" type="email" maxlength="120">

        <button class="btn" type="submit">Submit listing request</button>
        <p id="registrationMessage" class="error" role="status" aria-live="polite"></p>
      </form>
    </div>
  </section>
</div>
<script src="js/common.js"></script>
<script src="js/dasboard.js"></script>
<script src="js/register.js"></script>
<?php include 'includes/footer.php'; ?>