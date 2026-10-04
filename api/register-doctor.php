<?php
require 'helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonOut(['error' => 'Method not allowed'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input) || json_last_error() !== JSON_ERROR_NONE) {
    jsonOut(['error' => 'Invalid JSON request'], 400);
}

$fields = [
    'clinicName' => ['label' => 'Doctor or clinic name', 'max' => 100],
    'specialty' => ['label' => 'Specialty', 'max' => 80],
    'contactName' => ['label' => 'Contact person', 'max' => 80],
    'phone' => ['label' => 'Phone number', 'max' => 24],
];
$registration = [];
foreach ($fields as $key => $field) {
    if (!isset($input[$key]) || !is_string($input[$key])) {
        jsonOut(['error' => $field['label'] . ' is required'], 400);
    }
    $value = trim($input[$key]);
    if ($value === '' || mb_strlen($value) > $field['max']) {
        jsonOut(['error' => $field['label'] . ' is required and must be no more than ' . $field['max'] . ' characters'], 400);
    }
    $registration[$key] = $value;
}

if (!preg_match('/^[0-9+() -]{7,24}$/', $registration['phone'])) {
    jsonOut(['error' => 'Enter a valid phone number'], 400);
}

$email = $input['email'] ?? '';
if (!is_string($email) || mb_strlen(trim($email)) > 120) {
    jsonOut(['error' => 'Email must be no more than 120 characters'], 400);
}
$email = trim($email);
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonOut(['error' => 'Enter a valid email address'], 400);
}

$file = DATA_DIR . 'doctor-registration-requests.json';
$handle = @fopen($file, 'c+');
if (!$handle) {
    jsonOut(['error' => 'Could not save the listing request'], 500);
}
if (!flock($handle, LOCK_EX)) {
    fclose($handle);
    jsonOut(['error' => 'Could not lock registration data'], 500);
}

$contents = stream_get_contents($handle);
$requests = $contents === '' ? [] : json_decode($contents, true);
if (!is_array($requests) || ($contents !== '' && json_last_error() !== JSON_ERROR_NONE)) {
    flock($handle, LOCK_UN);
    fclose($handle);
    jsonOut(['error' => 'Registration data is invalid; request was not saved'], 500);
}

$requests[] = [
    'clinicName' => $registration['clinicName'],
    'specialty' => $registration['specialty'],
    'contactName' => $registration['contactName'],
    'phone' => $registration['phone'],
    'email' => $email,
    'status' => 'pending',
    'createdAt' => date(DATE_ATOM),
    'reviewedAt' => null,
    'reviewNote' => '',
];
$json = json_encode($requests, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if ($json === false || !ftruncate($handle, 0) || !rewind($handle) || fwrite($handle, $json) !== strlen($json) || !fflush($handle)) {
    flock($handle, LOCK_UN);
    fclose($handle);
    jsonOut(['error' => 'Could not save the listing request'], 500);
}

flock($handle, LOCK_UN);
fclose($handle);
jsonOut(['ok' => true, 'message' => 'Listing request saved. It will not appear in the directory automatically.'], 201);
