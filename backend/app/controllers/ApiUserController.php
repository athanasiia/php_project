<?php

namespace app\controllers;

use Exception;
use JsonException;

class ApiUserController
{
    public function new() : void
    {
        require VIEWS_PATH . "/users/new.php";
    }

    /**
     * @throws JsonException
     */
    public function get() : void
    {
        $apiUrl = API_BASE_UPL;

        header('Content-Type: application/json; charset=utf-8');
        $filters = [
            'status' => $_GET['status'] ?? null,
            'gender' => $_GET['gender'] ?? null,
            'search' => $_GET['search'] ?? null,
            'sort' => $_GET['sort'] ?? 'id',
            'order' => $_GET['order'] ?? 'ASC',
            'limit' => $GET['limit'] ?? 5,
            'offset' => $_GET['offset'] ?? 0,
        ];

        $apiUrl .= '?per_page=100';
        $apiUrl = $this->applyFilters($apiUrl, $filters);

        try {
            $ch = curl_init($apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . API_TOKEN,
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                $this->sendJsonResponse(false, 'Failed to retrieve users', null, ['api_error' => $response]);
            }

            $users = json_decode($response, true);

            $sortedUsers = $this->applySorting($users, $filters['sort'], $filters['order']);
            $paginatedUsers = array_slice($sortedUsers, $filters['offset'], $filters['limit']);

            $processedUsers = array_map(function($user) {
                return [
                    'id' => (int)$user['id'],
                    'email' => (string)$user['email'],
                    'name' => (string)$user['name'],
                    'gender' => (string)$user['gender'],
                    'status' => (string)$user['status']
                ];
            }, $paginatedUsers);

            http_response_code(200);
            echo json_encode($processedUsers);
        } catch (Exception $e) {
            $this->sendJsonResponse(false, 'Error retrieving users', null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * @throws JsonException
     */
    public function show(int $id) : void
    {
        try {
            $ch = curl_init(API_BASE_UPL . '/' . $id);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . API_TOKEN,
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $user = json_decode($response, true);
                $this->sendJsonResponse(true, 'User retrieved successfully', $user);
            } else {
                $this->sendJsonResponse(false, 'User not found', null, ['api_error' => $response]);
            }
        } catch (Exception $e) {
            $this->sendJsonResponse(false, 'Error retrieving user', null, ['error' => $e->getMessage()]);
        }
    }

    public function edit() : void
    {
        require VIEWS_PATH . "/users/new.php";
    }

    /**
     * @throws JsonException
     */
    public function create() : void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $resultMessage = $this->validateInput($input);
            if ($resultMessage !== "Success") {
                $this->sendJsonResponse(false, $resultMessage, null, ['error' => 'Validation failed']);
            }

            $ch = curl_init(API_BASE_UPL);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => json_encode($input),
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . API_TOKEN,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 201) {
                $user = json_decode($response, true);
                $this->sendJsonResponse(true, 'User created successfully', $user);
            } else {
                $this->sendJsonResponse(false, 'Failed to create user', null, ['api_error' => json_decode($response, true)]);
            }
        } catch (Exception $e) {
            $this->sendJsonResponse(false, 'Error creating user', null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * @throws JsonException
     */
    public function update(int $id) : void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $resultMessage = $this->validateInput($input);
            if ($resultMessage !== "Success") {
                $this->sendJsonResponse(false, $resultMessage, null, ['error' => 'Validation failed']);
            }

            $ch = curl_init(API_BASE_UPL . '/' . $id);
            curl_setopt_array($ch, [
                CURLOPT_CUSTOMREQUEST => 'PUT',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POSTFIELDS => json_encode($input),
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . API_TOKEN,
                    'Content-Type: application/json',
                    'Accept: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $user = json_decode($response, true);
                $this->sendJsonResponse(true, 'User updated successfully', $user);
            } else {
                $this->sendJsonResponse(false, 'Failed to update user', null, ['api_error' => json_decode($response, true)]);
            }
        } catch (Exception $e) {
            $this->sendJsonResponse(false, 'Error updating user', null, ['error' => $e->getMessage()]);
        }
    }

    /**
     * @throws JsonException
     */
    public function delete() : void
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $userIds = $input['ids'] ?? [];

            if (empty($userIds)) {
                $this->sendJsonResponse(false, 'No user IDs provided', null, ['error' => 'user_ids required']);
                return;
            }

            $results = [];
            foreach ($userIds as $id) {
                $ch = curl_init(API_BASE_UPL . '/' . $id);
                curl_setopt_array($ch, [
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => [
                        'Authorization: Bearer ' . API_TOKEN,
                        'Accept: application/json'
                    ]
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                $results[$id] = [
                    'success' => $httpCode === 204,
                    'response' => $httpCode === 204 ? 'Deleted' : json_decode($response, true)
                ];
            }

            $this->sendJsonResponse(true, 'Delete operations completed', $results);
        } catch (Exception $e) {
            $this->sendJsonResponse(false, 'Error deleting users', null, ['error' => $e->getMessage()]);
        }
    }

    private function applyFilters(string $url, array $filters) : string
    {
        if ($filters['status'] && $filters['status'] !== 'all') {
            $url .= '&status=' . $filters['status'];
        }

        if ($filters['gender'] && $filters['gender'] !== 'all') {
            $url .= '&gender=' . $filters['gender'];
        }

        if ($filters['search']) {
            $url .= '&name=' . urlencode($filters['search']);
        }

        return $url;
    }

    private function applySorting(array $users, string $sort, string $order): array
    {
        if (empty($users)) {
            return $users;
        }

        usort($users, function($a, $b) use ($sort, $order) {
            $valA = $a[$sort] ?? '';
            $valB = $b[$sort] ?? '';

            if ($sort === 'id') {
                $valA = (int)$valA;
                $valB = (int)$valB;
            } else {
                $valA = (string)$valA;
                $valB = (string)$valB;
            }

            if ($valA == $valB) {
                return 0;
            }

            $comparison = ($valA < $valB) ? -1 : 1;

            return (strtoupper($order) === 'DESC') ? -$comparison : $comparison;
        });

        return $users;
    }

    private function validateInput(mixed $input) : string
    {
        $required = ['email', 'name', 'gender', 'status'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                return "Field '$field' is required";
            }
        }

        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format";
        }

        return "Success";
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