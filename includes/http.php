<?php
// Minimal outbound HTTP GET, using cURL when available and falling back to
// file_get_contents. Returns the response body, or null on failure.
function http_get($url)
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_USERAGENT      => 'DrugMonitor/1.0',
        ]);
        $body = curl_exec($ch);
        curl_close($ch);
        return $body !== false ? $body : null;
    }

    if (ini_get('allow_url_fopen')) {
        $ctx = stream_context_create(['http' => [
            'timeout' => 8,
            'header'  => "User-Agent: DrugMonitor/1.0\r\n",
        ]]);
        $body = @file_get_contents($url, false, $ctx);
        return $body !== false ? $body : null;
    }

    return null;
}
