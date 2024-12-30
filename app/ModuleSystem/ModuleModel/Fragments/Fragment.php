<?php

namespace App\ModuleSystem\ModuleModel\Fragments;

use App\ModuleSystem\ModuleModel\Module;
use App\ModuleSystem\ModuleModel\ModuleClient;

abstract class Fragment
{

    private string $signature;
    protected function get(string $name): Fragment
    {
        return FragmentWire::fire($this->signature, "get", [$name]);
    }
    /**
     * get module
     * @return Module
     */
    final protected function getModule()
    {
        return FragmentWire::fire($this->signature, "getModule");
    }


    final protected function findModule(string $name): ?ModuleClient
    {
        return FragmentWire::fire($this->signature, "findModule", [$name]);
    }


    final function getPath(): string
    {
        return FragmentWire::fire($this->signature, "getPath");
    }

    final function getClass(): string
    {
        return get_class($this);
    }

    final function __call($method, $args)
    {
        return FragmentWire::fire($this->signature, $method, $args);
    }

    /**
     * default api method, that executed on fragment created
     * @return void
     */
    abstract function onCreate(): void;
}
