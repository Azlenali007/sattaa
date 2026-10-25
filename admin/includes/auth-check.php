<?php
/**
 * Admin Authentication Gatekeeper
 */
require_once __DIR__ . '/../../app/config/app.php';

$currentAdmin = require_admin_auth();
