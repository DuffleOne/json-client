<?php

namespace Duffleman\JSONClient;

use Duffleman\JSONClient\Collections\CollectionManager;
use Duffleman\JSONClient\Exceptions\JSONError;
use Duffleman\JSONClient\Exceptions\JSONLibraryException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

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

	public function get($url, $query = [], $headers = [])
	{
		return $this->request('GET', $url, [], $query, $headers);
	}

	private function request($method, $url, array $body = [], $query = [], $headers = [])
	{
		if (!empty($body)) {
			$body = encode($body);
			$headers['Content-Type'] = 'application/json';
		}

		if (empty($body)) {
			$body = null;
		}

		try {
			$response = $this->client->request($method, $url, [
				'query'   => $query,
				'body'    => $body,
				'headers' => $headers,
			]);
			$response_body = (string)$response->getBody();
		} catch (BadResponseException $exception) {
			return self::handleError($exception);
		}

		return CollectionManager::build(decode($response_body));
	}

	public static function handleError(BadResponseException $exception)
	{
		$response_body = (string)$exception->getResponse()->getBody();
		$array_body = decode($response_body);

		$code = $exception->getResponse()->getStatusCode();

		$message = null;
		if (isset($array_body['message'])) {
			$message = $array_body['message'];
		}

		throw new JSONError($message, $code, $array_body);
	}

	public function __call($name, $arguments)
	{
		$map = [
			0 => 'url',
			1 => 'body',
			2 => 'query',
			3 => 'headers',
		];

		/** @var string $url */
		/** @var array $body */
		/** @var array $query */
		/** @var array $headers */
		foreach ($map as $key => $value) {
			$$value = null;
			if (array_key_exists($key, $arguments)) {
				$$value = $arguments[$key];
			}
		}

		$allowed = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
		$method = strtoupper($name);

		if (!in_array($method, $allowed)) {
			throw new JSONLibraryException("bad_method");
		}

		return $this->request($method, $url, $body, $query, $headers);
	}
}