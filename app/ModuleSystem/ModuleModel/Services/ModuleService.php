<?php

namespace App\ModuleSystem\ModuleModel\Services;

use App\ModuleSystem\ModuleModel\Fragments\FragmentContext;
use App\ModuleSystem\ModuleModel\Fragments\FragmentMethod;
use App\ModuleSystem\ModuleModel\Fragments\MethodEvent;
use Closure;

abstract class ModuleService
{
    private FragmentContext $context;
    final function getContext(): FragmentContext
    {
        return $this->context;
    }

    abstract function onCall(MethodEvent $event): void;

    abstract function handle(string $classOrigin, string $methodName, Closure $next): FragmentMethod;
}
