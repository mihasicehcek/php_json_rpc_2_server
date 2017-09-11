<?php
/**
 * Created by PhpStorm.
 * User: Михаил
 * Date: 11.09.2017
 * Time: 10:40
 */

namespace PhpJsonRpc2\Tests;


use PhpJsonRpc2\PublicController;

class PublicCalculator implements PublicController
{

    public function add($a, $b){
        return $a + $b;
    }

}