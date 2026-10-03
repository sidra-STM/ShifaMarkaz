<?php
require 'helpers.php';
$id = $_GET['doctor'] ?? '';
if (!findDoctor($id)) jsonOut(['error' => 'Unknown doctor'], 404);
$q = readQueue($id);
jsonOut(['current' => $q['current'], 'waiting' => $q['lastIssued'] - $q['current']]);