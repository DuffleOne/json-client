<?php

use Duffleman\JSONClient\JSONClient;

require('vendor/autoload.php');

$client = new JSONClient('http://jsonplaceholder.typicode.com/');

try {
	$response = $client->get('users');

	foreach($response as $user)
	{
		echo($user->name . '<br>');
		echo($user['address']['geo']['lat'] . '<br>');
		echo($user->company->name . '<br>');
		echo("<br>");
	}

} catch (Exception $error) {
	dump($error);
}
