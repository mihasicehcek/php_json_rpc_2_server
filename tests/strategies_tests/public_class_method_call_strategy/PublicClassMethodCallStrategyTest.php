<?php

namespace PhpJsonRpc2\Tests;

use PhpJsonRpc2\JsonRpcServer;
use PhpJsonRpc2\PublicClassMethodCallStrategy;
use PHPUnit\Framework\TestCase;

class PublicClassMethodCallStrategyTest extends TestCase
{

    public function testCallProtectedCalss(){
        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode(["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\PrivateCalculator.add", "params" => ['a' => 10, 'b' => 15], "id" => 1]);

        $jsonRpcServer->setRequestAsJson($request);
        $jsonRpcServer->setCallStrategy(new PublicClassMethodCallStrategy());


        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));

        $this->assertObjectHasAttribute("error", $response);
        $this->assertObjectHasAttribute("id", $response);
        $this->assertObjectHasAttribute("code", $response->error);
        $this->assertObjectHasAttribute("message", $response->error);
        $this->assertEquals(-32601, $response->error->code);
        $this->assertEquals("Method not found", $response->error->message);
    }


    public function testCallPublicClass(){
        $jsonRpcServer = new JsonRpcServer();

        $request = json_encode(["jsonrpc" => "2.0", "method" => "\\PhpJsonRpc2\\Tests\\PublicCalculator.add", "params" => ['a' => 10, 'b' => 15], "id" => 1]);

        $jsonRpcServer->setRequestAsJson($request);
        $jsonRpcServer->setCallStrategy(new PublicClassMethodCallStrategy());

        $response = $jsonRpcServer->getResponse();

        $response = json_decode(json_encode($response));

        $this->assertObjectHasAttribute("result", $response);
        $this->assertObjectHasAttribute("id", $response);
        $this->assertEquals(1, $response->id);
        $this->assertEquals(25, $response->result);
    }

}