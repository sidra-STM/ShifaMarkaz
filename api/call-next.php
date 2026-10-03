<?php
require 'helpers.php';
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$id = $in['doctor'] ?? '';
if (!findDoctor($id)) jsonOut(['error' => 'Unknown doctor'], 404);

$result = withQueues(function (&$data) use ($id) {
    if (!isset($data[$id])) $data[$id] = seedQueue();
    $q =& $data[$id];
    if ($q['current'] >= $q['lastIssued']) {
        return ['ok' => false, 'current' => $q['current'], 'message' => 'No patients are waiting.'];
    }
    $q['current']++;
    return ['ok' => true, 'current' => $q['current']];
});
jsonOut($result);