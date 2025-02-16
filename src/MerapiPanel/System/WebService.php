<?php

namespace MerapiPanel\System;

use Il4mb\Routing\Http\Response;
use Il4mb\Routing\Router;
use MerapiPanel\App\Http\Request;

abstract class WebService
{

    public function __construct(Router $router)
    {
        // do something
    }

    abstract function handle(Request $request, Response $response): bool;

    function getPath(): string
    {
        $reflector = new \ReflectionClass($this);
        return dirname($reflector->getFileName());
    }

    abstract function dispath(Response $response): Response;
}
