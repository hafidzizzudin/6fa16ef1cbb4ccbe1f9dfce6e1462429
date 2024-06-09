<?php

namespace Src\Service;

use Src\Repository\User\UserRepository;
use Exception;
use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;
use Src\Entity\User;

class AuthService
{
    private string $jwtSecret;

    public function __construct(private UserRepository $userRepository)
    {
        $this->jwtSecret = $_ENV['JWT_SECRET'];
    }

    public function register(array $data): string
    {
        // check user with existing email
        $email = $data['email'];
        $user = $this->userRepository->getUserByEmail($email);
        if ($user) {
            throw new Exception("user with email $email already exist", 400);
        }

        // insert into repo
        $user = new User();
        $user->userID = Uuid::uuid4()->toString();
        $user->email = $email;
        $user->salt = bin2hex(random_bytes(10));
        $user->password = hash_hmac('sha256', $data['password'], $user->salt);

        $this->userRepository->insert($user->toArrayInsert());

        return $user->userID;
    }

    public function login(array $data): string
    {
        // get user
        $user = $this->userRepository->getUserByEmail($data['email']);

        // authenticate
        $user->authenticate($data['password']);

        return $this->generateToken($user);
    }

    private function generateToken(User $user): string
    {
        $now   = time();

        $payload = [
            'iss' => 'https://levart.com',
            'aud' => 'https://levart.com',
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + 3600,
            'email' => $user->email,
        ];

        $jwt = JWT::encode($payload, $this->jwtSecret, 'HS256');

        return $jwt;
    }
}
