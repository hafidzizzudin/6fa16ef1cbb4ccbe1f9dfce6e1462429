<?php

require '../vendor/autoload.php';

use Dotenv\Dotenv;

session_start();

// load env var
$dotenv = Dotenv::createImmutable('../');
$dotenv->load();

$client_id      = $_ENV['OKTA_CLIENTID'];
$client_secret  = $_ENV['OKTA_SECRET'];
$scope          = $_ENV['OKTA_SCOPE'];
$issuer         = $_ENV['OKTA_ISSUER'];
$redirect_uri   = $_ENV['OKTA_REDIRECT_URI'];
$oktaOrg        = $_ENV['OKTA_ORG'];

$metadata_url = "https://$oktaOrg/oauth2/default/.well-known/oauth-authorization-server";
// Fetch the authorization server metadata which contains a few URLs
// that we need later, such as the authorization and token endpoints
$metadata = http($metadata_url);

if (isset($_POST['logout'])) {
    http($metadata->end_session_endpoint);
    session_destroy();
    header('Location: ' . $redirect_uri);
    die();
}

// a code will be present if the user is coming back from the Okta authZ page, we need to confirm it's good
if (isset($_GET['code'])) {
    // confirm the authZ server has same state as the local web server's state
    if ($_SESSION['state'] != $_GET['state']) {
        die('Authorization server returned an invalid state parameter');
    }

    // if Okta's authZ server threw an error, describe it to user
    if (isset($_GET['error'])) {
        die('Authorization server returned an error: ' . htmlspecialchars($_GET['error']));
    }

    // call our custom http fn with the code that the authZ server gave us to get a token
    $response = http($metadata->token_endpoint, [
        'grant_type' => 'authorization_code',
        'code' => $_GET['code'],
        'redirect_uri' => $redirect_uri,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
    ]);

    // throw an error if we don't get back an access token
    if (!isset($response->access_token)) {
        die('Error fetching access token');
    }

    // we will use this access token to determine who is currently logged into our app
    // the token has an 'introspection endpoint' which will tell us the current username
    $token = http($metadata->introspection_endpoint, [
        'token' => $response->access_token,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
    ]);

    $_SESSION['access_token_check'] = $response->access_token;

    if ($token->active == 1) {
        $_SESSION['username'] = $token->username;
        header('Location: /');
        die();
    }
}

// If there is a username, they are logged in, and we'll show the logged-in view
if (isset($_SESSION['username'])) {
    echo '<p><b>Access_token:</b> <br>' . $_SESSION['access_token_check'] . '</p>';
    echo '<p><b>Logged in as:</b> <br>' . $_SESSION['username'] . '</p>';
    echo '<form method="post">';
    echo '<input type="submit" name="logout" class="button" value="Logout"/>';
    echo '</form>';
    die();
}

// If there is no username, they are logged out, so show them the login link
if (!isset($_SESSION['username'])) {
    // Generate a random state parameter for CSRF security
    $_SESSION['state'] = bin2hex(random_bytes(5));

    // Build the authorization URL by starting with the authorization endpoint
    // and adding a few query string parameters identifying this application
    $authorize_url = $metadata->authorization_endpoint . '?' . http_build_query([
        'response_type' => 'code',
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'state' => $_SESSION['state'],
        'scope' => 'openid',
    ]);
    echo '<p>Not logged in</p>';
    echo '<p><a href="' . $authorize_url . '">Log In</a></p>';
}

// fn to make http req and return json response
function http($url, array $params = [])
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // if there are params, turn this into a POST and not a GET
    if ($params)
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    return json_decode(curl_exec($ch));
}
