<?php

namespace App\HttpSystem\Routers;

interface Interceptor
{
    function addHandler(Route $route): bool;
    
    function preHandler(Route $route): bool;

    function postHandler(Route $route): bool;
}
