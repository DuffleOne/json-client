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