<?php

namespace PhpJsonRpc2\Tests;

use PhpJsonRpc2\JsonRpcServer;
use PHPUnit\Framework\TestCase;

class ClassMethodCallStrategyTest extends TestCase
{

    public function testCallUnexistedMethod(){
        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode(["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.foo", "params" => ['a' => 10, 'b' => 15], "id" => 1]);

        $jsonRpcServer->setRequestAsJson($request);


        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));

        $this->assertObjectHasAttribute("error", $response);
        $this->assertObjectHasAttribute("id", $response);
        $this->assertObjectHasAttribute("code", $response->error);
        $this->assertObjectHasAttribute("message", $response->error);
        $this->assertEquals(-32601, $response->error->code);
        $this->assertEquals("Method not found", $response->error->message);
    }


    public function testCallUnexistedClass(){
        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode(["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Failed.add", "params" => ['a' => 10, 'b' => 15], "id" => 1]);

        $jsonRpcServer->setRequestAsJson($request);


        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));

        $this->assertObjectHasAttribute("error", $response);
        $this->assertObjectHasAttribute("id", $response);
        $this->assertObjectHasAttribute("code", $response->error);
        $this->assertObjectHasAttribute("message", $response->error);
        $this->assertEquals(-32601, $response->error->code);
        $this->assertEquals("Method not found", $response->error->message);
    }

    public function testDivisonByZero(){
        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode(["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.methodThatThrowingException", "params" => ['number' => 50], "id" => 1]);

        $jsonRpcServer->setRequestAsJson($request);

        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));

        $this->assertObjectHasAttribute("error", $response);
        $this->assertObjectHasAttribute("id", $response);
        $this->assertObjectHasAttribute("code", $response->error);
        $this->assertObjectHasAttribute("message", $response->error);
        $this->assertEquals(-32603, $response->error->code);
        $this->assertEquals(1, $response->id);
        $this->assertEquals("Internal error", $response->error->message);
    }

    public function testCallingWithInvalidParameters(){
        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode(["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => ["a" => 5], "id" => 2]);

        $jsonRpcServer->setRequestAsJson($request);

        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));
        $this->assertObjectHasAttribute("error", $response);
        $this->assertObjectHasAttribute("id", $response);
        $this->assertObjectHasAttribute("code", $response->error);
        $this->assertObjectHasAttribute("message", $response->error);
        $this->assertEquals(-32602, $response->error->code);
        $this->assertEquals("	Invalid params", $response->error->message);


        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode(["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => ["b" => 5], "id" => 2]);

        $jsonRpcServer->setRequestAsJson($request);

        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));
        $this->assertObjectHasAttribute("error", $response);
        $this->assertObjectHasAttribute("id", $response);
        $this->assertObjectHasAttribute("code", $response->error);
        $this->assertObjectHasAttribute("message", $response->error);
        $this->assertEquals(-32602, $response->error->code);
        $this->assertEquals("	Invalid params", $response->error->message);


        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode(["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\Calulator.add", "params" => [5], "id" => 2]);

        $jsonRpcServer->setRequestAsJson($request);

        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));
        $this->assertObjectHasAttribute("error", $response);
        $this->assertObjectHasAttribute("id", $response);
        $this->assertObjectHasAttribute("code", $response->error);
        $this->assertObjectHasAttribute("message", $response->error);
        $this->assertEquals(-32602, $response->error->code);
        $this->assertEquals("	Invalid params", $response->error->message);
    }

}