<?php

namespace Src\Service;

use Exception;
use Src\Entity\Email;
use Src\Module\EmailSender\EmailSender;
use Src\Repository\Email\EmailRepository;

class EmailService
{
    public function __construct(
        private EmailRepository $emailRepository,
        private EmailSender $emailSender,
    ) {
    }

    public function sendEmail(Email $req): int
    {
        // save email in repository
        $id = $this->emailRepository->save($req);

        if (!$id) {
            throw new Exception("Failed to insert email");
        }

        // send email async
        $req->setID($id);
        $this->emailSender->send($req);

        return $id;
    }
}
