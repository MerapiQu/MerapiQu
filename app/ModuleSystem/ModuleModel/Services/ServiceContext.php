<?php

namespace App\ModuleSystem\ModuleModel\Services;

use App\ModuleSystem\ModuleModel\Fragments\FragmentContext;
use App\ModuleSystem\ModuleModel\Fragments\MethodEvent;
use App\ModuleSystem\ModuleModel\Services\ModuleService;
use Closure;
use ReflectionObject;

class ServiceContext
{
    private FragmentContext $context;
    /**
     * @var array<ModuleService>
     */
    private array $services = [];

    public function __construct(FragmentContext $context, array $services)
    {
        $this->context = $context;
        $this->services = array_map(function (ModuleService $service) {
            $this->injectProperty($service, "context", $this->context);
            return $service;
        }, $services);
    }

    
    protected function injectProperty(object $target, string $name, $value): void
    {
        $reflector = new ReflectionObject($target);
        while (!$reflector->hasProperty($name) && $reflector->getParentClass()) {
            $reflector = $reflector->getParentClass();
        }
        $property = $reflector->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($target, $value);
        $property->setAccessible(false);
    }


    function fireOnCall(MethodEvent $event): void
    {
        foreach ($this->services as $service) {
            $service->onCall($event);
        }
    }


    function handle(string $classOrigin, string $methodName, Closure $next)
    {
        $next = $this->createNextClosure($next,  count($this->services) - 1);
        return $next($classOrigin,  $methodName);
    }


    function createNextClosure(Closure $default, int $index): Closure
    {
        return function (string $classOrigin, string $methodName) use ($default, $index) {
            if ($index < 0) {
                return $default($classOrigin, $methodName);
            }

            $service = $this->services[$index];
            $next = $this->createNextClosure($default, $index - 1);
            return $service->handle($classOrigin, $methodName, $next);
        };
    }
}
