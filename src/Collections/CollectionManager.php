<?php

namespace Duffleman\JSONClient\Collections;

use Illuminate\Support\Collection;

class CollectionManager
{

	public static function build(array $array)
	{
		if (has_string_keys($array)) {
			return new Generic($array);
		}

		$array = array_map(function ($item) {
			return new Generic($item);
		}, $array);

		return new Collection($array);
	}
}