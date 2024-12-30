<?php

namespace App\ModuleSystem\ModuleModel\Fragments;

use ReflectionMethod;

class FragmentMethod
{
    private $reflectionMethod;
    private $provider = null;
    public function __construct(object $provider, ReflectionMethod $reflectionMethod)
    {
        $this->provider = $provider;
        $this->reflectionMethod = $reflectionMethod;
    }

    function isPublic(): bool
    {
        return $this->reflectionMethod->isPublic();
    }

    /**
     * Summary of getAttribute
     * @template T
     * @param class-string<T> $name
     * @return T|null
     */
    function getAttribute(string $name)
    {
        $attributes = $this->reflectionMethod->getAttributes($name);
        foreach ($attributes as $attribute) {
            return $attribute->newInstance();
        }
    }
    function hasAttribute(string $name): bool
    {
        $attributes = $this->reflectionMethod->getAttributes($name);
        return count($attributes) > 0;
    }
    function getName(): string
    {
        return $this->reflectionMethod->getName();
    }

    final static function fromString(object $provider, string $method): FragmentMethod
    {
        $reflector = new \ReflectionObject($provider);
        $method = $reflector->getMethod($method);
        return new static($provider, $method);
    }

    function __debugInfo()
    {
        return get_class_methods($this);
    }
}
