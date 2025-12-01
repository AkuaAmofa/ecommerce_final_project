<?php
// settings/core.php

// Show errors in development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Allow safe header redirects
if (!headers_sent()) {
    ob_start();
}

/* ================================
   AUTH & ROLE HELPERS
================================ */

/**
 * Is a user logged in?
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get logged-in user ID
 */
function getUserId(): ?int {
    return isLoggedIn() ? (int)$_SESSION['user_id'] : null;
}

/**
 * Get user name
 */
function getUserName(): ?string {
    return isLoggedIn() ? ($_SESSION['name'] ?? null) : null;
}

/**
 * Get user email
 */
function getUserEmail(): ?string {
    return isLoggedIn() ? ($_SESSION['email'] ?? null) : null;
}

/**
 * Unified role getter
 * 0 = customer
 * 1 = organizer
 * 2 = super admin
 */
function get_user_role(): ?int {
    return isLoggedIn() ? ($_SESSION['role'] ?? null) : null;
}

/**
 * Is organizer (role = 1)?
 */
function isOrganizer(): bool {
    return isLoggedIn() && isset($_SESSION['role']) && (int)$_SESSION['role'] === 1;
}

/**
 * Is super admin (super_admin = 1)?
 */
function isSuperAdmin(): bool {
    return isLoggedIn() && isset($_SESSION['super_admin']) && (int)$_SESSION['super_admin'] === 1;
}

/**
 * Is admin (organizer OR super admin)?
 * Used across your admin pages.
 */
function isAdmin(): bool {
    return isOrganizer() || isSuperAdmin();
}

/**
 * Human-readable role name
 */
function get_user_role_name(): string {
    if (!isLoggedIn()) return 'Guest';

    switch (get_user_role()) {
        case 1: return 'Organizer';
        case 2: return 'Super Admin';
        default: return 'Customer';
    }
}

/* ================================
   ACCESS CONTROL HELPERS
================================ */

/**
 * Require login
 */
function require_login($redirect_url = '../login/login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirect_url");
        exit();
    }
}

/**
 * Require any admin (organizer or super admin)
 */
function require_admin($redirect_url = '../index.php') {
    if (!isAdmin()) {
        header("Location: $redirect_url?error=access_denied");
        exit();
    }
}

/**
 * Require super admin only
 */
function require_super_admin($redirect_url = '../index.php') {
    if (!isSuperAdmin()) {
        header("Location: $redirect_url?error=access_denied");
        exit();
    }
}

/**
 * General access handler
 */
function can_access($required_role = 'any') {
    switch ($required_role) {
        case 'organizer':
            return isOrganizer();
        case 'super':
            return isSuperAdmin();
        case 'admin':
            return isAdmin();
        case 'customer':
            return isLoggedIn() && !isAdmin();
        case 'any':
            return isLoggedIn();
        default:
            return false;
    }
}

/* ================================
   OTHER HELPERS
================================ */

/** Client IP */
function get_client_ip(): string {
    foreach (['HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','REMOTE_ADDR'] as $key) {
        if (!empty($_SERVER[$key])) {
            return trim(explode(',', $_SERVER[$key])[0]);
        }
    }
    return '0.0.0.0';
}

/** Logging */
function log_user_activity($activity) {
    $user_info = isLoggedIn()
        ? "User ID: " . getUserId() . " (" . getUserName() . ")"
        : "Guest user";

    error_log("[Activity] $user_info - $activity");
}
