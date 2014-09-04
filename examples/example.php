<?php

require(dirname(__FILE__).'/../vendor/autoload.php');

$api = new Snoobi\Client([
    'consumer_key' => 'CONSUMER_KEY',
    'consumer_secret' => 'CONSUMER_SECRET',
    'token' => 'TOKEN',
    'token_secret' => 'TOKEN_SECRET'
]);

$result = $api->get('health');
print_r($result);
