<?php

if (!function_exists('encode')) {
	function encode(array $body)
	{
		$json = utf8_encode(json_encode($body, JSON_UNESCAPED_SLASHES));

		return $json;
	}
}

if (!function_exists('decode')) {
	function decode($body)
	{
		$array = json_decode($body, true);

		return $array;
	}
}
if (!function_exists('has_string_keys')) {
	function has_string_keys(array $array)
	{
		return count(array_filter(array_keys($array), 'is_string')) > 0;
	}
}

