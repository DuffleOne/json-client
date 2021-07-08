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
