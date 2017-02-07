<?php

namespace PhpJsonRpc2;

use Exception;

class MethodDoesNotExistException extends BaseJsonRpcException
{
    public function __construct($data = null, Exception $previous = null)
    {
        parent::__construct("Method not found", -32601, $data, $previous);
    }
}