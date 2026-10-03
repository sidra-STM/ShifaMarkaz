<?php
require 'helpers.php';
$id = $_GET['doctor'] ?? '';
$doctor = findDoctor($id);
if (!$doctor) jsonOut(['error' => 'Unknown doctor'], 404);
$q = readQueue($id);

$waiting = [];
$serving = null;
foreach ($q['tickets'] as $t) {
    if ($t['token'] > $q['current']) $waiting[] = $t;
    elseif ($t['token'] == $q['current']) $serving = $t;
}
jsonOut([
    'doctor' => $doctor['name'],
    'current' => $q['current'],
    'serving' => $serving,
    'waiting' => $waiting,
    'servedToday' => max(0, $q['current'] - 1)
]);