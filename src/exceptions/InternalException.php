<?php

namespace PhpJsonRpc2;

use Exception;

class InternalException extends BaseJsonRpcException
{
    public function __construct($data = null, Exception $previous = null)
    {
        parent::__construct("Internal error", -32603, $data, $previous);
    }
}