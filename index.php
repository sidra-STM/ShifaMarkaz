<?php
$pageTitle = 'Home';
$active = 'index';
include 'includes/header.php';
?>
<section class="hero">
  <div class="container">
    <span class="tag">Built for the people of Chitral</span>
    <h1>Not sure which doctor to see?</h1>
    <p>Get a specialty suggestion, find a Chitral doctor, and take a queue token in minutes. Demo prototype. No appointment is booked.</p>
    <a class="btn" href="navigator.php">Describe your symptoms</a>
    <a class="btn btn-outline" href="doctors.php">Browse doctors</a>
  </div>
</section>

<section class="section">
  <div class="container">
    <h2>How it works</h2>
    <div class="steps">
      <div class="step"><div class="num">1</div><strong>Describe symptoms</strong><p>Tell the navigator what is wrong, in simple words.</p></div>
      <div class="step"><div class="num">2</div><strong>Get the right specialty</strong><p>We suggest which type of doctor to see. No diagnosis.</p></div>
      <div class="step"><div class="num">3</div><strong>Browse sample doctors</strong><p>Explore demo specialties, clinics, and example visiting hours.</p></div>
      <div class="step"><div class="num">4</div><strong>Get a digital token</strong><p>Watch your turn from your phone instead of waiting in a corridor.</p></div>
    </div>
  </div>
</section>

<section class="problem">
  <div class="container">
    <h2>The problem we solve</h2>
    <div class="grid">
      <div class="card"><h3>Which doctor?</h3><p>Many people do not know which specialist to consult for their problem.</p></div>
      <div class="card"><h3>Where and when?</h3><p>Specialists visit on fixed days, and families travel long distances to find out they are not there.</p></div>
      <div class="card"><h3>How long to wait?</h3><p>Patients sit for hours at clinics with no idea when their turn will come.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container narrow">
    <h2>For doctors and clinics</h2>
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
<script src="js/common.js"></script>
<script src="js/register.js"></script>
<?php include 'includes/footer.php'; ?>