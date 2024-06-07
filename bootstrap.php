<?php
require 'vendor/autoload.php';

use Dotenv\Dotenv;
use Src\System\DatabaseConnector;

// load env var
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// init db connection
$dbConnection = (new DatabaseConnector())->getDbConnection();
