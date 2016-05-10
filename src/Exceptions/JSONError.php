<?php

namespace Duffleman\JSONClient\Exceptions;

use Duffleman\JSONClient\Generic;
use Exception;

class JSONError extends Exception
{

	private $body;

	public function __construct($message = "", $code = 0, array $body = [])
	{
		$this->body = $body;
		parent::__construct($message, $code, null);
	}

	public function response()
	{
		return new Generic($this->body);
	}
}