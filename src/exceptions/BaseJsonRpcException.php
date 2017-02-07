<?php

namespace PhpJsonRpc2;

use Exception;

abstract class BaseJsonRpcException extends \Exception implements \JsonSerializable
{

    public function __construct($message, $code, $data = null, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->data = $data;
    }

    private $id = null;

    private $data = null;

    public function jsonSerialize()
    {
        $json = [
            "jsonrpc" => "2.0",
            "error" => [
                "code" => $this->getCode(),
                "message" => $this->getMessage()
            ],
            "id" => $this->getId()
        ];

        if($this->data) $json["error"]["data"] = $this->data;

        return $json;

    }

    public function setId($id){
        $this->id = $id;
    }

    public function getId(){
        return $this->id;
    }

    public function getData(){
        return $this->data;
    }
}