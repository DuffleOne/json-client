# json-client
JSON Client for PHP

### About
I always use JSON API's but Guzzle alone requires a lot of setup for making it only handle JSON bodies and responses. So I built this.

### It's... *not* authoratative *anymore*.
Anything returned is decided by you. Review the example below for more detailed explanation on how. You can return a raw response object from Guzzle, a string, an array, or an Illuminate\Collection or a Generic.

A Generic is my own custom class that handle's any object of data. I would use the PHP default but I find it frustrating to work with.

### Helpers
This library also contains a `helpers.php` file that contains functions for quick encoding and decoding JSON strings with proper encoding in a safe form as the default `json_encode()` function needs a whole bunch of params.

### Modes
| Mode | Return Type                    |
| ---- | ------------------------------ |
| -1   | GuzzleHttp\Psr7\Response       |
| 0    | `string`                       |
| 1    | `array`                        |
| 2 *(default)* | Generic or Collection |

## Examples

### Basic Usage
```php
<?php

use Duffleman\JSONClient\JSONClient;

require('vendor/autoload.php');

$client = new JSONClient('http://jsonplaceholder.typicode.com/');

try {
    $response = $client->get('users');
    // Because it returns an array.
    // $response is of type Illuminate\Support\Collection

    foreach($response as $user)
    {
        // $user is a Duffleman\JSONClient\Collections\Generic
        // $user can be referenced like an object, or array.
        // $user can be dumped back out as a JSON string.
        // count($user) for a count of the elements within.
        // Items within $user can be Generics themselves.

        echo($user->name . "\n");
        echo($user['address']['geo']['lat'] . "\n");
        echo($user->company->name . "\n");
        echo("\n");
    }

} catch (Exception $error) {
    dump($error);
}
```

### Return result as array
```php
<?php

use Duffleman\JSONClient\JSONClient;

require('vendor/autoload.php');

$client = new JSONClient('http://jsonplaceholder.typicode.com/');

// mode(1) sets the output as an array.
$response = $client->mode(1)->get('users');

// mode is 1 here too, it remembers from before.
$response = $client->get('users');

var_dump(is_array($response)); // bool(true)
```

### Using Helpers
Currently, this library includes 3 helper functions it uses itself, but are public for you to use too, they exist in the Duffleman\JSONClient namespace. They won't override functions you write yourself with the same name.

#### `encode`
Encode encodes an array into JSON. You may ask why I don't just use `json_encode` provided by PHP. This is because `json_encode` does not encode slashes probably within strings at times. The exact function you need to call to encode JSON properly is:

`$json = json_encode($body, JSON_UNESCAPED_SLASHES);`

Cool right? So `encode` is a easy shortcut :)

#### `decode`
This just is an alias for `json_decode`. No reason for it, I'm just lazy and wanted it to conform with encode.

#### `has_string_keys`
Because PHP uses associative arrays, this lets the library know if it should be pushed into a Generic or Collection, does it have named keys? Returns bool(true/false).

#### Example
```php
<?php

use function Duffleman\JSONClient\encode;

require('vendor/autoload.php');

// Do not forget the use statement!
// But afterwards, we can use it as a normal function :)

$my_data = [
    'name'  => 'James',
    'age'   => 21,
    'admin' => true,
];

$json = encode($my_data);
```
