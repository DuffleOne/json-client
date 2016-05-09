<?php

use Duffleman\JSONClient\JSONClient;

require('vendor/autoload.php');

$client = new JSONClient('http://localhost:3000/', [
	'Authorization' => 'CuvvaInternal 01.test',
]);

$response = $client->post('1/stats', [
	'start' => '2015-04-01',
	'end'   => '2015-04-30',
]);

dump($response);