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

	protected static $version = '0.1.0';

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
	 * Should the response return as:
	 * -1: Response() object from Guzzle.
	 * 0: String
	 * 1: Array
	 * 2: Generic/Collection
	 *
	 * @var bool
	 */
	protected $mode = 2;

	/**
	 * JSONClient constructor.
	 *
	 * @param string $base_url
	 * @param array  $headers
	 */
	public function __construct($base_url = '', array $headers = [], $timeout = 10)
	{
		$opts = [];
		$opts['timeout'] = $this->timeout = $timeout;

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
	 * @return Collections\Generic|\Illuminate\Support\Collection|null|void
	 * @throws JSONError
	 * @throws JSONLibraryException
	 */
	public function request($method, $url, $body = [], $query = [], $headers = [])
	{
		list($body, $query, $headers) = $this->setupVariables($body, $query, $headers);

		if (!empty($body)) {
			$body = encode($body);
			$headers['Content-Type'] = 'application/json';
		} else {
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
			self::handleError($exception);
		}

		switch ($this->mode) {
			case -1:
				return $response;
			case 0:
				return $response_body;
			case 1:
				if(!empty($response_body)) {
					return decode($response_body);
				}

				return null;
			case 2:
				if(!empty($response_body)) {
					return CollectionManager::build(decode($response_body));
				}

				return null;
			default:
				throw new JSONLibraryException('unknown_mode_set');
		}
	}

	/**
	 * Return a promise for async requests.
	 *
	 * @param string $method
	 * @param string $url
	 * @param array  $body
	 * @param array  $query
	 * @param array  $headers
	 * @return \GuzzleHttp\Promise\PromiseInterface
	 */
	public function requestAsync($method, $url, $body = [], $query = [], $headers = [])
	{
		if (!empty($body)) {
			$body = encode($body);
			$headers['Content-Type'] = 'application/json';
		} else {
			$body = null;
		}

		$headers = array_merge($this->global_headers, $headers);

		return $this->client->requestAsync($method, $url, [
			'query'   => $query,
			'body'    => $body,
			'headers' => $headers,
		]);
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
	 * Set the mode fluently.
	 *
	 * @param int $mode
	 * @return $this
	 * @throws JSONLibraryException
	 */
	public function mode($mode)
	{
		$acceptable_modes = [-1, 0, 1, 2];
		if (!in_array($mode, $acceptable_modes)) {
			throw new JSONLibraryException('bad_mode_set');
		}
		$this->mode = $mode;

		return $this;
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

	/**
	 * Setup empty arrays if null given.
	 *
	 * @param $body
	 * @param $query
	 * @param $headers
	 * @return array
	 */
	private function setupVariables($body, $query, $headers)
	{
		if (is_null($headers)) {
			$headers = [];
		}
		if (is_null($query)) {
			$query = [];
		}
		if (is_null($body)) {
			$body = [];
		}

		return [$body, $query, $headers];
	}
}
