# json-client

JSON Client for PHP

## About

I always use JSON API's but Guzzle alone requires a lot of setup for making it only handle JSON bodies and responses. So I built this.

## Basic Usage

```php
<?php

require 'vendor/autoload.php';

use Duffleman\JSONClient\JSONClient;

$client = new JSONClient('http://jsonplaceholder.typicode.com/');

try {
    $response = $client->request('GET', 'users');

    foreach ($response as $user) {
        echo $user->name."\n";
        echo $user->address->geo->lat."\n";
        echo $user->company->name."\n";
        echo "\n";
    }
} catch (Exception $error) {
    dump($error);
}
```

## Post request

```php
<?php

require 'vendor/autoload.php';

use Duffleman\JSONClient\JSONClient;

// important trailing slash
$client = new JSONClient('https://api.avocado.cuv-nonprod.app/1/service-staff/');

try {
    $response = $client->request('POST', '1/latest/list_staff_public');

    foreach ($response as $staff) {
        echo $staff->about->name.' ('.$staff->about->role.')';
        echo "\n";
    }
} catch (Exception $error) {
    dump($error);
}
```

### Using Helpers

Currently, this library includes 2 helper functions it uses, but are public for you to use too. They exist in the Duffleman\JSONClient namespace.

#### Example

```php
<?php

use function Duffleman\JSONClient\encode;

require 'vendor/autoload.php';

// Do not forget the use statement!
// But afterwards, we can use it as a normal function :)

$my_data = [
    'name' => 'James',
    'age' => 21,
    'admin' => true,
];

$json = encode($my_data);

echo $json;
```
