<?php

namespace PhpJsonRpc2;

use Exception;

class InvalidParamsException extends BaseJsonRpcException
{
    public function __construct($data = null, Exception $previous = null)
    {
        parent::__construct("	Invalid params", -32602, $data, $previous);
    }
}