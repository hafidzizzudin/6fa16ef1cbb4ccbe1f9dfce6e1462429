<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Entity\SendEmailRequest;
use Src\Module\EmailSenderPHPMailer;
use Src\Repository\Email\EmailSQLRepository;
use Src\Service\EmailService;
use Src\System\DatabaseConnector;
use Src\System\Mailer;

// load env var
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// init system
$dbConnection = (new DatabaseConnector())->getDbConnection();
$phpMailer = (new Mailer())->getMailer();

// init repository
$emailRepository = new EmailSQLRepository($dbConnection);

// init module
$emailSender = new EmailSenderPHPMailer($phpMailer);

// init service
$emailService = new EmailService($emailRepository, $emailSender);

// test functionality
$emailService->sendEmail(new SendEmailRequest('user_id_1', true, 'hafidz.izzudin49@gmail.com', '<br>Nice<br>', 'payslip'));
