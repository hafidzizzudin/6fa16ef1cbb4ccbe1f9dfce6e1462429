<?php

namespace Src\Repository\Email;

use PDO;
use Src\Entity\Email;

class EmailSQLRepository implements EmailRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function save(Email $req): int
    {
        $insertStatement = "
            INSERT INTO email (user_id, is_html, email_to, body, subject) VALUES (:user_id, :is_html, :email_to, :body, :subject)
        ";

        $statement = $this->db->prepare($insertStatement);

        $statement->execute($req->toArrayInsert());

        return (int)$this->db->lastInsertId();
    }

    public function updateResult(Email $req)
    {
        $updateResultStmt = "
            UPDATE email SET status = :status, note = :note, updated_at = NOW() WHERE id = :id
        ";

        $stmt = $this->db->prepare($updateResultStmt);

        $stmt->execute($req->toUpdateResultArray());
    }
}
