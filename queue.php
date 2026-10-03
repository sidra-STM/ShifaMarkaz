<?php
$pageTitle = 'My Queue';
$active = 'queue';
include 'includes/header.php';
?>
<div class="container narrow">
  <h1 class="page-title">My Queue</h1>
  <p class="page-sub"><span class="live"></span>Live. Updates every 3 seconds.</p>
  <div id="box"><p class="empty">Loading...</p></div>
</div>
<script src="js/common.js"></script>
<script src="js/queue.js"></script>
<?php include 'includes/footer.php'; ?>