<?php
// Best-effort barcode -> drug lookup via openFDA. Returns JSON.
// Coverage is US-centric, so a miss is expected for many products; the
// front end then falls back to manual entry.
require_once __DIR__ . '/../includes/http.php';
header('Content-Type: application/json');

$code = preg_replace('/\D/', '', $_GET['barcode'] ?? '');
if (strlen($code) < 8) {
    echo json_encode(['found' => false]);
    exit;
}

// Try the drug label endpoint first (some labels carry a UPC), then the NDC endpoint.
$attempts = [
    'https://api.fda.gov/drug/label.json?search=openfda.upc:"' . $code . '"&limit=1',
    'https://api.fda.gov/drug/ndc.json?search=packaging.package_ndc:"' . $code . '"&limit=1',
    'https://api.fda.gov/drug/ndc.json?search=product_ndc:"' . $code . '"&limit=1',
];

foreach ($attempts as $url) {
    $body = http_get($url);
    if ($body === null) {
        continue;
    }
    $data = json_decode($body, true);
    if (empty($data['results'][0])) {
        continue;
    }

    $r = $data['results'][0];
    $of = $r['openfda'] ?? $r; // ndc endpoint has fields at top level

    $name = '';
    foreach (['brand_name', 'generic_name'] as $key) {
        if (!empty($of[$key])) {
            $name = is_array($of[$key]) ? $of[$key][0] : $of[$key];
            break;
        }
    }

    $strength = '';
    if (!empty($r['active_ingredients'][0]['strength'])) {
        $strength = $r['active_ingredients'][0]['strength'];
    }

    if ($name !== '') {
        echo json_encode(['found' => true, 'name' => $name, 'strength' => $strength]);
        exit;
    }
}

echo json_encode(['found' => false]);
