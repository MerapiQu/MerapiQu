<?php

namespace App\CoreModules;

class EnvironmentManager
{

    public static function addPath($path)
    {
        $debug = debug_backtrace()[1];
        error_log(print_r($debug, 1));
    }
}
