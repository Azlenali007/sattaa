<?php
/**
 * External API HTTP Client
 * Uses cURL if available, otherwise streams fallback
 */

class ApiClient {
    public static function fetch(string $url, ?string $apiKey = null, int $timeout = 8): array {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return ['success' => false, 'error' => 'Invalid API Endpoint URL'];
        }

        $headers = [
            "Accept: application/json",
            "User-Agent: SatkaMatkaSyncClient/1.0"
        ];
        if ($apiKey) {
            $headers[] = "Authorization: Bearer {$apiKey}";
            $headers[] = "X-Api-Key: {$apiKey}";
        }

        // cURL execution if extension is loaded
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => true,
            ]);

            $body = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($error) {
                return ['success' => false, 'error' => "Network Error: {$error}"];
            }

            if ($httpCode >= 400) {
                return ['success' => false, 'error' => "API returned HTTP status {$httpCode}"];
            }

            $decoded = json_decode((string)$body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['success' => false, 'error' => 'Invalid JSON received from API endpoint'];
            }

            return ['success' => true, 'data' => $decoded, 'raw' => $body];
        }

        // Stream Context Fallback (Pure standard PHP)
        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", $headers),
                'timeout' => $timeout,
                'ignore_errors' => true
            ],
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => true
            ]
        ];

        $context = stream_context_create($opts);
        $body = @file_get_contents($url, false, $context);

        if ($body === false) {
            return ['success' => false, 'error' => 'Remote API server unreachable (Timeout or Connection Refused)'];
        }

        // Check response header
        if (isset($http_response_header) && is_array($http_response_header)) {
            preg_match('{HTTP\/\S*\s(\d{3})}', $http_response_header[0], $match);
            $status = (int)($match[1] ?? 200);
            if ($status >= 400) {
                return ['success' => false, 'error' => "API returned HTTP status {$status}"];
            }
        }

        $decoded = json_decode((string)$body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'error' => 'Invalid JSON received from API endpoint'];
        }

        return ['success' => true, 'data' => $decoded, 'raw' => $body];
    }
}
