<?php

namespace Src\Service;

use Src\Entity\SendEmailRequest;
use Src\Module\EmailSender;
use Src\Repository\Email\EmailRepository;

class EmailService
{
    public function __construct(
        private EmailRepository $emailRepository,
        private EmailSender $emailSender,
    ) {
    }

    public function sendEmail(SendEmailRequest $req): void
    {
        // send email async
        $this->emailSender->send($req);

        // save email in repository
        $this->emailRepository->save($req);
    }
}
