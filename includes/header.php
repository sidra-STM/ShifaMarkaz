<?php
$pageTitle = $pageTitle ?? 'ShifaMarkaz';
$active = $active ?? '';
function navLink($file, $label, $active) {
    $cls = ($active === $file) ? 'active' : '';
    echo "<a class=\"$cls\" href=\"$file.php\">$label</a>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?> | ShifaMarkaz</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container nav">
    <a class="logo" href="index.php">ShifaMarkaz</a>
    <nav>
      <?php
        navLink('index', 'Home', $active);
        navLink('doctors', 'Find a Doctor', $active);
        navLink('navigator', 'AI Navigator', $active);
        navLink('queue', 'My Queue', $active);
        navLink('dashboard', 'Clinic Dashboard', $active);
      ?>
    </nav>
  </div>
</header>
<main>