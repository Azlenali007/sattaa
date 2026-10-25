<?php
/**
 * URL and Routing Helpers
 * Automatically supports root domain and subdirectory installations
 */

function app_base_url(): string {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $dir = dirname($scriptName);
    $dir = str_replace('\\', '/', $dir);

    // Strip internal subdirectories to get web root
    $dir = preg_replace('#/(admin|install|api|cron|tests)(/.*)?$#i', '', $dir);

    if ($dir === '/' || $dir === '\\' || $dir === '.') {
        return '';
    }

    return rtrim($dir, '/');
}

function url(string $path = ''): string {
    $base = app_base_url();
    $path = ltrim($path, '/');
    if ($path === '') {
        return $base === '' ? '/' : $base . '/';
    }
    return ($base === '' ? '' : $base) . '/' . $path;
}

function base_url(string $path = ''): string {
    return url($path);
}

function asset(string $path): string {
    $clean = ltrim($path, '/');
    if (str_starts_with($clean, 'assets/')) {
        return url($clean);
    }
    return url('assets/' . $clean);
}

function is_active_route(string $route): bool {
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $base = app_base_url();
    if ($base !== '' && str_starts_with($uri, $base)) {
        $uri = substr($uri, strlen($base));
    }
    $uri = trim($uri, '/');
    $route = trim($route, '/');
    if ($route === '' || $route === 'index.php') {
        return $uri === '' || $uri === 'index.php';
    }
    return str_starts_with($uri, $route);
}

function redirect(string $to): void {
    if (!str_starts_with($to, 'http://') && !str_starts_with($to, 'https://')) {
        $to = url($to);
    }
    header('Location: ' . $to);
    exit;
}
