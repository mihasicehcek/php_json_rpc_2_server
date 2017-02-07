<?php

namespace PhpJsonRpc2;

use Exception;

class InvalidRequestException extends BaseJsonRpcException
{
    public function __construct($data = null, Exception $previous = null)
    {
        parent::__construct("Invalid Request", -32600, $data, $previous);
    }
}