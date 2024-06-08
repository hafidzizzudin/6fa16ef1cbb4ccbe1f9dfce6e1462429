<?php

namespace Src\Repository\Email;

use PDO;
use Src\Entity\SendEmailRequest;

class EmailSQLRepository implements EmailRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function save(SendEmailRequest $req)
    {
        $insertStatement = "
            INSERT INTO email (user_id, is_html, email_to, body, subject) VALUES (:user_id, :is_html, :email_to, :body, :subject)
        ";

        $statement = $this->db->prepare($insertStatement);

        $statement->execute($req->toArray());
    }
}
