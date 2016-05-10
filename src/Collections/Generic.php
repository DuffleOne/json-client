<?php

namespace Duffleman\JSONClient\Collections;

use ArrayAccess;
use Countable;
use Illuminate\Support\Collection;

/**
 * Class Generic
 *
 * @package Duffleman\JSONClient\Collections
 */
class Generic implements Countable, ArrayAccess
{

	/**
	 * An array of elements this Generic holds values for.
	 *
	 * @var array
	 */
	protected $attributes = [];

	/**
	 * Generic constructor.
	 *
	 * This also turns elements of the original attribute array into
	 * Generic's themselves if they are string key'd arrays.
	 * Otherwise, they are returned as a new collection of Generic's.
	 *
	 * @param array $attributes
	 */
	public function __construct(array $attributes)
	{
		array_walk($attributes, function (&$item, $key) {
			if (is_array($item)) {
				if (has_string_keys($item)) {
					$item = new self($item);
				} else {
					$item = new Collection($item);
				}
			}
		});

		$this->attributes = $attributes;
	}

	/**
	 * Get an attribute by key.
	 *
	 * @param mixed $name
	 * @return mixed
	 */
	public function __get($name)
	{
		return $this->attributes[$name];
	}

	/**
	 * Set an attribute key to value.
	 *
	 * @param mixed $name
	 * @param mixed $value
	 * @return mixed
	 */
	public function __set($name, $value)
	{
		$this->attributes[$name] = $value;

		return $value;
	}

	/**
	 * Return the base attribute array.
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->attributes;
	}

	/**
	 * @inheritdoc
	 */
	public function count()
	{
		return count($this->attributes);
	}

	/**
	 * @inheritdoc
	 */
	public function offsetExists($offset)
	{
		return isset($this->attributes[$offset]);
	}

	/**
	 * @inheritdoc
	 */
	public function offsetGet($offset)
	{
		return isset($this->attributes[$offset]) ? $this->attributes[$offset] : null;
	}

	/**
	 * @inheritdoc
	 */
	public function offsetSet($offset, $value)
	{
		if (is_null($offset)) {
			$this->attributes[] = $value;
		} else {
			$this->attributes[$offset] = $value;
		}
	}

	/**
	 * @inheritdoc
	 */
	public function offsetUnset($offset)
	{
		unset($this->attributes[$offset]);
	}
}