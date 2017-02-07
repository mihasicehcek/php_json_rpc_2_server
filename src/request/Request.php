<?php

namespace PhpJsonRpc2;


use JsonParser\Exception;
use JsonParser\JsonParser;

class Request
{

    private $method;

    private $params;

    private $id;

    private $jsonrpc;

    public function __construct($jsonrpc, $method, $params, $id = null)
    {
        $this->method = $method;
        $this->params = $params;
        $this->id = $id;
        $this->jsonrpc = $jsonrpc;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Contains array or object. Depends of request type
     *
     * @return array | \stdClass
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Request with no id.
     *
     * @return boolean
     */
    public function isNotification(){
        return !$this->id ? true : false;
    }

    /**
     * @return boolean
     * */
    public function isRequestValid(){
        return $this->jsonrpc == "2.0" && $this->method;
    }


    /**
     * Create Request or Requests array from string
     *
     * @param $request string
     *
     * @throws ParseErrorException
     *
     * @return Request | Request[]
     * */
    public static function requestFactory($request)
    {
        try{
            $request = JsonParser::jsonDecode($request);
        }catch(Exception $ex){
            throw new ParseErrorException(null, $ex);
        }


        if(is_array($request)){
            $resultRequest = [];
            foreach ($request as $item){
                $resultRequest[] = self::createRequest($item);
            }
        }else{
            $resultRequest = self::createRequest($request);
        }

        return $resultRequest;
    }

    /**
     * Create Request object from stdClass
     *
     * @param $requestObj \stdClass
     *
     * @return Request
     * */
    private static function createRequest($requestObj){
        return new Request(
            isset($requestObj->jsonrpc) ? $requestObj->jsonrpc : null,
            isset($requestObj->method) ? $requestObj->method : null,
            isset($requestObj->params) ? $requestObj->params : null,
            isset($requestObj->id) ? $requestObj->id : null
        );
    }
}