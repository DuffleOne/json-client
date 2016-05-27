<?php

namespace Duffleman\JSONClient\Exceptions;

use Duffleman\JSONClient\Generic;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class JSONError
 *
 * @package Duffleman\JSONClient\Exceptions
 */
class JSONError extends Exception
{

	/**
	 * The body of the response.
	 *
	 * @var array
	 */
	private $body;

	/**
	 * JSONError constructor.
	 *
	 * @param string $message
	 * @param int    $code
	 * @param array  $body
	 */
	public function __construct($message = "", $code = 0, array $body = [])
	{
		$this->body = $body;
		parent::__construct($message, $code, null);
	}

	/**
	 * Return the response.
	 *
	 * @return array
	 */
	public function response()
	{
		return $this->body;
	}
}
