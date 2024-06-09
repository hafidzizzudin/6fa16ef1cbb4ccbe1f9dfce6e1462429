<?php
require 'vendor/autoload.php';

use Src\Repository\User\UserSQLRepository;
use Dotenv\Dotenv;
use Src\Controller\AuthController;
use Src\Controller\EmailController;
use Src\Module\EmailSender\EmailSenderPHPMailer;
use Src\Repository\Email\EmailSQLRepository;
use Src\Service\AuthService;
use Src\Service\EmailService;
use Src\System\AuthDBAuthenticator;
use Src\System\Authenticator;
use Src\System\DatabaseConnector;
use Src\System\Mailer;
use Src\System\OktaAuthenticator;
use Src\System\RedisQueue;

// load env var
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// init system
$dbConnection = (new DatabaseConnector())->getDbConnection();
$phpMailer = (new Mailer())->getMailer();
$queueContext = (new RedisQueue())->getContext();
$authenticator = new Authenticator();

// init repository
$emailRepository = new EmailSQLRepository($dbConnection);
$userRepository = new UserSQLRepository($dbConnection);

// init module
$emailSender = new EmailSenderPHPMailer($phpMailer, $queueContext);

// init service
$emailService = new EmailService($emailRepository, $emailSender);
$authService = new AuthService($userRepository);

// init controller
$authController = new AuthController($authService);
$emailController = new EmailController($emailService);
