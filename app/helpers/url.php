<?php
/**
 * URL and Routing Helpers
 */

function url(string $path = ''): string {
    $path = ltrim($path, '/');
    return '/' . $path;
}

function asset(string $path): string {
    return url('assets/' . ltrim($path, '/'));
}

function is_active_route(string $route): bool {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $uri = trim($uri, '/');
    $route = trim($route, '/');
    if ($route === '' || $route === 'index.php') {
        return $uri === '' || $uri === 'index.php';
    }
    return str_starts_with($uri, $route);
}

function redirect(string $to): void {
    header('Location: ' . url($to));
    exit;
}
