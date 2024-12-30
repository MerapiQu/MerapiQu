<?php

namespace App\ModuleSystem;

use App\ModuleSystem\ModuleModel\Fragments\Fragment;
use App\ModuleSystem\ModuleModel\Fragments\FragmentClient;
use App\ModuleSystem\ModuleModel\Fragments\FragmentMethod;
use App\ModuleSystem\ModuleModel\Fragments\MethodEvent;
use App\ModuleSystem\ModuleModel\Services\ModuleService;
use Closure;

class DefaultService extends ModuleService
{
    private ModuleManager $moduleManager;
    function __construct(ModuleManager $moduleManager)
    {
        $this->moduleManager = $moduleManager;
    }

    function onCall(MethodEvent $event): void
    {
        
    }

    function handle(string $classOrigin, string $method, Closure $next): FragmentMethod
    {
        if ($classOrigin === Fragment::class) {
            if ($method === "get") {
                return FragmentMethod::fromString($this->getContext(), "getProvider");
            }
            if ($method === "getModule") {
                return FragmentMethod::fromString($this->getContext()->getModule(), "getProvider");
            }
            if ($method === "findModule") {
                return FragmentMethod::fromString($this->moduleManager, "get");
            }
            if ($method === "getPath") {
                return FragmentMethod::fromString($this->getContext(), "getPath");
            }
        }
        if ($classOrigin == FragmentClient::class) {
            if ($method === "get") {
                return FragmentMethod::fromString($this->getContext(), "getClient");
            }
        }
        return $next($classOrigin, $method);
    }
}
