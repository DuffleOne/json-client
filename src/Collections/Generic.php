<?php

namespace Duffleman\JSONClient\Collections;

use ArrayAccess;
use Countable;

class Generic implements Countable, ArrayAccess
{

	protected $attributes = [];

	public function __construct(array $attributes)
	{
		array_walk($attributes, function (&$item, $key) {
			if (is_array($item)) {
				$item = new self($item);
			}
		});

		$this->attributes = $attributes;
	}

	public function __get($name)
	{
		return $this->attributes[$name];
	}

	public function __set($name, $value)
	{
		$this->attributes[$name] = $value;

		return $value;
	}

	public function all()
	{
		return $this->attributes;
	}

	public function count()
	{
		return count($this->attributes);
	}

	public function offsetExists($offset)
	{
		return isset($this->attributes[$offset]);
	}

	public function offsetGet($offset)
	{
		return isset($this->attributes[$offset]) ? $this->attributes[$offset] : null;
	}

	public function offsetSet($offset, $value)
	{
		if (is_null($offset)) {
			$this->attributes[] = $value;
		} else {
			$this->attributes[$offset] = $value;
		}
	}

	public function offsetUnset($offset)
	{
		unset($this->attributes[$offset]);
	}
}
