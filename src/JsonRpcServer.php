<?php

namespace PhpJsonRpc2;


class JsonRpcServer
{

    /**@var ICallStrategy*/
    private $callStrategy;

    /**@var Request*/
    private $request;

    /**@var Request[]*/
    private $requests = null;

    /**@var BaseJsonRpcException*/
    private $error = null;

    public function __construct()
    {
        $this->setCallStrategy(new ClassMethodCallStrategy());
    }

    /**
     * Change procedure calling strategy
     *
     * @param $callStrategy ICallStrategy
     *
     * @return void
     * */
    public function setCallStrategy(ICallStrategy $callStrategy)
    {
        $this->callStrategy = $callStrategy;
    }

    /**
     * @param $request Request | Request[]
     *
     * @return void
     * */
    public function setRequest($request)
    {
        if(is_array($request)){
            $this->requests = $request;
        }else{
            $this->request = $request;
        }
    }

    /**
     * @param $json string
     *
     * @return void
     * */
    public function setRequestAsJson($json){
        try{
            $this->setRequest(Request::requestFactory($json));
        }catch (BaseJsonRpcException $ex){
            $this->error = $ex;
        }
    }

    /**
     * Return mixed array of Response or subtype of BaseJsonRpcException
     * or single JsonSerializable object or Response object
     *
     * @return \JsonSerializable | \JsonSerializable[]
     *
     * */
    public function getResponse()
    {
        $response = null;

        if($this->error) return $this->error;

        if(!is_null($this->requests)){
            if(count($this->requests) == 0) return new InvalidRequestException();
            $response = [];
            foreach($this->requests as $request){
                $result = $this->callRequest($request);
                if($result) $response[] = $result;
            }

            if(count($response) == 0) $response = null;
        }else{
            $result = $this->callRequest($this->request);
            if($result) $response = $result;
        }

        return $response;
    }

    /**
     * @param $request Request
     *
     * @return Response | BaseJsonRpcException
     * */
    private function callRequest(Request $request){
        $response = null;

        try{
            $result = $this->callStrategy->call($request->getMethod(), $request->getParams());
            if(!$request->isNotification()){
                $response = new Response($request->getId(), $result);
            }
        }catch(BaseJsonRpcException $ex){
            if(!$request->isNotification()) $ex->setId($request->getId());
            $response = $ex;
        }

        return $response;
    }
}