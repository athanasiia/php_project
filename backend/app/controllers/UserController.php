<?php
namespace app\controllers;

use database\DatabaseConnection;
use Exception;
use JsonException;

class UserController
{
    private DatabaseConnection $db;

    public function __construct() {
        $this->db = DatabaseConnection::getInstance();
    }

    public function new() : void
    {
        require VIEWS_PATH . "/users/new.php";
    }

    /**
     * @throws JsonException
     */
    public function create() : void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $required = ['email', 'name', 'country', 'city', 'gender'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->sendJsonResponse(false, "Field '$field' is required", null, ['error' => 'Validation failed']);
                return;
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->sendJsonResponse(false, "Invalid email format", null, ['error' => 'Validation failed']);
            return;
        }

        try {
            $userData = [
                'email' => $data['email'],
                'name' => $data['name'],
                'country' => $data['country'],
                'city' => $data['city'],
                'gender' => $data['gender'],
                'status' => $data['status'] ?? 'active'
            ];

            $userId = $this->db->createUser($userData);

            $this->sendJsonResponse(true, 'User created successfully', [
                'id' => $userId,
                'user' => $userData
            ]);

        } catch (Exception $e) {
            $this->sendJsonResponse(false, "Error creating user", null, ['error' => $e->getMessage()]);
        }
    }

    public function index() : void
    {
        require VIEWS_PATH . "/users/new.php";
    }

    /**
     * @throws JsonException
     */
    public function show(int $id) : void
    {
        try {
            $user = $this->db->getUser($id);
            $this->sendJsonResponse(true, 'User found', $user);
        } catch (Exception $e) {
            $this->sendJsonResponse(false, 'User not found', null, ['error' => $e->getMessage()]);
        }
    }

    public function edit() : void
    {
        require VIEWS_PATH . "/users/new.php";
    }

    /**
     * @throws JsonException
     */
    public function update(int $id) : void
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $required = ['email', 'name', 'country', 'city', 'gender'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $this->sendJsonResponse(false, "Field '$field' is required", null, ['error' => 'Validation failed']);
                return;
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->sendJsonResponse(false, "Invalid email format", null, ['error' => 'Validation failed']);
            return;
        }

        try {
            $this->db->updateUser($id, $data);
            $this->sendJsonResponse(true, 'User updated successfully', ['id' => $id]);
        } catch (Exception $e) {
            $this->sendJsonResponse(false, 'Update failed', null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * @throws JsonException
     */
    public function delete() : void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $ids = $data['ids'] ?? [];

        try {
            if (empty($ids)) {
                $this->sendJsonResponse(false, 'No user IDs provided', null, ['ids' => 'Empty ID array']);
                return;
            }

            $invalidIds = [];
            foreach ($ids as $id) {
                if (!is_numeric($id) || $id < 0) {
                    $invalidIds[] = $id;
                }
            }

            if (!empty($invalidIds)) {
                $this->sendJsonResponse(false, 'Invalid user IDs provided', null, ['ids' => $invalidIds]);
                return;
            }

            $deletedCount = $this->db->deleteUsers($ids);

            if ($deletedCount > 0) {
                $this->sendJsonResponse(true,
                    "Successfully deleted $deletedCount user(s)",
                    ['deleted_ids' => $ids]
                );
            } else {
                $this->sendJsonResponse(false, 'No users were deleted', null, ['error' => 'Delete operation failed']);
            }

        } catch (Exception $e) {
            $this->sendJsonResponse(false, 'Server error', null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * @throws JsonException
     */
    public function apiGetAllUsers() : void
    {
        header('Content-Type: application/json; charset=utf-8');

        $filters = [
            'status' => $_GET['status'] ?? null,
            'gender' => $_GET['gender'] ?? null,
            'search' => $_GET['search'] ?? null,
            'sort' => $_GET['sort'] ?? 'id',
            'order' => $_GET['order'] ?? 'ASC',
            'limit' => $GET['limit'] ?? null,
            'offset' => $_GET['offset'] ?? null,
        ];

        try {
            $users = $this->db->getAllUsers($filters);

            $processedUsers = array_map(function($user) {
                return [
                    'id' => (int)$user['id'],
                    'email' => (string)$user['email'],
                    'name' => (string)$user['name'],
                    'city' => (string)$user['city'],
                    'country' => (string)$user['country'],
                    'gender' => (string)$user['gender'],
                    'status' => (string)$user['status']
                ];
            }, $users);

            echo json_encode($processedUsers);

        } catch (Exception $e) {
            $this->sendJsonResponse(false, 'Could not get users', null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * @throws JsonException
     */
    private function sendJsonResponse(bool $success, string $message, ?array $data = null, ?array $errors = null) : void
    {
        header('Content-Type: application/json');

        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        $statusCode = $success ? 200 : 400;
        http_response_code($statusCode);

        echo json_encode($response, JSON_THROW_ON_ERROR);
    }
}