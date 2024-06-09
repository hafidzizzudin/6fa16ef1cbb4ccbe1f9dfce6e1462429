<?php

use Src\Entity\Email;

require 'bootstrap.php';

$mailQueue = $queueContext->createQueue('email_queue');
$consumer = $queueContext->createConsumer($mailQueue);

while (true) {
    $message = $consumer->receive();

    // process mail message
    $prop = $message->getProperties();
    $email = Email::fromArray($prop);

    try {
        $emailSender->sendEmail($email);
        $email->setStatus(Email::STATUS_SUCCESS);
        echo "sending email id " . $email->getID() . " success\n";
    } catch (\Throwable $e) {
        $email->setNote($e->getMessage());
        $email->setStatus(Email::STATUS_FAILED);
        echo "sending email id " . $email->getID() . " failed\n";
    } finally {
        $emailRepository->updateResult($email);
    }

    $consumer->acknowledge($message);
}
