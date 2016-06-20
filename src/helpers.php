<?php

namespace Duffleman\JSONClient;

function encode(array $body)
{
    $json = json_encode($body, JSON_UNESCAPED_SLASHES);

    return $json;
}

function decode($body)
{
    $array = json_decode($body, true);

    return $array;
}

function has_string_keys(array $array)
{
    return count(array_filter(array_keys($array), 'is_string')) > 0;
}
