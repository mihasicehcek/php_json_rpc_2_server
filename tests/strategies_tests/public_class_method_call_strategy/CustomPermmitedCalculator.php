<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 14.09.2017
 * Time: 13:57
 */

namespace PhpJsonRpc2\Tests;


class CustomPermmitedCalculator implements PublicClassMethodCallStrategyTestInterface
{

    public function add($a, $b){
        return $a + $b;
    }

}