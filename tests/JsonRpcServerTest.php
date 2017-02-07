<?php

namespace PhpJsonRpc2\Tests;

use PhpJsonRpc2\JsonRpcServer;
use PhpJsonRpc2\ParseErrorException;
use PhpJsonRpc2\Request;
use PhpJsonRpc2\Response;
use PHPUnit\Framework\TestCase;

class JsonRpcServerTest extends TestCase
{

    public function responseObjectTest($res){
        $this->assertObjectHasAttribute("jsonrpc", $res);
        $this->assertEquals("2.0", $res->jsonrpc);
        $this->assertObjectHasAttribute("id", $res);
    }

    public function responseSuccessObjectValid($res){
        $this->responseObjectTest($res);
        $this->assertObjectHasAttribute("result", $res);
    }

    public function responseErrorObjectValid($err){
        $this->responseObjectTest($err);
        $this->assertObjectHasAttribute("error", $err);
        $this->assertObjectHasAttribute("code", $err->error);
        $this->assertObjectHasAttribute("message", $err->error);
    }

    public function testRpcWithPositionalParameters(){
        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode(["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => [10, 15], "id" => 1]);

        $jsonRpcServer->setRequestAsJson($request);

        $response = $jsonRpcServer->getResponse();

        $this->assertInstanceOf(Response::class, $response);
        /**@var $response Response*/
        $this->assertEquals(25, $response->getResult());
        $this->assertEquals(1, $response->getId());

        $this->responseSuccessObjectValid(json_decode(json_encode($response)));
    }

    public function testRpcWithNamedParameters(){
        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode(["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => ['a' => 10, 'b' => 15], "id" => 2]);

        $jsonRpcServer->setRequestAsJson($request);

        $response = $jsonRpcServer->getResponse();

        /**@var $response Response*/
        $this->assertEquals(25, $response->getResult());
        $this->assertEquals(2, $response->getId());
        $this->responseSuccessObjectValid(json_decode(json_encode($response)));

        $request = json_encode(["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => ['b' => 15, 'a' => 10], "id" => 3]);

        $jsonRpcServer->setRequestAsJson($request);

        $response = $jsonRpcServer->getResponse();

        $this->assertEquals(25, $response->getResult());
        $this->assertEquals(3, $response->getId());
        $this->responseSuccessObjectValid(json_decode(json_encode($response)));
    }

    public function testRpcBatchWithPositionalParameters(){
        $jsonRpcServer = new JsonRpcServer();

        $request = [
            ["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => [1, 1], "id" => 1],
            ["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.diff", "params" => [2, 2], "id" => 2],
            ["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => [3, 3], "id" => 5]
        ];

        $request = json_encode($request);

        $jsonRpcServer->setRequestAsJson($request);

        $response = $jsonRpcServer->getResponse();

        $this->assertEquals(3, count($response));
        foreach($response as $item){
            $this->assertInstanceOf(Response::class, $item);
        }

        /**@var $response Response[]*/
        $this->assertEquals(2, $response[0]->getResult());
        $this->assertEquals(0, $response[1]->getResult());
        $this->assertEquals(6, $response[2]->getResult());
        $this->assertEquals(5, $response[2]->getId());
        $response = json_decode(json_encode($response));
        $this->responseSuccessObjectValid($response[0]);
        $this->responseSuccessObjectValid($response[1]);
        $this->responseSuccessObjectValid($response[2]);
    }

    public function testRpcNotification(){
        $jsonRpcServer = new JsonRpcServer();

        $request = ["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => [3, 3]];

        $request = json_encode($request);

        $jsonRpcServer->setRequestAsJson($request);

        $response = $jsonRpcServer->getResponse();

        $this->assertNull($response);
    }

    public function testRpcBatchNotification(){
        $jsonRpcServer = new JsonRpcServer();

        $request = [
            ["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => [1, 1]],
            ["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.diff", "params" => [2, 2]],
            ["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => [3, 3]]
        ];

        $request = json_encode($request);

        $jsonRpcServer->setRequestAsJson($request);

        $response = $jsonRpcServer->getResponse();

        $this->assertNull($response);
    }

    public function testRpcCallWithEmptyArray(){
        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode([]);

        $jsonRpcServer->setRequestAsJson($request);

        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));

        $this->responseErrorObjectValid($response);
        $this->assertEquals(-32600, $response->error->code);
        $this->assertEquals("Invalid Request", $response->error->message);
    }

    public function testInvalidRequest(){
        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode(["jsonrpc" => "2.0", "method" => 1, "params" => "bar"]);

        $jsonRpcServer->setRequestAsJson($request);


        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));

        $this->responseErrorObjectValid($response);
        $this->assertEquals(-32600, $response->error->code);
        $this->assertEquals("Invalid Request", $response->error->message);
    }

    public function testRpcCallWithNotEmptyArray(){
        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode([1]);

        $jsonRpcServer->setRequestAsJson($request);


        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));


        $this->responseErrorObjectValid($response[0]);
        $this->assertEquals(-32600, $response[0]->error->code);
        $this->assertEquals("Invalid Request", $response[0]->error->message);
    }

    public function testRpcCallBatch(){
        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode([
            ["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => [1, 1], "id" => 1],
            ["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.diff", "params" => [2, 2], "id" => 2],
            ["foo" => "boo"],
            ["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => [3, 3], "id" => 5]
        ]);

        $jsonRpcServer->setRequestAsJson($request);

        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));


        $this->assertEquals(2, $response[0]->result);


        $this->responseErrorObjectValid($response[2]);
        $this->assertEquals(-32600, $response[2]->error->code);
        $this->assertEquals("Invalid Request", $response[2]->error->message);
    }

    public function testParseErrorThrowing(){
        $this->expectException(ParseErrorException::class);

        Request::requestFactory('{"jsonrpc": "2.0", "method": "foobar, "params": "bar", "baz]');
    }


    public function testRequestWithParseError(){
        $jsonRpcServer = new JsonRpcServer();

        $jsonRpcServer->setRequestAsJson('{"jsonrpc": "2.0", "method": "foobar, "params": "bar", "baz]');

        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));

        $this->responseErrorObjectValid($response);
        $this->assertEquals(-32700, $response->error->code);
        $this->assertEquals("Parse error", $response->error->message);

    }

}