<?php
require 'helpers.php';
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$id = $in['doctor'] ?? '';

withQueues(function (&$data) use ($id) {
    if ($id !== '') {
        $data[$id] = seedQueue();
    } else {
        foreach (getDoctors() as $d) $data[$d['id']] = seedQueue();
    }
});
jsonOut(['ok' => true]);