<?php

namespace Duffleman\JSONClient;

use GuzzleHttp\Client;

class JSONClient
{

    protected $client;

    protected $timeout = 2.0;

    public function __construct($base_url = '', $headers = [])
    {
        $opts = [];
        $opts['timeout'] = $this->timeout;
        $opts['headers'] = [
            'User-Agent' => \GuzzleHttp\default_user_agent() . ' json-client/0.1',
        ];

        if (!empty($base_url)) {
            $opts['base_uri'] = $base_url;
        }

        $this->client = new Client($opts);
    }
}