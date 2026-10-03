<?php
$pageTitle = 'Home';
$active = 'index';
include 'includes/header.php';
?>
<section class="hero">
  <div class="container">
    <span class="tag">Built for the people of Chitral</span>
    <h1>Right doctor. Right time.<br>No long waiting.</h1>
    <p>Describe your problem, find the right doctor in Chitral, and take a digital token from your phone.</p>
    <a class="btn" href="navigator.php">Describe your symptoms</a>
    <a class="btn btn-outline" href="doctors.php">Browse doctors</a>
  </div>
  <svg class="mountains" viewBox="0 0 1200 120" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
    <path d="M0 120 L0 80 L120 30 L200 70 L330 10 L450 75 L560 40 L680 85 L800 25 L920 80 L1040 45 L1200 90 L1200 120 Z" fill="#99f6e4" opacity="0.55"/>
    <path d="M0 120 L0 100 L150 60 L260 95 L400 50 L520 100 L650 65 L780 105 L900 70 L1050 100 L1200 75 L1200 120 Z" fill="#0f766e" opacity="0.85"/>
  </svg>
</section>

<section class="section">
  <div class="container">
    <h2>How it works</h2>
    <div class="steps">
      <div class="step"><div class="num">1</div><strong>Describe symptoms</strong><p>Tell the navigator what is wrong, in simple words.</p></div>
      <div class="step"><div class="num">2</div><strong>Get the right specialty</strong><p>We suggest which type of doctor to see. No diagnosis.</p></div>
      <div class="step"><div class="num">3</div><strong>Pick a Chitral doctor</strong><p>See who is available, where and when.</p></div>
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