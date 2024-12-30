<?php

namespace App\ModuleSystem\ModuleModel\Fragments;

use BadMethodCallException;

/**
 * FragmentWire
 * @description is a wire used for connection bettween fragment
 * @author      ilham b <durianbohong@gmail.com>
 * @copyright   2024    ilham b
 */

class FragmentWire
{

    private static $callStack = [];
    private static $fragments = [];
    /**
     * attach transformer to the wire
     * @param \App\ModuleSystem\ModuleModel\Fragments\WireTransformer $provider
     * @return void
     */
    public static function attach(WireTransformer $provider)
    {
        self::$fragments[$provider->getSignature()] = $provider;
    }
    /**
     * send invoke request
     * @param mixed $signature
     * @param mixed $method
     * @param array $args
     * @throws \BadMethodCallException
     * @throws \Exception
     * @return mixed
     */
    public static function fire($signature, $method, $args = [])
    {

        $origin = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[1] ?? [];
        $invoke = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[2] ?? [];

        if (empty($method)) {
            throw new BadMethodCallException("Can't execute empty method");
        }
        if (isset($origin['class'])) {

            if (!isset(self::$fragments[$signature])) {
                throw new \Exception("signature does't relate with any provider");
            }

            $callSignature = "$signature:$method";
            if (array_key_exists($callSignature, self::$callStack)) {
                throw new \Exception("Failed execute $method, infinite looping detect, call from " . ($invoke['class'] ?? ("$invoke[file]:$invoke[line]")));
            }
            // Add the current call to the stack
            self::$callStack[] = $callSignature;


            $result = self::$fragments[$signature]->call($origin['class'], $method, $args);


            // Remove the current call from the stack
            array_pop(self::$callStack);

            return $result;
        }
    }
}
