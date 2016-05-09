<?php

namespace Duffleman\JSONClient;

use GuzzleHttp\Client;

class JSONClient
{

    protected $client;

    public static function __construct($base_url = null, $headers = [])
    {
        $base = $base_url ?? '';
        $instance = self::$instance = new self($base, new Client());
        $instance->base_url = $base;

        return $instance;
    }
}