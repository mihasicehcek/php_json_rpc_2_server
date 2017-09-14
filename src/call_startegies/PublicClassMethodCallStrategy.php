<?php

namespace PhpJsonRpc2;


class PublicClassMethodCallStrategy implements ICallStrategy
{

    private $permitionClass;

    public function __construct($permitionClass = null)
    {
        $this->permitionClass = $permitionClass ? $permitionClass : PublicController::class;
    }

    /**
     * @param $method string method name
     *
     * @param $params array
     *
     * @throws MethodDoesNotExistException
     *
     * @throws InvalidRequestException
     *
     * @throws InternalException
     *
     * @throws InvalidParamsException
     *
     * @return Response
     * */
    public function call($method, $params)
    {
        try{
            list($class, $method) = explode(".", $method);
        }catch(\Exception $ex){
            throw new InvalidRequestException();
        }

        try {
            $refClass = new \ReflectionClass($class);

            $controller = new $class();
            if(!($controller instanceof $this->permitionClass)){
                throw new MethodDoesNotExistException();
            }

            $method = $refClass->getMethod($method);
        }catch(\ReflectionException $ex){
            throw new MethodDoesNotExistException(null, $ex);
        }
        $callParams = [];


        $methodParameters = $method->getParameters();
        if(is_array($params)){
            $callParams = $params;
            $count = 0;
            foreach($methodParameters as $methodParameter){
                if(!$methodParameter->isDefaultValueAvailable()) $count++;
            }
            if($count > count($callParams)) throw new InvalidParamsException();
        }else{
            foreach($methodParameters as $methodParameter){
                if(isset($params->{$methodParameter->getName()})){
                    $callParams[$methodParameter->getPosition()] = $params->{$methodParameter->getName()};
                }else if(!$methodParameter->isDefaultValueAvailable()){
                    throw new InvalidParamsException();
                }
            }
        }

        try{
            return $method->invokeArgs($controller, $callParams);
        }catch (InternalException $ex){
            throw $ex;
        }catch (\Exception $ex){
            throw new InternalException(null, $ex);
        }
    }
}