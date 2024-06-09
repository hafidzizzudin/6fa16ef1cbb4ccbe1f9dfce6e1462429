<?php

use Src\Controller\AuthController;
use Src\Controller\EmailController;

require "../bootstrap.php";

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

error_reporting(0);

$userEmail = '';
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// validate path
validatePath($uri);

try {
    serve($uri, $authController, $emailController);
} catch (\Throwable $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['message' => "Internal error " . $e->getMessage()]);
}

function serve(array $uri, AuthController $authController, EmailController $emailController)
{
    $response = array();
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    try {
        switch ($requestMethod) {
            case 'POST':
                $response = serveByUri($uri, $authController, $emailController);
                break;
            default:
                $response = notFoundResponse();
                break;
        }
    } catch (\Throwable $e) {
        switch ($e->getCode()) {
            case 400:
                header('HTTP/1.1 400 Bad Request');
                echo json_encode(['message' => "Bad request: " . $e->getMessage()]);
                return;
            case 401:
                header('HTTP/1.1 401 Unauthorized');
                echo json_encode(['message' => "Unauthorized: " . $e->getMessage()]);
                return;
            default:
                header('HTTP/1.1 500 Internal Server Error');
                echo json_encode(['message' => "Internal error: " . $e->getMessage()]);
                return;
        }
    }

    header($response['status_code_header']);
    if (@$response['body']) {
        echo $response['body'];
    }
}

function serveByUri(array $uri): array
{
    global $authController, $emailController, $userEmail;

    switch ($uri[1]) {
        case 'login':
            return $authController->login();
        case 'register':
            return $authController->register();
        default:
            if (!authenticate())
                throw new Exception("need login first", 401);

            return $emailController->sendEmail($userEmail);
    }
}

function notFoundResponse(): array
{
    return [
        'status_code_header' => 'HTTP/1.1 404 Not Found'
    ];
}

function validatePath(array $uri)
{
    // check length path
    if (empty($uri)) {
        header("HTTP/1.1 404 Not Found");
        exit();
    }

    // check uri 1st index
    if (!in_array($uri[1], ['email', 'login', 'register'], true)) {
        header("HTTP/1.1 404 Not Found");
        exit();
    }
}

function authenticate()
{
    global $authenticator, $userEmail;

    try {
        switch (true) {
            case array_key_exists('HTTP_AUTHORIZATION', $_SERVER):
                $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
                break;
            case array_key_exists('Authorization', $_SERVER):
                $authHeader = $_SERVER['Authorization'];
                break;
            default:
                $authHeader = null;
                break;
        }

        preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
        if (!isset($matches[1])) {
            throw new \Exception('No Bearer Token');
        }

        $userEmail =  $authenticator->verify($matches[1]);

        return !empty($userEmail);
    } catch (\Exception $e) {
        return false;
    }
}
