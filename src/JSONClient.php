<?php

namespace Duffleman\JSONClient;

use GuzzleHttp\Client;

class JSONClient
{

	protected $client;

	protected $global_headers = [
		'Accept' => 'application/json',
	];

	protected $timeout = 10.0;

	public function __construct($base_url = '', array $headers = [])
	{
		$opts = [];
		$opts['timeout'] = $this->timeout;
		$opts['headers'] = [
			'User-Agent' => \GuzzleHttp\default_user_agent() . ' json-client/0.1',
		];

		if (!empty($base_url)) {
			$opts['base_uri'] = $base_url;
		}

		$opts['headers'] = array_merge($this->global_headers, $opts['headers'], $headers);

		$this->client = new Client($opts);
	}

	private function request($method, $url, array $body = [], $query = [], $headers = [])
	{
		if (!empty($body)) {
			$body = encode($body);
			$headers['Content-Type'] = 'application/json';
		}

		$response = $this->client->request($method, $url, [
			'query'   => $query,
			'body'    => $body,
			'headers' => $headers,
		]);
		$response_body = (string)$response->getBody();

		return new Generic(decode($response_body));
	}

	public function get($url, $query = [], $headers = [])
	{
		return $this->request('GET', $url, [], $query, $headers);
	}
	

	public function __call($name, $arguments)
	{
		// TODO: if $query or $headers is not set, this does not work.
		list($url, $body, $query, $headers) = $arguments;

		return $this->request('POST', $url, $body, $query, $headers);
	}
}