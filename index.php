<?php

require 'vendor/Slim/Slim/Slim.php';
require 'vendor/PHP-OAuth2/Client.php';
require 'vendor/PHP-OAuth2/GrantType/IGrantType.php';
require 'vendor/PHP-OAuth2/GrantType/AuthorizationCode.php';

session_cache_limiter(false);
session_start();

\Slim\Slim::registerAutoloader();

const AUTHORIZATION_ENDPOINT = 'http://192.168.1.107:3001/oauth/authorize';
const TOKEN_ENDPOINT = 'http://192.168.1.107:3001/oauth/token';

const CLIENT_ID = 'leGRivqSXaSgmDYEo7lOYvebDKABT1YLoOxCfNNL';
const CLIENT_SECRET = 'jlnU9gHO24rI25Ji3I5BkmgkpE58dtCDOfzLDkiC';
const REDIRECT_URI = "http://localhost:3000/oauth/callback";

$client = new OAuth2\Client(CLIENT_ID, CLIENT_SECRET);
$app = new \Slim\Slim();

$app->get('/oauth/test', function () use ($app,$client) {
	$auth_url = $client->getAuthenticationUrl(AUTHORIZATION_ENDPOINT, REDIRECT_URI);
	$app->redirect($auth_url);
});

$app->get('/oauth/callback', function () use ($app,$client) {
	$params = array('code' => $_GET['code'], 'redirect_uri' => REDIRECT_URI);
	$access_token = $client->getAccessToken(TOKEN_ENDPOINT, 'authorization_code', $params);
	$_SESSION["access_token"] = $access_token['result']['access_token'];
	echo "Successfully authenticated with the server";
});

$app->get('/some_page', function () use ($app,$client) {
	$client->setAccessToken($_SESSION["access_token"]);
    $response = $client->fetch('http://192.168.1.107:3001/api/data.json');
    var_dump($response, $response['result']);
});

$app->run();

?>