<?php

namespace Src\Repository\User;

use PDO;
use Src\Entity\User;

class UserSQLRepository  implements UserRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function insert(array $data): int
    {
        $insertStatement  = "
            INSERT INTO users (user_id, email, password, salt) VALUES (:user_id, :email, :password, :salt)
        ";

        $statement = $this->db->prepare($insertStatement);

        $statement->execute($data);

        return (int)$this->db->lastInsertId();
    }

    public function getUserByEmail(string $email): ?User
    {
        $statement = "
            SELECT 
                id, user_id, email, password, salt
            FROM
                users
            WHERE 
                email = ?
            LIMIT
                1
        ";

        $statement = $this->db->prepare($statement);
        $statement->execute([$email]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$result) return null;

        return User::fromArray($result);
    }
}
