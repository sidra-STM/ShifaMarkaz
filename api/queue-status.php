<?php
require 'helpers.php';
$id = $_GET['doctor'] ?? '';
$token = (int)($_GET['token'] ?? 0);
if (!findDoctor($id)) jsonOut(['error' => 'Unknown doctor'], 404);
$q = readQueue($id);
if ($token < 1 || $token > $q['lastIssued']) jsonOut(['error' => 'Token not found. Please take a new token.'], 404);
jsonOut(statusFor($q, $token));