<?php
// Returns openFDA label warnings for a drug name, as JSON.
require_once __DIR__ . '/../includes/http.php';
header('Content-Type: application/json');

$name = trim($_GET['name'] ?? '');
if ($name === '') {
    echo json_encode(['found' => false]);
    exit;
}

$q = urlencode('"' . $name . '"');
$url = 'https://api.fda.gov/drug/label.json?search='
     . '(openfda.brand_name:' . $q . '+OR+openfda.generic_name:' . $q . ')&limit=1';

$body = http_get($url);
if ($body === null) {
    echo json_encode(['found' => false, 'error' => 'unreachable']);
    exit;
}

$data = json_decode($body, true);
if (empty($data['results'][0])) {
    echo json_encode(['found' => false]);
    exit;
}

$result = $data['results'][0];
$parts = [];
foreach (['boxed_warning', 'drug_interactions', 'warnings', 'warnings_and_cautions'] as $field) {
    if (!empty($result[$field])) {
        $parts[] = is_array($result[$field]) ? implode("\n", $result[$field]) : $result[$field];
    }
}

$text = trim(implode("\n\n", $parts));
if ($text === '') {
    echo json_encode(['found' => false]);
    exit;
}
if (function_exists('mb_substr') && mb_strlen($text) > 1800) {
    $text = mb_substr($text, 0, 1800) . '...';
}

echo json_encode(['found' => true, 'warnings' => $text]);
