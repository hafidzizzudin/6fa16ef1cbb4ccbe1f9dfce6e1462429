<?php

namespace Src\Controller;

use Exception;
use Src\Entity\Email;
use Src\Service\EmailService;

class EmailController
{
    public function __construct(private EmailService $emailService)
    {
    }

    public function sendEmail(string $userEmail): array
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $data['user_id'] = $userEmail;

        $this->validateSendEmail($data);

        $req = Email::fromRequest($data);

        $id = $this->emailService->sendEmail($req);

        if (!$id) {
            throw new Exception("Error Processing Request", 500);
        }

        return [
            'status_code_header' => 'HTTP/1.1 200 OK',
            'body' => json_encode(['id' => $id], JSON_PRETTY_PRINT),
        ];
    }

    private function validateSendEmail(array $req)
    {
        // check empty value
        foreach ($req as $key => $val) {
            if (empty($val)) {
                throw new Exception("empty value for $key");
            }
        }
    }
}
