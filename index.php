<?php

use Duffleman\JSONClient\JSONClient;

require('vendor/autoload.php');

$client = JSONClient::build();

dump($client);