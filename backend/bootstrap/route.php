<?php
return [
    'GET' => [
        '/' => ["app\controllers\AppController", "index"],
        '/users/new' => ["app\controllers\UserController", "new"],
        '/users' => ["app\controllers\UserController", "get"],
        '/users/{id}' => ["app\controllers\UserController", "show"],
        '/users/{id}/edit' => ["app\controllers\UserController", "edit"],

        '/api/users/new' => ["app\controllers\ApiUserController", "new"],
        '/api/users' => ["app\controllers\ApiUserController", "get"],
        '/api/users/{id}' => ["app\controllers\ApiUserController", "show"],
        '/api/users/{id}/edit' => ["app\controllers\ApiUserController", "edit"],
    ],
    'POST' => [
        '/users/create' => ["app\controllers\UserController", "create"],

        '/api/users/create' => ["app\controllers\ApiUserController", "create"],
    ],
    'PUT' => [
        '/users/{id}' => ["app\controllers\UserController", "update"],

        '/api/users/{id}' => ["app\controllers\ApiUserController", "update"],
    ],
    'DELETE' => [
        '/users' => ["app\controllers\UserController", "delete"],

        '/api/users' => ["app\controllers\ApiUserController", "delete"],
    ]
];