<?php
// Test file to verify Vercel PHP runtime is working
echo json_encode([
    'status' => 'success',
    'message' => 'PHP runtime is working on Vercel',
    'php_version' => phpversion(),
    'environment' => getenv('CI_ENVIRONMENT') ?: 'not set',
    'time' => date('Y-m-d H:i:s')
]);
