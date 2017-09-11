<?php

namespace PhpJsonRpc2\Tests;


class Calulator
{
    public function add($a, $b){
        return $a + $b;
    }

    public function diff($a, $b){
        return $a - $b;
    }

    public function addWithDefault($a = 5, $b = 10){
        return $a + $b;
    }

    public function methodThatThrowingException($number){
        $number++;
        throw new \Exception();
    }
}