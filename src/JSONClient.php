<?php

namespace Duffleman\JSONClient;

use Duffleman\JSONClient\Collections\CollectionManager;
use Duffleman\JSONClient\Exceptions\JSONError;
use Duffleman\JSONClient\Exceptions\JSONLibraryException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;

/**
 * Class JSONClient
 *
 * @package Duffleman\JSONClient
 */
class JSONClient
{

	protected static $version = '0.0.5';

	/**
	 * Holds the root Guzzle client we work on top of.
	 *
	 * @var Client
	 */
	protected $client;

	/**
	 * Any global headers we care about.
	 * In this case, we ALWAYS want a JSON response where possible.
	 *
	 * @var array
	 */
	protected $global_headers = [
		'Accept' => 'application/json',
	];

	/**
	 * The base timeout for requests.
	 *
	 * @var float
	 */
	protected $timeout = 10.0;

	/**
	 * JSONClient constructor.
	 *
	 * @param string $base_url
	 * @param array  $headers
	 */
	public function __construct($base_url = '', array $headers = [])
	{
		$opts = [];
		$opts['timeout'] = $this->timeout;

		$this->global_headers['User-Agent'] = \GuzzleHttp\default_user_agent() . ' json-client/' . self::$version;
		$this->global_headers = array_merge($this->global_headers, $headers);

		if (!empty($base_url)) {
			$opts['base_uri'] = $base_url;
		}

		$this->client = new Client($opts);
	}

	/**
	 * Special method for GET because we never pass in a body.
	 *
	 * @param string $url
	 * @param array  $query
	 * @param array  $headers
	 * @return Collections\Generic|\Illuminate\Support\Collection|void
	 */
	public function get($url, $query = [], $headers = [])
	{
		$headers = array_merge($this->global_headers, $headers);

		return $this->request('GET', $url, [], $query, $headers);
	}

	/**
	 * The main meaty request method, handles
	 * all outgoing requests and deals with responses.
	 *
	 * @param string $method
	 * @param string $url
	 * @param array  $body
	 * @param array  $query
	 * @param array  $headers
	 * @return Collections\Generic|\Illuminate\Support\Collection|void
	 * @throws JSONError
	 */
	private function request($method, $url, $body = [], $query = [], $headers = [])
	{
		if (!empty($body)) {
			$body = encode($body);
			$headers['Content-Type'] = 'application/json';
		}

		if (empty($body)) {
			$body = null;
		}

		$headers = array_merge($this->global_headers, $headers);

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

	/**
	 * Handles the error for us, just a bit of code abstraction.
	 *
	 * @param BadResponseException $exception
	 * @throws JSONError
	 */
	public static function handleError(BadResponseException $exception)
	{
		$response_body = (string)$exception->getResponse()->getBody();
		$array_body = decode($response_body);

		$code = $exception->getResponse()->getStatusCode();

		$message = null;
		if (isset($array_body['message'])) {
			$message = $array_body['message'];
		} elseif (isset($array_body['code'])) {
			$message = $array_body['code'];
		}

		throw new JSONError($message, $code, $array_body);
	}

	/**
	 * Handles quick functions for all known HTTP verbs.
	 *
	 * @param $name
	 * @param $arguments
	 * @return Collections\Generic|\Illuminate\Support\Collection|void
	 * @throws JSONLibraryException
	 */
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
