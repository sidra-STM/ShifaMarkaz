<?php
header('Content-Type: application/json');

$file = __DIR__ . '/../data/doctors.json';
if (!file_exists($file)) {
    http_response_code(500);
    echo json_encode(["error" => "doctors.json not found"]);
    exit;
}

$doctors = json_decode(file_get_contents($file), true);

// Optional filter: api/doctors.php?specialty=Cardiologist
if (!empty($_GET['specialty'])) {
    $wanted = strtolower($_GET['specialty']);
    $doctors = array_values(array_filter($doctors, function ($d) use ($wanted) {
        return strtolower($d['specialty']) === $wanted;
    }));
}

echo json_encode($doctors);