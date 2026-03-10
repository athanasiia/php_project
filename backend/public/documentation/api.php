<?php
require(__DIR__ . '/../../../vendor/autoload.php');
$openapi = (new \OpenApi\Generator)->generate([__DIR__ . '/../../']);
header('Content-Type: application/json');
echo $openapi->toJSON();