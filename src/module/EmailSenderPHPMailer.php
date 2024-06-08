<?php

namespace Src\Module;

use PHPMailer\PHPMailer\PHPMailer;
use Src\Entity\SendEmailRequest;

class EmailSenderPHPMailer implements EmailSender
{
    public function __construct(private PHPMailer $mailer)
    {
    }

    public function send(SendEmailRequest $req)
    {
        $this->mailer->isHTML($req->getIsHtml());
        $this->mailer->addAddress($req->getEmailTo());
        $this->mailer->Body = $req->getBody();
        $this->mailer->Subject = $req->getSubject();

        $this->mailer->send();
    }
}
