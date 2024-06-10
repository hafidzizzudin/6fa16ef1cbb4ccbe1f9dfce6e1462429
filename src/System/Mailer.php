<?php

namespace Src\System;

use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $host = $_ENV['MAIL_HOST'];
        $port = $_ENV['MAIL_PORT'];
        $username = $_ENV['MAIL_USERNAME'];
        $password = $_ENV['MAIL_PASSWORD'];
        $fromAddress = $_ENV['MAIL_FROM_ADDRESS'];
        $fromName = $_ENV['MAIL_FROM_NAME'];

        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host = $host;
        $this->mailer->SMTPAuth = true;
        $this->mailer->Port = $port;
        $this->mailer->Username = $username;
        $this->mailer->Password = $password;
        $this->mailer->setFrom($fromAddress, $fromName);
    }

    public function getMailer(): PHPMailer
    {
        return $this->mailer;
    }
}
