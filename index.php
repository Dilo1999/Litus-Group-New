<?php

// cPanel document root bootstrap:
// - Local/dev: Laravel lives at ./public/index.php (normal Laravel layout)
// - cPanel (main domain locked to /public_html): keep the git checkout in a
//   subfolder like /public_html/litusgroup/ and route the domain root to
//   /public_html/litusgroup/public/index.php
$candidates = [
    __DIR__ . '/public/index.php',
    __DIR__ . '/litusgroup/public/index.php',
];

foreach ($candidates as $frontController) {
    if (is_file($frontController)) {
        require $frontController;
        return;
    }
}

http_response_code(500);
header('Content-Type: text/plain; charset=utf-8');
echo "Laravel front controller not found. Expected one of:\n- public/index.php\n- litusgroup/public/index.php\n";

