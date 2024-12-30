<?php

namespace App\HttpSystem\Middlewares;

use App\HttpSystem\Request;
use App\HttpSystem\Response;
use Closure;

class MiddlewareExecutor
{
    private $middlewares = [];
    public function __construct(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }


    public function __invoke($request, Closure $next): Response
    {
        $next = $this->createNextClosure($next,  count($this->middlewares) - 1);
        return $next($request);
    }


    function createNextClosure(Closure $default, int $index): Closure
    {
        return function (Request $request) use ($default, $index) {
            
            if ($index < 0) {
                return $default($request);
            }

            $middleware = $this->middlewares[$index];
            $next = $this->createNextClosure($default, $index - 1);

            return $middleware->handle($request, $next);
        };
    }
}
