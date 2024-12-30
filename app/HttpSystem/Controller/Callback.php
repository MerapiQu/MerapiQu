<?php

namespace App\HttpSystem\Controller;

use ReflectionClass;

class Callback
{
    private object $object;
    public string $method;
    public array $parameters;

    private function __construct(object $object, string $method, array $parameters = [])
    {
        $this->object   = $object;
        $this->method   = $method;
        $this->parameters = $parameters;
    }

    public function __invoke()
    {
        $payload = [];
        $arguments = func_get_args();
        $arguments = array_merge($arguments, $this->parameters);
        $parameters = $this->getParameters();

        foreach ($parameters as $i => $parameter) {
            $matched = false;

            if ($parameter->hasType()) {
                $expectedType = (string)$parameter->getType();

                foreach ($arguments as $key => $argument) {
                    // Check for primitive types
                    if (
                        ($expectedType === 'int' && is_int($argument)) ||
                        ($expectedType === 'string' && is_string($argument)) ||
                        ($expectedType === 'bool' && is_bool($argument)) ||
                        ($expectedType === 'float' && is_float($argument)) ||
                        (class_exists($expectedType) && is_a($argument, $expectedType, true)) || // Handle class inheritance
                        (interface_exists($expectedType) && in_array($expectedType, class_implements($argument))) // Handle interfaces
                    ) {
                        $payload[] = $argument;
                        unset($arguments[$key]); // Remove matched argument
                        $matched = true;
                        break;
                    }
                }
            }

            // If no match, add default value or null
            if (!$matched) {
                if ($parameter->isDefaultValueAvailable()) {
                    $payload[] = $parameter->getDefaultValue();
                } else {
                    $payload[] = null;
                }
            }
        }

        // Call the method on the object with the constructed payload
        return call_user_func_array([$this->object, $this->method], $payload);
    }

    static function from(object $object, string $methodName, array $parameters = []): Callback
    {
        
        return new Callback($object, $methodName, $parameters);
    }

    function getParameters()
    {
        $className = get_class($this->object);
        $reflectionClass = new ReflectionClass($className);
        return $reflectionClass->getMethod($this->method)->getParameters();
    }

    function __debugInfo()
    {
        return [
            "object" => $this->object,
            "method" => $this->method
        ];
    }
}
