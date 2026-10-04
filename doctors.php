<?php
$pageTitle = 'Find a Doctor';
$active = 'doctors';
include 'includes/header.php';
?>
<div class="container">
  <h1 class="page-title">Find a Doctor in Chitral</h1>
  <p class="page-sub">Sample listings only; availability is not verified. Search by name, clinic or specialty.</p>

  <input id="search" class="search" type="text" placeholder="Search doctors, clinics...">
  <div id="chips" class="chips"></div>
  <div id="grid" class="grid"><p class="empty">Loading doctors...</p></div>
</div>
<script src="js/doctors.js"></script>
<?php include 'includes/footer.php'; ?>