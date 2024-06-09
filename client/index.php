<?php

require '../vendor/autoload.php';

use Dotenv\Dotenv;

session_start();

// load env var
$dotenv = Dotenv::createImmutable('../');
$dotenv->load();

$client_id     = $_ENV['OKTA_CLIENTID'];
$client_secret = $_ENV['OKTA_SECRET'];
$scope        = $_ENV['OKTA_SCOPE'];
$issuer       = $_ENV['OKTA_ISSUER'];

$token = obtainToken($issuer, $client_id, $client_secret, $scope);
echo '<br><br>' . $token;
die();

function obtainToken($issuer, $clientId, $clientSecret, $scope)
{
    echo "Obtaining token...\n";

    // prepare the request
    $uri = $issuer . '/v1/token';
    $token = base64_encode("$clientId:$clientSecret");
    $payload = http_build_query([
        'grant_type' => 'client_credentials',
        'scope'      => $scope
    ]);

    // build the curl request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        "Authorization: Basic $token"
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // process and return the response
    $response = curl_exec($ch);

    $response = json_decode($response, true);
    if (
        !isset($response['access_token'])
        || !isset($response['token_type'])
    ) {
        exit('failed, exiting.');
    }

    // here's your token to use in API requests
    return $response['token_type'] . " " . $response['access_token'];
}
