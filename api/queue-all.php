<?php
require 'helpers.php';
$out = [];
foreach (getDoctors() as $d) {
    $q = readQueue($d['id']);
    $out[$d['id']] = ['current' => $q['current'], 'waiting' => $q['lastIssued'] - $q['current']];
}
jsonOut($out);