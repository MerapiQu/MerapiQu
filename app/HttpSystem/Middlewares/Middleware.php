<?php

namespace App\HttpSystem\Middlewares;

use App\HttpSystem\Request;

abstract class Middleware
{
    abstract public function handle(Request $request, \Closure $next);
}
