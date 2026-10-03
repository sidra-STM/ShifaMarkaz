<?php
require 'helpers.php';
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$id = $in['doctor'] ?? '';
$name = trim($in['name'] ?? '');
$phone = trim($in['phone'] ?? '');

if (!findDoctor($id)) jsonOut(['error' => 'Unknown doctor'], 404);
if ($name === '' || $phone === '') jsonOut(['error' => 'Name and phone are required'], 400);
$name = mb_substr($name, 0, 60);
$phone = mb_substr($phone, 0, 20);

$result = withQueues(function (&$data) use ($id, $name, $phone) {
    if (!isset($data[$id])) $data[$id] = seedQueue();
    $q =& $data[$id];
    $q['lastIssued']++;
    $token = $q['lastIssued'];
    $q['tickets'][] = ['token' => $token, 'name' => $name, 'phone' => $phone, 'time' => date('h:i A')];
    return statusFor($q, $token);
});
jsonOut($result);