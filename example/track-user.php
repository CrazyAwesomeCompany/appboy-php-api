<?php

use GuzzleHttp\Client;
use CAC\AppBoy\Api\AppBoyApi;

require __DIR__ . '/../vendor/autoload.php';

// Edit configuration
$appboyEndpoint = 'https://rest.api.appboy.eu';
$appboyAppId = '{APPBOY_APP_ID}';
// End configuration


$guzzle = new Client([
    'base_uri' => $appboyEndpoint, // Make sure to use your application endpoint
    'verify' => false,
]);

$api = new AppBoyApi($guzzle, $appboyAppId);


$users = [
    [
        'external_id' => 'nick001',
        'first_name' => 'Nick',
        'last_name' => 'de Groot',
        'email' => 'nick@crazyawesomecompany.com',
        'email_subscribe' => 'opted_in',
    ],
];

$response = $api->trackUsers($users);

var_dump($response->getBody()->getContents());
