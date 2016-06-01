<?php

namespace Duffleman\JSONClient\Exceptions;

use Exception;

/**
 * Class JSONError.
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
    public function __construct($message = '', $code = 0, $body = [])
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
