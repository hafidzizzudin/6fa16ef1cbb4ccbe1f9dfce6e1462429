<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Src\Entity\SendEmailRequest;
use Src\Repository\Email\EmailMysqlRepository;
use Src\Service\EmailService;
use Src\System\DatabaseConnector;

// load env var
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// init db connection
$dbConnection = (new DatabaseConnector())->getDbConnection();

// init repository
$emailRepository = new EmailMysqlRepository($dbConnection);

// init service
$emailService = new EmailService($emailRepository);

$emailService->sendEmail(new SendEmailRequest('user_id_1', false, 'hafidz.izzudin49@gmail.com', '<br>Nice<br>'));
$emailService->sendEmail(new SendEmailRequest('user_id_1', true, 'hafidz.izzudin49@gmail.com', '<br>Nice<br>'));
