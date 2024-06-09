<?php

namespace Src\Module\EmailSender;

use Interop\Queue\Context;
use PHPMailer\PHPMailer\PHPMailer;
use Src\Entity\Email;

class EmailSenderPHPMailer implements EmailSender
{
    private bool $shouldQueue;

    public function __construct(private PHPMailer $mailer, private Context $queueContext)
    {
        $queue = $_ENV['QUEUE'];
        $this->shouldQueue = $queue != "sync";
    }

    public function sendEmail(Email $req)
    {
        $this->mailer->isHTML($req->getIsHtml());
        $this->mailer->addAddress($req->getEmailTo());
        $this->mailer->Body = $req->getBody();
        $this->mailer->Subject = $req->getSubject();

        $this->mailer->send();

        $this->resetConnection();
    }

    public function send(Email $req)
    {
        // if not queue, then send event synchronously and return
        if (!$this->shouldQueue) {
            return $this->sendEmail($req);
        }

        $emailQueue = $this->queueContext->createQueue('email_queue');
        $emailMessage = $this->queueContext->createMessage('', $req->toArrayQueue());
        $this->queueContext->createProducer()->send($emailQueue, $emailMessage);
    }

    public function resetConnection()
    {
        $smtp =  $this->mailer->getSMTPInstance();
        if ($smtp !== null and $smtp->connected()) {
            $smtp->reset();
        }
    }
}
