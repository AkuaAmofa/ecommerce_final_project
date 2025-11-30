<?php
// settings/core.php

// Show errors in dev
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Allow safe header() redirects after output
if (!headers_sent()) {
    ob_start();
}

/* -----------------------------
   Auth helpers
------------------------------*/

/** Is any user logged in? */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/** Is the logged-in user an admin? (role 1) */
function isAdmin(): bool {
    return isLoggedIn() && isset($_SESSION['role']) && (int)$_SESSION['role'] === 1;
}

/** Is the logged-in user a super admin? */
function isSuperAdmin(): bool {
    return isLoggedIn() && isset($_SESSION['super_admin']) && (int)$_SESSION['super_admin'] === 1;
}

/** Get logged-in user id or null */
function getUserId(): ?int {
    return isLoggedIn() ? (int)$_SESSION['user_id'] : null;
}

/** Get logged-in user's name or null */
function getUserName(): ?string {
    return isLoggedIn() ? (string)($_SESSION['name'] ?? null) : null;
}

/** Get logged-in user's email or null */
function getUserEmail(): ?string {
    return isLoggedIn() ? (string)($_SESSION['email'] ?? null) : null;
}

/** Get user role */
function get_user_role(): ?int {
    if (isLoggedIn()) {
        return $_SESSION['role'] ?? null;
    }
    return null;
}

/** Get user role name as string */
function get_user_role_name(): string {
    if (!isLoggedIn()) {
        return 'Guest';
    }
    return isAdmin() ? 'Admin' : 'Customer';
}

/* Compatibility aliases (older code) */
function is_logged_in() { return isLoggedIn(); }
function is_admin()     { return isAdmin(); }
function get_user_id()  { return getUserId(); }
function get_user_name() { return getUserName(); }
function get_user_email() { return getUserEmail(); }

/* -----------------------------
   Access Control Functions
------------------------------*/

/**
 * Require user to be logged in - redirect if not
 * @param string $redirect_url - URL to redirect to if not logged in (default: login page)
 */
function require_login($redirect_url = 'login/login.php') {
    if (!is_logged_in()) {
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Require admin privileges - redirect if not admin
 * @param string $redirect_url - URL to redirect to if not admin (default: index page)
 */
function require_admin($redirect_url = 'index.php') {
    if (!is_admin()) {
        // Log unauthorized access attempt
        error_log("Unauthorized admin access attempt by user ID: " . (get_user_id() ?? 'guest'));
        header("Location: $redirect_url?error=access_denied");
        exit();
    }
}

/**
 * Check if current user can access a specific resource
 * @param string $required_role - 'admin' or 'customer' or 'any'
 * @return bool - Returns true if user can access, false otherwise
 */
function can_access($required_role = 'any') {
    switch ($required_role) {
        case 'admin':
            return is_admin();
        case 'customer':
            return is_logged_in() && !is_admin();
        case 'any':
            return is_logged_in();
        default:
            return false;
    }
}

/* -----------------------------
   NEW: helpers used by cart
------------------------------*/

/** Same as getUserId() but with a modern, explicit name for clarity */
function current_user_id(): ?int {
    return getUserId();
}

/** Basic client IP detection (sufficient for the lab) */
function get_client_ip(): string {
    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
        if (!empty($_SERVER[$key])) {
            // X-Forwarded-For can contain comma-separated list; we want the first hop
            $ip = explode(',', $_SERVER[$key])[0];
            return trim($ip);
        }
    }
    return '0.0.0.0';
}

/**
 * Log user activity (optional function for tracking)
 * @param string $activity - Description of the activity
 */
function log_user_activity($activity) {
    $user_info = is_logged_in()
        ? "User ID: " . get_user_id() . " (" . get_user_name() . ")"
        : "Guest user";

    error_log("User Activity - $user_info - $activity");
}
