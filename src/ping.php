<?php
require_once __DIR__ . '/config.php';
try {
    $pdo->query('SELECT 1');
    echo 'connected';
} catch (Exception $e) {
    http_response_code(500);
    echo 'not connected';
}