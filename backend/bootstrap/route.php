<?php
return [
    'GET' => [
        '/' => ["app\controllers\AppController", "index"],
        '/users/new' => ["app\controllers\UserController", "new"],
        '/users/result' => ["app\controllers\UserController", "result"],
    ],
    'POST' => [
        '/users/create' => ["app\controllers\UserController", "create"],
    ],
];