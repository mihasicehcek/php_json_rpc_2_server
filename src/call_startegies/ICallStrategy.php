<?php

namespace PhpJsonRpc2;


interface ICallStrategy
{

    /**
     * @param $method string method name
     *
     * @param $params array
     *
     * @return Response
     * */
    public function call($method, $params);
}