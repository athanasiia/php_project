<?php
return [
    'GET' => [
        '/' => ["app\controllers\AppController", "index"],
        '/users/new' => ["app\controllers\UserController", "new"],
        '/users' => ["app\controllers\UserController", "index"],
        '/api/users/{id}' => ["app\controllers\UserController", "show"],
        '/users/{id}/edit' => ["app\controllers\UserController", "edit"],
        '/api/users' => ["app\controllers\UserController", "apiGetAllUsers"],
    ],
    'POST' => [
        '/api/users/create' => ["app\controllers\UserController", "create"],
    ],
    'PUT' => [
        '/api/users/{id}' => ["app\controllers\UserController", "update"],
    ],
    'DELETE' => [
        '/api/users/{id}' => ["app\controllers\UserController", "delete"],
    ]
];