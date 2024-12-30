<?php

namespace App\ModuleSystem\ModuleModel\Fragments;

/**
 * WireTransformer
 * @description like a transformer, this entity used by their wires
 * @author      ilham b <durianbohong@gmail.com>
 * @copyright   2024    ilham b
 */
abstract class WireTransformer
{

    function __construct()
    {
        FragmentWire::attach($this);
    }
    /**
     * when wire send invoke request
     * @param string $classOrigin a class name where invoke request
     * @param string $method a method name to invoke
     * @param array $args argument for method
     * @return mixed
     */
    abstract function call(string $classOrigin, string $method, array $args = []);
    /**
     * get uniq identifier
     * @return string
     */
    abstract function getSignature(): string;
}
