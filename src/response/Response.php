<?php

namespace PhpJsonRpc2;


class Response implements \JsonSerializable
{

    private $id;

    private $jsonrpc = "2.0";

    private $result;

    public function __construct($id, $result = null)
    {
        $this->id = $id;
        $this->result = $result;
    }

    /**
     * @param $result mixed
     *
     * @return void
     * */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * @return mixed
     * */
    public function getResult(){
        return $this->result;
    }

    /**
     * @return integer
     * */
    public function getId(){
        return $this->id;
    }

    function jsonSerialize()
    {
        return ["jsonrpc" => $this->jsonrpc, "result" => $this->getResult(), "id" => $this->getId()];
    }
}