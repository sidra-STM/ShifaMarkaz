<?php
$pageTitle = 'Home';
$active = 'index';
include 'includes/header.php';
?>
<section class="hero">
  <div class="container">
    <span class="tag">Built for the people of Chitral</span>
    <h1>Find the right doctor. Skip the long wait.</h1>
    <p>Describe your problem, see which type of doctor suits you, browse sample Chitral doctors, and take a digital queue token. This does not book an appointment time.</p>
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

<?php include 'includes/footer.php'; ?>