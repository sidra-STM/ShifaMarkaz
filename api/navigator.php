<?php
require 'helpers.php';
$configFile = __DIR__ . '/config.php';
$config = is_file($configFile) ? require $configFile : [];
if (!is_array($config)) jsonOut(['error' => 'Invalid navigator configuration'], 500);

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

function emergencyOut($language = 'english') {
    $messages = [
        'english' => 'Your symptoms may be an emergency. Please go to the nearest hospital or call Rescue 1122 now. Do not wait for an appointment.',
        'urdu' => 'یہ علامات ہنگامی ہو سکتی ہیں۔ براہ کرم قریبی ہسپتال جائیں یا فوراً ریسکیو 1122 پر کال کریں۔',
        'roman_urdu' => 'Yeh alamat emergency ho sakti hain. Barah-e-karam qareebi hospital jayen ya foran Rescue 1122 par call karein.'
    ];
    jsonOut([
        'type' => 'emergency',
        'message' => $messages[$language] ?? $messages['english']
    ]);
}

function resultOut($specialty, $source, $note = '', $language = 'english') {
    $docs = array_values(array_filter(getDoctors(), function ($d) use ($specialty) {
        return $d['specialty'] === $specialty;
    }));
    $messages = [
        'english' => "Based on what you described, a {$specialty} is the most suitable type of doctor to see first. This is guidance only, not a diagnosis.",
        'urdu' => "آپ کی بتائی ہوئی علامات کے مطابق، پہلے {$specialty} سے رجوع کرنا مناسب ہو سکتا ہے۔ یہ صرف رہنمائی ہے، تشخیص نہیں۔",
        'roman_urdu' => "Aap ki batayi hui alamat ke mutabiq, pehle {$specialty} se rujoo karna munasib ho sakta hai. Yeh sirf rehnumai hai, tashkhees nahi."
    ];
    $message = $messages[$language] ?? $messages['english'];
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
        . "Choose responseLanguage as exactly english, urdu, or roman_urdu to match the user's language. "
        . "Allowed specialties: " . implode(', ', $specialties) . ". "
        . "If symptoms sound life-threatening, return type emergency. "
        . ($forceResult ? "You have enough information now. You MUST return type result. " : "After at most 3 questions you must return type result. ")
        . 'Output ONLY JSON in one of these shapes: '
        . '{"type":"question","responseLanguage":"english|urdu|roman_urdu","message":"..."} or '
        . '{"type":"result","responseLanguage":"english|urdu|roman_urdu","message":"...","specialty":"one allowed specialty"} or '
        . '{"type":"emergency","responseLanguage":"english|urdu|roman_urdu","message":"..."}';

    $contents = [];
    foreach ($messages as $m) {
        $contents[] = ['role' => $m['role'] === 'assistant' ? 'model' : 'user', 'parts' => [['text' => $m['content']]]];
    }
    $body = [
        'systemInstruction' => ['parts' => [['text' => $system]]],
        'contents' => $contents,
        'generationConfig' => ['responseMimeType' => 'application/json', 'temperature' => 0.3]
    ];

    $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
    $payload = json_encode($body);
    $res = false;
    $code = 0;
    $err = '';
    for ($attempt = 0; $attempt < 2; $attempt++) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'x-goog-api-key: ' . $key],
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_SSL_VERIFYPEER => false // local XAMPP prototype only (fixes certificate error on Windows)
        ]);
        $res = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($res !== false && !in_array($code, [429, 500, 502, 503, 504], true)) break;
        if ($attempt === 0) usleep(500000);
    }

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
    if (!is_array($parsed) || !isset($parsed['type']) || !is_string($parsed['type'])) {
        return [null, 'Bad AI reply: ' . mb_substr($text, 0, 100)];
    }
    return [$parsed, ''];
}

//[$ai, $aiError] = callGemini($config, $messages, $specialties, $userTurns >= 3);
[$ai, $aiError] = callGemini($config, $messages, $specialties, $userTurns >= 3);
if (!$ai && !empty($config['gemini_fallback_model'])) {
    $config2 = $config;
    $config2['gemini_model'] = $config['gemini_fallback_model'];
    [$ai, $aiError2] = callGemini($config2, $messages, $specialties, $userTurns >= 3);
    $aiError = $ai ? '' : ($aiError . ' | fallback: ' . $aiError2);
}

if ($ai) {
    $type = $ai['type'];
    $language = in_array($ai['responseLanguage'] ?? '', ['english', 'urdu', 'roman_urdu'], true)
        ? $ai['responseLanguage']
        : 'english';
    if ($type === 'emergency') emergencyOut($language);
    if ($type === 'result' && in_array($ai['specialty'] ?? '', $specialties, true)) {
        resultOut($ai['specialty'], 'gemini', '', $language);
    }
    if ($type === 'question' && $userTurns < 3) {
        $questions = [
            'english' => ['How long have you had this problem?', 'Is it getting worse, improving, or staying the same?'],
            'urdu' => ['یہ مسئلہ کب سے ہے؟', 'کیا یہ مسئلہ بڑھ رہا ہے، بہتر ہو رہا ہے، یا ویسا ہی ہے؟'],
            'roman_urdu' => ['Yeh masla kab se hai?', 'Kya yeh masla barh raha hai, behtar ho raha hai, ya waisa hi hai?']
        ];
        $question = $questions[$language][$userTurns - 1];
        jsonOut(['type' => 'question', 'message' => $question, 'source' => 'gemini']);
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
resultOut($specialty, 'fallback', $aiError);