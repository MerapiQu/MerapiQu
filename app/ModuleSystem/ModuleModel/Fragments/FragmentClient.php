<?php

namespace App\ModuleSystem\ModuleModel\Fragments;

class FragmentClient
{

    readonly string $signature;
    final function __construct(string $signature)
    {
        $this->signature = $signature;
    }

    public function get(string $name): FragmentClient
    {
        if (strtolower($name) === "service") {
            throw new \InvalidArgumentException("Not allowed get default service");
        }
        return FragmentWire::fire($this->signature, "get", [$name]);
    }

    public function __call($method, $args)
    {
        return FragmentWire::fire($this->signature, $method, $args);
    }
}
