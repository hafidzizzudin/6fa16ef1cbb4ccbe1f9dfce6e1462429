<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Entity\Email;
use Src\Module\EmailSender\EmailSenderPHPMailer;
use Src\Repository\Email\EmailSQLRepository;
use Src\System\DatabaseConnector;
use Src\System\Mailer;
use Src\System\RedisQueue;

// load env var
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// setup queue
$queueContext = (new RedisQueue())->getContext();

// setup system
$dbConnection = (new DatabaseConnector())->getDbConnection();
$emailRepository = new EmailSQLRepository($dbConnection);
$phpMailer = (new Mailer())->getMailer();
$emailSender = new EmailSenderPHPMailer($phpMailer, $queueContext);

// prepare consumer
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
