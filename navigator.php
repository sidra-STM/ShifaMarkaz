<?php
$pageTitle = 'AI Navigator';
$active = 'navigator';
include 'includes/header.php';
?>
<div class="container narrow">
  <h1 class="page-title">AI Health Navigator</h1>
  <p class="page-sub">Describe your problem. I will suggest which type of doctor to see. I do not diagnose or prescribe.</p>

  <div class="chips">
    <button class="chip example" data-text="My child has fever and cough since yesterday">Child has fever and cough</button>
    <button class="chip example" data-text="I have back pain when walking">Back pain when walking</button>
    <button class="chip example" data-text="I have a skin rash with itching on my arms">Skin rash</button>
    <button class="chip example" data-text="I have chest pain">Chest pain</button>
  </div>

  <div id="chat" class="chat"></div>
  <div class="chat-row">
    <input id="msg" type="text" placeholder="Type your symptoms..." maxlength="300">
    <button id="send" class="btn">Send</button>
  </div>
  <p class="disclaimer">Not a diagnosis. In an emergency, go to the nearest hospital or call Rescue 1122.</p>
</div>
<script src="js/common.js"></script>
<script src="js/navigator.js"></script>
<?php include 'includes/footer.php'; ?>