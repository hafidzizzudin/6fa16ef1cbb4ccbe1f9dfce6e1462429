<?php

namespace Src\Repository\User;

use Src\Entity\User;

interface UserRepository
{
    public function insert(array $data): int;
    public function getUserByEmail(string $email): ?User;
}
