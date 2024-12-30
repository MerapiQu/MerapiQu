<?php

namespace App\ModuleSystem\ModuleModel\Fragments;

class MethodEvent
{

    readonly string $origin;
    readonly string $methodName;
    readonly array $params;
    readonly FragmentMethod $fragmentMethod;

    public function __construct(string $origin, string $methodName, array $params, FragmentMethod $fragmentMethod)
    {
        $this->origin = $origin;
        $this->params = $params;
        $this->methodName     = $methodName;
        $this->fragmentMethod = $fragmentMethod;
    }
}
