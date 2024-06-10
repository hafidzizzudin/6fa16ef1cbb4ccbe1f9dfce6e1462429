<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Entity\Email;
use Src\Module\EmailSender\EmailSenderPHPMailer;
use Src\Repository\Email\EmailSQLRepository;
use Src\Service\EmailService;
use Src\System\DatabaseConnector;
use Src\System\Mailer;
use Src\System\RedisQueue;

// load env var
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// init system
$dbConnection = (new DatabaseConnector())->getDbConnection();
$phpMailer = (new Mailer())->getMailer();
$queueContext = (new RedisQueue())->getContext();

// init repo
$emailRepository = new EmailSQLRepository($dbConnection);

// init module 
$emailSender = new EmailSenderPHPMailer($phpMailer, $queueContext);

// init service
$emailService = new EmailService($emailRepository, $emailSender);

for ($i = 0; $i < 1; $i++) {
    $emailService->sendEmail(new Email("user_id_" . ($i + 1), true, 'hafidz.izzudin' . ($i + 1) . '@gmail.com', '<br>Nice adasdasd<br>', "payslip " . ($i + 1)));
}
