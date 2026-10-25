<?php
/**
 * CSRF Protection Middleware
 */

function enforce_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf()) {
            http_response_code(403);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                json_response(['error' => 'Invalid or expired CSRF security token.'], 403);
            }
            die("Security check failed: Invalid CSRF token. Please refresh the page and try again.");
        }
    }
}
