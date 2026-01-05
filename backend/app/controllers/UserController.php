<?php
namespace app\controllers;

class UserController
{
    public function new() {
        require VIEWS_PATH . "/users/new.php";
    }

    public function create() {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $validationResult = $this->validateData($data);

        if (!$validationResult['success']) {
            $this->sendJsonResponse(false, $validationResult['message'], $validationResult['errors']);
            return;
        }

        $this->sendJsonResponse(true, 'User created successfully', null);
    }

    public function result() {
        require VIEWS_PATH . "/users/new.php";
    }

    private function validateData($data) {
        $errors = [];

        if (empty($data['email'])) {
            $errors['email'] = 'Email is required';
        } else if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format';
        }

        if (empty($data['name'])) {
            $errors['name'] = 'Name is required';
        }

        if (empty($data['country'])) {
            $errors['country'] = 'Country is required';
        }

        if (empty($data['city'])) {
            $errors['city'] = 'City is required';
        }

        if (empty($data['gender'])) {
            $errors['gender'] = 'Gender is required';
        } else if (!in_array($data['gender'], ['male', 'female'])) {
            $errors['gender'] = 'Invalid gender value';
        }

        if (empty($data['status'])) {
            $errors['status'] = 'Status is required';
        } else if (!in_array($data['status'], ['active', 'inactive'])) {
            $errors['status'] = 'Invalid status value';
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors
            ];
        }

        return ['success' => true];
    }

    private function sendJsonResponse($success, $message, $errors = null) {
        header('Content-Type: application/json');

        $response = [
            'success' => $success,
            'message' => $message
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        echo json_encode($response);
        exit;
    }
}