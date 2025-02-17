<?php

namespace MerapiPanel\App\Http;

class Request extends \Il4mb\Routing\Http\Request
{
    private static self $instance;

    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
