<?php

namespace MerapiPanel\Admin;

use Il4mb\Routing\Http\Request;
use Closure;

class Middleware implements \Il4mb\Routing\Middlewares\Middleware
{

    function handle(Request $request, Closure $next)
    {
        $next();
    }
}
