<?php

namespace Src\Repository\Email;

use PDO;
use Src\Entity\SendEmailRequest;

class EmailMysqlRepository implements EmailRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function saveEmail(SendEmailRequest $req)
    {
        $insertStatement = "
            INSERT INTO email (user_id, is_html, email_to, body) VALUES (:user_id, :is_html, :email_to, :body)
        ";

        $statement = $this->db->prepare($insertStatement);

        $statement->execute($req->toArray());
    }
}
