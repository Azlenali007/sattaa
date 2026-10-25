<?php
/**
 * Built-in PHP Development Server Router
 * Handles static assets and rewrites for clean routing
 */

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $uri;

// Serve existing static file directly
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if ($ext === 'php') {
        require $file;
        return true;
    }
    return false; // Let built-in server serve static assets (css, js, svg, images)
}

// Handle directory index
if (is_dir($file)) {
    $indexPhp = rtrim($file, '/') . '/index.php';
    if (file_exists($indexPhp)) {
        require $indexPhp;
        return true;
    }
}

// Check if adding .php matches a file
if (file_exists(__DIR__ . $uri . '.php')) {
    require __DIR__ . $uri . '.php';
    return true;
}

// Check install routing
if ($uri === '/install' || $uri === '/install/') {
    require __DIR__ . '/install/index.php';
    return true;
}
if (str_starts_with($uri, '/install/')) {
    $installFile = __DIR__ . $uri;
    if (file_exists($installFile) && !is_dir($installFile)) {
        $ext = pathinfo($installFile, PATHINFO_EXTENSION);
        if ($ext === 'php') {
            require $installFile;
            return true;
        }
        return false;
    }
    if (file_exists($installFile . '.php')) {
        require $installFile . '.php';
        return true;
    }
}

// Default fallback to index.php
if ($uri === '/' || $uri === '/index.php') {
    require __DIR__ . '/index.php';
    return true;
}

// Check admin routing
if (str_starts_with($uri, '/admin')) {
    $adminFile = __DIR__ . $uri;
    if (file_exists($adminFile . '.php')) {
        require $adminFile . '.php';
        return true;
    }
    if (file_exists(__DIR__ . '/admin/index.php')) {
        require __DIR__ . '/admin/index.php';
        return true;
    }
}

// Otherwise 404
if (file_exists(__DIR__ . '/404.php')) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    return true;
}

http_response_code(404);
echo "404 Not Found";
return true;
