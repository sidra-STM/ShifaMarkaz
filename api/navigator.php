<?php
require 'helpers.php';
$config = require __DIR__ . '/config.php';

$in = json_decode(file_get_contents('php://input'), true) ?: [];
$clean = [];
foreach (($in['messages'] ?? []) as $m) {
    if (isset($m['role'], $m['content']) && in_array($m['role'], ['user', 'assistant'], true)) {
        $clean[] = ['role' => $m['role'], 'content' => mb_substr(trim($m['content']), 0, 500)];
    }
}
$messages = array_slice($clean, -12);
if (count($messages) === 0 || $messages[0]['role'] !== 'user') jsonOut(['error' => 'No message'], 400);

$userText = '';
$userTurns = 0;
foreach ($messages as $m) {
    if ($m['role'] === 'user') { $userText .= ' ' . mb_strtolower($m['content']); $userTurns++; }
}

$specialties = ['General Physician', 'Pediatrician', 'Cardiologist', 'Gynecologist', 'Dermatologist', 'Orthopedic', 'ENT'];

function emergencyOut($message = null) {
    jsonOut([
        'type' => 'emergency',
        'message' => $message ?: 'Your symptoms may be an emergency. Please go to the nearest hospital or call Rescue 1122 now. Do not wait for an appointment.'
    ]);
}

function resultOut($specialty, $message, $source, $note = '') {
    $docs = array_values(array_filter(getDoctors(), function ($d) use ($specialty) {
        return $d['specialty'] === $specialty;
    }));
    jsonOut(['type' => 'result', 'message' => $message, 'specialty' => $specialty, 'doctors' => $docs, 'source' => $source, 'note' => $note]);
}

// 1) RED FLAGS: our own code checks first, before any AI
$redFlags = ['chest pain', 'cant breathe', "can't breathe", 'cannot breathe', 'hard to breathe', 'difficulty breathing',
    'trouble breathing', 'shortness of breath', 'short of breath', 'unconscious', 'fainted', 'seizure',
    'heavy bleeding', 'severe bleeding', 'vomiting blood', 'coughing blood', 'stroke', 'face drooping',
    'poison', 'snake bite', 'suicide', 'want to die', 'seene mein dard', 'seene me dard', 'saans nahi',
    'behosh', 'سینے میں درد', 'سانس'];
foreach ($redFlags as $flag) {
    if (mb_strpos($userText, $flag) !== false) emergencyOut();
}

// 2) Gemini
function callGemini($config, $messages, $specialties, $forceResult) {
    $key = $config['gemini_key'] ?? '';
    $model = $config['gemini_model'] ?? '';
    if ($key === '' || strpos($key, 'PASTE') === 0 || $model === '' || strpos($model, 'PASTE') === 0) {
        return [null, 'Gemini not configured'];
    }

    $system = "You are the health navigation assistant of ShifaMarkaz in Chitral, Pakistan. "
        . "Your ONLY job is to help the person decide which TYPE of doctor to see. "
        . "Rules: Never diagnose. Never name a disease as a conclusion. Never suggest medicines or doses. "
        . "Ask short follow-up questions ONE at a time (duration, severity, age of patient, other symptoms). "
        . "Reply in the same language the user writes (English, Urdu or Roman Urdu), using simple words. "
        . "Allowed specialties: " . implode(', ', $specialties) . ". "
        . "If symptoms sound life-threatening, return type emergency. "
        . ($forceResult ? "You have enough information now. You MUST return type result. " : "After at most 3 questions you must return type result. ")
        . 'Output ONLY JSON in one of these shapes: '
        . '{"type":"question","message":"..."} or '
        . '{"type":"result","message":"1-2 simple sentences saying which type of doctor fits and why, without naming a disease","specialty":"one allowed specialty"} or '
        . '{"type":"emergency","message":"..."}';

    $contents = [];
    foreach ($messages as $m) {
        $contents[] = ['role' => $m['role'] === 'assistant' ? 'model' : 'user', 'parts' => [['text' => $m['content']]]];
    }
    $body = [
        'systemInstruction' => ['parts' => [['text' => $system]]],
        'contents' => $contents,
        'generationConfig' => ['responseMimeType' => 'application/json', 'temperature' => 0.3]
    ];

    $ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: ' . $key],
        CURLOPT_POSTFIELDS => json_encode($body),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => false // local XAMPP prototype only (fixes certificate error on Windows)
    ]);
    $res = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($res === false) return [null, 'Network error: ' . $err];
    if ($code !== 200) return [null, 'Gemini HTTP ' . $code];

    $data = json_decode($res, true);

    // Read ALL text parts (this model may add "thinking" parts), skip thought parts
    $text = '';
    foreach (($data['candidates'][0]['content']['parts'] ?? []) as $part) {
        if (isset($part['text']) && empty($part['thought'])) $text .= $part['text'];
    }

    // Keep only the JSON part, in case the model adds extra words or code fences
    $start = strpos($text, '{');
    $end = strrpos($text, '}');
    if ($start === false || $end === false || $end < $start) return [null, 'Bad AI reply: ' . mb_substr($text, 0, 100)];
    $text = substr($text, $start, $end - $start + 1);

    $parsed = json_decode($text, true);
    if (!is_array($parsed) || !isset($parsed['type'])) return [null, 'Bad AI reply: ' . mb_substr($text, 0, 100)];
    return [$parsed, ''];
}

[$ai, $aiError] = callGemini($config, $messages, $specialties, $userTurns >= 3);

if ($ai) {
    $type = $ai['type'];
    $text = trim($ai['message'] ?? '');
    if ($type === 'emergency') emergencyOut($text ?: null);
    if ($type === 'result') {
        $spec = in_array($ai['specialty'] ?? '', $specialties, true) ? $ai['specialty'] : 'General Physician';
        resultOut($spec, $text ?: "A {$spec} is the best type of doctor to see first.", 'gemini');
    }
    if ($type === 'question' && $text !== '' && $userTurns < 3) {
        jsonOut(['type' => 'question', 'message' => $text, 'source' => 'gemini']);
    }
}

// 3) BACKUP RULES (used if Gemini fails or is not set up)
$map = [
    'Pediatrician' => ['child', 'baby', 'infant', 'toddler', 'kid', 'bacha', 'bachay', 'bachi'],
    'Cardiologist' => ['heart', 'palpitation', 'blood pressure'],
    'Gynecologist' => ['pregnan', 'period', 'menstrual', 'gynec', 'hamal'],
    'Dermatologist' => ['skin', 'rash', 'itch', 'acne', 'hair fall', 'khujli'],
    'Orthopedic' => ['bone', 'joint', 'back pain', 'knee', 'fracture', 'shoulder', 'neck pain', 'kamar'],
    'ENT' => ['ear', 'nose', 'throat', 'sinus', 'tonsil', 'hearing', 'kaan', 'gala']
];
$specialty = 'General Physician';
foreach ($map as $spec => $words) {
    foreach ($words as $w) {
        if (mb_strpos($userText, $w) !== false) { $specialty = $spec; break 2; }
    }
}

if ($userTurns === 1) {
    jsonOut(['type' => 'question', 'source' => 'fallback', 'note' => $aiError,
        'message' => 'How long have you had this problem, and is it getting worse? Please also tell me the age of the patient.']);
}
resultOut($specialty, "Based on what you described, a {$specialty} is the most suitable type of doctor to see first. This is guidance only, not a diagnosis.", 'fallback', $aiError);