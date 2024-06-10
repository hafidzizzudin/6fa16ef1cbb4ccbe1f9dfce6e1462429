<?php

namespace Src\Controller;

use Exception;
use Src\Service\AuthService;

class AuthController
{
    public function __construct(private AuthService $authService)
    {
    }

    public function register(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!@$data['email'] || !@$data['password']) {
            throw new Exception("email and password are required", 400);
        }

        $userId = $this->authService->register($data);

        return [
            'status_code_header' => 'HTTP/1.1 200 OK',
            'body' => json_encode(['user_id' => $userId], JSON_PRETTY_PRINT),
        ];
    }

    public function login(): array
    {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!@$data['email'] || !@$data['password']) {
            throw new Exception("email and password are required", 400);
        }

        $token = $this->authService->login($data);

        return [
            'status_code_header' => 'HTTP/1.1 200 OK',
            'body' => json_encode(['token' => $token], JSON_PRETTY_PRINT),
        ];
    }
}
