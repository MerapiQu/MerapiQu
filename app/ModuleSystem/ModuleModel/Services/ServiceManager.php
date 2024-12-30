<?php

namespace App\ModuleSystem\ModuleModel\Services;

use App\ModuleSystem\ModuleModel\Fragments\FragmentContext;
use App\ModuleSystem\ModuleModel\Fragments\FragmentMethod;

class ServiceManager
{
    /**
     * @var array<ModuleService>
     */
    private $services = [];

    public function __construct(array $services)
    {
        $this->services = $services;
    }

    function addService(ModuleService $service)
    {
        $this->services[] = $service;
    }

    
    function load(FragmentContext $fragmentContext) {
        return new ServiceContext($fragmentContext, $this->services);
    }
}