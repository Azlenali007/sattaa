<?php
/**
 * Common Application Utilities
 */

function set_flash(string $type, string $message): void {
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array {
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function json_response(array $data, int $statusCode = 200): void {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function format_app_date(?string $dateStr, string $format = 'd M Y'): string {
    if (!$dateStr) return '';
    try {
        $dt = new DateTime($dateStr);
        return $dt->format($format);
    } catch (Exception $e) {
        return $dateStr;
    }
}

function format_digits(?string $val): array {
    if (empty($val)) return ['-', '-'];
    $val = trim($val);
    $parts = preg_split('/\s+/', $val);
    if (count($parts) >= 2) {
        return [$parts[0], $parts[1]];
    }
    $chars = str_split($val);
    if (count($chars) === 1) {
        return [$chars[0], ''];
    }
    return [implode('', array_slice($chars, 0, count($chars) - 1)), end($chars)];
}
