<?php

namespace Duffleman\JSONClient;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Class JSONClient.
 */
class JSONClient
{
    protected static string $version = '2.0.0';
    protected $client;
    protected float $timeout = 10;
    protected array $global_headers = [
        'Accept' => 'application/json',
    ];

    public function __construct(string $base_url, array $opts = [])
    {
        $opts['base_uri'] = $base_url;

        $this->global_headers['User-Agent'] = \GuzzleHttp\default_user_agent().' json-client/'.self::$version;

        if (array_key_exists('headers', $opts)) {
            $this->global_headers = array_merge($this->global_headers, $opts['headers']);
        }

        $this->client = new Client($opts);
    }

    public function request(string $method, string $url, array $body = [], array $query = [], array $headers = [])
    {
        if (!empty($body)) {
            $body = encode($body);
            $headers['Content-Type'] = 'application/json';
        } else {
            $body = null;
        }

        $headers = array_merge($this->global_headers, $headers);

        $response = $this->client->request($method, $url, [
            'query' => $query,
            'body' => $body,
            'headers' => $headers,
        ]);

        $response_body = (string) $response->getBody();

        $out = json_decode($response_body);

        if (JSON_ERROR_NONE === json_last_error()) {
            return $out;
        }

        return $response_body;
    }
}
