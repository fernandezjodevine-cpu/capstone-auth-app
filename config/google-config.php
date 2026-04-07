<?php
$config = require __DIR__ . '/google-oauth.php';

if (!defined('GOOGLE_CLIENT_ID')) {
    define('GOOGLE_CLIENT_ID', $config['client_id'] ?? '');
}

if (!defined('GOOGLE_CLIENT_SECRET')) {
    define('GOOGLE_CLIENT_SECRET', $config['client_secret'] ?? '');
}

if (!defined('GOOGLE_REDIRECT_URI')) {
    define('GOOGLE_REDIRECT_URI', $config['redirect_uri'] ?? '');
}

if (!defined('GOOGLE_OAUTH_SCOPES')) {
    define('GOOGLE_OAUTH_SCOPES', $config['scopes'] ?? ['email', 'profile']);
}
?>