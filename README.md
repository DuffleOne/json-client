# json-client
JSON Client for PHP

### About
I always use JSON API's but Guzzle alone requires a lot of setup for making it only handle JSON bodies and responses. So I built this.

### It's... authoratative.
Everything returned is either a collection of Generic's or a Collection of Generic's. Where a Generic is my own custom class that handle's any object of data. I would use the PHP default but I find it frustrating to work with. If you like the idea of this library but don't want to deal with my rubbish implementation of a Generic, please submit a bug report or issue and I'll build a requestRaw() method that just returns an array.

### Helpers
This library also contains a `helpers.php` file that contains functions for quick encoding and decoding JSON strings with proper encoding in a safe form as the default `json_encode()` function needs a whole bunch of params.

## Example
```php
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
```