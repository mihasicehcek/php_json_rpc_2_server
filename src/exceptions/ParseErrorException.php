<?php

namespace PhpJsonRpc2;

use Exception;

/**
 * Invalid JSON was received by the server. An error occurred on the server while parsing the JSON text.
 * */
class ParseErrorException extends BaseJsonRpcException
{
    public function __construct($data = null, Exception $previous = null)
    {
        parent::__construct("Parse error", -32700, $data, $previous);
    }
}