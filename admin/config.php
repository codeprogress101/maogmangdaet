<?php
// Global configuration values for the admin panel.
declare(strict_types=1);

// Update these credentials to match your database server.
const DB_DSN = 'mysql:host=127.0.0.1;dbname=maogmangdaet;charset=utf8mb4';
const DB_USER = 'root';
const DB_PASSWORD = '';

// Session and security configuration constants.
const SESSION_NAME = 'maog_admin_session';
const SESSION_IDLE_TIMEOUT = 20 * 60;        // 20 minutes in seconds.
const SESSION_ABSOLUTE_TIMEOUT = 8 * 60 * 60; // 8 hours in seconds.
const FAILED_LOGIN_LIMIT = 5;                // Lock the account after 5 failed attempts.
const ACCOUNT_LOCK_DURATION = 15 * 60;       // 15 minutes lock duration.

// File upload configuration.
const UPLOAD_DIR = __DIR__ . '/uploads';
const MAX_PDF_SIZE = 10 * 1024 * 1024; // 10 MB.