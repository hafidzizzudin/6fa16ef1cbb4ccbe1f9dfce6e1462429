<?php


namespace Src\Entity;

use Exception;

class User
{
    public int $id;
    public string $userID;
    public string $email;
    public string $password;
    public string $salt;

    public function toArrayInsert(): array
    {
        return [
            'user_id' => $this->userID,
            'email' => $this->email,
            'password' => $this->password,
            'salt' => $this->salt,
        ];
    }

    public static function fromArray(array $data): User
    {
        $user = new User();
        $user->id = $data['id'];
        $user->userID = $data['user_id'];
        $user->email = $data['email'];
        $user->password = $data['password'];
        $user->salt = $data['salt'];

        return $user;
    }

    public function authenticate(string $password)
    {
        $password = hash_hmac('sha256', $password, $this->salt);

        if ($password != $this->password) {
            throw new Exception('invalid email or password');
        }
    }
}
