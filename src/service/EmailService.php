<?php

namespace Src\Service;

use Src\Entity\SendEmailRequest;
use Src\Repository\Email\EmailRepository;

class EmailService
{
    public function __construct(private EmailRepository $emailRepository)
    {
    }

    public function sendEmail(SendEmailRequest $req): void
    {
        // send email async

        // save email in repository
        $this->emailRepository->saveEmail($req);
    }
}
