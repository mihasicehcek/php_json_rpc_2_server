# PHP JsonRPC2 Server

## Description

It is php implementation of the [JSON-RPC 2.0](http://www.jsonrpc.org/specification "Specification") server

Working principle is very simple
1. Create an instance of the class \PhpJsonRpc2\JsonRpcServer
2. In method setRequest pass instance or array of \PhpJsonRpc2\Request
3. Using method getResponse getting instance or array of instances \PhpJsonRpc2\Response

There is also a \PhpJsonRpc2\ICallStrategy interface that can configure \PhpJsonRpc2\JsonRpcServer for changing strategy of calling procedure.
By default \PhpJsonRpc2\JsonRpcServer is configured by strategy \PhpJsonRpc2\ClassMethodCallStrategy, that divide request parameter "method"(for example Calculator.sum) by dot and create "Calculator" instance and call its method "sum".

##Examples

###Simple Example

```php
//simulating a request
$json = json_encode([
    "jsonrpc" => "2.0", 
    "method" => "Calculator.sum", 
    "params" => [ "a" => 10, "b" => 15], 
    "id" => 1
]);

//Creating instance of \PhpJsonRpc2\JsonRpcServer
$jsonRpcServer = new \PhpJsonRpc2\JsonRpcServer();

//Passing json string
$jsonRpcServer->setRequestAsJson($json);

//geting Response
$response = $jsonRpcServer->getResponse(); 

echo json_encode($response); //{ jsonrpc : "2.0", result : 2, id : 1}

```

###Expamle with pre-conversion json string into object

You can convert json into instance or instances array before passing it into server object.
That can be implemented using static method\PhpJsonRpc2\Request::requestFactory($json).
But in this case you need to look after handling \PhpJsonRpc2\ParseErrorException, that can be throwed in case of invalid json parsing

```php
$json = json_encode([
    "jsonrpc" => "2.0", 
    "method" => "Calculator.sum", 
    "params" => [10, 15], 
    "id" => 1
]);

//Create request from json
$request = Request::requestFactory($json);

$jsonRpcServer = new \PhpJsonRpc2\JsonRpcServer();

//pass json into setRequest method
$jsonRpcServer->setRequest($request);

$response = $jsonRpcServer->getResponse();  

echo json_encode($response); //{ jsonrpc : "2.0", result : 2, id : 1}

```

##Notification

[Notification](http://www.jsonrpc.org/specification#notification "Specification") if passed, the response is returned null.

```php
$json = json_encode([
    "jsonrpc" => "2.0", 
    "method" => "Calculator.saveSum", 
    "params" => [15], 
    "id" => null
]);

$jsonRpcServer = new \PhpJsonRpc2\JsonRpcServer();

$jsonRpcServer->setRequestAsJson($json);

$response = $jsonRpcServer->getResponse(); //null
```

##Batch

See [Batch](http://www.jsonrpc.org/specification#batch "Specification")

```php
$json = json_encode([
    [
        "jsonrpc" => "2.0", 
        "method" => "Calculator.sum", 
        "params" => [15, 15], 
        "id" => 1
    ],
    [
        "jsonrpc" => "2.0", 
        "method" => "Calculator.sum", 
        "params" => [10, 50], 
        "id" => 2
    ]
]);

$jsonRpcServer = new \PhpJsonRpc2\JsonRpcServer();

$jsonRpcServer->setRequestAsJson($json);

$response = $jsonRpcServer->getResponse(); //null

json_encode($response) //[{ jsonrpc : "2.0", result : 15, id : 1},{ jsonrpc : "2.0", result : 60, id : 2}]
```

##Custom strategy call

You can define your own methods strategy call
For example we want to provide "method" not as class.method, but invoke some simple functions

```php
//Create our implementation if \PhpJsonRpc2\ICallStrategy interface(For simplicity, we imagine that we pass only the positional parameters)
class SimpleMethodCallStrategy implements ICallStrategy
{

    /**
    *@param $method string method name(define in "method" parameter of json object)
    *@param $params array It can be as indexing and associative array  
    */
    public function call($method, $params)
    {
        $function = new \ReflectionFunction($method);
        
        return $function->invokeArgs($params);
    }
   
}

$request = [
    "jsonrpc" => "2.0", 
    "method" => "sum", 
    "params" => [10, 15], 
    "id" => 1
];

$json = json_encode($request);

$jsonRpcServer = new \PhpJsonRpc2\JsonRpcServer();
//configure instance of JsonRpcServer by insatnce of SimpleMethodCallStrategy
$jsonRpcServer->setCallStrategy(new SimpleMethodCallStrategy());

$jsonRpcServer->setRequestAsJson($json);

$response = $jsonRpcServer->getResponse();  

echo json_encode($response); //{ jsonrpc : "2.0", result : 2, id : 1}
```

##Exceptions

All exceptions is subtype of base \PhpJsonRpc2\BaseJsonRpcException.
Exceptions not throwing, but its instance returned in response.

```php
$json = json_encode('{"jsonrpc": "2.0", "method": "foobar, "params": "bar", "baz]');

$jsonRpcServer = new \PhpJsonRpc2\JsonRpcServer();

$jsonRpcServer->setRequestAsJson($json);

$response = $jsonRpcServer->getResponse(); 

echo json_encode($response); //{ jsonrpc : "2.0", error : { code : -32700, message : "Parse error"}, id : null}

```

If you want to throw an exception in procedures, you need to throw instance of  \PhpJsonRpc2\InternalException or its subtype  