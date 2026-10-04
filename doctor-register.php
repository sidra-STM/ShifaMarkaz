<?php
$pageTitle = 'Doctor Registration';
$active = '';
include 'includes/header.php';
?>
<section class="section">
  <div class="container narrow">
    <h1 class="page-title">For doctors and clinics</h1>
    <p class="page-sub">Submit your details to request a listing in the ShifaMarkaz directory. Your registration will be reviewed and verified before you appear in ShifaMarkaz.</p>
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
<script src="js/common.js"></script>
<script src="js/register.js"></script>
<?php include 'includes/footer.php'; ?>
