<?php

use Duffleman\JSONClient\JSONClient;

require('vendor/autoload.php');

$client = new JSONClient('http://jsonplaceholder.typicode.com/');

try {
	$response = $client->get('users');

	foreach($response as $user)
	{
		dd($user->company->name);
	}

} catch (Exception $error) {
	dump($error);
}
