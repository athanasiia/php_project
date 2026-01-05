<?php
require_once __DIR__ . '/base-paths.php';
require_once __DIR__ . '/base-config.php';

spl_autoload_register(function ($className) {
    $file = BASE_PATH . '/' . str_replace('\\', '/', $className) . '.php';
    if (file_exists($file)) require_once $file;
});