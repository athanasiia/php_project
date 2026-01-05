<?php
require_once __DIR__ . '/../bootstrap/autoload.php';

$app = new system\App();
$app->setRoutes(include __DIR__ . '/../bootstrap/route.php');
$app->run();

