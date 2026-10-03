<?php
header('Content-Type: application/json');
define('DATA_DIR', __DIR__ . '/../data/');

function jsonOut($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

function getDoctors() {
    return json_decode(file_get_contents(DATA_DIR . 'doctors.json'), true) ?: [];
}

function findDoctor($id) {
    foreach (getDoctors() as $d) {
        if ($d['id'] === $id) return $d;
    }
    return null;
}

// A new queue starts with demo patients so the screens look alive
function seedQueue() {
    $names = ['Demo Patient A', 'Demo Patient B', 'Demo Patient C', 'Demo Patient D'];
    $tickets = [];
    for ($i = 0; $i < 4; $i++) {
        $tickets[] = ['token' => 15 + $i, 'name' => $names[$i], 'phone' => '03XX-XXXXXXX', 'time' => date('h:i A')];
    }
    return ['current' => 14, 'lastIssued' => 18, 'tickets' => $tickets];
}

// Safe READ (waits if someone is writing)
function readQueue($id) {
    $fp = fopen(DATA_DIR . 'queues.json', 'r');
    flock($fp, LOCK_SH);
    $data = json_decode(stream_get_contents($fp), true);
    flock($fp, LOCK_UN);
    fclose($fp);
    if (!is_array($data) || !isset($data[$id])) return seedQueue();
    return $data[$id];
}

// Safe WRITE: locks the file, lets you change $data, saves it
function withQueues($fn) {
    $fp = fopen(DATA_DIR . 'queues.json', 'c+');
    if (!$fp) jsonOut(['error' => 'Cannot open queues.json'], 500);
    flock($fp, LOCK_EX);
    $data = json_decode(stream_get_contents($fp), true);
    if (!is_array($data)) $data = [];
    $result = $fn($data);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data, JSON_PRETTY_PRINT));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return $result;
}

// Works out the patient's position
function statusFor($q, $token) {
    $current = $q['current'];
    $ahead = max(0, $token - $current - 1);
    if ($token == $current) $status = 'serving';
    elseif ($token < $current) $status = 'passed';
    elseif ($ahead == 0) $status = 'next';
    else $status = 'waiting';
    return ['current' => $current, 'yourToken' => $token, 'ahead' => $ahead, 'estimatedMinutes' => $ahead * 10, 'status' => $status];
}