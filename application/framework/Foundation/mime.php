<?php
/**
 * @var string $filename
 */

return match (pathinfo($filename, PATHINFO_EXTENSION)) {
    'css' => 'text/css',
    'htm', 'html' => 'text/html',
    'txt' => 'text/plain',
    'xml' => 'text/xml',
    'gif' => 'image/gif',
    'jpg', 'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'svg' => 'image/svg+xml',
    'js' => 'application/javascript',
    'json' => 'application/json',
    'pdf' => 'application/pdf',
    'ttf', 'otf' => 'font/ttf',
    'woff' => 'font/woff',
    'woff2' => 'font/woff2',
    default => 'application/octet-stream'
};